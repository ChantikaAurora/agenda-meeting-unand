<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Model minimal untuk tabel `lokasi`.
 * NOTE: sama seperti Unit.php, ini punya rekan (feature/unit-lokasi).
 * Edit file ini kalau perlu tambahan, jangan duplikat.
 *
 * @property int $lokasi_id
 * @property int $unit_id
 * @property string $kategori_lokasi
 * @property string $lokasi
 * @property bool $is_active
 *
 * @property Unit $unit
 */
class Lokasi extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%lokasi}}';
    }

    public function rules()
    {
        return [
            [['unit_id', 'kategori_lokasi', 'lokasi'], 'required'],
            ['unit_id', 'integer'],
            [['kategori_lokasi', 'lokasi'], 'string', 'max' => 150],
            ['is_active', 'boolean'],
            [
                'unit_id', 'exist', 'skipOnError' => true,
                'targetClass' => Unit::class, 'targetAttribute' => ['unit_id' => 'unit_id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'lokasi_id' => 'ID',
            'unit_id' => 'Unit',
            'kategori_lokasi' => 'Kategori Lokasi',
            'lokasi' => 'Nama Lokasi/Ruangan',
        ];
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['unit_id' => 'unit_id']);
    }
}
