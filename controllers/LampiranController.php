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
    /** Nilai kolom jenis_lampiran untuk notulen dan foto dokumentasi. */
    private const JENIS_NOTULEN = 'notulen';
    private const JENIS_FOTO = 'Dokumentasi Rapat';

    public $layout = 'admin';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete', 'delete-notulen', 'index', 'preview', 'document', 'download'],
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
                    'delete-notulen' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreate($agenda_id, $notulen = 0)
    {
        $agenda = $this->findAgenda($agenda_id);
        $isNotulen = (bool) $notulen;

        if ($isNotulen) {
            $this->layout = 'notulis';

            // Satu agenda hanya punya satu notulen aktif. Kalau sudah ada
            // (misalnya file fisiknya hilang), arahkan ke halaman edit.
            if ($this->findNotulenOrNull($agenda->agenda_id) !== null) {
                return $this->redirect(['update', 'agenda_id' => $agenda->agenda_id, 'notulen' => 1]);
            }
        }

        $model = new Lampiran();
        $model->agenda_id = $agenda->agenda_id;
        $model->jenis_lampiran = $isNotulen ? self::JENIS_NOTULEN : self::JENIS_FOTO;
        $file = null;

        if (Yii::$app->request->isPost) {
            // Sengaja tanpa $model->load(): semua nilai diisi eksplisit di bawah
            // supaya field lain (status, deleted_at, email_sent_at, dst.) tidak
            // bisa disusupkan lewat POST.
            $model->uploaded_by = (int) Yii::$app->user->id;
            $model->created_by = (int) Yii::$app->user->id;

            if ($isNotulen) {
                // Form notulen mengirim 'status' dan 'ringkasan' sebagai field
                // tingkat atas (bukan Lampiran[...]), jadi dibaca langsung.
                $model->status = $this->postedStatus(Lampiran::STATUS_DRAFT);
                $model->ringkasan = $this->postedRingkasan();

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
                if ($model->uploadFile === null) {
                    $model->addError('uploadFile', 'Silakan pilih foto terlebih dahulu.');
                } else {
                    $model->file_path = 'uploads/lampiran/' . Yii::$app->security->generateRandomString(24)
                        . '.' . strtolower($model->uploadFile->extension);
                }
            }

            if (!$model->hasErrors() && $model->validate()) {
                $directory = Yii::getAlias('@webroot/' . ($isNotulen ? 'uploads/notulen' : 'uploads/lampiran'));
                FileHelper::createDirectory($directory, 0755);
                $filePath = Yii::getAlias('@webroot/' . $model->file_path);
                $upload = $isNotulen ? $file : $model->uploadFile;

                if ($upload !== null && $upload->saveAs($filePath) && $model->save(false)) {
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
                if ($isNotulen) {
                    $model->addError('file_path', 'Notulen gagal disimpan. Silakan coba lagi.');
                } else {
                    $model->addError('uploadFile', 'Foto gagal disimpan. Silakan coba lagi.');
                }
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
        $model = $this->findNotulenOrNull($agenda->agenda_id);

        if ($model === null) {
            // Bawa notulen=1 supaya yang terbuka form upload NOTULEN, bukan foto.
            return $this->redirect(['create', 'agenda_id' => $agenda->agenda_id, 'notulen' => 1]);
        }

        if (Yii::$app->request->isPost) {
            $model->ringkasan = $this->postedRingkasan();
            $model->status = $this->postedStatus((string) $model->status);
            $model->updated_by = (int) Yii::$app->user->id;
            $model->updated_at = date('Y-m-d H:i:s');
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
        // findAgenda() memastikan agenda yang sudah dihapus tidak bisa diunduh notulennya.
        $agenda = $this->findAgenda($agenda_id);
        $model = $this->findLampiran($agenda->agenda_id);
        $filePath = Yii::getAlias('@webroot/' . ltrim($model->file_path, '/'));

        if (!is_file($filePath)) {
            throw new NotFoundHttpException('File notulen tidak ditemukan di server.');
        }

        return Yii::$app->response->sendFile($filePath, $model->original_name ?: basename($filePath));
    }

    /**
     * Hapus foto dokumentasi (dipanggil dari halaman detail agenda).
     */
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

    /**
     * Hapus berkas notulen milik satu agenda (dipanggil dari halaman "Lihat Notulen").
     * Record di-soft-delete, file fisiknya ikut dihapus dari server.
     */
    public function actionDeleteNotulen($agenda_id)
    {
        $agenda = $this->findAgenda($agenda_id);
        $model = $this->findLampiran($agenda->agenda_id);

        $filePath = Yii::getAlias('@webroot/' . ltrim($model->file_path, '/'));
        $model->deleted_at = date('Y-m-d H:i:s');
        $model->updated_by = (int) Yii::$app->user->id;
        $model->updated_at = date('Y-m-d H:i:s');
        $model->save(false);

        if (is_file($filePath)) {
            @unlink($filePath);
        }

        Yii::$app->session->setFlash('success', 'Berkas notulen berhasil dihapus.');
        return $this->redirect(['/notulis/index']);
    }

    private function findAgenda($id): Agenda
    {
        $agenda = Agenda::findOne(['agenda_id' => $id, 'deleted_at' => null]);
        if ($agenda === null) {
            throw new NotFoundHttpException('Agenda yang diminta tidak ditemukan.');
        }
        return $agenda;
    }

    /**
     * Notulen aktif terbaru milik agenda, atau null kalau belum ada.
     * Hanya jenis 'notulen': foto dokumentasi tidak ikut terhitung.
     */
    private function findNotulenOrNull($agendaId): ?Lampiran
    {
        return Lampiran::find()
            ->andWhere([
                'agenda_id' => $agendaId,
                'jenis_lampiran' => self::JENIS_NOTULEN,
                'deleted_at' => null,
            ])
            ->orderBy(['lampiran_id' => SORT_DESC])
            ->one();
    }

    private function findLampiran($agendaId): Lampiran
    {
        $model = $this->findNotulenOrNull($agendaId);

        if ($model === null) {
            throw new NotFoundHttpException('Notulen untuk agenda ini belum tersedia.');
        }

        return $model;
    }

    /**
     * Status dari form (top-level field 'status'), hanya nilai yang valid.
     */
    private function postedStatus(string $fallback): string
    {
        $status = Yii::$app->request->post('status');

        return is_string($status) && array_key_exists($status, Lampiran::optsStatus())
            ? $status
            : $fallback;
    }

    /**
     * Ringkasan dari form (top-level field 'ringkasan'), string atau null.
     */
    private function postedRingkasan(): ?string
    {
        $ringkasan = Yii::$app->request->post('ringkasan');
        if (!is_string($ringkasan)) {
            return null;
        }

        $ringkasan = trim($ringkasan);

        return $ringkasan !== '' ? $ringkasan : null;
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