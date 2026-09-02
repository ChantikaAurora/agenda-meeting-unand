<?php

namespace app\controllers;

use Yii;
use app\models\Agenda;
use app\models\Member;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * MemberController implements the CRUD actions for Member model.
 */
class MemberController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Member models.
     *
     * @return string
     */
    public function actionIndex()
    {
        /*
         * ============================
         * DATA MEMBER
         * ============================
         */
        $dataProvider = new ActiveDataProvider([
            'query' => Member::find(),
        ]);


        /*
         * ============================
         * DATA DAFTAR HADIR
         * ============================
         */

        // Daftar agenda
        $agendaList = Agenda::find()
            ->orderBy(['tanggal' => SORT_DESC])
            ->all();

        // Filter
        $agendaId = Yii::$app->request->get('agenda_id', '');
        $status = Yii::$app->request->get('status', '');
        $q = trim((string) Yii::$app->request->get('q', ''));


        /*
         * Query peserta + absensi
         */
        $query = (new Query())
            ->select([
                'am.agenda_id',
                'am.member_id',

                'm.nama',
                'm.identitas_number',
                'm.instansi',

                'ag.pembahasan',

                'ab.absensi_id',
                'ab.waktu_scan',
                'ab.tanda_tangan_path',
            ])

            ->from(['am' => 'agenda_member'])

            ->innerJoin(
                ['m' => 'member'],
                'm.member_id = am.member_id'
            )

            ->innerJoin(
                ['ag' => 'agenda'],
                'ag.agenda_id = am.agenda_id'
            )

            ->leftJoin(
                ['ab' => 'absensi'],
                'ab.agenda_id = am.agenda_id
                 AND ab.member_id = am.member_id
                 AND ab.deleted_at IS NULL'
            )

            ->where([
                'am.deleted_at' => null
            ]);


        /*
         * ============================
         * FILTER AGENDA
         * ============================
         */
        if ($agendaId !== '') {
            $query->andWhere([
                'am.agenda_id' => $agendaId
            ]);
        }


        /*
         * ============================
         * SEARCH
         * ============================
         */
        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'm.nama', $q],
                ['like', 'm.identitas_number', $q],
            ]);
        }


        /*
         * ============================
         * FILTER STATUS
         * ============================
         */
        if ($status === 'hadir') {

            $query->andWhere([
                'is not',
                'ab.absensi_id',
                null
            ]);

        } elseif ($status === 'tidak_hadir') {

            $query->andWhere([
                'ab.absensi_id' => null
            ]);
        }


        /*
         * ============================
         * AMBIL DATA
         * ============================
         */
        $hadirRows = $query
            ->orderBy([
                'ag.tanggal' => SORT_DESC,
                'm.nama' => SORT_ASC
            ])
            ->all();


        /*
         * ============================
         * STATISTIK
         * ============================
         */
        $totalPeserta = count($hadirRows);

        $totalHadir = count(
            array_filter(
                $hadirRows,
                fn ($row) => $row['absensi_id'] !== null
            )
        );

        $totalTidakHadir = $totalPeserta - $totalHadir;


        /*
         * ============================
         * DATA PROVIDER DAFTAR HADIR
         * ============================
         */
        $hadirDataProvider = new ArrayDataProvider([
            'allModels' => $hadirRows,

            'pagination' => [
                'pageSize' => 10,
            ],
        ]);


        /*
         * ============================
         * RENDER HALAMAN MEMBER
         * ============================
         */
        return $this->render('index', [

            // Data Member
            'dataProvider' => $dataProvider,

            // Data Daftar Hadir
            'hadirDataProvider' => $hadirDataProvider,
            'agendaList' => $agendaList,
            'agendaId' => $agendaId,
            'status' => $status,
            'q' => $q,

            // Statistik
            'totalPeserta' => $totalPeserta,
            'totalHadir' => $totalHadir,
            'totalTidakHadir' => $totalTidakHadir,
        ]);
    }


    /**
     * Menampilkan halaman Daftar Hadir secara terpisah.
     *
     * Fungsi ini tetap dipertahankan.
     * Tidak digunakan ketika Daftar Hadir dibuka
     * melalui tab Kelola Peserta.
     *
     * @return string
     */
    public function actionDaftarHadir()
    {
        $this->layout = 'blank';

        $agendaList = Agenda::find()
            ->orderBy(['tanggal' => SORT_DESC])
            ->all();

        $agendaId = Yii::$app->request->get('agenda_id', '');
        $status = Yii::$app->request->get('status', '');
        $q = trim((string) Yii::$app->request->get('q', ''));

        $query = (new Query())
            ->select([
                'am.agenda_id',
                'am.member_id',
                'm.nama',
                'm.identitas_number',
                'm.instansi',
                'ag.pembahasan',
                'ab.absensi_id',
                'ab.waktu_scan',
                'ab.tanda_tangan_path',
            ])

            ->from(['am' => 'agenda_member'])

            ->innerJoin(
                ['m' => 'member'],
                'm.member_id = am.member_id'
            )

            ->innerJoin(
                ['ag' => 'agenda'],
                'ag.agenda_id = am.agenda_id'
            )

            ->leftJoin(
                ['ab' => 'absensi'],
                'ab.agenda_id = am.agenda_id
                 AND ab.member_id = am.member_id
                 AND ab.deleted_at IS NULL'
            )

            ->where([
                'am.deleted_at' => null
            ]);

        if ($agendaId !== '') {
            $query->andWhere([
                'am.agenda_id' => $agendaId
            ]);
        }

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'm.nama', $q],
                ['like', 'm.identitas_number', $q],
            ]);
        }

        if ($status === 'hadir') {
            $query->andWhere([
                'is not',
                'ab.absensi_id',
                null
            ]);

        } elseif ($status === 'tidak_hadir') {
            $query->andWhere([
                'ab.absensi_id' => null
            ]);
        }

        $hadirRows = $query
            ->orderBy([
                'ag.tanggal' => SORT_DESC,
                'm.nama' => SORT_ASC
            ])
            ->all();

        $totalPeserta = count($hadirRows);

        $totalHadir = count(
            array_filter(
                $hadirRows,
                fn ($row) => $row['absensi_id'] !== null
            )
        );

        $totalTidakHadir = $totalPeserta - $totalHadir;

        $hadirDataProvider = new ArrayDataProvider([
            'allModels' => $hadirRows,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);

        return $this->render('daftar-hadir', [
            'hadirDataProvider' => $hadirDataProvider,
            'agendaList' => $agendaList,
            'agendaId' => $agendaId,
            'status' => $status,
            'q' => $q,
            'totalPeserta' => $totalPeserta,
            'totalHadir' => $totalHadir,
            'totalTidakHadir' => $totalTidakHadir,
        ]);
    }


    /**
     * Generate dan download PDF Daftar Hadir Peserta,
     * mengikuti filter yang sedang aktif (agenda, status, pencarian).
     *
     * @return \yii\web\Response
     */
    public function actionExportPdf()
    {
        $agendaId = Yii::$app->request->get('agenda_id', '');
        $status = Yii::$app->request->get('status', '');
        $q = trim((string) Yii::$app->request->get('q', ''));

        $query = (new Query())
            ->select([
                'am.agenda_id',
                'am.member_id',
                'm.nama',
                'm.identitas_number',
                'm.instansi',
                'ag.pembahasan',
                'ab.absensi_id',
                'ab.waktu_scan',
            ])
            ->from(['am' => 'agenda_member'])
            ->innerJoin(['m' => 'member'], 'm.member_id = am.member_id')
            ->innerJoin(['ag' => 'agenda'], 'ag.agenda_id = am.agenda_id')
            ->leftJoin(
                ['ab' => 'absensi'],
                'ab.agenda_id = am.agenda_id AND ab.member_id = am.member_id AND ab.deleted_at IS NULL'
            )
            ->where(['am.deleted_at' => null]);

        if ($agendaId !== '') {
            $query->andWhere(['am.agenda_id' => $agendaId]);
        }

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'm.nama', $q],
                ['like', 'm.identitas_number', $q],
            ]);
        }

        if ($status === 'hadir') {
            $query->andWhere(['is not', 'ab.absensi_id', null]);
        } elseif ($status === 'tidak_hadir') {
            $query->andWhere(['ab.absensi_id' => null]);
        }

        $rows = $query
            ->orderBy(['ag.tanggal' => SORT_DESC, 'm.nama' => SORT_ASC])
            ->all();

        $agendaLabel = 'Semua Agenda';
        if ($agendaId !== '') {
            $agenda = Agenda::findOne((int) $agendaId);
            if ($agenda) {
                $agendaLabel = $agenda->pembahasan ?: 'Agenda';
            }
        }

        $html = '<h2 style="text-align:center;">Daftar Hadir Peserta</h2>';
        $html .= '<p style="text-align:center; color:#666; margin-bottom: 16px;">Agenda: ' . Html::encode($agendaLabel) . '</p>';
        $html .= '<p style="text-align:center; color:#666; margin-top: 0;">Dicetak pada: ' . date('d M Y H:i') . ' WIB</p>';
        $html .= '<table width="100%" cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse; font-size:12px;">';
        $html .= '<thead><tr style="background:#f0f0f0;">
                    <th>No</th>
                    <th>Nama Peserta</th>
                    <th>NIK/NIM</th>
                    <th>Unit/Bagian</th>
                    <th>Status</th>
                    <th>Waktu Scan</th>
                  </tr></thead><tbody>';

        if (empty($rows)) {
            $html .= '<tr><td colspan="6" style="text-align:center; padding:12px;">Tidak ada data peserta.</td></tr>';
        } else {
            $no = 1;
            foreach ($rows as $row) {
                $hadir = $row['absensi_id'] !== null;
                $waktu = $hadir ? date('H:i', strtotime($row['waktu_scan'])) . ' WIB' : '-';
                $statusLabel = $hadir ? 'Hadir' : 'Tidak Hadir';

                $html .= '<tr>
                            <td>' . $no++ . '</td>
                            <td>' . Html::encode($row['nama'] ?: '-') . '</td>
                            <td>' . Html::encode($row['identitas_number'] ?: '-') . '</td>
                            <td>' . Html::encode($row['instansi'] ?: '-') . '</td>
                            <td>' . $statusLabel . '</td>
                            <td>' . $waktu . '</td>
                          </tr>';
            }
        }

        $html .= '</tbody></table>';

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return Yii::$app->response->sendContentAsFile(
            $dompdf->output(),
            'daftar-hadir-peserta-' . date('Y-m-d') . '.pdf',
            ['mimeType' => 'application/pdf', 'inline' => false]
        );
    }


    /**
     * Displays a single Member model.
     *
     * @param int $member_id Member ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($member_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($member_id),
        ]);
    }


    /**
     * Creates a new Member model.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Member();

        if ($this->request->isPost) {

            if ($model->load($this->request->post())) {

                $model->created_by = Yii::$app->user->id;

                if ($model->save()) {
                    return $this->redirect([
                        'view',
                        'member_id' => $model->member_id
                    ]);
                }
            }

        } else {

            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing Member model.
     *
     * @param int $member_id Member ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($member_id)
    {
        $model = $this->findModel($member_id);

        if (
            $this->request->isPost &&
            $model->load($this->request->post())
        ) {

            $model->updated_by = Yii::$app->user->id;

            if ($model->save()) {
                return $this->redirect([
                    'view',
                    'member_id' => $model->member_id
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing Member model.
     *
     * @param int $member_id Member ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($member_id)
    {
        $this->findModel($member_id)->delete();

        return $this->redirect([
            'index'
        ]);
    }


    /**
     * Finds the Member model based on its primary key value.
     *
     * @param int $member_id Member ID
     * @return Member
     * @throws NotFoundHttpException
     */
    protected function findModel($member_id)
    {
        if (
            ($model = Member::findOne([
                'member_id' => $member_id
            ])) !== null
        ) {
            return $model;
        }

        throw new NotFoundHttpException(
            'The requested page does not exist.'
        );
    }
}