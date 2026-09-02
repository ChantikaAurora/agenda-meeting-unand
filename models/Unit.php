<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "unit".
 *
 * @property int $unit_id
 * @property string $nama_unit
 * @property string $kategori_unit
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property int $is_active
 *
 * @property User $createdBy
 * @property Lokasi[] $lokasis
 * @property User $updatedBy
 */
class Unit extends \yii\db\ActiveRecord
{
    const KATEGORI_UNIT_FAKULTAS = 'fakultas';
    const KATEGORI_UNIT_DIREKTORAT = 'direktorat';
    const KATEGORI_UNIT_LEMBAGA = 'lembaga';
    const KATEGORI_UNIT_UPT = 'upt';

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

    public static function tableName()
    {
        return 'unit';
    }

    public function rules()
    {
        return [
            [['created_by', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['nama_unit', 'kategori_unit'], 'required'],
            [['kategori_unit'], 'string'],
            [['created_by', 'updated_by', 'is_active'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['nama_unit'], 'string', 'max' => 150],
            ['kategori_unit', 'in', 'range' => array_keys(self::optsKategoriUnit())],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'unit_id' => 'Unit ID',
            'nama_unit' => 'Nama Unit',
            'kategori_unit' => 'Kategori Unit',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_active' => 'Is Active',
        ];
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'created_by']);
    }

    public function getLokasis()
    {
        return $this->hasMany(Lokasi::class, ['unit_id' => 'unit_id']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['user_id' => 'updated_by']);
    }

    public static function optsKategoriUnit()
    {
        return [
            self::KATEGORI_UNIT_FAKULTAS => 'fakultas',
            self::KATEGORI_UNIT_DIREKTORAT => 'direktorat',
            self::KATEGORI_UNIT_LEMBAGA => 'lembaga',
            self::KATEGORI_UNIT_UPT => 'upt',
        ];
    }

    public function displayKategoriUnit()
    {
        return self::optsKategoriUnit()[$this->kategori_unit];
    }

    public function isKategoriUnitFakultas()
    {
        return $this->kategori_unit === self::KATEGORI_UNIT_FAKULTAS;
    }

    public function setKategoriUnitToFakultas()
    {
        $this->kategori_unit = self::KATEGORI_UNIT_FAKULTAS;
    }

    public function isKategoriUnitDirektorat()
    {
        return $this->kategori_unit === self::KATEGORI_UNIT_DIREKTORAT;
    }

    public function setKategoriUnitToDirektorat()
    {
        $this->kategori_unit = self::KATEGORI_UNIT_DIREKTORAT;
    }

    public function isKategoriUnitLembaga()
    {
        return $this->kategori_unit === self::KATEGORI_UNIT_LEMBAGA;
    }

    public function setKategoriUnitToLembaga()
    {
        $this->kategori_unit = self::KATEGORI_UNIT_LEMBAGA;
    }

    public function isKategoriUnitUpt()
    {
        return $this->kategori_unit === self::KATEGORI_UNIT_UPT;
    }

    public function setKategoriUnitToUpt()
    {
        $this->kategori_unit = self::KATEGORI_UNIT_UPT;
    }
}