<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
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
 *
 * @property Lokasi $lokasi
 */
class Agenda extends ActiveRecord
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
        return '{{%agenda}}';
    }

    public function rules()
    {
        return [
            [['pembahasan', 'tanggal', 'tahun_akademik', 'waktu_mulai', 'waktu_selesai', 'lokasi_id', 'status'], 'required'],
            ['deskripsi', 'string'],
            ['tanggal', 'date', 'format' => 'php:Y-m-d'],
            [['waktu_mulai', 'waktu_selesai'], 'time', 'format' => 'php:H:i'],
            ['lokasi_id', 'integer'],
            [['nomor_surat', 'qr_code_value', 'qr_code_path'], 'string', 'max' => 255],
            ['pembahasan', 'string', 'max' => 255],
            ['tahun_akademik', 'string', 'max' => 20],
            ['status', 'in', 'range' => array_keys(self::statusList())],
            [
                'lokasi_id', 'exist', 'skipOnError' => true,
                'targetClass' => Lokasi::class, 'targetAttribute' => ['lokasi_id' => 'lokasi_id'],
            ],
            ['waktu_selesai', 'validateWaktu'],
        ];
    }

    public function validateWaktu($attribute, $params)
    {
        if (!$this->hasErrors('waktu_mulai') && !$this->hasErrors('waktu_selesai')) {
            if (strtotime($this->waktu_selesai) <= strtotime($this->waktu_mulai)) {
                $this->addError($attribute, 'Waktu selesai harus lebih besar dari waktu mulai.');
            }
        }
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
            'status' => 'Status',
            'created_at' => 'Dibuat Pada',
        ];
    }

    public function getLokasi()
    {
        return $this->hasOne(Lokasi::class, ['lokasi_id' => 'lokasi_id']);
    }

    public function generateQrToken(): string
    {
        return 'AGD-' . Yii::$app->security->generateRandomString(32);
    }
}
