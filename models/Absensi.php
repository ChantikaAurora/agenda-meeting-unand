<?php

declare(strict_types=1);

namespace app\models;

use Yii;

/**
 * This is the model class for table "absensi".
 *
 * @property int $absensi_id
 * @property int $agenda_id
 * @property int|null $member_id
 * @property string $tipe_identitas
 * @property string $identitas_number
 * @property string|null $jabatan
 * @property string|null $instansi
 * @property string $nama
 * @property string|null $email
 * @property string|null $data_tambahan
 * @property string $tanda_tangan_path
 * @property string $waktu_scan
 * @property string|null $ip_address
 * @property string|null $device_info
 * @property string|null $deleted_at
 *
 * @property Agenda $agenda
 * @property Member $member
 */
class Absensi extends \yii\db\ActiveRecord
{
    /**
     * Skenario untuk form absensi publik (hasil scan QR).
     * Dipisah dari skenario default supaya `tanda_tangan_path` (yang baru terisi
     * SETELAH file PNG ditulis ke disk) tidak ikut divalidasi sebagai required.
     */
    public const SCENARIO_PUBLIC = 'public';

    public const JABATAN_LAINNYA = 'Lainnya';

    /**
     * Atribut virtual (tidak ada kolomnya di tabel).
     * Menampung data URL base64 dari signature pad.
     */
    public $signatureData;

    /**
     * Atribut virtual untuk isian bebas ketika dropdown Jabatan dipilih "Lainnya".
     */
    public $jabatanLainnya;

    public static function tableName()
    {
        return 'absensi';
    }

    /**
     * Pilihan tipe identitas pada form publik.
     */
    public static function tipeIdentitasList(): array
    {
        return [
            'KTP' => 'KTP',
            'NIM' => 'NIM (Mahasiswa)',
            'NIP' => 'NIP (Pegawai)',
            'NIDN' => 'NIDN (Dosen)',
            'Lainnya' => 'Lainnya',
        ];
    }

    /**
     * Pilihan jabatan pada form publik. Nilai di luar daftar ini tetap boleh
     * disimpan lewat opsi "Lainnya" (isian bebas).
     */
    public static function jabatanList(): array
    {
        return [
            'Rektor' => 'Rektor',
            'Wakil Rektor' => 'Wakil Rektor',
            'Dekan' => 'Dekan',
            'Wakil Dekan' => 'Wakil Dekan',
            'Ketua Jurusan' => 'Ketua Jurusan',
            'Ketua Program Studi' => 'Ketua Program Studi',
            'Kepala Biro' => 'Kepala Biro',
            'Kepala Bagian' => 'Kepala Bagian',
            'Dosen' => 'Dosen',
            'Tenaga Kependidikan' => 'Tenaga Kependidikan',
            'Mahasiswa' => 'Mahasiswa',
            'Tamu Undangan' => 'Tamu Undangan',
            self::JABATAN_LAINNYA => 'Lainnya',
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_PUBLIC] = [
            'agenda_id',
            'tipe_identitas',
            'identitas_number',
            'jabatan',
            'jabatanLainnya',
            'instansi',
            'nama',
            'email',
            'signatureData',
        ];

        return $scenarios;
    }

