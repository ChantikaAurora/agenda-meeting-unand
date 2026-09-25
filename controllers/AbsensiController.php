<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Absensi;
use app\models\Agenda;
use app\models\Member;
use app\services\AgendaStatusResolver;
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
                        'actions' => [
                            'scan',
                            'submit',
                            'lookup',
                        ],
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

        /*
         * ============================================================
         * VALIDASI DATA FORM
         * ============================================================
         */
        if (!$model->validate()) {
            Yii::$app->session->setFlash(
                'error',
                $this->firstErrorOf($model)
                    ?? 'Absensi gagal disimpan, silakan periksa kembali data yang diisi.'
            );

            return $this->render('scan', [
                'model' => $model,
                'agenda' => $agenda,
                'token' => $token,
            ]);
        }

        /*
         * ============================================================
         * CEK DUPLICATE ABSENSI
         * ============================================================
         *
         * Satu nomor identitas hanya boleh melakukan absensi
         * satu kali pada agenda yang sama.
         *
         * Nama TIDAK digunakan sebagai kunci duplicate.
         *
         * Jadi:
         *
         * Sri Ayu Lestari + NIM 12345 = boleh 1 kali
         * Sri Ayu Lestari + NIM 67890 = tetap boleh
         * Sri Ayu Lestari + NIM 12345 = ditolak
         *
         * Dengan begitu dua orang yang namanya sama tetap bisa absen,
         * selama nomor identitas mereka berbeda.
         */

        $identitas = trim(
            (string) $model->identitas_number
        );

        $sudahAbsen = Absensi::find()
            ->where([
                'agenda_id' => $agenda->agenda_id,
                'identitas_number' => $identitas,
                'deleted_at' => null,
            ])
            ->exists();

        /*
         * Jika nomor identitas sudah pernah melakukan absensi
         * pada agenda yang sama, tolak absensi kedua.
         */
        if ($sudahAbsen) {
            Yii::$app->session->setFlash(
                'error',
                'Nomor identitas Anda sudah tercatat hadir pada agenda ini. '
                . 'Anda tidak dapat melakukan absensi dua kali.'
            );

            return $this->render('scan', [
                'model' => $model,
                'agenda' => $agenda,
                'token' => $token,
            ]);
        }

        /*
         * ============================================================
         * SIMPAN ABSENSI BARU
         * ============================================================
         */

        $target = new Absensi();

        $target->agenda_id = $agenda->agenda_id;
        $target->tipe_identitas = $model->tipe_identitas;
        $target->identitas_number = $identitas;
        $target->jabatan = $model->resolveJabatan();
        $target->instansi = $model->instansi;
        $target->nama = $model->nama;
        $target->email = $model->email;

        /*
         * Cari member berdasarkan nomor identitas.
         */
        $member = Member::findOne([
            'identitas_number' => $identitas,
            'deleted_at' => null,
        ]);

        $target->member_id = $member->member_id ?? null;

        /*
         * ============================================================
         * SIMPAN TANDA TANGAN
         * ============================================================
         */
        $target->tanda_tangan_path = $this->saveSignatureFile(
            (string) $model->signatureData,
            $agenda->agenda_id,
            $identitas
        );

        /*
         * ============================================================
         * SIMPAN INFORMASI PERANGKAT
         * ============================================================
         */
        $target->ip_address = $request->userIP;

        $target->device_info = mb_substr(
            (string) $request->userAgent,
            0,
            255
        );

        $target->waktu_scan = date('Y-m-d H:i:s');

        /*
         * ============================================================
         * SIMPAN DATA ABSENSI
         * ============================================================
         */
        if ($target->save(false)) {
            return $this->render('success', [
                'agenda' => $agenda,
                'absensi' => $target,
            ]);
        }

        /*
         * Jika penyimpanan gagal.
         */
        Yii::$app->session->setFlash(
            'error',
            $this->firstErrorOf($target)
                ?? 'Absensi gagal disimpan, silakan coba lagi.'
        );

        return $this->render('scan', [
            'model' => $model,
            'agenda' => $agenda,
            'token' => $token,
        ]);
    }

    public function actionLookup(
        ?string $token = null,
        string $q = ''
    ): array {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $agenda = $this->findAgendaByToken($token);

        $keyword = trim($q);

        if (
            $agenda === null
            || mb_strlen($keyword) < self::LOOKUP_MIN_CHARS
        ) {
            return [
                'items' => [],
            ];
        }

        $like = [
            'like',
            'nama',
            $keyword,
        ];

        $results = [];

        $pushRow = function (
            array $row,
            string $source
        ) use (&$results): void {
            $key = trim(
                (string) (
                    $row['identitas_number'] ?? ''
                )
            );

            if ($key === '') {
                $key = 'nama:' . mb_strtolower(
                    trim((string) $row['nama'])
                );
            }

            if (isset($results[$key])) {
                return;
            }

            $results[$key] = [
                'nama' => (string) $row['nama'],

                'tipe_identitas' => (string) (
                    $row['tipe_identitas'] ?? ''
                ),

                'identitas_number' => (string) (
                    $row['identitas_number'] ?? ''
                ),

                'jabatan' => (string) (
                    $row['jabatan'] ?? ''
                ),

                'instansi' => (string) (
                    $row['instansi'] ?? ''
                ),

                'email' => (string) (
                    $row['email'] ?? ''
                ),

                'source' => $source,
            ];
        };

        /*
         * ============================================================
         * 1. CARI ABSENSI PADA AGENDA INI
         * ============================================================
         */
        $rows = Absensi::find()
            ->select([
                'nama',
                'tipe_identitas',
                'identitas_number',
                'jabatan',
                'instansi',
                'email',
            ])
            ->where([
                'agenda_id' => $agenda->agenda_id,
                'deleted_at' => null,
            ])
            ->andWhere($like)
            ->orderBy([
                'waktu_scan' => SORT_DESC,
            ])
            ->limit(self::LOOKUP_LIMIT)
            ->asArray()
            ->all();

        foreach ($rows as $row) {
            $pushRow(
                $row,
                'absensi_agenda_ini'
            );
        }

        /*
         * ============================================================
         * 2. CARI RIWAYAT ABSENSI
         * ============================================================
         */
        if (count($results) < self::LOOKUP_LIMIT) {
            $rows = Absensi::find()
                ->select([
                    'nama',
                    'tipe_identitas',
                    'identitas_number',
                    'jabatan',
                    'instansi',
                    'email',
                ])
                ->where([
                    'deleted_at' => null,
                ])
                ->andWhere($like)
                ->orderBy([
                    'waktu_scan' => SORT_DESC,
                ])
                ->limit(self::LOOKUP_LIMIT * 2)
                ->asArray()
                ->all();

            foreach ($rows as $row) {
                $pushRow(
                    $row,
                    'riwayat_absensi'
                );
            }
        }

        /*
         * ============================================================
         * 3. MASTER MEMBER
         * ============================================================
         */
        if (count($results) < self::LOOKUP_LIMIT) {
            $rows = Member::find()
                ->select([
                    'nama',
                    'tipe_identitas',
                    'identitas_number',
                    'jabatan',
                    'instansi',
                    'email',
                ])
                ->where([
                    'deleted_at' => null,
                    'is_active' => 1,
                ])
                ->andWhere($like)
                ->orderBy([
                    'nama' => SORT_ASC,
                ])
                ->limit(self::LOOKUP_LIMIT * 2)
                ->asArray()
                ->all();

            foreach ($rows as $row) {
                $pushRow(
                    $row,
                    'master_member'
                );
            }
        }

        return [
            'items' => array_slice(
                array_values($results),
                0,
                self::LOOKUP_LIMIT
            ),
        ];
    }

    /**
     * Memastikan agenda boleh digunakan untuk absensi.
     *
     * Aturan:
     * - Absensi dibuka 30 menit sebelum rapat.
     * - Absensi ditutup 30 menit setelah rapat selesai.
     */
    protected function guardAgenda(
        ?Agenda $agenda
    ): ?string {
        if ($agenda === null) {
            return $this->render('error', [
                'message' =>
                    'QR Code tidak valid, sudah kedaluwarsa, '
                    . 'atau agenda tidak ditemukan.',
            ]);
        }

        /*
         * Agenda yang dibatalkan tidak boleh menerima absensi.
         */
        if (
            $agenda->status === Agenda::STATUS_DIBATALKAN
        ) {
            return $this->render('error', [
                'message' =>
                    'Agenda ini sudah dibatalkan, '
                    . 'absensi tidak dapat dilakukan.',
            ]);
        }

        /*
         * ============================================================
         * CEK WAKTU ABSENSI
         * ============================================================
         *
         * absensiTerbuka() adalah sumber aturan waktunya.
         *
         * Dibuka:
         *     30 menit sebelum rapat
         *
         * Ditutup:
         *     30 menit setelah rapat selesai
         */
        if (!$agenda->absensiTerbuka()) {
            $sekarang =
                AgendaStatusResolver::sekarang();

            $mulai =
                $agenda->getJadwalMulai();

            $selesai =
                $agenda->getJadwalSelesai();

            /*
             * ========================================================
             * BELUM MASUK WAKTU ABSENSI
             * ========================================================
             */
            if (
                $mulai !== null
                && $sekarang < $mulai
            ) {
                $dibuka = $mulai->modify(
                    '-' . Agenda::menitAbsensiDibuka()
                    . ' minutes'
                );

                $pesan =
                    'Absensi belum dibuka. '
                    . 'Absensi dibuka pada '
                    . $dibuka->format('d/m/Y')
                    . ' pukul '
                    . $dibuka->format('H:i')
                    . ' WIB.';
            }

            /*
             * ========================================================
             * SUDAH MELEWATI BATAS ABSENSI
             * ========================================================
             */
            elseif ($selesai !== null) {
                /*
                 * Batas akhir absensi:
                 * 30 menit setelah rapat selesai.
                 */
                $ditutup = $selesai->modify(
                    '+30 minutes'
                );

                if ($sekarang > $ditutup) {
                    $pesan =
                        'Absensi sudah ditutup. '
                        . 'Batas absensi adalah sampai '
                        . $ditutup->format('d/m/Y')
                        . ' pukul '
                        . $ditutup->format('H:i')
                        . ' WIB.';
                } else {
                    $pesan =
                        'Absensi tidak dapat dilakukan '
                        . 'saat ini.';
                }
            }

            /*
             * ========================================================
             * KONDISI LAIN
             * ========================================================
             */
            else {
                $pesan =
                    'Absensi belum dapat dilakukan '
                    . 'untuk agenda ini.';
            }

            return $this->render('error', [
                'message' => $pesan,
            ]);
        }

        return null;
    }

    protected function firstErrorOf(
        Absensi $model
    ): ?string {
        foreach (
            $model->getFirstErrors()
            as $error
        ) {
            return $error;
        }

        return null;
    }

    protected function findAgendaByToken(
        ?string $token
    ): ?Agenda {
        if (
            $token === null
            || trim($token) === ''
        ) {
            return null;
        }

        return Agenda::findOne([
            'qr_code_value' => $token,
            'deleted_at' => null,
        ]);
    }

    protected function saveSignatureFile(
        string $dataUrl,
        int $agendaId,
        string $identitasNumber
    ): string {
        $base64 = substr(
            $dataUrl,
            strlen('data:image/png;base64,')
        );

        $binary = base64_decode(
            $base64,
            true
        );

        $dir = Yii::getAlias(
            '@webroot/uploads/signatures/'
            . $agendaId
        );

        if (!is_dir($dir)) {
            FileHelper::createDirectory(
                $dir,
                0755
            );
        }

        $safeIdentity = preg_replace(
            '/[^A-Za-z0-9_-]/',
            '_',
            $identitasNumber
        );

        $filename =
            'ttd_'
            . $safeIdentity
            . '_'
            . substr(
                md5(
                    $agendaId
                    . '|'
                    . $identitasNumber
                ),
                0,
                10
            )
            . '.png';

        file_put_contents(
            $dir . '/'
            . $filename,
            $binary
        );

        return
            'uploads/signatures/'
            . $agendaId
            . '/'
            . $filename;
    }
}