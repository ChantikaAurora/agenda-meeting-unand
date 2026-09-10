<?php

namespace app\controllers;

use Yii;
use app\models\Agenda;
use app\models\Lampiran;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class LampiranController extends Controller
{
    public $layout = 'notulis';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => static function () {
                            return !Yii::$app->user->isGuest
                                && Yii::$app->user->identity->can('manageLampiran');
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionCreate($agenda_id)
    {
        $agenda = $this->findAgenda($agenda_id);
        $model = new Lampiran();
        $model->agenda_id = $agenda->agenda_id;
        $model->jenis_lampiran = 'notulen';
        $model->status = Lampiran::STATUS_FINAL;

        if (Yii::$app->request->isPost) {
            $model->ringkasan = Yii::$app->request->post('ringkasan');
            $model->status = Yii::$app->request->post('status', Lampiran::STATUS_FINAL);
            $model->file_path = '';
            $file = UploadedFile::getInstanceByName('file');

            if ($file === null) {
                $model->addError('file_path', 'Silakan pilih file notulen terlebih dahulu.');
            } elseif (($uploadError = $this->validateUpload($file)) !== null) {
                $model->addError('file_path', $uploadError);
            } else {
                $directory = Yii::getAlias('@webroot/uploads/notulen');
                FileHelper::createDirectory($directory, 0755);
                $filename = Yii::$app->security->generateRandomString(24) . '.' . strtolower($file->extension);
                $relativePath = 'uploads/notulen/' . $filename;

                if (!$file->saveAs($directory . DIRECTORY_SEPARATOR . $filename)) {
                    $model->addError('file_path', 'File notulen gagal disimpan.');
                } else {
                    $model->file_path = $relativePath;
                    $model->uploaded_by = (int) Yii::$app->user->id;
                    $model->created_by = (int) Yii::$app->user->id;

                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', 'Notulen berhasil diunggah.');
                        return $this->redirect(['/notulis/index']);
                    }
                }
            }
        }

        return $this->render('create', [
            'agenda' => $agenda,
            'model' => $model,
        ]);
    }

    public function actionUpdate($agenda_id)
    {
        $agenda = $this->findAgenda($agenda_id);
        $model = Lampiran::find()
            ->andWhere(['agenda_id' => $agenda->agenda_id, 'deleted_at' => null])
            ->orderBy(['lampiran_id' => SORT_DESC])
            ->one();

        if ($model === null) {
            return $this->redirect(['create', 'agenda_id' => $agenda->agenda_id]);
        }

        if (Yii::$app->request->isPost) {
            $model->ringkasan = Yii::$app->request->post('ringkasan');
            $model->status = Yii::$app->request->post('status', Lampiran::STATUS_FINAL);
            $file = UploadedFile::getInstanceByName('file');

            if ($file !== null) {
                if (($uploadError = $this->validateUpload($file)) !== null) {
                    $model->addError('file_path', $uploadError);
                } else {
                    $directory = Yii::getAlias('@webroot/uploads/notulen');
                    FileHelper::createDirectory($directory, 0755);
                    $filename = Yii::$app->security->generateRandomString(24) . '.' . strtolower($file->extension);
                    $oldPath = Yii::getAlias('@webroot/' . $model->file_path);

                    if ($file->saveAs($directory . DIRECTORY_SEPARATOR . $filename)) {
                        $model->file_path = 'uploads/notulen/' . $filename;
                        if (is_file($oldPath)) {
                            @unlink($oldPath);
                        }
                    } else {
                        $model->addError('file_path', 'File notulen gagal disimpan.');
                    }
                }
            }

            if (!$model->hasErrors() && $model->save(false)) {
                Yii::$app->session->setFlash('success', 'Notulen berhasil diperbarui.');
                return $this->redirect(['/notulis/index']);
            }
        }

        return $this->render('update', [
            'agenda' => $agenda,
            'model' => $model,
        ]);
    }

    private function findAgenda($id): Agenda
    {
        $agenda = Agenda::findOne(['agenda_id' => $id, 'deleted_at' => null]);
        if ($agenda === null) {
            throw new NotFoundHttpException('Agenda yang diminta tidak ditemukan.');
        }
        return $agenda;
    }

    private function validateUpload(UploadedFile $file): ?string
    {
        if ($file->error !== UPLOAD_ERR_OK) {
            return 'File gagal diunggah. Silakan pilih file kembali.';
        }

        $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        if (!in_array(strtolower($file->extension), $allowedExtensions, true)) {
            return 'Format file harus PDF, DOC, DOCX, XLS, XLSX, PPT, atau PPTX.';
        }

        if ($file->size > 10 * 1024 * 1024) {
            return 'Ukuran file maksimal 10 MB.';
        }

        return null;
    }
}
