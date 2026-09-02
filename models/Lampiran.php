<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lampiran".
 *
 * @property int $lampiran_id
 * @property int $agenda_id
 * @property string $jenis_lampiran
 * @property string|null $ringkasan
 * @property string $file_path
 * @property string $status
 * @property string|null $email_sent_at
 * @property int|null $email_sent_by
 * @property int $uploaded_by
 * @property string $uploaded_at
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Agenda $agenda
 * @property User $createdBy
 * @property EmailLog[] $emailLogs
 * @property User $emailSentBy
 * @property User $updatedBy
 * @property User $uploadedBy
 */
class Lampiran extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_FINAL = 'final';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lampiran';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ringkasan', 'email_sent_at', 'email_sent_by', 'created_by', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'draft'],
            [['agenda_id', 'jenis_lampiran', 'file_path', 'uploaded_by'], 'required'],
            [['agenda_id', 'email_sent_by', 'uploaded_by', 'created_by', 'updated_by'], 'integer'],
            [['ringkasan', 'status'], 'string'],
            [['email_sent_at', 'uploaded_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['jenis_lampiran'], 'string', 'max' => 50],
            [['file_path'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['agenda_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agenda::class, 'targetAttribute' => ['agenda_id' => 'agenda_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['email_sent_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['email_sent_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'user_id']],
            [['uploaded_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['uploaded_by' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lampiran_id' => 'Lampiran ID',
            'agenda_id' => 'Agenda ID',
            'jenis_lampiran' => 'Jenis Lampiran',
            'ringkasan' => 'Ringkasan',
            'file_path' => 'File Path',
            'status' => 'Status',
            'email_sent_at' => 'Email Sent At',
            'email_sent_by' => 'Email Sent By',
            'uploaded_by' => 'Uploaded By',
            'uploaded_at' => 'Uploaded At',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    public function getAgenda()
    {
        return $this->hasOne(Agenda::class, ['agenda_id' => 'agenda_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'created_by']);
    }

    public function getEmailLogs()
    {
        return $this->hasMany(EmailLog::class, ['lampiran_id' => 'lampiran_id']);
    }

    public function getEmailSentBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'email_sent_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'updated_by']);
    }

    public function getUploadedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'uploaded_by']);
    }

    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_DRAFT => 'draft',
            self::STATUS_FINAL => 'final',
        ];
    }

    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    public function isStatusDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function setStatusToDraft()
    {
        $this->status = self::STATUS_DRAFT;
    }

    public function isStatusFinal()
    {
        return $this->status === self::STATUS_FINAL;
    }

    public function setStatusToFinal()
    {
        $this->status = self::STATUS_FINAL;
    }
}