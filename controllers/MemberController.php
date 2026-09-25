<?php

namespace app\controllers;

use Yii;
use app\models\Agenda;
use app\models\Member;
use app\models\DaftarHadirQuery;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use Dompdf\Dompdf;
use Dompdf\Options;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;


/**
 * MemberController implements the CRUD actions for Member model.
 */
class MemberController extends Controller
{
    /**
     * Gunakan layout admin.
     */
    public $layout = 'admin';


    /**
     * Access Control dan HTTP Verb.
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,

                'rules' => [
                    [
                        'allow' => true,

                        'actions' => [
                            'index',
                            'view',
                            'daftar-hadir',
                            'export-pdf',
                            'export-csv',
                        ],

                        'matchCallback' => function () {
                            /** @var \app\models\User $identity */
                            $identity = Yii::$app->user->identity;

                            return !Yii::$app->user->isGuest
                                && $identity !== null
                                && $identity->can('manageMember');
                        },
                    ],

                    [
                        'allow' => true,

                        'actions' => [
                            'create',
                            'update',
                            'delete',
                        ],

                        'matchCallback' => function () {
                            /** @var \app\models\User $identity */
                            $identity = Yii::$app->user->identity;

                            return !Yii::$app->user->isGuest
                                && $identity !== null
                                && $identity->can('manageMember');
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
        ]);
    }


    /**
     * Lists all Member models.
     *
     * @return string
     */
    public function actionIndex()
    {
        /*
         * ============================
         * DATA MEMBER
         * ============================
         */
        $dataProvider = new ActiveDataProvider([
            'query' => Member::find()
                ->where([
                    'deleted_at' => null
                ]),
        ]);


        /*
         * ============================
         * DATA DAFTAR HADIR
         * ============================
         */

        $agendaList = Agenda::find()
            ->orderBy([
                'tanggal' => SORT_DESC
            ])
            ->all();


        /*
         * ============================
         * FILTER
         * ============================
         */
        $agendaId = trim(
            (string) Yii::$app->request->get(
                'agenda_id',
                ''
            )
        );

        $status = trim(
            (string) Yii::$app->request->get(
                'status',
                ''
            )
        );

        $q = trim(
            (string) Yii::$app->request->get(
                'q',
                ''
            )
        );


        /*
         * ============================
         * QUERY PESERTA + ABSENSI
         * ============================
         */
        $query = (new Query())
            ->select([
                'am.agenda_id',
                'am.member_id',

                'm.nama',
                'm.identitas_number',
                'm.instansi',

                'ag.pembahasan',

                'ab.absensi_id',
                'ab.waktu_scan',
                'ab.tanda_tangan_path',
            ])

            ->from([
                'am' => 'agenda_member'
            ])

            ->innerJoin(
                ['m' => 'member'],
                'm.member_id = am.member_id'
            )

            ->innerJoin(
                ['ag' => 'agenda'],
                'ag.agenda_id = am.agenda_id'
            )

            ->leftJoin(
                ['ab' => 'absensi'],
                'ab.agenda_id = am.agenda_id
                 AND ab.member_id = am.member_id
                 AND ab.deleted_at IS NULL'
            )

            ->where([
                'am.deleted_at' => null
            ]);


        /*
         * ============================
         * FILTER AGENDA
         * ============================
         */
        if ($agendaId !== '') {
            $query->andWhere([
                'am.agenda_id' => $agendaId
            ]);
        }


        /*
         * ============================
         * SEARCH
         * ============================
         */
        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'm.nama', $q],
                ['like', 'm.identitas_number', $q],
            ]);
        }


        /*
         * ============================
         * FILTER STATUS
         * ============================
         */
        if ($status === 'hadir') {
            $query->andWhere([
                'is not',
                'ab.absensi_id',
                null
            ]);
        } elseif ($status === 'tidak_hadir') {
            $query->andWhere([
                'ab.absensi_id' => null
            ]);
        }


        /*
         * ============================
         * AMBIL DATA
         * ============================
         */
        $hadirRows = $query
            ->orderBy([
                'ag.tanggal' => SORT_DESC,
                'm.nama' => SORT_ASC
            ])
            ->all();


        /*
         * ============================
         * STATISTIK
         * ============================
         */
        $totalPeserta = count(
            $hadirRows
        );

        $totalHadir = count(
            array_filter(
                $hadirRows,
                fn ($row) =>
                    $row['absensi_id'] !== null
            )
        );

        $totalTidakHadir =
            $totalPeserta - $totalHadir;


        /*
         * ============================
         * DATA PROVIDER
         * ============================
         */
        $hadirDataProvider = new ArrayDataProvider([
            'allModels' => $hadirRows,

            'pagination' => [
                'pageSize' => 10,
            ],
        ]);


        /*
         * ============================
         * RENDER
         * ============================
         */
        return $this->render('index', [
            'dataProvider' => $dataProvider,

            'hadirDataProvider' =>
                $hadirDataProvider,

            'agendaList' =>
                $agendaList,

            'agendaId' =>
                $agendaId,

            'status' =>
                $status,

            'q' =>
                $q,

            'totalPeserta' =>
                $totalPeserta,

            'totalHadir' =>
                $totalHadir,

            'totalTidakHadir' =>
                $totalTidakHadir,
        ]);
    }


    /**
     * Menampilkan halaman Daftar Hadir.
     *
     * @return string
     */
    public function actionDaftarHadir()
    {
        $agendaList = Agenda::find()
            ->orderBy([
                'tanggal' => SORT_DESC
            ])
            ->all();


        $agendaId = trim(
            (string) Yii::$app->request->get(
                'agenda_id',
                ''
            )
        );

        $status = trim(
            (string) Yii::$app->request->get(
                'status',
                ''
            )
        );

        $q = trim(
            (string) Yii::$app->request->get(
                'q',
                ''
            )
        );


        /*
         * Query tetap dipertahankan.
         */
        $query = (new Query())
            ->select([
                'am.agenda_id',
                'am.member_id',
                'm.nama',
                'm.identitas_number',
                'm.instansi',
                'ag.pembahasan',
                'ab.absensi_id',
                'ab.waktu_scan',
                'ab.tanda_tangan_path',
            ])

            ->from([
                'am' => 'agenda_member'
            ])

            ->innerJoin(
                ['m' => 'member'],
                'm.member_id = am.member_id'
            )

            ->innerJoin(
                ['ag' => 'agenda'],
                'ag.agenda_id = am.agenda_id'
            )

            ->leftJoin(
                ['ab' => 'absensi'],
                'ab.agenda_id = am.agenda_id
                 AND ab.member_id = am.member_id
                 AND ab.deleted_at IS NULL'
            )

            ->where([
                'am.deleted_at' => null
            ]);


        if ($agendaId !== '') {
            $query->andWhere([
                'am.agenda_id' => $agendaId
            ]);
        }


        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'm.nama', $q],
                ['like', 'm.identitas_number', $q],
            ]);
        }


        if ($status === 'hadir') {
            $query->andWhere([
                'is not',
                'ab.absensi_id',
                null
            ]);
        } elseif ($status === 'tidak_hadir') {
            $query->andWhere([
                'ab.absensi_id' => null
            ]);
        }


        /*
         * Gunakan query utama daftar hadir.
         * Query ini sudah menyediakan:
         * jabatan
         * tanda_tangan_path
         */
        $hadirRows = DaftarHadirQuery::fetch([
            'agenda_id' => $agendaId,
            'status' => $status,
            'q' => $q,
        ]);


        $totalPeserta =
            count($hadirRows);


        $totalHadir = count(
            array_filter(
                $hadirRows,
                fn ($row) =>
                    $row['absensi_id'] !== null
            )
        );


        $totalTidakHadir =
            $totalPeserta - $totalHadir;


        $hadirDataProvider =
            new ArrayDataProvider([
                'allModels' =>
                    $hadirRows,

                'pagination' => [
                    'pageSize' => 10
                ],
            ]);


        return $this->render(
            'daftar-hadir',
            [
                'hadirDataProvider' =>
                    $hadirDataProvider,

                'agendaList' =>
                    $agendaList,

                'agendaId' =>
                    $agendaId,

                'status' =>
                    $status,

                'q' =>
                    $q,

                'totalPeserta' =>
                    $totalPeserta,

                'totalHadir' =>
                    $totalHadir,

                'totalTidakHadir' =>
                    $totalTidakHadir,
            ]
        );
    }


    /**
     * Export daftar hadir ke PDF.
     *
     * Format:
     *
     * No | Nama Peserta | Nomor Identitas |
     * Unit/Bagian | Jabatan | TTD
     *
     * TTD digital ditampilkan sebagai gambar.
     *
     * @return Response
     */
    public function actionExportPdf()
    {
        /*
         * ============================
         * FILTER
         * ============================
         */
        $agendaId = trim(
            (string) Yii::$app->request->get(
                'agenda_id',
                ''
            )
        );

        $status = trim(
            (string) Yii::$app->request->get(
                'status',
                ''
            )
        );

        $q = trim(
            (string) Yii::$app->request->get(
                'q',
                ''
            )
        );


        /*
         * ============================
         * DATA
         * ============================
         */
        $rows = DaftarHadirQuery::fetch([
            'agenda_id' => $agendaId,
            'status' => $status,
            'q' => $q,
        ]);


        /*
         * ============================
         * AGENDA
         * ============================
         */
        $agendaLabel =
            'Semua Agenda';


        if ($agendaId !== '') {

            $agenda = Agenda::findOne(
                (int) $agendaId
            );

            if ($agenda) {
                $agendaLabel =
                    $agenda->pembahasan
                    ?: 'Agenda';
            }
        }


        /*
         * ============================
         * HEADER PDF
         * ============================
         */
        $html = '
            <h2
                style="
                    text-align:center;
                    margin-bottom:5px;
                "
            >
                DAFTAR HADIR PESERTA
            </h2>
        ';


        $html .= '
            <p
                style="
                    text-align:center;
                    margin-top:0;
                    margin-bottom:4px;
                "
            >
                <strong>Agenda:</strong>
                ' . Html::encode(
                    $agendaLabel
                ) . '
            </p>
        ';


        $html .= '
            <p
                style="
                    text-align:center;
                    margin-top:0;
                    color:#666;
                    font-size:10px;
                "
            >
                Dicetak pada:
                ' . date(
                    'd M Y H:i'
                ) . '
                WIB
            </p>
        ';


        /*
         * ============================
         * TABEL
         * ============================
         */
        $html .= '
            <table
                width="100%"
                cellpadding="6"
                cellspacing="0"
                border="1"
                style="
                    border-collapse:collapse;
                    font-size:10px;
                    width:100%;
                "
            >

                <thead>
                    <tr
                        style="
                            background:#f0f0f0;
                            text-align:center;
                        "
                    >

                        <th width="5%">
                            No
                        </th>

                        <th width="22%">
                            Nama Peserta
                        </th>

                        <th width="20%">
                            Nomor Identitas
                        </th>

                        <th width="17%">
                            Unit/Bagian
                        </th>

                        <th width="14%">
                            Jabatan
                        </th>

                        <th width="22%">
                            TTD
                        </th>

                    </tr>
                </thead>

                <tbody>
        ';


        /*
         * ============================
         * DATA KOSONG
         * ============================
         */
        if (empty($rows)) {

            $html .= '
                <tr>
                    <td
                        colspan="6"
                        style="
                            text-align:center;
                            padding:15px;
                        "
                    >
                        Tidak ada data peserta.
                    </td>
                </tr>
            ';

        } else {

            $no = 1;


            foreach ($rows as $row) {

                /*
                 * ============================
                 * TTD
                 * ============================
                 */
                $signatureHtml = '';

                $signatureFile =
                    $this->resolveSignatureFile(
                        $row['tanda_tangan_path']
                        ?? ''
                    );


                if (
                    $row['absensi_id'] !== null
                    && $signatureFile !== null
                ) {

                    $mimeType =
                        $this->getSignatureMimeType(
                            $signatureFile
                        );


                    if ($mimeType !== null) {

                        $signatureData =
                            @file_get_contents(
                                $signatureFile
                            );


                        if (
                            $signatureData !== false
                        ) {

                            $signatureBase64 =
                                base64_encode(
                                    $signatureData
                                );


                            $signatureHtml = '
                                <img
                                    src="data:'
                                    . $mimeType
                                    . ';base64,'
                                    . $signatureBase64
                                    . '"
                                    style="
                                        width:100px;
                                        height:45px;
                                    "
                                >
                            ';
                        }
                    }
                }


                /*
                 * Jika TTD tidak tersedia.
                 */
                if (
                    $signatureHtml === ''
                ) {

                    $signatureHtml = '
                        <div
                            style="
                                height:45px;
                                text-align:center;
                                padding-top:15px;
                            "
                        >
                            __________________
                        </div>
                    ';
                }


                /*
                 * ============================
                 * BARIS
                 * ============================
                 */
                $html .= '
                    <tr>

                        <td
                            style="
                                text-align:center;
                                vertical-align:middle;
                            "
                        >
                            ' . $no++ . '
                        </td>

                        <td
                            style="
                                vertical-align:middle;
                            "
                        >
                            ' . Html::encode(
                                $row['nama']
                                ?: '-'
                            ) . '
                        </td>

                        <td
                            style="
                                vertical-align:middle;
                            "
                        >
                            ' . Html::encode(
                                $row['identitas_number']
                                ?: '-'
                            ) . '
                        </td>

                        <td
                            style="
                                vertical-align:middle;
                            "
                        >
                            ' . Html::encode(
                                $row['instansi']
                                ?: '-'
                            ) . '
                        </td>

                        <td
                            style="
                                vertical-align:middle;
                            "
                        >
                            ' . Html::encode(
                                $row['jabatan']
                                ?: '-'
                            ) . '
                        </td>

                        <td
                            style="
                                height:55px;
                                text-align:center;
                                vertical-align:middle;
                            "
                        >
                            ' . $signatureHtml . '
                        </td>

                    </tr>
                ';
            }
        }


        $html .= '
                </tbody>
            </table>
        ';


        /*
         * ============================
         * DOMPDF
         * ============================
         */
        $options =
            new Options();

        $options->set(
            'isRemoteEnabled',
            true
        );


        $dompdf =
            new Dompdf(
                $options
            );


        $dompdf->loadHtml(
            $html
        );


        $dompdf->setPaper(
            'A4',
            'portrait'
        );


        $dompdf->render();


        return Yii::$app->response
            ->sendContentAsFile(
                $dompdf->output(),

                'daftar-hadir-peserta-'
                . date('Y-m-d')
                . '.pdf',

                [
                    'mimeType' =>
                        'application/pdf',

                    'inline' => false,
                ]
            );
    }


    /**
     * Export daftar hadir ke Excel XLSX.
     *
     * Format:
     *
     * No | Nama Peserta | Nomor Identitas |
     * Unit/Bagian | Jabatan | TTD
     *
     * TTD digital ditampilkan sebagai gambar.
     *
     * Route tetap actionExportCsv()
     * agar tombol lama tidak perlu diubah.
     *
     * @return Response
     */
    public function actionExportCsv()
    {
        /*
         * ============================
         * FILTER
         * ============================
         */
        $agendaId = trim(
            (string) Yii::$app->request->get(
                'agenda_id',
                ''
            )
        );

        $status = trim(
            (string) Yii::$app->request->get(
                'status',
                ''
            )
        );

        $q = trim(
            (string) Yii::$app->request->get(
                'q',
                ''
            )
        );


        /*
         * ============================
         * DATA
         * ============================
         */
        $rows = DaftarHadirQuery::fetch([
            'agenda_id' => $agendaId,
            'status' => $status,
            'q' => $q,
        ]);


        /*
         * ============================
         * AGENDA
         * ============================
         */
        $agendaLabel =
            'Semua Agenda';


        if ($agendaId !== '') {

            $agenda = Agenda::findOne(
                (int) $agendaId
            );

            if ($agenda) {
                $agendaLabel =
                    $agenda->pembahasan
                    ?: 'Agenda';
            }
        }


        /*
         * ============================
         * SPREADSHEET
         * ============================
         */
        $spreadsheet =
            new Spreadsheet();


        $sheet =
            $spreadsheet->getActiveSheet();


        $sheet->setTitle(
            'Daftar Hadir'
        );


        /*
         * ============================
         * JUDUL
         * ============================
         */
        $sheet->mergeCells(
            'A1:F1'
        );


        $sheet->setCellValue(
            'A1',
            'DAFTAR HADIR PESERTA'
        );


        $sheet->getStyle(
            'A1'
        )->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],
        ]);


        $sheet->getRowDimension(
            1
        )->setRowHeight(25);


        /*
         * ============================
         * AGENDA
         * ============================
         */
        $sheet->mergeCells(
            'A2:F2'
        );


        $sheet->setCellValueExplicit(
            'A2',
            'Agenda: ' . $agendaLabel,
            DataType::TYPE_STRING
        );


        $sheet->getStyle(
            'A2'
        )->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],
        ]);


        /*
         * ============================
         * TANGGAL CETAK
         * ============================
         */
        $sheet->mergeCells(
            'A3:F3'
        );


        $sheet->setCellValueExplicit(
            'A3',
            'Dicetak pada: '
            . date('d M Y H:i')
            . ' WIB',
            DataType::TYPE_STRING
        );


        $sheet->getStyle(
            'A3'
        )->applyFromArray([
            'font' => [
                'size' => 9,
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],
        ]);


        /*
         * ============================
         * HEADER
         * ============================
         */
        $headers = [
            'No',
            'Nama Peserta',
            'Nomor Identitas',
            'Unit/Bagian',
            'Jabatan',
            'TTD',
        ];


        $sheet->fromArray(
            $headers,
            null,
            'A5'
        );


        /*
         * ============================
         * STYLE HEADER
         * ============================
         */
        $sheet->getStyle(
            'A5:F5'
        )->applyFromArray([
            'font' => [
                'bold' => true,
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,

                'wrapText' => true,
            ],

            'fill' => [
                'fillType' =>
                    Fill::FILL_SOLID,

                'startColor' => [
                    'rgb' => 'E7E6E6',
                ],
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,
                ],
            ],
        ]);


        $sheet->getRowDimension(
            5
        )->setRowHeight(30);


        /*
         * ============================
         * DATA
         * ============================
         */
        $rowNumber = 6;
        $no = 1;


        foreach ($rows as $row) {

            /*
             * Nomor
             */
            $sheet->setCellValue(
                'A' . $rowNumber,
                $no++
            );


            /*
             * Gunakan explicit string
             * untuk data teks.
             *
             * Ini membantu mencegah
             * input teks dianggap formula
             * oleh Excel.
             */
            $sheet->setCellValueExplicit(
                'B' . $rowNumber,
                (string) (
                    $row['nama'] ?: '-'
                ),
                DataType::TYPE_STRING
            );


            $sheet->setCellValueExplicit(
                'C' . $rowNumber,
                (string) (
                    $row['identitas_number']
                    ?: '-'
                ),
                DataType::TYPE_STRING
            );


            $sheet->setCellValueExplicit(
                'D' . $rowNumber,
                (string) (
                    $row['instansi']
                    ?: '-'
                ),
                DataType::TYPE_STRING
            );


            $sheet->setCellValueExplicit(
                'E' . $rowNumber,
                (string) (
                    $row['jabatan']
                    ?: '-'
                ),
                DataType::TYPE_STRING
            );


            /*
             * ============================
             * TTD
             * ============================
             */
            $signatureFile =
                $this->resolveSignatureFile(
                    $row['tanda_tangan_path']
                    ?? ''
                );


            if (
                $row['absensi_id'] !== null
                && $signatureFile !== null
            ) {

                $mimeType =
                    $this->getSignatureMimeType(
                        $signatureFile
                    );


                /*
                 * Drawing hanya digunakan
                 * untuk file gambar yang valid.
                 */
                if ($mimeType !== null) {

                    $drawing =
                        new Drawing();


                    $drawing->setName(
                        'Tanda Tangan'
                    );


                    $drawing->setDescription(
                        'Tanda tangan peserta'
                    );


                    $drawing->setPath(
                        $signatureFile
                    );


                    $drawing->setHeight(
                        45
                    );


                    $drawing->setCoordinates(
                        'F' . $rowNumber
                    );


                    $drawing->setOffsetX(
                        10
                    );


                    $drawing->setOffsetY(
                        5
                    );


                    $drawing->setWorksheet(
                        $sheet
                    );
                }
            }


            /*
             * Tinggi baris untuk TTD.
             */
            $sheet
                ->getRowDimension(
                    $rowNumber
                )
                ->setRowHeight(55);


            $rowNumber++;
        }


        /*
         * ============================
         * BARIS TERAKHIR
         * ============================
         */
        $lastRow = max(
            5,
            $rowNumber - 1
        );


        /*
         * ============================
         * BORDER TABEL
         * ============================
         */
        $sheet
            ->getStyle(
                'A5:F' . $lastRow
            )
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,
                    ],
                ],
            ]);


        /*
         * ============================
         * ALIGNMENT
         * ============================
         */
        $sheet
            ->getStyle(
                'A6:A' . $lastRow
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );


        $sheet
            ->getStyle(
                'C6:C' . $lastRow
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );


        $sheet
            ->getStyle(
                'F6:F' . $lastRow
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );


        $sheet
            ->getStyle(
                'A6:F' . $lastRow
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $sheet
            ->getStyle(
                'A5:F' . $lastRow
            )
            ->getAlignment()
            ->setWrapText(true);


        /*
         * ============================
         * LEBAR KOLOM
         * ============================
         */
        $sheet
            ->getColumnDimension('A')
            ->setWidth(7);


        $sheet
            ->getColumnDimension('B')
            ->setWidth(28);


        $sheet
            ->getColumnDimension('C')
            ->setWidth(22);


        $sheet
            ->getColumnDimension('D')
            ->setWidth(20);


        $sheet
            ->getColumnDimension('E')
            ->setWidth(18);


        $sheet
            ->getColumnDimension('F')
            ->setWidth(22);


        /*
         * ============================
         * FILTER
         * ============================
         */
        $sheet->setAutoFilter(
            'A5:F' . $lastRow
        );


        /*
         * ============================
         * FREEZE HEADER
         * ============================
         */
        $sheet->freezePane(
            'A6'
        );


        /*
         * ============================
         * PRINT SETTING
         * ============================
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
            ->setTop(0.5);


        $sheet->getPageMargins()
            ->setRight(0.5);


        $sheet->getPageMargins()
            ->setBottom(0.5);


        $sheet->getPageMargins()
            ->setLeft(0.5);


        /*
         * ============================
         * NAMA FILE
         * ============================
         */
        $filenameParts = [
            'daftar-hadir'
        ];


        if ($agendaId !== '') {
            $filenameParts[] =
                $agendaId;
        }


        $filenameParts[] =
            date('Ymd-His');


        $filename =
            implode(
                '-',
                $filenameParts
            )
            . '.xlsx';


        /*
         * ============================
         * GENERATE XLSX
         * ============================
         */
        $writer =
            new Xlsx(
                $spreadsheet
            );


        ob_start();

        $writer->save(
            'php://output'
        );

        $content =
            ob_get_clean();


        return Yii::$app->response
            ->sendContentAsFile(
                $content,
                $filename,
                [
                    'mimeType' =>
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                    'inline' => false,
                ]
            );
    }


    /**
     * Memastikan file TTD benar-benar berada
     * di dalam folder webroot aplikasi.
     *
     * Mencegah path seperti:
     * ../../file-rahasia
     *
     * @param string $relativePath
     * @return string|null
     */
    private function resolveSignatureFile(
        string $relativePath
    ): ?string {
        $relativePath =
            trim($relativePath);


        if ($relativePath === '') {
            return null;
        }


        /*
         * Jangan menerima URL eksternal.
         */
        if (
            preg_match(
                '#^(https?:)?//#i',
                $relativePath
            )
        ) {
            return null;
        }


        /*
         * Normalisasi slash.
         */
        $relativePath =
            ltrim(
                str_replace(
                    '\\',
                    '/',
                    $relativePath
                ),
                '/'
            );


        /*
         * Tolak traversal.
         */
        if (
            str_contains(
                $relativePath,
                '../'
            )
            ||
            str_contains(
                $relativePath,
                '..\\'
            )
        ) {
            return null;
        }


        $webroot =
            realpath(
                Yii::getAlias(
                    '@webroot'
                )
            );


        if ($webroot === false) {
            return null;
        }


        $file =
            realpath(
                $webroot
                . DIRECTORY_SEPARATOR
                . $relativePath
            );


        if ($file === false) {
            return null;
        }


        /*
         * Pastikan file tetap berada
         * di dalam webroot.
         */
        $webrootPrefix =
            rtrim(
                $webroot,
                DIRECTORY_SEPARATOR
            )
            . DIRECTORY_SEPARATOR;


        if (
            !str_starts_with(
                $file,
                $webrootPrefix
            )
        ) {
            return null;
        }


        if (!is_file($file)) {
            return null;
        }


        return $file;
    }


    /**
     * Memastikan file TTD adalah gambar
     * yang didukung.
     *
     * @param string $file
     * @return string|null
     */
    private function getSignatureMimeType(
        string $file
    ): ?string {
        if (!is_file($file)) {
            return null;
        }


        $mimeType = null;


        if (
            function_exists(
                'mime_content_type'
            )
        ) {
            $mimeType =
                mime_content_type(
                    $file
                );
        }


        $allowed = [
            'image/png',
            'image/jpeg',
            'image/gif',
        ];


        if (
            !in_array(
                $mimeType,
                $allowed,
                true
            )
        ) {
            return null;
        }


        return $mimeType;
    }


    /**
     * Displays a single Member model.
     *
     * @param int $member_id Member ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($member_id)
    {
        return $this->render(
            'view',
            [
                'model' =>
                    $this->findModel(
                        $member_id
                    ),
            ]
        );
    }


    /**
     * Creates a new Member model.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model =
            new Member();


        if ($this->request->isPost) {

            if (
                $model->load(
                    $this->request->post()
                )
            ) {

                $model->created_by =
                    Yii::$app->user->id;


                if ($model->save()) {

                    return $this->redirect([
                        'view',
                        'member_id' =>
                            $model->member_id
                    ]);
                }
            }

        } else {

            $model->loadDefaultValues();
        }


        return $this->render(
            'create',
            [
                'model' => $model,
            ]
        );
    }


    /**
     * Updates an existing Member model.
     *
     * @param int $member_id Member ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($member_id)
    {
        $model =
            $this->findModel(
                $member_id
            );


        if (
            $this->request->isPost
            &&
            $model->load(
                $this->request->post()
            )
        ) {

            $model->updated_by =
                Yii::$app->user->id;


            if ($model->save()) {

                return $this->redirect([
                    'view',
                    'member_id' =>
                        $model->member_id
                ]);
            }
        }


        return $this->render(
            'update',
            [
                'model' => $model,
            ]
        );
    }


    /**
     * Deletes an existing Member model.
     *
     * @param int $member_id Member ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($member_id)
    {
        $model =
            $this->findModel(
                $member_id
            );


        $model->deleted_at =
            date('Y-m-d H:i:s');


        $model->is_active = 0;


        $model->updated_by =
            Yii::$app->user->id;


        $model->save(
            false,
            [
                'deleted_at',
                'is_active',
                'updated_by',
                'updated_at',
            ]
        );


        return $this->redirect([
            'index'
        ]);
    }


    /**
     * Finds the Member model based on its primary key value.
     *
     * @param int $member_id Member ID
     * @return Member
     * @throws NotFoundHttpException
     */
    protected function findModel($member_id)
    {
        if (
            ($model = Member::findOne([
                'member_id' =>
                    $member_id
            ])) !== null
        ) {
            return $model;
        }


        throw new NotFoundHttpException(
            'The requested page does not exist.'
        );
    }
}