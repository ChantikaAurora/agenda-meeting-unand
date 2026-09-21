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
    public $layout = 'admin';

    public function actionDashboard()
    {
        $this->layout = 'notulis';
        $today = date('Y-m-d');
        $agendas = Agenda::find()
            ->andWhere(['deleted_at' => null])
            ->with(['lokasi', 'lampirans'])
            ->orderBy(['tanggal' => SORT_DESC])
            ->limit(5)
            ->all();

        $totalAgenda = (int) Agenda::find()->andWhere(['deleted_at' => null])->count();
        $agendaHariIni = (int) Agenda::find()
            ->andWhere(['deleted_at' => null, 'tanggal' => $today])
            ->count();
        $belumDiunggah = 0;
        $sudahDiunggah = 0;

        $allAgendas = Agenda::find()
            ->andWhere(['deleted_at' => null])
            ->with('lampirans')
            ->all();
        foreach ($allAgendas as $agenda) {
            $status = $this->hitungStatusNotulen($agenda);
            if ($status === 'Belum Diunggah') {
                $belumDiunggah++;
            } else {
                $sudahDiunggah++;
            }
        }

        return $this->render('dashboard', [
            'totalAgenda' => $totalAgenda,
            'agendaHariIni' => $agendaHariIni,
            'belumDiunggah' => $belumDiunggah,
            'sudahDiunggah' => $sudahDiunggah,
            'agendas' => $agendas,
        ]);
    }
    /**
     * Menampilkan daftar semua agenda beserta status notulennya,
     * dengan dukungan pencarian judul dan filter status notulen.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'notulis';
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
