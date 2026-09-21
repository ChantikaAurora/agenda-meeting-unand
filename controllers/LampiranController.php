<?php

namespace app\controllers;

use Yii;
use app\models\Agenda;
use app\models\Lampiran;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class LampiranController extends Controller
{
    public $layout = 'admin';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'delete'],
                        'matchCallback' => function () {
                            /** @var \app\models\User $identity */
                            $identity = Yii::$app->user->identity;
                            return !Yii::$app->user->isGuest && $identity->can('manageLampiran');
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreate($agenda_id)
    {
        $agenda = $this->findAgenda($agenda_id);
        $model = new Lampiran();
        $model->agenda_id = $agenda->agenda_id;
        $model->jenis_lampiran = 'Dokumentasi Rapat';

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->agenda_id = $agenda->agenda_id;
            $model->jenis_lampiran = 'Dokumentasi Rapat';
            $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');

            if ($model->uploadFile !== null) {
                $model->file_path = 'uploads/lampiran/' . Yii::$app->security->generateRandomString(24)
                    . '.' . strtolower($model->uploadFile->extension);
            }
            $model->uploaded_by = (int) Yii::$app->user->id;
            $model->created_by = (int) Yii::$app->user->id;

            if ($model->validate()) {
                $directory = Yii::getAlias('@webroot/uploads/lampiran');
                FileHelper::createDirectory($directory, 0755);
                $filePath = Yii::getAlias('@webroot/' . $model->file_path);

                if ($model->uploadFile->saveAs($filePath) && $model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Foto dokumentasi berhasil diunggah.');
                    return $this->redirect(['/agenda/view', 'id' => $agenda->agenda_id]);
                }

                if (is_file($filePath)) {
                    @unlink($filePath);
                }
                $model->addError('uploadFile', 'Foto gagal disimpan. Silakan coba lagi.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'agenda' => $agenda,
        ]);
    }

    public function actionDelete($id)
    {
        $model = Lampiran::findOne(['lampiran_id' => $id, 'deleted_at' => null]);
        if ($model === null) {
            throw new NotFoundHttpException('Foto dokumentasi tidak ditemukan.');
        }

        $filePath = Yii::getAlias('@webroot/' . $model->file_path);
        $model->deleted_at = date('Y-m-d H:i:s');
        $model->updated_by = (int) Yii::$app->user->id;
        $model->save(false);

        if (is_file($filePath)) {
            @unlink($filePath);
        }

        Yii::$app->session->setFlash('success', 'Foto dokumentasi berhasil dihapus.');
        return $this->redirect(['/agenda/view', 'id' => $model->agenda_id]);
    }

    private function findAgenda($id): Agenda
    {
        $agenda = Agenda::findOne(['agenda_id' => $id, 'deleted_at' => null]);
        if ($agenda === null) {
            throw new NotFoundHttpException('Agenda yang diminta tidak ditemukan.');
        }
        return $agenda;
    }
}
