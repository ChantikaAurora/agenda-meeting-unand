<?php

namespace app\controllers;

use app\models\Agenda;
use Dompdf\Dompdf;
use Dompdf\Options;
use Yii;
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

    public function actionUndangan($agenda_id)
    {
        return $this->renderInvitation($agenda_id, false);
    }

    public function actionDownloadUndangan($agenda_id)
    {
        $model = $this->findAgenda($agenda_id);
        $html = $this->renderPartial('undangan', ['model' => $model, 'forPdf' => true]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return Yii::$app->response->sendContentAsFile(
            $dompdf->output(),
            'undangan-rapat-' . $model->agenda_id . '.pdf',
            ['mimeType' => 'application/pdf']
        );
    }

    private function renderInvitation($agendaId, bool $forPdf)
    {
        return $this->render('undangan', [
            'model' => $this->findAgenda($agendaId),
            'forPdf' => $forPdf,
        ]);
    }

    private function findAgenda($agendaId): Agenda
    {
        $model = Agenda::findOne(['agenda_id' => $agendaId, 'deleted_at' => null]);
        if ($model === null) {
            throw new NotFoundHttpException('Agenda yang diminta tidak ditemukan.');
        }
        return $model;
    }
}
