<?php

namespace app\controllers;

use app\models\Agenda;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

/**
 * NotulisController menampilkan halaman Daftar Agenda versi Notulis.
 */
class NotulisController extends Controller
{
    /**
     * Menampilkan daftar semua agenda beserta status notulennya,
     * dengan dukungan pencarian judul dan filter status notulen.
     *
     * @return string
     */
    public function actionIndex()
    {
        $search = \Yii::$app->request->get('search');
        $statusFilter = \Yii::$app->request->get('status_notulen');

        $query = Agenda::find()->with(['lokasi', 'lampirans']);

        if (!empty($search)) {
            $query->andWhere(['like', 'pembahasan', $search]);
        }

        $query->orderBy(['tanggal' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        // Filter status notulen dilakukan setelah query (karena statusnya turunan dari relasi Lampiran, bukan kolom langsung)
        if (!empty($statusFilter)) {
            $filtered = array_filter($dataProvider->getModels(), function ($model) use ($statusFilter) {
                return $this->hitungStatusNotulen($model) === $statusFilter;
            });
            $dataProvider->setModels(array_values($filtered));
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'search' => $search,
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Helper untuk menghitung label status notulen suatu Agenda.
     */
    private function hitungStatusNotulen(Agenda $model)
    {
        $lampirans = $model->lampirans;

        if (empty($lampirans)) {
            return 'Belum Diunggah';
        }

        $lampiranTerbaru = end($lampirans);

        if ($lampiranTerbaru->status === 'draft') {
            return 'Draft';
        }

        if (!empty($lampiranTerbaru->email_sent_at)) {
            return 'Email Terkirim';
        }

        return 'Selesai Diunggah';
    }
}