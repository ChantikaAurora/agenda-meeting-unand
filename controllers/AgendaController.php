<?php

namespace app\controllers;

use Yii;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use app\models\Agenda;
use app\models\AgendaSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AgendaController extends Controller
{
    public $layout = 'admin';

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'matchCallback' => function () {
                            /** @var \app\models\User $identity */
                            $identity = Yii::$app->user->identity;
                            return !Yii::$app->user->isGuest
                                && ($identity->can('manageAgenda') || $identity->can('viewAgenda'));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete', 'generate-qr', 'preview-invitations', 'send-invitations'],
                        'matchCallback' => function () {
                            /** @var \app\models\User $identity */
                            $identity = Yii::$app->user->identity;
                            return !Yii::$app->user->isGuest && $identity->can('manageAgenda');
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'generate-qr' => ['POST'],
                    'send-invitations' => ['POST'],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new AgendaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Agenda();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                if (empty($model->status)) {
                    $model->status = Agenda::STATUS_TERJADWAL;
                }

                if ($model->save()) {
                    $this->generateAndSaveQr($model);
                    Yii::$app->session->setFlash('success', 'Agenda berhasil dibuat, QR Code otomatis digenerate.');
                    return $this->redirect(['view', 'id' => $model->agenda_id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost
            && $model->load(Yii::$app->request->post())
            && $model->save()
        ) {
            Yii::$app->session->setFlash('success', 'Agenda berhasil diperbarui.');
            return $this->redirect(['view', 'id' => $model->agenda_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted_at = date('Y-m-d H:i:s');
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Agenda berhasil dihapus.');
        return $this->redirect(['index']);
    }

    public function actionGenerateQr($id)
    {
        $model = $this->findModel($id);
        $this->generateAndSaveQr($model);

        Yii::$app->session->setFlash('success', 'QR Code berhasil dibuat ulang.');
        return $this->redirect(['view', 'id' => $model->agenda_id]);
    }

    public function actionSendInvitations($id)
    {
        $model = $this->findModel($id);
        $members = $this->invitationMembers($model);

        if (empty($members)) {
            Yii::$app->session->setFlash('error', 'Belum ada peserta aktif dengan alamat email yang valid.');
            return $this->redirect(['view', 'id' => $model->agenda_id]);
        }

        $senderEmail = Yii::$app->params['senderEmail'] ?? 'noreply@example.com';
        $senderName = Yii::$app->params['senderName'] ?? 'Sistem Agenda Rapat';
        $sentCount = 0;
        $failedCount = 0;
        $latestLampiran = $this->latestLampiran($model);

        foreach ($members as $agendaMember) {
            $member = $agendaMember->member;
            $subject = 'Undangan Rapat: ' . $model->pembahasan;
            $body = $this->invitationBody($model, $member->nama);

            $sent = Yii::$app->mailer->compose()
                ->setTo($member->email)
                ->setFrom([$senderEmail => $senderName])
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();

            if ($latestLampiran !== null) {
                Yii::$app->db->createCommand()->insert('{{%email_log}}', [
                    'lampiran_id' => $latestLampiran->lampiran_id,
                    'member_id' => $member->member_id,
                    'nama' => $member->nama,
                    'email' => $member->email,
                    'status' => $sent ? 'terkirim' : 'gagal',
                    'sent_by' => Yii::$app->user->id,
                ])->execute();
            }

            $sent ? $sentCount++ : $failedCount++;
        }

        if ($latestLampiran !== null && $sentCount > 0) {
            $latestLampiran->email_sent_at = date('Y-m-d H:i:s');
            $latestLampiran->email_sent_by = Yii::$app->user->id;
            $latestLampiran->save(false);
        }

        Yii::$app->session->setFlash(
            $failedCount === 0 ? 'success' : 'warning',
            "Undangan berhasil diproses: {$sentCount} terkirim" . ($failedCount > 0 ? ", {$failedCount} gagal." : '.')
        );
        return $this->redirect(['view', 'id' => $model->agenda_id]);
    }

    public function actionPreviewInvitations($id)
    {
        $model = $this->findModel($id);
        $members = $this->invitationMembers($model);

        if (empty($members)) {
            Yii::$app->session->setFlash('error', 'Belum ada peserta aktif dengan alamat email yang valid.');
            return $this->redirect(['view', 'id' => $model->agenda_id]);
        }

        $sampleMember = $members[0]->member;
        return $this->render('invitation-preview', [
            'model' => $model,
            'members' => $members,
            'subject' => 'Undangan Rapat: ' . $model->pembahasan,
            'body' => $this->invitationBody($model, $sampleMember->nama),
        ]);
    }

    private function latestLampiran(Agenda $model): ?\app\models\Lampiran
    {
        $lampirans = array_values(array_filter($model->lampirans, static function ($lampiran) {
            return $lampiran->deleted_at === null;
        }));
        return empty($lampirans) ? null : end($lampirans);
    }

    private function invitationMembers(Agenda $model): array
    {
        return array_values(array_filter($model->agendaMembers, static function ($agendaMember) {
            return $agendaMember->deleted_at === null
                && $agendaMember->member !== null
                && $agendaMember->member->deleted_at === null
                && $agendaMember->member->is_active
                && filter_var($agendaMember->member->email, FILTER_VALIDATE_EMAIL);
        }));
    }

    private function invitationBody(Agenda $model, string $memberName): string
    {
        $scanUrl = Url::to(['/absensi/scan', 'token' => $model->qr_code_value], true);

        return "Yth. {$memberName},\n\n"
            . "Anda diundang untuk menghadiri rapat berikut:\n"
            . "Agenda: {$model->pembahasan}\n"
            . 'Tanggal: ' . Yii::$app->formatter->asDate($model->tanggal, 'php:d F Y') . "\n"
            . "Waktu: " . substr($model->waktu_mulai, 0, 5) . ' - ' . substr($model->waktu_selesai, 0, 5) . " WIB\n"
            . "Lokasi: " . ($model->lokasi->lokasi ?? '-') . "\n\n"
            . "Untuk melakukan presensi, buka tautan berikut:\n{$scanUrl}\n\n"
            . "Terima kasih.";
    }

    private function generateAndSaveQr(Agenda $model): void
    {
        $token = $model->generateQrToken();
        $scanUrl = Url::to(['/absensi/scan', 'token' => $token], true);

        $dir = Yii::getAlias('@webroot/uploads/qrcodes');
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir, 0755);
        }

        $filename = $token . '.png';
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($scanUrl)
            ->size(300)
            ->margin(10)
            ->build();
        $result->saveToFile($dir . '/' . $filename);

        if (!empty($model->qr_code_path)) {
            $oldFile = Yii::getAlias('@webroot/' . $model->qr_code_path);
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        $model->qr_code_value = $token;
        $model->qr_code_path = 'uploads/qrcodes/' . $filename;
        $model->save(false);
    }

    /**
     * @throws NotFoundHttpException kalau agenda tidak ditemukan / sudah dihapus (soft delete)
     */
    protected function findModel($id): Agenda
    {
        $model = Agenda::findOne(['agenda_id' => $id, 'deleted_at' => null]);
        if ($model === null) {
            throw new NotFoundHttpException('Agenda yang diminta tidak ditemukan.');
        }
        return $model;
    }
}
