<?php

namespace app\models;

use app\services\AgendaStatusResolver;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * AgendaSearch dipakai di halaman index untuk filter, search, & sorting.
 */
class AgendaSearch extends Agenda
{
    /** @var string|null 'akan_datang' | 'selesai' | null (semua) */
    public $waktuFilter;

    public function rules()
    {
        return [
            [['agenda_id', 'lokasi_id'], 'integer'],
            [['nomor_surat', 'pembahasan', 'tahun_akademik', 'tanggal', 'waktuFilter'], 'safe'],
            // Whitelist ketat: nilai status dari query string tidak pernah
            // masuk ke SQL kalau bukan salah satu status yang dikenal.
            ['status', 'in', 'range' => array_keys(self::statusList())],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Agenda::find()->andWhere(['deleted_at' => null]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['tanggal' => SORT_DESC, 'waktu_mulai' => SORT_ASC],
            ],
            'pagination' => ['pageSize' => 10],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['tanggal' => $this->tanggal]);

        if (!empty($this->pembahasan)) {
            $keyword = $this->pembahasan;

            $matchingLokasiIds = Lokasi::find()
                ->select('lokasi_id')
                ->leftJoin('{{%unit}}', '{{%unit}}.unit_id = {{%lokasi}}.unit_id')
                ->where(['like', '{{%lokasi}}.lokasi', $keyword])
                ->orWhere(['like', '{{%unit}}.nama_unit', $keyword])
                ->column();

            $query->andWhere(['or',
                ['like', 'pembahasan', $keyword],
                ['like', 'nomor_surat', $keyword],
                ['lokasi_id' => $matchingLokasiIds],
            ]);
        }

        $now = date('Y-m-d H:i:s');

        if (!empty($this->status)) {
            $query->andWhere($this->kondisiStatus($this->status, $now));
        }

        if ($this->waktuFilter === 'akan_datang') {
            $query->andWhere(new Expression("CONCAT(tanggal, ' ', waktu_selesai) >= :nowUpcoming", [':nowUpcoming' => $now]));
        } elseif ($this->waktuFilter === 'selesai') {
            $query->andWhere(new Expression("CONCAT(tanggal, ' ', waktu_selesai) < :nowPast", [':nowPast' => $now]));
        }

        return $dataProvider;
    }

    private function kondisiStatus(string $status, string $now): array
    {
        $otomatis = ['status' => AgendaStatusResolver::STATUS_OTOMATIS];
        $mulai = new Expression("CONCAT(tanggal, ' ', waktu_mulai)");
        $selesai = new Expression("CONCAT(tanggal, ' ', waktu_selesai)");

        return match ($status) {
            self::STATUS_TERJADWAL => ['and', $otomatis, ['>', $mulai, $now]],
            self::STATUS_BERLANGSUNG => ['and', $otomatis, ['<=', $mulai, $now], ['>=', $selesai, $now]],
            self::STATUS_SELESAI => ['and', $otomatis, ['<', $selesai, $now]],
            default => ['status' => $status],
        };
    }
}
