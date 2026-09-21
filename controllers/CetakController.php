<?php

namespace app\controllers;

use app\models\Agenda;
use Dompdf\Dompdf;
use Dompdf\Options;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CetakController menampilkan halaman cetak dokumen agenda dan QR Code.
 */
class CetakController extends Controller
{
    public $layout = 'admin';

    /**
     * Sebelumnya controller ini tidak punya filter akses sama sekali, sehingga
     * siapa pun -- termasuk tamu yang belum login -- bisa membuka
     * /cetak/dokumen?agenda_id=... dan membaca isi rapat beserta token QR
     * presensinya. Token itu cukup untuk memalsukan kehadiran, jadi halaman
     * cetak harus menuntut izin yang sama dengan halaman detail agenda.
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => static function () {
                            /** @var \app\models\User|null $identity */
                            $identity = Yii::$app->user->identity;

                            return $identity !== null
                                && ($identity->can('manageAgenda') || $identity->can('viewAgenda'));
                        },
                    ],
                ],
            ],
        ]);
    }

    /**
     * Menampilkan halaman cetak dokumen agenda beserta QR Code presensi.
     *
     * @param int $agenda_id
     * @return string
     * @throws NotFoundHttpException jika agenda tidak ditemukan
     */
    public function actionDokumen($agenda_id)
    {
        // findAgenda() ikut menyaring deleted_at, jadi agenda yang sudah dihapus
        // tidak bisa dicetak ulang lewat URL.
        $model = $this->findAgenda($agenda_id);

        return $this->render('dokumen', [
            'model' => $model,
            'forPdf' => false,
        ]);
    }

    /**
     * Unduh dokumen agenda sebagai PDF yang dirender di server.
     *
     * Cetak lewat browser selalu berisiko menempelkan URL, judul tab, dan
     * tanggal di tepi kertas karena itu pengaturan milik pengguna, bukan
     * milik halaman. Jalur ini menghasilkan PDF yang bersih apa pun setelan
     * browser-nya.
     */
    public function actionDownloadDokumen($agenda_id)
    {
        $model = $this->findAgenda($agenda_id);
        $html = $this->renderPartial('dokumen', ['model' => $model, 'forPdf' => true]);

        $options = new Options();
        // isRemoteEnabled dibiarkan mati: seluruh aset dokumen (QR) sudah
        // ditanam sebagai data URI, jadi renderer tidak perlu -- dan tidak
        // boleh -- menarik apa pun dari jaringan.
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return Yii::$app->response->sendContentAsFile(
            $dompdf->output(),
            'dokumen-agenda-' . $model->agenda_id . '.pdf',
            ['mimeType' => 'application/pdf']
        );
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