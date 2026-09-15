<?php

namespace app\models;

use yii\data\ActiveDataProvider;

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
            [['nomor_surat', 'pembahasan', 'tahun_akademik', 'status', 'tanggal', 'waktuFilter'], 'safe'],
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

        $query->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['tanggal' => $this->tanggal]);

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
        if ($this->waktuFilter === 'akan_datang') {
            $query->andWhere("CONCAT(tanggal, ' ', waktu_selesai) >= :now", [':now' => $now]);
        } elseif ($this->waktuFilter === 'selesai') {
            $query->andWhere("CONCAT(tanggal, ' ', waktu_selesai) < :now", [':now' => $now]);
        }

        return $dataProvider;
    }
}
