<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "lokasi".
 *
 * @property int $lokasi_id
 * @property int $unit_id
 * @property string $kategori_lokasi
 * @property string $lokasi
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property int $is_active
 *
 * @property Agenda[] $agendas
 * @property User $createdBy
 * @property Unit $unit
 * @property User $updatedBy
 */
class Lokasi extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lokasi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['unit_id', 'kategori_lokasi', 'lokasi'], 'required'],
            [['unit_id', 'created_by', 'updated_by', 'is_active'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['kategori_lokasi'], 'string', 'max' => 100],
            [['lokasi'], 'string', 'max' => 150],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::class, 'targetAttribute' => ['unit_id' => 'unit_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lokasi_id' => 'Lokasi ID',
            'unit_id' => 'Unit ID',
            'kategori_lokasi' => 'Kategori Lokasi',
            'lokasi' => 'Lokasi',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_active' => 'Is Active',
        ];
    }

    public function getAgendas()
    {
        return $this->hasMany(Agenda::class, ['lokasi_id' => 'lokasi_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'created_by']);
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['unit_id' => 'unit_id']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'updated_by']);
    }
}
