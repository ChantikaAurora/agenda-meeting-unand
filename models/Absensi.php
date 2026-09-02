<?php

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
    public static function tableName()
    {
        return 'absensi';
    }

    public function rules()
    {
        return [
            [['member_id', 'jabatan', 'instansi', 'email', 'data_tambahan', 'ip_address', 'device_info', 'deleted_at'], 'default', 'value' => null],
            [['waktu_scan'], 'default', 'value' => new \yii\db\Expression('CURRENT_TIMESTAMP')],
            [['agenda_id', 'tipe_identitas', 'identitas_number', 'nama', 'tanda_tangan_path'], 'required'],
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
            [['agenda_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agenda::class, 'targetAttribute' => ['agenda_id' => 'agenda_id']],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::class, 'targetAttribute' => ['member_id' => 'member_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'absensi_id' => 'Absensi ID',
            'agenda_id' => 'Agenda ID',
            'member_id' => 'Member ID',
            'tipe_identitas' => 'Tipe Identitas',
            'identitas_number' => 'NIK/NIM',
            'jabatan' => 'Jabatan',
            'instansi' => 'Instansi',
            'nama' => 'Nama',
            'email' => 'Email',
            'data_tambahan' => 'Data Tambahan',
            'tanda_tangan_path' => 'Tanda Tangan',
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