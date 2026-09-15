<?php

namespace app\controllers;

use Yii;
use app\models\Agenda;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CetakController menampilkan halaman cetak dokumen agenda dan QR Code.
 */
class CetakController extends Controller
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
                        'matchCallback' => function () {
                            /** @var \app\models\User $identity */
                            $identity = Yii::$app->user->identity;
                            return !Yii::$app->user->isGuest
                                && ($identity->can('manageAgenda') || $identity->can('viewAgenda'));
                        },
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param int $agenda_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException jika agenda tidak ditemukan / sudah dihapus
     */
    public function actionDokumen($agenda_id)
    {
        $model = Agenda::findOne(['agenda_id' => $agenda_id, 'deleted_at' => null]);

        if ($model === null) {
            throw new NotFoundHttpException('Agenda yang diminta tidak ditemukan.');
        }

        if (empty($model->qr_code_path) || !is_file(Yii::getAlias('@webroot/' . $model->qr_code_path))) {
            Yii::$app->session->setFlash('error', 'QR Code belum tersedia untuk agenda ini. Silakan generate QR terlebih dahulu di halaman detail agenda.');
            return $this->redirect(['/agenda/view', 'id' => $model->agenda_id]);
        }

        return $this->render('dokumen', [
            'model' => $model,
        ]);
    }
}
