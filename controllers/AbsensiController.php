<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Absensi;
use app\models\Agenda;
use app\models\Member;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;

class AbsensiController extends Controller
{
    public $layout = 'public';

    private const LOOKUP_LIMIT = 8;

    private const LOOKUP_MIN_CHARS = 3;

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['scan', 'submit', 'lookup'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'submit' => ['POST'],
                    'lookup' => ['GET'],
                ],
            ],
        ]);
    }

    public function actionScan(?string $token = null)
    {
        $agenda = $this->findAgendaByToken($token);

        if (($guard = $this->guardAgenda($agenda)) !== null) {
            return $guard;
        }

        $model = new Absensi();
        $model->scenario = Absensi::SCENARIO_PUBLIC;

        return $this->render('scan', [
            'model' => $model,
            'agenda' => $agenda,
            'token' => (string) $token,
        ]);
    }

    public function actionSubmit()
    {
        $request = Yii::$app->request;
        $token = (string) $request->post('token');
        $agenda = $this->findAgendaByToken($token);

        if (($guard = $this->guardAgenda($agenda)) !== null) {
            return $guard;
        }

        $model = new Absensi();
        $model->scenario = Absensi::SCENARIO_PUBLIC;
        $model->load($request->post());
        $model->agenda_id = $agenda->agenda_id;

        if (!$model->validate()) {
            Yii::$app->session->setFlash(
                'error',
                $this->firstErrorOf($model) ?? 'Absensi gagal disimpan, silakan periksa kembali data yang diisi.',
            );

            return $this->render('scan', [
                'model' => $model,
                'agenda' => $agenda,
                'token' => $token,
            ]);
        }

        $target = Absensi::find()
            ->where([
                'agenda_id' => $agenda->agenda_id,
                'identitas_number' => $model->identitas_number,
                'deleted_at' => null,
            ])
            ->one() ?? new Absensi();

        $target->agenda_id = $agenda->agenda_id;
        $target->tipe_identitas = $model->tipe_identitas;
        $target->identitas_number = $model->identitas_number;
        $target->jabatan = $model->resolveJabatan();
        $target->instansi = $model->instansi;
        $target->nama = $model->nama;
        $target->email = $model->email;

        $member = Member::findOne([
            'identitas_number' => $model->identitas_number,
            'deleted_at' => null,
        ]);
        $target->member_id = $member->member_id ?? null;

        $target->tanda_tangan_path = $this->saveSignatureFile(
            (string) $model->signatureData,
            $agenda->agenda_id,
            (string) $model->identitas_number,
        );
        $target->ip_address = $request->userIP;
        $target->device_info = mb_substr((string) $request->userAgent, 0, 255);
        $target->waktu_scan = date('Y-m-d H:i:s');

        if ($target->save(false)) {
            return $this->render('success', [
                'agenda' => $agenda,
                'absensi' => $target,
            ]);
        }

        Yii::$app->session->setFlash(
            'error',
            $this->firstErrorOf($target) ?? 'Absensi gagal disimpan, silakan coba lagi.',
        );

        return $this->render('scan', [
            'model' => $model,
            'agenda' => $agenda,
            'token' => $token,
        ]);
    }

    public function actionLookup(?string $token = null, string $q = ''): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $agenda = $this->findAgendaByToken($token);
        $keyword = trim($q);

        if ($agenda === null || mb_strlen($keyword) < self::LOOKUP_MIN_CHARS) {
            return ['items' => []];
        }

        $like = ['like', 'nama', $keyword];
        $results = [];

        $pushRow = function (array $row, string $source) use (&$results): void {
            $key = trim((string) ($row['identitas_number'] ?? ''));
            if ($key === '') {
                $key = 'nama:' . mb_strtolower(trim((string) $row['nama']));
            }

            if (isset($results[$key])) {
                return;
            }

            $results[$key] = [
                'nama' => (string) $row['nama'],
                'tipe_identitas' => (string) ($row['tipe_identitas'] ?? ''),
                'identitas_number' => (string) ($row['identitas_number'] ?? ''),
                'jabatan' => (string) ($row['jabatan'] ?? ''),
                'instansi' => (string) ($row['instansi'] ?? ''),
                'email' => (string) ($row['email'] ?? ''),
                'source' => $source,
            ];
        };

        $rows = Absensi::find()
            ->select(['nama', 'tipe_identitas', 'identitas_number', 'jabatan', 'instansi', 'email'])
            ->where(['agenda_id' => $agenda->agenda_id, 'deleted_at' => null])
            ->andWhere($like)
            ->orderBy(['waktu_scan' => SORT_DESC])
            ->limit(self::LOOKUP_LIMIT)
            ->asArray()
            ->all();
        foreach ($rows as $row) {
            $pushRow($row, 'absensi_agenda_ini');
        }

        if (count($results) < self::LOOKUP_LIMIT) {
            $rows = Absensi::find()
                ->select(['nama', 'tipe_identitas', 'identitas_number', 'jabatan', 'instansi', 'email'])
                ->where(['deleted_at' => null])
                ->andWhere($like)
                ->orderBy(['waktu_scan' => SORT_DESC])
                ->limit(self::LOOKUP_LIMIT * 2)
                ->asArray()
                ->all();
            foreach ($rows as $row) {
                $pushRow($row, 'riwayat_absensi');
            }
        }

        // 3) Master member.
        if (count($results) < self::LOOKUP_LIMIT) {
            $rows = Member::find()
                ->select(['nama', 'tipe_identitas', 'identitas_number', 'jabatan', 'instansi', 'email'])
                ->where(['deleted_at' => null, 'is_active' => 1])
                ->andWhere($like)
                ->orderBy(['nama' => SORT_ASC])
                ->limit(self::LOOKUP_LIMIT * 2)
                ->asArray()
                ->all();
            foreach ($rows as $row) {
                $pushRow($row, 'master_member');
            }
        }

        return ['items' => array_slice(array_values($results), 0, self::LOOKUP_LIMIT)];
    }

    protected function guardAgenda(?Agenda $agenda): ?string
    {
        if ($agenda === null) {
            return $this->render('error', [
                'message' => 'QR Code tidak valid, sudah kedaluwarsa, atau agenda tidak ditemukan.',
            ]);
        }

        if ($agenda->status === Agenda::STATUS_DIBATALKAN) {
            return $this->render('error', [
                'message' => 'Agenda ini sudah dibatalkan, absensi tidak dapat dilakukan.',
            ]);
        }

        return null;
    }

    protected function firstErrorOf(Absensi $model): ?string
    {
        foreach ($model->getFirstErrors() as $error) {
            return $error;
        }

        return null;
    }

    protected function findAgendaByToken(?string $token): ?Agenda
    {
        if ($token === null || trim($token) === '') {
            return null;
        }

        return Agenda::findOne(['qr_code_value' => $token, 'deleted_at' => null]);
    }

    protected function saveSignatureFile(string $dataUrl, int $agendaId, string $identitasNumber): string
    {
        $base64 = substr($dataUrl, strlen('data:image/png;base64,'));
        $binary = base64_decode($base64, true);

        $dir = Yii::getAlias('@webroot/uploads/signatures/' . $agendaId);
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir, 0755);
        }

        $safeIdentity = preg_replace('/[^A-Za-z0-9_-]/', '_', $identitasNumber);
        $filename = 'ttd_' . $safeIdentity . '_' . substr(md5($agendaId . '|' . $identitasNumber), 0, 10) . '.png';

        file_put_contents($dir . '/' . $filename, $binary);

        return 'uploads/signatures/' . $agendaId . '/' . $filename;
    }
}
