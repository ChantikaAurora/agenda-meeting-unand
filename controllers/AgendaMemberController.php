<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Agenda;
use app\models\AgendaMember;
use app\models\Member;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Mengelola daftar undangan (agenda_member) SEKALIGUS pengiriman emailnya.
 *
 * Sengaja digabung jadi satu alur (bukan dua langkah "tambah peserta" lalu
 * "kirim undangan" terpisah) supaya tidak ada undangan yang tersimpan tanpa
 * pernah benar-benar dikirim emailnya, dan supaya status kirim selalu jelas
 * per orang.
 *
 *  - actionIndex   : daftar lengkap undangan + status kirim + tombol hapus
 *  - actionCompose : pilih penerima (checkbox) + tulis subjek/isi + kirim
 *  - actionDelete  : hapus (soft delete) satu undangan
 */
class AgendaMemberController extends Controller
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
                        'actions' => ['index', 'compose', 'delete'],
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
                    'compose' => ['GET', 'POST'],
                ],
            ],
        ]);
    }

    /**
     * Daftar lengkap undangan untuk satu agenda, dengan status kirim email.
     */
    public function actionIndex(int $agenda_id)
    {
        $agenda = $this->findAgenda($agenda_id);

        // 'deleted_at' ada di dua tabel (agenda_member & member), jadi harus
        // ditulis lengkap dengan nama tabelnya -- kalau tidak, MySQL menolak
        // query dengan error "Column 'deleted_at' ... is ambiguous".
        $invitations = AgendaMember::find()
            ->where([
                'agenda_member.agenda_id' => $agenda->agenda_id,
                'agenda_member.deleted_at' => null,
            ])
            ->joinWith('member')
            ->andWhere(['member.deleted_at' => null])
            ->orderBy(['member.nama' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'agenda' => $agenda,
            'invitations' => $invitations,
        ]);
    }

    /**
     * Satu halaman untuk pilih penerima + tulis isi email + kirim.
     * Memilih seseorang yang belum ada di agenda_member otomatis menambahkan
     * dia sebagai undangan (peran sesuai dropdown per-baris), lalu email
     * dikirim ke semua yang dicentang.
     */
    public function actionCompose(int $agenda_id)
    {
        $agenda = $this->findAgenda($agenda_id);

        $existingByMemberId = [];
        foreach ($agenda->agendaMembers as $agendaMember) {
            if ($agendaMember->deleted_at === null) {
                $existingByMemberId[(int) $agendaMember->member_id] = $agendaMember;
            }
        }

        $members = Member::find()
            ->where(['is_active' => 1, 'deleted_at' => null])
            ->orderBy(['nama' => SORT_ASC])
            ->all();

        $defaultSubject = 'Undangan Rapat: ' . $agenda->pembahasan;
        $defaultBody = $this->defaultBodyTemplate($agenda);

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $selectedIds = array_map('intval', (array) ($post['member_ids'] ?? []));
            $peranPerMember = (array) ($post['peran'] ?? []);
            $subject = trim((string) ($post['subject'] ?? ''));
            $body = (string) ($post['body'] ?? '');

            if (empty($selectedIds)) {
                Yii::$app->session->setFlash('error', 'Pilih minimal satu penerima.');
            } elseif ($subject === '' || trim($body) === '') {
                Yii::$app->session->setFlash('error', 'Subjek dan isi email tidak boleh kosong.');
            } else {
                [$sentCount, $failedCount] = $this->sendToSelected(
                    $agenda,
                    $selectedIds,
                    $peranPerMember,
                    $existingByMemberId,
                    $subject,
                    $body,
                );

                Yii::$app->session->setFlash(
                    $failedCount === 0 ? 'success' : 'warning',
                    "Undangan diproses: {$sentCount} terkirim" . ($failedCount > 0 ? ", {$failedCount} gagal." : '.')
                );
                return $this->redirect(['/agenda/view', 'id' => $agenda->agenda_id]);
            }

            // Validasi gagal -- render ulang form dengan isian yang tadi
            // sudah diketik, supaya tidak perlu diulang dari awal.
            return $this->render('compose', [
                'agenda' => $agenda,
                'members' => $members,
                'existingByMemberId' => $existingByMemberId,
                'subject' => $subject,
                'body' => $body,
                'checkedIds' => $selectedIds,
            ]);
        }

        return $this->render('compose', [
            'agenda' => $agenda,
            'members' => $members,
            'existingByMemberId' => $existingByMemberId,
            'subject' => $defaultSubject,
            'body' => $defaultBody,
            'checkedIds' => array_keys($existingByMemberId),
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = AgendaMember::findOne(['id' => $id, 'deleted_at' => null]);
        if ($model === null) {
            throw new NotFoundHttpException('Undangan tidak ditemukan.');
        }

        $agendaId = $model->agenda_id;
        $model->deleted_at = date('Y-m-d H:i:s');
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Undangan berhasil dihapus.');
        return $this->redirect(['/agenda-member/index', 'agenda_id' => $agendaId]);
    }

    /**
     * @param int[] $selectedIds member_id yang dicentang
     * @param array<string,string> $peranPerMember ['<member_id>' => 'peserta'|'narasumber'|'moderator']
     * @param array<int,AgendaMember> $existingByMemberId
     * @return array{0:int,1:int} [jumlah terkirim, jumlah gagal]
     */
    private function sendToSelected(
        Agenda $agenda,
        array $selectedIds,
        array $peranPerMember,
        array $existingByMemberId,
        string $subject,
        string $body
    ): array {
        $senderEmail = Yii::$app->params['senderEmail'] ?? 'noreply@example.com';
        $senderName = Yii::$app->params['senderName'] ?? 'Sistem Agenda Rapat';
        $sentCount = 0;
        $failedCount = 0;

        $members = Member::find()
            ->where(['member_id' => $selectedIds, 'is_active' => 1, 'deleted_at' => null])
            ->all();

        foreach ($members as $member) {
            if (empty($member->email) || !filter_var($member->email, FILTER_VALIDATE_EMAIL)) {
                $failedCount++;
                continue;
            }

            $agendaMember = $existingByMemberId[(int) $member->member_id] ?? null;
            if ($agendaMember === null) {
                $agendaMember = new AgendaMember();
                $agendaMember->agenda_id = $agenda->agenda_id;
                $agendaMember->member_id = $member->member_id;
                $agendaMember->created_by = (int) Yii::$app->user->id;
            }

            $peran = $peranPerMember[(string) $member->member_id] ?? AgendaMember::PERAN_PESERTA;
            if (in_array($peran, array_keys(AgendaMember::optsPeran()), true)) {
                $agendaMember->peran = $peran;
            }

            $personalizedBody = str_replace('{nama}', $member->nama, $body);

            $sent = Yii::$app->mailer->compose()
                ->setTo($member->email)
                ->setFrom([$senderEmail => $senderName])
                ->setSubject($subject)
                ->setTextBody($personalizedBody)
                ->send();

            $agendaMember->email_status = $sent ? AgendaMember::EMAIL_TERKIRIM : AgendaMember::EMAIL_GAGAL;
            $agendaMember->email_sent_at = date('Y-m-d H:i:s');
            $agendaMember->save(false);

            $sent ? $sentCount++ : $failedCount++;
        }

        return [$sentCount, $failedCount];
    }

    private function defaultBodyTemplate(Agenda $agenda): string
    {
        $scanUrl = Url::to(['/absensi/scan', 'token' => $agenda->qr_code_value], true);

        return "Yth. {nama},\n\n"
            . "Anda diundang untuk menghadiri rapat berikut:\n"
            . "Agenda: {$agenda->pembahasan}\n"
            . 'Tanggal: ' . Yii::$app->formatter->asDate($agenda->tanggal, 'php:d F Y') . "\n"
            . 'Waktu: ' . substr((string) $agenda->waktu_mulai, 0, 5) . ' - ' . substr((string) $agenda->waktu_selesai, 0, 5) . " WIB\n"
            . 'Lokasi: ' . ($agenda->lokasi->lokasi ?? '-') . "\n\n"
            . "Untuk melakukan presensi, buka tautan berikut:\n{$scanUrl}\n\n"
            . 'Terima kasih.';
    }

    private function findAgenda(int $id): Agenda
    {
        $agenda = Agenda::findOne(['agenda_id' => $id, 'deleted_at' => null]);
        if ($agenda === null) {
            throw new NotFoundHttpException('Agenda yang diminta tidak ditemukan.');
        }
        return $agenda;
    }
}
