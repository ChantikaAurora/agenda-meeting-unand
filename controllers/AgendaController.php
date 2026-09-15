<?php

namespace app\controllers;

use Yii;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use app\models\Agenda;
use app\models\AgendaSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AgendaController extends Controller
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
                        'actions' => ['index', 'view'],
                        'matchCallback' => function () {
                            /** @var \app\models\User $identity */
                            $identity = Yii::$app->user->identity;
                            return !Yii::$app->user->isGuest
                                && ($identity->can('manageAgenda') || $identity->can('viewAgenda'));
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete', 'generate-qr'],
                        'matchCallback' => function () {
                            /** @var \app\models\User $identity */
                            $identity = Yii::$app->user->identity;
                            return !Yii::$app->user->isGuest && $identity->can('manageAgenda');
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'generate-qr' => ['POST'],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new AgendaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Agenda();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                if (empty($model->status)) {
                    $model->status = Agenda::STATUS_TERJADWAL;
                }

                if ($model->save()) {
                    $this->generateAndSaveQr($model);
                    Yii::$app->session->setFlash('success', 'Agenda berhasil dibuat, QR Code otomatis digenerate.');
                    return $this->redirect(['view', 'id' => $model->agenda_id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost
            && $model->load(Yii::$app->request->post())
            && $model->save()
        ) {
            Yii::$app->session->setFlash('success', 'Agenda berhasil diperbarui.');
            return $this->redirect(['view', 'id' => $model->agenda_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted_at = date('Y-m-d H:i:s');
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Agenda berhasil dihapus.');
        return $this->redirect(['index']);
    }

    public function actionGenerateQr($id)
    {
        $model = $this->findModel($id);
        $this->generateAndSaveQr($model);

        Yii::$app->session->setFlash('success', 'QR Code berhasil dibuat ulang.');
        return $this->redirect(['view', 'id' => $model->agenda_id]);
    }

    private function generateAndSaveQr(Agenda $model): void
    {
        $token = $model->generateQrToken();
        $scanUrl = Url::to(['/absensi/scan', 'token' => $token], true);

        $dir = Yii::getAlias('@webroot/uploads/qrcodes');
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir, 0755);
        }

        $filename = $token . '.png';
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($scanUrl)
            ->size(300)
            ->margin(10)
            ->build();
        $result->saveToFile($dir . '/' . $filename);

        if (!empty($model->qr_code_path)) {
            $oldFile = Yii::getAlias('@webroot/' . $model->qr_code_path);
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        $model->qr_code_value = $token;
        $model->qr_code_path = 'uploads/qrcodes/' . $filename;
        $model->save(false);
    }

    /**
     * @throws NotFoundHttpException kalau agenda tidak ditemukan / sudah dihapus (soft delete)
     */
    protected function findModel($id): Agenda
    {
        $model = Agenda::findOne(['agenda_id' => $id, 'deleted_at' => null]);
        if ($model === null) {
            throw new NotFoundHttpException('Agenda yang diminta tidak ditemukan.');
        }
        return $model;
    }
}
