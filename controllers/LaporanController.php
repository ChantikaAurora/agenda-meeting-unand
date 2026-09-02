<?php

namespace app\controllers;

use app\models\Unit;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * LaporanController menampilkan rekapitulasi pelaksanaan rapat per unit kerja.
 */
class LaporanController extends Controller
{
    public function actionIndex()
    {
        $filter = $this->getFilterParams();
        $rekap = $this->buildRekap($filter);

        return $this->render('index', array_merge($filter, [
            'units' => Unit::find()->orderBy('nama_unit')->all(),
            'rekap' => $rekap['data'],
            'totalAgenda' => $rekap['totalAgenda'],
            'rataRataKehadiran' => $rekap['rataRataKehadiran'],
            'totalNotulen' => $rekap['totalNotulen'],
        ]));
    }

    public function actionExportPdf()
    {
        $filter = $this->getFilterParams();
        $rekap = $this->buildRekap($filter);

        $html = '<h2 style="text-align:center;">Laporan & Rekapitulasi Rapat</h2>';
        $html .= '<p style="text-align:center; color:#666;">Periode: ' . $filter['tanggalMulai'] . ' s/d ' . $filter['tanggalSelesai'] . '</p>';
        $html .= '<table width="100%" cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse; font-size:12px;">';
        $html .= '<thead><tr style="background:#f0f0f0;">
                    <th>Periode</th>
                    <th>Unit/Fakultas</th>
                    <th>Jumlah Agenda</th>
                    <th>Peserta Hadir</th>
                    <th>Tingkat Kehadiran</th>
                    <th>Status Notulen</th>
                  </tr></thead><tbody>';

        foreach ($rekap['data'] as $row) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($row['periode']) . '</td>
                        <td>' . htmlspecialchars($row['unit']) . '</td>
                        <td>' . $row['jumlah_agenda'] . '</td>
                        <td>' . $row['peserta_hadir'] . '</td>
                        <td>' . $row['tingkat_kehadiran'] . '%</td>
                        <td>' . htmlspecialchars($row['status_notulen']) . '</td>
                      </tr>';
        }

        $html .= '</tbody></table>';

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return Yii::$app->response->sendContentAsFile(
            $dompdf->output(),
            'laporan-rekapitulasi-' . date('Y-m-d') . '.pdf',
            ['mimeType' => 'application/pdf', 'inline' => false]
        );
    }

    public function actionExportExcel()
    {
        $filter = $this->getFilterParams();
        $rekap = $this->buildRekap($filter);

        $csv = "Periode,Unit/Fakultas,Jumlah Agenda,Peserta Hadir,Tingkat Kehadiran,Status Notulen\n";
        foreach ($rekap['data'] as $row) {
            $csv .= implode(',', [
                '"' . $row['periode'] . '"',
                '"' . str_replace('"', '""', $row['unit']) . '"',
                $row['jumlah_agenda'],
                $row['peserta_hadir'],
                $row['tingkat_kehadiran'] . '%',
                '"' . $row['status_notulen'] . '"',
            ]) . "\n";
        }

        return Yii::$app->response->sendContentAsFile(
            $csv,
            'laporan-rekapitulasi-' . date('Y-m-d') . '.csv',
            ['mimeType' => 'text/csv']
        );
    }

    private function getFilterParams()
    {
        return [
            'tanggalMulai' => Yii::$app->request->get('tanggal_mulai', date('Y-m-01')),
            'tanggalSelesai' => Yii::$app->request->get('tanggal_selesai', date('Y-m-t')),
            'unitId' => Yii::$app->request->get('unit_id', ''),
            'statusFilter' => Yii::$app->request->get('status_notulen', ''),
        ];
    }

    private function buildRekap(array $filter)
    {
        $units = Unit::find()->orderBy('nama_unit')->all();
        $data = [];
        $totalAgenda = 0;
        $totalHadir = 0;
        $totalInvited = 0;
        $totalNotulen = 0;
        $periodeLabel = date('M Y', strtotime($filter['tanggalMulai']));

        foreach ($units as $unit) {
            if ($filter['unitId'] !== '' && (int) $filter['unitId'] !== $unit->unit_id) {
                continue;
            }

            $agendaIds = (new Query())
                ->select('a.agenda_id')
                ->from(['a' => 'agenda'])
                ->innerJoin(['l' => 'lokasi'], 'l.lokasi_id = a.lokasi_id')
                ->where(['l.unit_id' => $unit->unit_id])
                ->andWhere(['between', 'a.tanggal', $filter['tanggalMulai'], $filter['tanggalSelesai']])
                ->column();

            $jumlahAgenda = count($agendaIds);
            if ($jumlahAgenda === 0) {
                continue;
            }

            $invited = (int) (new Query())->from('agenda_member')->where(['agenda_id' => $agendaIds])->count();
            $hadir = (int) (new Query())->from('absensi')->where(['agenda_id' => $agendaIds])->count();
            $agendaBerNotulen = (int) (new Query())
                ->select('agenda_id')
                ->distinct()
                ->from('lampiran')
                ->where(['agenda_id' => $agendaIds])
                ->count();

            $tingkatKehadiran = $invited > 0 ? round(($hadir / $invited) * 100) : 0;
            $statusNotulen = $agendaBerNotulen >= $jumlahAgenda
                ? 'Lengkap'
                : $agendaBerNotulen . '/' . $jumlahAgenda . ' Berkas';

            if ($filter['statusFilter'] === 'lengkap' && $statusNotulen !== 'Lengkap') {
                continue;
            }
            if ($filter['statusFilter'] === 'belum' && $statusNotulen === 'Lengkap') {
                continue;
            }

            $data[] = [
                'periode' => $periodeLabel,
                'unit' => $unit->nama_unit,
                'jumlah_agenda' => $jumlahAgenda,
                'peserta_hadir' => $hadir,
                'tingkat_kehadiran' => $tingkatKehadiran,
                'status_notulen' => $statusNotulen,
            ];

            $totalAgenda += $jumlahAgenda;
            $totalHadir += $hadir;
            $totalInvited += $invited;
            $totalNotulen += $agendaBerNotulen;
        }

        return [
            'data' => $data,
            'totalAgenda' => $totalAgenda,
            'rataRataKehadiran' => $totalInvited > 0 ? round(($totalHadir / $totalInvited) * 100) : 0,
            'totalNotulen' => $totalNotulen,
        ];
    }
}