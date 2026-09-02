<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Model minimal untuk tabel `unit`.
 * NOTE: modul Unit & Lokasi jadi tanggung jawab rekan (lihat pembagian kerja).
 * File ini sengaja dibuat minimal karena Agenda butuh relasi ke Lokasi -> Unit.
 * Kalau nanti perlu tambah validasi/behavior, EDIT file ini (jangan bikin file baru
 * yang sama), supaya tidak ada 2 versi Unit.php yang bentrok saat merge.
 *
 * @property int $unit_id
 * @property string $nama_unit
 * @property string $kategori_unit
 * @property bool $is_active
 */
class Unit extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%unit}}';
    }

    public function rules()
    {
        return [
            [['nama_unit', 'kategori_unit'], 'required'],
            ['nama_unit', 'string', 'max' => 150],
            ['kategori_unit', 'in', 'range' => ['fakultas', 'direktorat', 'lembaga', 'upt']],
            ['is_active', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'unit_id' => 'ID',
            'nama_unit' => 'Nama Unit',
            'kategori_unit' => 'Kategori Unit',
            'is_active' => 'Aktif',
        ];
    }
}
