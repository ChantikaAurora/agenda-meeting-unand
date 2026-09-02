<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "member".
 */
class Member extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'member';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $email = trim((string) $this->email);
            $nama = trim((string) $this->nama);

            if ($email === '' && $nama !== '') {
                $this->email = $this->buildGmailEmailFromName($nama);
            } elseif ($email !== '' && stripos($email, '@') === false) {
                $this->email = $this->buildGmailEmailFromName($email);
            }

            return true;
        }

        return false;
    }

    protected function buildGmailEmailFromName(string $value): string
    {
        $normalized = strtolower($value);
        $normalized = preg_replace('/[^a-z0-9\s._-]+/', '', $normalized);
        $normalized = preg_replace('/\s+/', '.', trim($normalized));
        $normalized = preg_replace('/\.+/', '.', $normalized);
        $normalized = trim($normalized, '.');

        return $normalized !== '' ? $normalized . '@gmail.com' : '';
    }

    public function rules()
    {
        return [
            [['jabatan', 'instansi', 'tipe_identitas', 'identitas_number', 'email', 'created_by', 'updated_by', 'deleted_at'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['nama'], 'required'],
            [['created_by', 'updated_by', 'is_active'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['nama', 'instansi', 'email'], 'string', 'max' => 150],
            [['email'], 'email'],
            [['jabatan', 'identitas_number'], 'string', 'max' => 100],
            [['tipe_identitas'], 'string', 'max' => 50],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'nama' => 'Nama',
            'jabatan' => 'Jabatan',
            'instansi' => 'Instansi',
            'tipe_identitas' => 'Tipe Identitas',
            'identitas_number' => 'Identitas Number',
            'email' => 'Email',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_active' => 'Is Active',
        ];
    }

    public function getAbsensis()
    {
        return $this->hasMany(Absensi::class, ['member_id' => 'member_id']);
    }

    public function getAgendaMembers()
    {
        return $this->hasMany(AgendaMember::class, ['member_id' => 'member_id']);
    }

    public function getAgendas()
    {
        return $this->hasMany(Agenda::class, ['agenda_id' => 'agenda_id'])->viaTable('agenda_member', ['member_id' => 'member_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'created_by']);
    }

    public function getEmailLogs()
    {
        return $this->hasMany(EmailLog::class, ['member_id' => 'member_id']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'updated_by']);
    }

}