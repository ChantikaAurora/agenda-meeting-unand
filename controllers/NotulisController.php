<?php

namespace app\controllers;

use Yii;
use app\models\Agenda;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * NotulisController menampilkan halaman Daftar Agenda versi Notulis.
 */
class NotulisController extends Controller
{
    public $layout = 'admin';

    /**
     * Kontrol akses: hanya user yang sudah login dan punya permission
     * manageAgenda / viewAgenda yang boleh membuka halaman notulis.
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['dashboard', 'index'],
                        'matchCallback' => function () {
                            /** @var \app\models\User $identity */
                            $identity = Yii::$app->user->identity;
                            return !Yii::$app->user->isGuest
                                && ($identity->can('manageAgenda') || $identity->can('viewAgenda'));
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'dashboard' => ['GET'],
                    'index' => ['GET'],
                ],
            ],
        ]);
    }

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
        $search = Yii::$app->request->get('search');
        $statusFilter = Yii::$app->request->get('status_notulen');

        $query = Agenda::find()
            ->andWhere(['deleted_at' => null])
            ->with(['lokasi', 'lampirans']);

        if (!empty($search)) {
            $query->andWhere(['like', 'pembahasan', $search]);
        }

        $query->orderBy(['tanggal' => SORT_DESC]);

        // Status notulen adalah nilai turunan dari relasi Lampiran, bukan kolom
        // langsung di tabel agenda, jadi tidak bisa difilter lewat SQL WHERE.
        // Sebelumnya filter ini diterapkan SETELAH ActiveDataProvider melakukan
        // paginasi (hanya 10 baris per halaman), sehingga:
        //   - baris yang cocok di halaman lain tidak pernah ikut tersaring,
        //   - jumlah total & jumlah halaman yang ditampilkan tetap memakai
        //     angka sebelum difilter, sehingga paginasi jadi tidak akurat.
        // Perbaikannya: ketika ada filter status, ambil semua data lebih dulu,
        // saring di PHP, baru bungkus hasilnya dengan ArrayDataProvider supaya
        // total & paginasi mengikuti jumlah data yang benar-benar cocok.
        if (!empty($statusFilter)) {
            $semuaAgenda = $query->all();
            $agendaTersaring = array_values(array_filter(
                $semuaAgenda,
                function ($model) use ($statusFilter) {
                    return $this->hitungStatusNotulen($model) === $statusFilter;
                }
            ));

            $dataProvider = new ArrayDataProvider([
                'allModels' => $agendaTersaring,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
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