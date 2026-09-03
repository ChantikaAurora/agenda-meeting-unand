<?php

namespace app\controllers;

use app\models\Agenda;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CetakController menampilkan halaman cetak dokumen agenda dan QR Code.
 */
class CetakController extends Controller
{
    /**
     * Layout khusus tanpa navbar, supaya tampilan bersih untuk dicetak.
     * @var string
     */
    // public $layout = 'cetak';
    public $layout = 'admin';

    /**
     * Menampilkan halaman cetak dokumen agenda beserta QR Code presensi.
     *
     * @param int $agenda_id
     * @return string
     * @throws NotFoundHttpException jika agenda tidak ditemukan
     */
    public function actionDokumen($agenda_id)
    {
        $model = Agenda::findOne($agenda_id);

        if ($model === null) {
            throw new NotFoundHttpException('Agenda yang diminta tidak ditemukan.');
        }

        return $this->render('dokumen', [
            'model' => $model,
        ]);
    }
}
