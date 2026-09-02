<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "agenda".
 *
 * @property int $agenda_id
 * @property string|null $nomor_surat
 * @property string $pembahasan
 * @property string|null $deskripsi
 * @property string $tanggal
 * @property string $tahun_akademik
 * @property string $waktu_mulai
 * @property string $waktu_selesai
 * @property int $lokasi_id
 * @property string|null $qr_code_value
 * @property string|null $qr_code_path
 * @property string $status
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Absensi[] $absensis
 * @property AgendaMember[] $agendaMembers
 * @property User $createdBy
 * @property Lampiran[] $lampirans
 * @property Lokasi $lokasi
 * @property Member[] $members
 * @property User $updatedBy
 */
class Agenda extends \yii\db\ActiveRecord
{
    public const STATUS_TERJADWAL = 'terjadwal';
    public const STATUS_BERLANGSUNG = 'berlangsung';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    public static function statusList(): array
    {
        return [
            self::STATUS_TERJADWAL => 'Terjadwal',
            self::STATUS_BERLANGSUNG => 'Berlangsung',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
    }

    public static function tableName()
    {
        return 'agenda';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    public function rules()
    {
        return [
            [['nomor_surat', 'deskripsi', 'qr_code_value', 'qr_code_path', 'created_by', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['pembahasan', 'tanggal', 'tahun_akademik', 'waktu_mulai', 'waktu_selesai', 'lokasi_id', 'status'], 'required'],
            [['deskripsi'], 'string'],
            [['tanggal'], 'date', 'format' => 'php:Y-m-d'],
            [['waktu_mulai', 'waktu_selesai'], 'time', 'format' => 'php:H:i'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['lokasi_id', 'created_by', 'updated_by'], 'integer'],
            [['nomor_surat'], 'string', 'max' => 100],
            [['pembahasan', 'qr_code_value', 'qr_code_path'], 'string', 'max' => 255],
            [['tahun_akademik'], 'string', 'max' => 20],
            ['status', 'in', 'range' => array_keys(self::statusList())],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['lokasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lokasi::class, 'targetAttribute' => ['lokasi_id' => 'lokasi_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'user_id']],
            ['waktu_selesai', 'validateWaktu'],
        ];
    }

    /**
     * Validasi kustom: waktu selesai harus lebih besar dari waktu mulai.
     */
    public function validateWaktu($attribute, $params)
    {
        if (!$this->hasErrors('waktu_mulai') && !$this->hasErrors('waktu_selesai')) {
            if (strtotime($this->waktu_selesai) <= strtotime($this->waktu_mulai)) {
                $this->addError($attribute, 'Waktu selesai harus lebih besar dari waktu mulai.');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'agenda_id' => 'ID',
            'nomor_surat' => 'Nomor Surat',
            'pembahasan' => 'Pembahasan',
            'deskripsi' => 'Deskripsi',
            'tanggal' => 'Tanggal',
            'tahun_akademik' => 'Tahun Akademik',
            'waktu_mulai' => 'Waktu Mulai',
            'waktu_selesai' => 'Waktu Selesai',
            'lokasi_id' => 'Lokasi',
            'qr_code_value' => 'QR Code Value',
            'qr_code_path' => 'QR Code Path',
            'status' => 'Status',
            'created_by' => 'Dibuat Oleh',
            'created_at' => 'Dibuat Pada',
            'updated_by' => 'Diperbarui Oleh',
            'updated_at' => 'Diperbarui Pada',
            'deleted_at' => 'Dihapus Pada',
        ];
    }

    public function getAbsensis()
    {
        return $this->hasMany(Absensi::class, ['agenda_id' => 'agenda_id']);
    }

    public function getAgendaMembers()
    {
        return $this->hasMany(AgendaMember::class, ['agenda_id' => 'agenda_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'created_by']);
    }

    public function getLampirans()
    {
        return $this->hasMany(Lampiran::class, ['agenda_id' => 'agenda_id']);
    }

    public function getLokasi()
    {
        return $this->hasOne(Lokasi::class, ['lokasi_id' => 'lokasi_id']);
    }

    public function getMembers()
    {
        return $this->hasMany(Member::class, ['member_id' => 'member_id'])->viaTable('agenda_member', ['agenda_id' => 'agenda_id']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'updated_by']);
    }

    /**
     * Generate token unik untuk QR absensi. Sengaja BUKAN pakai agenda_id langsung
     * (gampang ditebak/diubah manual di URL) -- token acak panjang praktis
     * tidak bisa ditebak, sesuai poin keamanan QR di dokumen desain proyek ini.
     */
    public function generateQrToken(): string
    {
        return 'AGD-' . Yii::$app->security->generateRandomString(32);
    }
}
