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
use yii\web\Response;
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
                        'actions' => ['create', 'update', 'delete', 'index', 'preview', 'document', 'download'],
                        'matchCallback' => static function () {
                            /** @var \app\models\User|null $identity */
                            $identity = Yii::$app->user->identity;
                            return !Yii::$app->user->isGuest 
                                && $identity !== null 
                                && $identity->can('manageLampiran');
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

    public function actionCreate($agenda_id, $notulen = 0)
    {
        $agenda = $this->findAgenda($agenda_id);
        $model = new Lampiran();
        $model->agenda_id = $agenda->agenda_id;
        $isNotulen = (bool) $notulen;
        $model->jenis_lampiran = $isNotulen ? 'notulen' : 'Dokumentasi Rapat';
        if ($isNotulen) {
            $this->layout = 'notulis';
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->agenda_id = $agenda->agenda_id;
            $model->jenis_lampiran = $isNotulen ? 'notulen' : 'Dokumentasi Rapat';
            $model->uploaded_by = (int) Yii::$app->user->id;
            $model->created_by = (int) Yii::$app->user->id;

            if ($isNotulen) {
                $file = UploadedFile::getInstanceByName('file');
                if ($file === null) {
                    $model->addError('file_path', 'Silakan pilih file notulen terlebih dahulu.');
                } elseif (($uploadError = $this->validateUpload($file)) !== null) {
                    $model->addError('file_path', $uploadError);
                } else {
                    $model->file_path = 'uploads/notulen/' . Yii::$app->security->generateRandomString(24)
                        . '.' . strtolower($file->extension);
                    $model->original_name = $this->getOriginalFilename($file);
                }
            } else {
                $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
                if ($model->uploadFile !== null) {
                    $model->file_path = 'uploads/lampiran/' . Yii::$app->security->generateRandomString(24)
                        . '.' . strtolower($model->uploadFile->extension);
                }
            }

            if (!$model->hasErrors() && $model->validate()) {
                $directory = Yii::getAlias('@webroot/' . ($isNotulen ? 'uploads/notulen' : 'uploads/lampiran'));
                FileHelper::createDirectory($directory, 0755);
                $filePath = Yii::getAlias('@webroot/' . $model->file_path);
                $upload = $isNotulen ? $file : $model->uploadFile;

                if ($upload->saveAs($filePath) && $model->save(false)) {
                    if ($isNotulen) {
                        Yii::$app->session->setFlash('success', 'Notulen berhasil diunggah.');
                        return $this->redirect(['/notulis/index']);
                    }
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
            'isNotulen' => $isNotulen,
        ]);
    }

    public function actionUpdate($agenda_id, $notulen = 0)
    {
        if ((bool) $notulen) {
            $this->layout = 'notulis';
        }
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
                        $model->original_name = $this->getOriginalFilename($file);
                        $model->status = Lampiran::STATUS_FINAL;
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

    public function actionIndex($agenda_id, $notulen = 0)
    {
        if ((bool) $notulen) {
            $this->layout = 'notulis';
        }
        $agenda = $this->findAgenda($agenda_id);
        $model = $this->findLampiran($agenda->agenda_id);
        $filePath = Yii::getAlias('@webroot/' . ltrim($model->file_path, '/'));

        return $this->render('index', [
            'agenda' => $agenda,
            'model' => $model,
            'fileUrl' => Yii::getAlias('@web/' . ltrim($model->file_path, '/')),
            'fileExists' => is_file($filePath),
        ]);
    }

    public function actionPreview($agenda_id, $notulen = 0)
    {
        if ((bool) $notulen) {
            $this->layout = 'notulis';
        }
        $agenda = $this->findAgenda($agenda_id);
        $model = $this->findLampiran($agenda->agenda_id);
        $filePath = Yii::getAlias('@webroot/' . ltrim($model->file_path, '/'));
        $extension = strtolower(pathinfo($model->file_path, PATHINFO_EXTENSION));

        if (!is_file($filePath)) {
            throw new NotFoundHttpException('File notulen tidak ditemukan di server.');
        }

        $previewHtml = $extension === 'docx' ? $this->buildDocxPreview($filePath) : null;

        return $this->render('preview', [
            'agenda' => $agenda,
            'model' => $model,
            'fileUrl' => Yii::getAlias('@web/' . ltrim($model->file_path, '/')),
            'extension' => $extension,
            'previewHtml' => $previewHtml,
        ]);
    }

    public function actionDocument($agenda_id, $notulen = 0)
    {
        if ((bool) $notulen) {
            $this->layout = 'notulis';
        }
        $agenda = $this->findAgenda($agenda_id);
        $model = $this->findLampiran($agenda->agenda_id);
        $filePath = Yii::getAlias('@webroot/' . ltrim($model->file_path, '/'));
        $extension = strtolower(pathinfo($model->file_path, PATHINFO_EXTENSION));

        if (!is_file($filePath)) {
            throw new NotFoundHttpException('File notulen tidak ditemukan di server.');
        }

        if ($extension !== 'pdf') {
            return $this->redirect(['download', 'agenda_id' => $agenda->agenda_id]);
        }

        return $this->render('document', [
            'agenda' => $agenda,
            'model' => $model,
            'fileUrl' => Yii::$app->request->baseUrl . '/' . ltrim($model->file_path, '/'),
        ]);
    }

    public function actionDownload($agenda_id): Response
    {
        $model = $this->findLampiran($agenda_id);
        $filePath = Yii::getAlias('@webroot/' . ltrim($model->file_path, '/'));

        if (!is_file($filePath)) {
            throw new NotFoundHttpException('File notulen tidak ditemukan di server.');
        }

        return Yii::$app->response->sendFile($filePath, $model->original_name ?: basename($filePath));
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

    private function findLampiran($agendaId): Lampiran
    {
        $model = Lampiran::find()
            ->andWhere(['agenda_id' => $agendaId, 'deleted_at' => null])
            ->orderBy(['lampiran_id' => SORT_DESC])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Notulen untuk agenda ini belum tersedia.');
        }

        return $model;
    }

    private function getOriginalFilename(UploadedFile $file): string
    {
        $filename = basename($file->name);
        $filename = preg_replace('/[^A-Za-z0-9._ -]/', '_', $filename);

        return trim($filename) !== '' ? trim($filename) : 'notulen.' . strtolower($file->extension);
    }

    private function buildDocxPreview(string $filePath): ?string
    {
        $archive = new \ZipArchive();
        if ($archive->open($filePath) !== true) {
            return null;
        }

        $documentXml = $archive->getFromName('word/document.xml');
        $archive->close();
        if ($documentXml === false) {
            return null;
        }

        $xml = new \DOMDocument();
        $xml->preserveWhiteSpace = false;
        if (!$xml->loadXML($documentXml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return null;
        }

        $xpath = new \DOMXPath($xml);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $paragraphs = [];
        foreach ($xpath->query('//w:body/w:p') as $paragraph) {
            $text = '';
            foreach ($xpath->query('.//w:t', $paragraph) as $textNode) {
                $text .= $textNode->textContent;
            }
            $paragraphs[] = '<p>' . nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8')) . '</p>';
        }

        return $paragraphs === [] ? null : implode('', $paragraphs);
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