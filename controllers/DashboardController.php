<?php

namespace app\controllers;

use DateTime;
use Yii;
use app\models\Agenda;
use yii\filters\AccessControl;
use yii\web\Controller;

class DashboardController extends Controller
{
    public $layout = 'admin';

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => static fn() => !Yii::$app->user->isGuest,
                    ],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $today = date('Y-m-d');

        // Bulan yang ditampilkan di mini kalender bisa digeser lewat ?month=YYYY-MM.
        // Validasi format ketat dulu sebelum dipakai, supaya input URL yang aneh
        // tidak bisa bikin query error atau nyasar ke tanggal yang tidak valid.
        $monthParam = Yii::$app->request->get('month');
        $calendarMonth = (is_string($monthParam) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $monthParam))
            ? DateTime::createFromFormat('Y-m-d', $monthParam . '-01')
            : new DateTime('first day of this month');

        if ($calendarMonth === false) {
            $calendarMonth = new DateTime('first day of this month');
        }

        $totalAgenda = Agenda::find()->andWhere(['deleted_at' => null])->count();
        $agendaHariIni = Agenda::find()->andWhere(['deleted_at' => null, 'tanggal' => $today])->count();

        // Member & Lampiran belum punya model AR (modul rekan/belum dibangun),
        // jadi diambil lewat query langsung supaya tidak bergantung ke file yang belum ada.
        $totalPeserta = (int) Yii::$app->db->createCommand(
            'SELECT COUNT(*) FROM {{%member}} WHERE deleted_at IS NULL'
        )->queryScalar();

        $totalLampiran = (int) Yii::$app->db->createCommand(
            'SELECT COUNT(*) FROM {{%lampiran}} WHERE deleted_at IS NULL'
        )->queryScalar();

        $agendaTerbaru = Agenda::find()
            ->andWhere(['deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        $agendaMendatang = Agenda::find()
            ->andWhere(['deleted_at' => null])
            ->andWhere(['>=', 'tanggal', $today])
            ->orderBy(['tanggal' => SORT_ASC, 'waktu_mulai' => SORT_ASC])
            ->limit(5)
            ->all();

        $tanggalDenganAgenda = Agenda::find()
            ->select('tanggal')
            ->andWhere(['deleted_at' => null])
            ->andWhere(['between', 'tanggal', $calendarMonth->format('Y-m-01'), $calendarMonth->format('Y-m-t')])
            ->column();

        return $this->render('index', [
            'totalAgenda' => $totalAgenda,
            'agendaHariIni' => $agendaHariIni,
            'totalPeserta' => $totalPeserta,
            'totalLampiran' => $totalLampiran,
            'agendaTerbaru' => $agendaTerbaru,
            'agendaMendatang' => $agendaMendatang,
            'tanggalDenganAgenda' => $tanggalDenganAgenda,
            'calendarMonth' => $calendarMonth,
        ]);
    }
}