    public function rules()
    {
        return [
            [['member_id', 'jabatan', 'instansi', 'email', 'data_tambahan', 'ip_address', 'device_info', 'deleted_at'], 'default', 'value' => null],
            [['waktu_scan'], 'default', 'value' => new \yii\db\Expression('CURRENT_TIMESTAMP')],

            [['agenda_id', 'tipe_identitas', 'identitas_number', 'nama'], 'required'],

            // PENTING: pada form publik, tanda_tangan_path belum ada nilainya saat
            // validate() dipanggil (file PNG baru ditulis setelah validasi lolos).
            // Karena itu required-nya dikecualikan untuk skenario publik dan diganti
            // oleh validasi signatureData di bawah.
            [['tanda_tangan_path'], 'required', 'except' => self::SCENARIO_PUBLIC],

            [['signatureData'], 'required', 'on' => self::SCENARIO_PUBLIC, 'message' => 'Tanda tangan digital wajib diisi.'],
            [['signatureData'], 'validateSignature', 'on' => self::SCENARIO_PUBLIC],

            // Pada form publik, email & jabatan wajib (mengikuti desain form).
            [['email', 'jabatan'], 'required', 'on' => self::SCENARIO_PUBLIC],
            [['jabatanLainnya'], 'string', 'max' => 100],
            [
                ['jabatanLainnya'],
                'required',
                'on' => self::SCENARIO_PUBLIC,
                'when' => static fn (self $model): bool => $model->jabatan === self::JABATAN_LAINNYA,
                'whenClient' => "function (attribute, value) {
                    var jabatan = document.getElementById('absensi-jabatan');
                    return jabatan && jabatan.value === '" . self::JABATAN_LAINNYA . "';
                }",
                'message' => 'Jabatan wajib diisi.',
            ],

            [['agenda_id', 'member_id'], 'integer'],
            [['waktu_scan', 'deleted_at'], 'safe'],
            [['tipe_identitas'], 'string', 'max' => 50],
            [['identitas_number'], 'string', 'max' => 100],
            [['jabatan'], 'string', 'max' => 100],
            [['instansi'], 'string', 'max' => 150],
            [['nama', 'email'], 'string', 'max' => 150],
            [['data_tambahan', 'tanda_tangan_path', 'device_info'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 45],
            [['email'], 'email'],
            [['identitas_number'], 'trim'],
            [['nama', 'instansi', 'jabatanLainnya'], 'trim'],
            [['agenda_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agenda::class, 'targetAttribute' => ['agenda_id' => 'agenda_id']],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::class, 'targetAttribute' => ['member_id' => 'member_id']],
        ];
    }

    /**
     * Memastikan signatureData benar-benar data URL PNG yang valid dan tidak kosong.
     */
    public function validateSignature(string $attribute): void
    {
        $value = (string) $this->$attribute;

        if ($value === '') {
            return; // sudah ditangani rule 'required'
        }

        $prefix = 'data:image/png;base64,';
        if (!str_starts_with($value, $prefix)) {
            $this->addError($attribute, 'Format tanda tangan tidak dikenali, silakan ulangi.');
            return;
        }

        $binary = base64_decode(substr($value, strlen($prefix)), true);
        if ($binary === false || strlen($binary) < 1024) {
            $this->addError($attribute, 'Tanda tangan terbaca kosong, silakan bubuhkan ulang.');
        }
    }

    /**
     * Jabatan final: gabungan dropdown + isian bebas "Lainnya".
     */
    public function resolveJabatan(): ?string
    {
        if ($this->jabatan === self::JABATAN_LAINNYA) {
            $custom = trim((string) $this->jabatanLainnya);
            return $custom !== '' ? $custom : null;
        }

        $jabatan = trim((string) $this->jabatan);

        return $jabatan !== '' ? $jabatan : null;
    }

    public function attributeLabels()
    {
        return [
            'absensi_id' => 'Absensi ID',
            'agenda_id' => 'Agenda ID',
            'member_id' => 'Member ID',
            'tipe_identitas' => 'Tipe Identitas',
            'identitas_number' => 'Nomor Identitas',
            'jabatan' => 'Jabatan',
            'jabatanLainnya' => 'Jabatan',
            'instansi' => 'Instansi',
            'nama' => 'Nama',
            'email' => 'Email',
            'data_tambahan' => 'Data Tambahan',
            'tanda_tangan_path' => 'Tanda Tangan',
            'signatureData' => 'Tanda Tangan Digital',
            'waktu_scan' => 'Waktu Scan',
            'ip_address' => 'IP Address',
            'device_info' => 'Device Info',
            'deleted_at' => 'Deleted At',
        ];
    }

    public function getAgenda()
    {
        return $this->hasOne(Agenda::class, ['agenda_id' => 'agenda_id']);
    }

    public function getMember()
    {
        return $this->hasOne(Member::class, ['member_id' => 'member_id']);
    }
}
