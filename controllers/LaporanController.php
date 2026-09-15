<?php

namespace app\controllers;

use app\models\Unit;
use Yii;
use yii\db\Query;
use yii\web\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanController extends Controller
{
    /**
     * Gunakan layout admin
     */
    public $layout = 'admin';

    /**
     * HALAMAN UTAMA LAPORAN
     */
    public function actionIndex()
    {
        $filter = $this->getFilterParams();
        $rekap = $this->buildRekap($filter);

        return $this->render('index', array_merge($filter, [
            'units' => Unit::find()
                ->orderBy('nama_unit')
                ->all(),

            'rekap' => $rekap['data'],
            'totalAgenda' => $rekap['totalAgenda'],
            'rataRataKehadiran' => $rekap['rataRataKehadiran'],
            'totalNotulen' => $rekap['totalNotulen'],
        ]));
    }


    /**
     * =========================================================
     * EXPORT PDF
     * =========================================================
     */
    public function actionExportPdf()
    {
        $filter = $this->getFilterParams();
        $rekap = $this->buildRekap($filter);

        /*
         * LOGO
         */
        $logoHtml = '';

        $logoPath = Yii::getAlias('@webroot/images/logo-unand.png');

        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(
                file_get_contents($logoPath)
            );

            $logoHtml = '
                <img
                    src="data:image/png;base64,' . $logoBase64 . '"
                    style="width:58px; height:auto;"
                >
            ';
        }

        $periode =
            $this->formatTanggal($filter['tanggalMulai'])
            . ' s/d '
            . $this->formatTanggal($filter['tanggalSelesai']);

        $tanggalCetak = date('d M Y H:i');

        /*
         * HTML PDF
         */
        $html = '
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<style>

@page {
    margin: 35px 42px 35px 42px;
}

body {
    font-family: DejaVu Sans, sans-serif;
    color: #27332e;
    font-size: 10px;
    margin: 0;
}

/* =========================================================
   HEADER
   ========================================================= */

.header {
    width: 100%;
    border-bottom: 1px solid #d6dcd8;
    padding-bottom: 12px;
    margin-bottom: 22px;
}

.header-table {
    width: 100%;
    border-collapse: collapse;
}

.logo {
    width: 70px;
    vertical-align: middle;
}

.logo img {
    width: 55px;
}

.identity {
    vertical-align: middle;
}

.identity .unand {
    font-size: 15px;
    font-weight: bold;
    color: #185c37;
}

.identity .system {
    font-size: 8px;
    color: #7c8580;
    margin-top: 3px;
}

.document-info {
    text-align: right;
    vertical-align: middle;
    font-size: 7px;
    color: #858d89;
}

.document-info strong {
    color: #37423d;
    font-size: 8px;
}


/* =========================================================
   JUDUL
   ========================================================= */

.title {
    text-align: center;
    margin-bottom: 20px;
}

.title h1 {
    font-size: 17px;
    color: #26332d;
    margin: 0;
}

.title p {
    color: #7d8581;
    font-size: 8px;
    margin: 5px 0 0;
}


/* =========================================================
   RINGKASAN
   ========================================================= */

.summary {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 22px;
}

.summary td {
    width: 33.33%;
    padding: 10px 12px;
    border: 1px solid #e0e5e2;
    background: #fbfcfb;
}

.summary-label {
    font-size: 7px;
    color: #7c8580;
    text-transform: uppercase;
}

.summary-value {
    margin-top: 4px;
    font-size: 14px;
    font-weight: bold;
    color: #185c37;
}


/* =========================================================
   SECTION
   ========================================================= */

.section-title {
    font-size: 11px;
    font-weight: bold;
    color: #26332d;
    margin-bottom: 7px;
}


/* =========================================================
   TABLE
   ========================================================= */

.report-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.report-table th {
    background: #f1f4f2;
    color: #43504a;
    border: 1px solid #d8dfdb;
    padding: 7px 5px;
    font-size: 7px;
    font-weight: bold;
    text-align: center;
}

.report-table td {
    border: 1px solid #e0e5e2;
    padding: 7px 5px;
    font-size: 8px;
    color: #3d4742;
}

.report-table tr:nth-child(even) td {
    background: #fafbfa;
}

.center {
    text-align: center;
}

.status-lengkap {
    color: #16804b;
    font-weight: bold;
}

.status-belum {
    color: #a56c00;
    font-weight: bold;
}


/* =========================================================
   FOOTER
   ========================================================= */

.footer {
    margin-top: 20px;
    padding-top: 8px;
    border-top: 1px solid #e2e6e4;
    text-align: center;
    color: #929995;
    font-size: 7px;
}

</style>

</head>

<body>


<!-- HEADER -->

<div class="header">

<table class="header-table">

<tr>

<td class="logo">
    ' . $logoHtml . '
</td>

<td class="identity">

    <div class="unand">
        UNIVERSITAS ANDALAS
    </div>

    <div class="system">
        Sistem Informasi Agenda Rapat
    </div>

</td>

<td class="document-info">

    <strong>LAPORAN RAPAT</strong><br>
    Dicetak: ' . htmlspecialchars($tanggalCetak) . '

</td>

</tr>

</table>

</div>


<!-- TITLE -->

<div class="title">

    <h1>
        Laporan &amp; Rekapitulasi Rapat
    </h1>

    <p>
        Periode ' . htmlspecialchars($periode) . '
    </p>

</div>


<!-- SUMMARY -->

<table class="summary">

<tr>

<td>

    <div class="summary-label">
        Total Agenda
    </div>

    <div class="summary-value">
        ' . $rekap['totalAgenda'] . ' Rapat
    </div>

</td>

<td>

    <div class="summary-label">
        Rata-rata Kehadiran
    </div>

    <div class="summary-value">
        ' . $rekap['rataRataKehadiran'] . '%
    </div>

</td>

<td>

    <div class="summary-label">
        Total Notulen
    </div>

    <div class="summary-value">
        ' . $rekap['totalNotulen'] . ' Berkas
    </div>

</td>

</tr>

</table>


<!-- TABLE -->

<div class="section-title">
    Rekapitulasi per Unit Kerja
</div>

<table class="report-table">

<thead>

<tr>

    <th style="width:5%;">
        NO
    </th>

    <th style="width:12%;">
        PERIODE
    </th>

    <th style="width:28%;">
        UNIT / FAKULTAS
    </th>

    <th style="width:13%;">
        JUMLAH AGENDA
    </th>

    <th style="width:14%;">
        PESERTA HADIR
    </th>

    <th style="width:14%;">
        TINGKAT KEHADIRAN
    </th>

    <th style="width:14%;">
        STATUS NOTULEN
    </th>

</tr>

</thead>

<tbody>
';

        if (empty($rekap['data'])) {

            $html .= '
<tr>

    <td colspan="7" class="center">
        Tidak ada data laporan pada periode yang dipilih.
    </td>

</tr>
';

        } else {

            $no = 1;

            foreach ($rekap['data'] as $row) {

                $statusClass =
                    $row['status_notulen'] === 'Lengkap'
                        ? 'status-lengkap'
                        : 'status-belum';

                $html .= '
<tr>

    <td class="center">
        ' . $no++ . '
    </td>

    <td class="center">
        ' . htmlspecialchars($row['periode']) . '
    </td>

    <td>
        ' . htmlspecialchars($row['unit']) . '
    </td>

    <td class="center">
        ' . $row['jumlah_agenda'] . '
    </td>

    <td class="center">
        ' . $row['peserta_hadir'] . '
    </td>

    <td class="center">
        ' . $row['tingkat_kehadiran'] . '%
    </td>

    <td class="center ' . $statusClass . '">
        ' . htmlspecialchars($row['status_notulen']) . '
    </td>

</tr>
';
            }
        }

        $html .= '

</tbody>

</table>


<div class="footer">

    Sistem Informasi Agenda Rapat Universitas Andalas
    &nbsp; • &nbsp;
    Laporan dibuat secara otomatis

</div>


</body>
</html>
';


        /*
         * DOMPDF
         */

        $options = new Options();

        $options->set(
            'defaultFont',
            'DejaVu Sans'
        );

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper(
            'A4',
            'landscape'
        );

        $dompdf->render();


        return Yii::$app->response->sendContentAsFile(
            $dompdf->output(),
            'laporan-rekapitulasi-' . date('Y-m-d') . '.pdf',
            [
                'mimeType' => 'application/pdf',
                'inline' => false,
            ]
        );
    }


    /**
     * =========================================================
     * EXPORT EXCEL
     * =========================================================
     */
    public function actionExportExcel()
    {
        $filter = $this->getFilterParams();

        $rekap = $this->buildRekap($filter);

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Rekap Rapat');


        /*
         * =====================================================
         * WARNA
         * =====================================================
         */

        $darkText = '27332E';
        $green = '185C37';
        $lightGreen = 'EAF1ED';
        $softGray = 'F3F5F4';
        $borderColor = 'D9DFDC';
        $muted = '707A75';


        /*
         * =====================================================
         * LOGO
         * =====================================================
         */

        $logoPath =
            Yii::getAlias(
                '@webroot/images/logo-unand.png'
            );

        if (file_exists($logoPath)) {

            $drawing = new Drawing();

            $drawing->setName(
                'Logo Universitas Andalas'
            );

            $drawing->setDescription(
                'Logo Universitas Andalas'
            );

            $drawing->setPath(
                $logoPath
            );

            /*
             * Logo dibuat kecil
             */
            $drawing->setHeight(48);

            $drawing->setCoordinates(
                'A1'
            );

            $drawing->setOffsetX(8);

            $drawing->setOffsetY(4);

            $drawing->setWorksheet(
                $sheet
            );
        }


        /*
         * =====================================================
         * JUDUL
         * =====================================================
         */

        $sheet->mergeCells(
            'B1:G1'
        );

        $sheet->setCellValue(
            'B1',
            'LAPORAN & REKAPITULASI RAPAT'
        );

        $sheet->mergeCells(
            'B2:G2'
        );

        $sheet->setCellValue(
            'B2',
            'Universitas Andalas'
        );

        $sheet->mergeCells(
            'B3:G3'
        );

        $sheet->setCellValue(
            'B3',
            'Sistem Informasi Agenda Rapat'
        );

        $sheet->mergeCells(
            'B4:G4'
        );

        $sheet->setCellValue(
            'B4',
            'Periode: '
            . $this->formatTanggal(
                $filter['tanggalMulai']
            )
            . ' s/d '
            . $this->formatTanggal(
                $filter['tanggalSelesai']
            )
        );


        /*
         * =====================================================
         * STYLE JUDUL
         * =====================================================
         */

        $sheet->getStyle(
            'B1:G1'
        )->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => [
                    'rgb' => $darkText,
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_LEFT,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

        ]);


        $sheet->getStyle(
            'B2:G2'
        )->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => [
                    'rgb' => $green,
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_LEFT,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

        ]);


        $sheet->getStyle(
            'B3:G3'
        )->applyFromArray([

            'font' => [
                'size' => 9,
                'color' => [
                    'rgb' => $muted,
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_LEFT,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

        ]);


        $sheet->getStyle(
            'B4:G4'
        )->applyFromArray([

            'font' => [
                'size' => 9,
                'color' => [
                    'rgb' => $muted,
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_LEFT,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

        ]);


        $sheet->getRowDimension(1)
            ->setRowHeight(27);

        $sheet->getRowDimension(2)
            ->setRowHeight(20);

        $sheet->getRowDimension(3)
            ->setRowHeight(18);

        $sheet->getRowDimension(4)
            ->setRowHeight(20);


        /*
         * =====================================================
         * GARIS PEMBATAS
         * =====================================================
         */

        $sheet->getStyle(
            'A4:G4'
        )->getBorders()
            ->getBottom()
            ->setBorderStyle(
                Border::BORDER_THIN
            );

        $sheet->getStyle(
            'A4:G4'
        )->getBorders()
            ->getBottom()
            ->getColor()
            ->setRGB(
                'D3AA19'
            );


        /*
         * =====================================================
         * SUMMARY
         * =====================================================
         */

        $sheet->mergeCells(
            'A6:B6'
        );

        $sheet->mergeCells(
            'C6:D6'
        );

        $sheet->mergeCells(
            'E6:G6'
        );

        $sheet->mergeCells(
            'A7:B7'
        );

        $sheet->mergeCells(
            'C7:D7'
        );

        $sheet->mergeCells(
            'E7:G7'
        );


        $sheet->setCellValue(
            'A6',
            'Total Agenda'
        );

        $sheet->setCellValue(
            'C6',
            'Rata-rata Kehadiran'
        );

        $sheet->setCellValue(
            'E6',
            'Total Notulen'
        );


        $sheet->setCellValue(
            'A7',
            $rekap['totalAgenda'] . ' Rapat'
        );

        $sheet->setCellValue(
            'C7',
            $rekap['rataRataKehadiran'] . '%'
        );

        $sheet->setCellValue(
            'E7',
            $rekap['totalNotulen'] . ' Berkas'
        );


        /*
         * Label summary
         */

        $sheet->getStyle(
            'A6:G6'
        )->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 9,
                'color' => [
                    'rgb' => $muted,
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

        ]);


        /*
         * Nilai summary
         */

        $sheet->getStyle(
            'A7:G7'
        )->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 13,
                'color' => [
                    'rgb' => $green,
                ],
            ],

            'fill' => [
                'fillType' =>
                    Fill::FILL_SOLID,

                'startColor' => [
                    'rgb' => 'FAFBFA',
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

            'borders' => [
                'outline' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' =>
                            $borderColor,
                    ],
                ],
            ],

        ]);


        $sheet->getRowDimension(6)
            ->setRowHeight(19);

        $sheet->getRowDimension(7)
            ->setRowHeight(25);


        /*
         * =====================================================
         * JUDUL TABEL
         * =====================================================
         */

        $sheet->mergeCells(
            'A9:G9'
        );

        $sheet->setCellValue(
            'A9',
            'Rekapitulasi per Unit Kerja'
        );

        $sheet->getStyle(
            'A9:G9'
        )->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => [
                    'rgb' => $darkText,
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_LEFT,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

        ]);

        $sheet->getRowDimension(9)
            ->setRowHeight(22);


        /*
         * =====================================================
         * HEADER TABEL
         * =====================================================
         */

        $headerRow = 10;

        $headers = [

            'NO',

            'PERIODE',

            'UNIT / FAKULTAS',

            'JUMLAH AGENDA',

            'PESERTA HADIR',

            'TINGKAT KEHADIRAN',

            'STATUS NOTULEN',

        ];


        $sheet->fromArray(
            $headers,
            null,
            'A' . $headerRow
        );


        $sheet->getStyle(
            'A10:G10'
        )->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 9,
                'color' => [
                    'rgb' => $darkText,
                ],
            ],

            'fill' => [
                'fillType' =>
                    Fill::FILL_SOLID,

                'startColor' => [
                    'rgb' => $softGray,
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,

                'wrapText' => true,
            ],

            'borders' => [
                'top' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' =>
                            $borderColor,
                    ],
                ],

                'bottom' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' =>
                            $borderColor,
                    ],
                ],

                'left' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' =>
                            $borderColor,
                    ],
                ],

                'right' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' =>
                            $borderColor,
                    ],
                ],
            ],

        ]);


        $sheet->getRowDimension(10)
            ->setRowHeight(30);


        /*
         * =====================================================
         * DATA
         * =====================================================
         */

        $currentRow = 11;

        $no = 1;


        foreach ($rekap['data'] as $row) {

            $sheet->setCellValue(
                'A' . $currentRow,
                $no++
            );

            $sheet->setCellValue(
                'B' . $currentRow,
                $row['periode']
            );

            $sheet->setCellValue(
                'C' . $currentRow,
                $row['unit']
            );

            $sheet->setCellValue(
                'D' . $currentRow,
                $row['jumlah_agenda']
            );

            $sheet->setCellValue(
                'E' . $currentRow,
                $row['peserta_hadir']
            );

            $sheet->setCellValue(
                'F' . $currentRow,
                $row['tingkat_kehadiran'] / 100
            );

            $sheet->getStyle(
                'F' . $currentRow
            )->getNumberFormat()
                ->setFormatCode(
                    '0%'
                );

            $sheet->setCellValue(
                'G' . $currentRow,
                $row['status_notulen']
            );


            /*
             * Border
             */

            $sheet->getStyle(
                'A' . $currentRow . ':G' . $currentRow
            )->applyFromArray([

                'borders' => [

                    'top' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' =>
                                $borderColor,
                        ],
                    ],

                    'bottom' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' =>
                                $borderColor,
                        ],
                    ],

                    'left' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' =>
                                $borderColor,
                        ],
                    ],

                    'right' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' =>
                                $borderColor,
                        ],
                    ],

                ],

                'alignment' => [

                    'vertical' =>
                        Alignment::VERTICAL_CENTER,

                ],

            ]);


            /*
             * Kolom tengah
             */

            $sheet->getStyle(
                'A' . $currentRow . ':B' . $currentRow
            )->getAlignment()
                ->setHorizontal(
                    Alignment::HORIZONTAL_CENTER
                );

            $sheet->getStyle(
                'D' . $currentRow . ':G' . $currentRow
            )->getAlignment()
                ->setHorizontal(
                    Alignment::HORIZONTAL_CENTER
                );


            /*
             * Baris selang-seling
             */

            if ($currentRow % 2 === 0) {

                $sheet->getStyle(
                    'A' . $currentRow . ':G' . $currentRow
                )->getFill()
                    ->setFillType(
                        Fill::FILL_SOLID
                    );

                $sheet->getStyle(
                    'A' . $currentRow . ':G' . $currentRow
                )->getFill()
                    ->getStartColor()
                    ->setRGB(
                        'FAFBFA'
                    );
            }


            /*
             * Status
             */

            if (
                $row['status_notulen'] === 'Lengkap'
            ) {

                $sheet->getStyle(
                    'G' . $currentRow
                )->getFont()
                    ->setBold(true)
                    ->getColor()
                    ->setRGB(
                        '16804B'
                    );

            } else {

                $sheet->getStyle(
                    'G' . $currentRow
                )->getFont()
                    ->setBold(true)
                    ->getColor()
                    ->setRGB(
                        'A66A00'
                    );
            }


            /*
             * Tingkat kehadiran
             */

            $sheet->getStyle(
                'F' . $currentRow
            )->getFont()
                ->setBold(true)
                ->getColor()
                ->setRGB(
                    $green
                );


            $sheet->getRowDimension(
                $currentRow
            )->setRowHeight(23);


            $currentRow++;
        }


        /*
         * =====================================================
         * JIKA DATA KOSONG
         * =====================================================
         */

        if (empty($rekap['data'])) {

            $sheet->mergeCells(
                'A11:G11'
            );

            $sheet->setCellValue(
                'A11',
                'Tidak ada data laporan pada periode yang dipilih.'
            );

            $sheet->getStyle(
                'A11:G11'
            )->applyFromArray([

                'font' => [
                    'italic' => true,
                    'size' => 9,
                    'color' => [
                        'rgb' => $muted,
                    ],
                ],

                'alignment' => [
                    'horizontal' =>
                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                ],

            ]);

            $currentRow = 12;
        }


        /*
         * =====================================================
         * LEBAR KOLOM
         * =====================================================
         */

        $sheet->getColumnDimension('A')
            ->setWidth(7);

        $sheet->getColumnDimension('B')
            ->setWidth(15);

        $sheet->getColumnDimension('C')
            ->setWidth(36);

        $sheet->getColumnDimension('D')
            ->setWidth(18);

        $sheet->getColumnDimension('E')
            ->setWidth(17);

        $sheet->getColumnDimension('F')
            ->setWidth(20);

        $sheet->getColumnDimension('G')
            ->setWidth(21);


        /*
         * =====================================================
         * FREEZE + FILTER
         * =====================================================
         */

        if (!empty($rekap['data'])) {

            $lastDataRow =
                $currentRow - 1;

            $sheet->setAutoFilter(
                'A10:G' . $lastDataRow
            );
        }

        $sheet->freezePane(
            'A11'
        );


        /*
         * =====================================================
         * PRINT
         * =====================================================
         */

        $sheet->getPageSetup()
            ->setOrientation(
                PageSetup::ORIENTATION_LANDSCAPE
            );

        $sheet->getPageSetup()
            ->setPaperSize(
                PageSetup::PAPERSIZE_A4
            );

        $sheet->getPageSetup()
            ->setFitToWidth(1);

        $sheet->getPageSetup()
            ->setFitToHeight(0);


        $sheet->getPageMargins()
            ->setTop(0.45);

        $sheet->getPageMargins()
            ->setRight(0.35);

        $sheet->getPageMargins()
            ->setBottom(0.45);

        $sheet->getPageMargins()
            ->setLeft(0.35);


        /*
         * =====================================================
         * FOOTER
         * =====================================================
         */

        $sheet->getHeaderFooter()
            ->setOddFooter(
                '&C Sistem Informasi Agenda Rapat Universitas Andalas'
            );


        /*
         * =====================================================
         * SIMPAN XLSX
         * =====================================================
         */

        $writer = new Xlsx(
            $spreadsheet
        );

        $tempFile = tempnam(
            sys_get_temp_dir(),
            'laporan_rekap_'
        );

        $writer->save(
            $tempFile
        );

        $content =
            file_get_contents(
                $tempFile
            );

        @unlink(
            $tempFile
        );


        return Yii::$app->response->sendContentAsFile(
            $content,
            'laporan-rekapitulasi-' . date('Y-m-d') . '.xlsx',
            [
                'mimeType' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                'inline' => false,
            ]
        );
    }


    /**
     * =========================================================
     * FILTER
     * =========================================================
     */
    private function getFilterParams()
    {
        return [

            'tanggalMulai' =>
                Yii::$app->request->get(
                    'tanggal_mulai',
                    date('Y-m-01')
                ),

            'tanggalSelesai' =>
                Yii::$app->request->get(
                    'tanggal_selesai',
                    date('Y-m-t')
                ),

            'unitId' =>
                Yii::$app->request->get(
                    'unit_id',
                    ''
                ),

            'statusFilter' =>
                Yii::$app->request->get(
                    'status_notulen',
                    ''
                ),
        ];
    }


    /**
     * =========================================================
     * BUILD REKAP
     * =========================================================
     */
    private function buildRekap(array $filter)
    {
        $units = Unit::find()
            ->orderBy('nama_unit')
            ->all();

        $data = [];

        $totalAgenda = 0;
        $totalHadir = 0;
        $totalInvited = 0;
        $totalNotulen = 0;


        $periodeLabel = date(
            'M Y',
            strtotime(
                $filter['tanggalMulai']
            )
        );


        foreach ($units as $unit) {

            if (
                $filter['unitId'] !== '' &&
                (int) $filter['unitId']
                    !== $unit->unit_id
            ) {
                continue;
            }


            /*
             * Agenda
             */

            $agendaIds = (new Query())
                ->select(
                    'a.agenda_id'
                )
                ->from(
                    ['a' => 'agenda']
                )
                ->innerJoin(
                    ['l' => 'lokasi'],
                    'l.lokasi_id = a.lokasi_id'
                )
                ->where([
                    'l.unit_id' =>
                        $unit->unit_id
                ])
                ->andWhere([
                    'between',
                    'a.tanggal',
                    $filter['tanggalMulai'],
                    $filter['tanggalSelesai']
                ])
                ->column();


            $jumlahAgenda =
                count($agendaIds);


            if (
                $jumlahAgenda === 0
            ) {
                continue;
            }


            /*
             * Peserta diundang
             */

            $invited = (int) (new Query())
                ->from(
                    'agenda_member'
                )
                ->where([
                    'agenda_id' =>
                        $agendaIds
                ])
                ->count();


            /*
             * Peserta hadir
             */

            $hadir = (int) (new Query())
                ->from(
                    'absensi'
                )
                ->where([
                    'agenda_id' =>
                        $agendaIds
                ])
                ->count();


            /*
             * Notulen
             */

            $agendaBerNotulen =
                (int) (new Query())
                    ->select(
                        'agenda_id'
                    )
                    ->distinct()
                    ->from(
                        'lampiran'
                    )
                    ->where([
                        'agenda_id' =>
                            $agendaIds
                    ])
                    ->count();


            /*
             * Kehadiran
             */

            $tingkatKehadiran =
                $invited > 0
                    ? round(
                        ($hadir / $invited)
                        * 100
                    )
                    : 0;


            /*
             * Status notulen
             */

            $statusNotulen =
                $agendaBerNotulen
                    >= $jumlahAgenda

                    ? 'Lengkap'

                    : $agendaBerNotulen
                        . '/'
                        . $jumlahAgenda
                        . ' Berkas';


            /*
             * Filter status
             */

            if (
                $filter['statusFilter']
                    === 'lengkap' &&
                $statusNotulen
                    !== 'Lengkap'
            ) {
                continue;
            }


            if (
                $filter['statusFilter']
                    === 'belum' &&
                $statusNotulen
                    === 'Lengkap'
            ) {
                continue;
            }


            /*
             * Simpan data
             */

            $data[] = [

                'periode' =>
                    $periodeLabel,

                'unit' =>
                    $unit->nama_unit,

                'jumlah_agenda' =>
                    $jumlahAgenda,

                'peserta_hadir' =>
                    $hadir,

                'tingkat_kehadiran' =>
                    $tingkatKehadiran,

                'status_notulen' =>
                    $statusNotulen,
            ];


            /*
             * Total
             */

            $totalAgenda +=
                $jumlahAgenda;

            $totalHadir +=
                $hadir;

            $totalInvited +=
                $invited;

            $totalNotulen +=
                $agendaBerNotulen;
        }


        return [

            'data' =>
                $data,

            'totalAgenda' =>
                $totalAgenda,

            'rataRataKehadiran' =>
                $totalInvited > 0

                    ? round(
                        ($totalHadir / $totalInvited)
                        * 100
                    )

                    : 0,

            'totalNotulen' =>
                $totalNotulen,
        ];
    }


    /**
     * =========================================================
     * FORMAT TANGGAL
     * =========================================================
     */
    private function formatTanggal($tanggal)
    {
        if (!$tanggal) {
            return '-';
        }

        return date(
            'd M Y',
            strtotime($tanggal)
        );
    }
}