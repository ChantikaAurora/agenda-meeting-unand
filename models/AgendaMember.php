<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agenda_member".
 *
 * @property int $id
 * @property int $agenda_id
 * @property int $member_id
 * @property string $peran
 * @property int|null $created_by
 * @property string $created_at
 * @property string|null $deleted_at
 *
 * @property Agenda $agenda
 * @property Member $member
 * @property User $createdBy
 */
class AgendaMember extends \yii\db\ActiveRecord
{
    const PERAN_PESERTA = 'peserta';
    const PERAN_NARASUMBER = 'narasumber';
    const PERAN_MODERATOR = 'moderator';

    public static function tableName()
    {
        return 'agenda_member';
    }

    public function rules()
    {
        return [
            [['created_by', 'deleted_at'], 'default', 'value' => null],
            [['peran'], 'default', 'value' => self::PERAN_PESERTA],
            [['agenda_id', 'member_id'], 'required'],
            [['agenda_id', 'member_id', 'created_by'], 'integer'],
            [['created_at', 'deleted_at'], 'safe'],
            ['peran', 'in', 'range' => array_keys(self::optsPeran())],
            [['agenda_id', 'member_id'], 'unique', 'targetAttribute' => ['agenda_id', 'member_id']],
            [['agenda_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agenda::class, 'targetAttribute' => ['agenda_id' => 'agenda_id']],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::class, 'targetAttribute' => ['member_id' => 'member_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'user_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agenda_id' => 'Agenda ID',
            'member_id' => 'Member ID',
            'peran' => 'Peran',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
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

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'created_by']);
    }

    public static function optsPeran()
    {
        return [
            self::PERAN_PESERTA => 'Peserta',
            self::PERAN_NARASUMBER => 'Narasumber',
            self::PERAN_MODERATOR => 'Moderator',
        ];
    }

    public function displayPeran()
    {
        return self::optsPeran()[$this->peran] ?? $this->peran;
    }
}