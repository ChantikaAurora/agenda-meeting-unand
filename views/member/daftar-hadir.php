<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $hadirDataProvider */
/** @var app\models\Agenda[] $agendaList */
/** @var string $agendaId */
/** @var string $status */
/** @var string $q */
/** @var int $totalPeserta */
/** @var int $totalHadir */
/** @var int $totalTidakHadir */

?>

<?php

/*
 * ==========================================
 * CSS
 * ==========================================
 */

$this->registerCss(<<<CSS
.btn-dh-search {
    background: #1f4d2c;
    border-color: #1f4d2c;
    color: #fff;
    border-radius: .5rem;
    font-size: .85rem;
}

.btn-dh-search:hover,
.btn-dh-search:focus {
    background: #173d22;
    border-color: #173d22;
    color: #fff;
}

.dh-wrapper {
    width: 100%;
    margin: 0 auto;
}

.dh-breadcrumb {
    font-size: .85rem;
    color: var(--bs-secondary-color);
    margin-bottom: .75rem;
}

.dh-breadcrumb a {
    color: var(--bs-secondary-color);
    text-decoration: none;
}
.dh-breadcrumb a:hover {
    color: #1f4d2c;
    text-decoration: underline;
}

.dh-breadcrumb .current {
    color: #1f4d2c;
    font-weight: 600;
}
.dh-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: .2rem;
}

.dh-header p {
    color: var(--bs-secondary-color);
    margin-bottom: 0;
}

.dh-actions .btn {
    border-radius: .5rem;
    font-size: .85rem;
}


/* Statistik */

.stat-card {
    background: #fff;
    border: 1px solid #eef0f2;
    border-radius: .9rem;
    padding: 1.1rem 1.3rem;

    display: flex;
    align-items: center;

    gap: 1rem;

    height: 100%;

    box-shadow:
        0 1px 2px rgba(16, 24, 40, .04);
}

.stat-card .stat-icon {

    width: 46px;
    height: 46px;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 1.2rem;

    flex-shrink: 0;
}

.stat-icon-total {
    background: rgba(64, 179, 216, .15);
    color: #2f8fb0;
}

.stat-icon-hadir {
    background: rgba(131, 201, 51, .15);
    color: #5a9424;
}

.stat-icon-tidak {
    background: rgba(220, 53, 69, .12);
    color: #dc3545;
}

.stat-card .stat-label {

    font-size: .82rem;

    color: var(--bs-secondary-color);

    margin-bottom: .2rem;
}

.stat-card .stat-value {

    font-size: 1.8rem;

    font-weight: 700;

    line-height: 1;

    color: #1a1a1a;
}


/* Toolbar */

.dh-toolbar {

    background: #fff;

    border: 1px solid #eef0f2;

    border-radius: .9rem;

    padding: .9rem 1rem;

    display: flex;

    flex-wrap: wrap;

    gap: .6rem;

    box-shadow:
        0 1px 2px rgba(16, 24, 40, .04);
}

.dh-toolbar .search-box {

    position: relative;

    flex: 1 1 260px;
}

.dh-toolbar .search-box input {

    padding-left: 2.1rem;

    border-radius: .5rem;
}

.dh-toolbar .search-box .search-icon {

    position: absolute;

    left: .7rem;

    top: 50%;

    transform: translateY(-50%);

    color: var(--bs-secondary-color);
}

.dh-toolbar select.form-select {

    border-radius: .5rem;

    min-width: 160px;
}


/* Table */

.dh-table-card {

    background: #fff;

    border: 1px solid #eef0f2;

    border-radius: .9rem;

    box-shadow:
        0 1px 2px rgba(16, 24, 40, .04);

    overflow: hidden;
}

.dh-table {

    margin-bottom: 0;
}

.dh-table thead th {

    background: #fafbfc;

    border-bottom: 1px solid #eef0f2;

    color: var(--bs-secondary-color);

    font-size: .75rem;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: .03em;

    white-space: nowrap;

    padding: .8rem 1rem;
}

.dh-table tbody td {

    padding: .85rem 1rem;

    vertical-align: middle;

    border-bottom: 1px solid #f2f3f5;

    font-size: .9rem;
}

.dh-table tbody tr:last-child td {

    border-bottom: none;
}

.dh-name {

    font-weight: 600;

    color: #1a1a1a;
}


/* Status */

.status-badge {

    display: inline-flex;

    align-items: center;

    gap: .35rem;

    padding: .3rem .7rem;

    border-radius: 50rem;

    font-size: .78rem;

    font-weight: 600;
}

.status-badge.badge-hadir {

    background: rgba(131, 201, 51, .15);

    color: #5a9424;
}

.status-badge.badge-tidak {

    background: rgba(220, 53, 69, .12);

    color: #dc3545;
}


/* Footer */

.dh-footer {

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: .5rem .25rem 0;

    font-size: .85rem;

    color: var(--bs-secondary-color);
}

.dh-footer .pagination {

    margin-bottom: 0;
}

.dh-footer .page-link {

    border-radius: .4rem;

    margin: 0 .15rem;

    border: 1px solid #eef0f2;

    color: #1a1a1a;
}

.dh-footer .page-item.active .page-link {

    background: #40B3D8;

    border-color: #40B3D8;
}


/* Print */

@media print {

    .dh-actions,
    .dh-toolbar,
    .dh-footer,
    .nav-tabs {
        display: none !important;
    }

    .dh-wrapper {
        max-width: 100%;
    }

}

CSS
);


/*
 * ==========================================
 * JAVASCRIPT
 * ==========================================
 */

$this->registerJs(<<<JS

/*
 * Tombol PDF
 */

var pdfBtn =
    document.getElementById('dh-pdf-btn');

if (pdfBtn) {

    pdfBtn.addEventListener(
        'click',
        function () {

            var params = new URLSearchParams(window.location.search);
            var exportParams = new URLSearchParams();

            ['agenda_id', 'status', 'q'].forEach(function (key) {
                var value = params.get(key);
                if (value !== null && value !== '') {
                    exportParams.set(key, value);
                }
            });

            var url = '/index.php?r=member/export-pdf';
            if (exportParams.toString()) {
                url += '&' + exportParams.toString();
            }

            window.location.href = url;

        }
    );

}


/*
 * Tombol Export (CSV)
 */

var exportBtn =
    document.getElementById('dh-export-btn');

if (exportBtn) {

    exportBtn.addEventListener(
        'click',
        function () {

            var params = new URLSearchParams(window.location.search);
            var exportParams = new URLSearchParams();

            ['agenda_id', 'status', 'q'].forEach(function (key) {
                var value = params.get(key);
                if (value !== null && value !== '') {
                    exportParams.set(key, value);
                }
            });

            var url = '/index.php?r=member/export-csv';
            if (exportParams.toString()) {
                url += '&' + exportParams.toString();
            }

            window.location.href = url;

        }
    );
}

/*
 * Auto submit filter
 */

document
    .querySelectorAll(
        '.dh-toolbar select[data-autosubmit]'
    )
    .forEach(function (el) {

        el.addEventListener(
            'change',
            function () {

                el.form.submit();

            }
        );

    });

JS
);

?>


<div class="dh-wrapper">
    <div class="dh-wrapper">

    <?php
    $selectedAgenda = null;
    if ($agendaId !== '') {
        foreach ($agendaList as $agendaItem) {
            if ((string) $agendaItem->agenda_id === (string) $agendaId) {
                $selectedAgenda = $agendaItem;
                break;
            }
        }
    }
    ?>

    <div class="dh-breadcrumb">
        <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a>
        &nbsp;›&nbsp;
        <a href="<?= Url::to(['/agenda/index']) ?>">Kelola Agenda</a>
        <?php if ($selectedAgenda !== null): ?>
            &nbsp;›&nbsp;
            <a href="<?= Url::to(['/agenda/view', 'id' => $selectedAgenda->agenda_id]) ?>">
                <?= Html::encode($selectedAgenda->pembahasan) ?>
            </a>
        <?php endif; ?>
        &nbsp;›&nbsp;
        <span class="current">Daftar Hadir</span>
    </div>

    <!-- =============================== -->
    <!-- HEADER -->
    <!-- =============================== -->

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 gap-2 dh-header">

        <div>

            <h1>
                Daftar Hadir Peserta
            </h1>

            <p>
                Kelola dan pantau kehadiran peserta untuk agenda meeting di Universitas Andalas.
            </p>

        </div>


        <div class="d-flex gap-2 dh-actions">

            <button
                type="button"
                id="dh-pdf-btn"
                class="btn btn-outline-secondary">

                &#128196; PDF

            </button>


            <button
                type="button"
                id="dh-export-btn"
                class="btn"
                style="background:#83C933; color:#fff;">

                &#11015; Export

            </button>

        </div>

    </div>


    <!-- =============================== -->
    <!-- STATISTIK -->
    <!-- =============================== -->

    <div class="row g-3 mb-4">


        <!-- Total -->

        <div class="col-12 col-md-4">

            <div class="stat-card">

                <div class="stat-icon stat-icon-total">
                    &#128101;
                </div>

                <div>

                    <div class="stat-label">
                        Total Peserta
                    </div>

                    <div class="stat-value">
                        <?= (int) $totalPeserta ?>
                    </div>

                </div>

            </div>

        </div>


        <!-- Hadir -->

        <div class="col-12 col-md-4">

            <div class="stat-card">

                <div class="stat-icon stat-icon-hadir">
                    &#10003;
                </div>

                <div>

                    <div class="stat-label">
                        Hadir
                    </div>

                    <div class="stat-value">
                        <?= (int) $totalHadir ?>
                    </div>

                </div>

            </div>

        </div>


        <!-- Tidak Hadir -->

        <div class="col-12 col-md-4">

            <div class="stat-card">

                <div class="stat-icon stat-icon-tidak">
                    &#10005;
                </div>

                <div>

                    <div class="stat-label">
                        Tidak Hadir
                    </div>

                    <div class="stat-value">
                        <?= (int) $totalTidakHadir ?>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- =============================== -->
    <!-- TABEL -->
    <!-- =============================== -->

    <div class="dh-table-card">

        <div class="table-responsive">

            <table class="table dh-table">


                <thead>

                <tr>

                    <th>
                        NIK/NIM
                    </th>

                    <th>
                        Nama Peserta
                    </th>

                    <th>
                        Unit/Bagian
                    </th>

                    <th>
                        Waktu Scan
                    </th>

                    <th>
                        Status
                    </th>

                    <th class="text-end">
                        Aksi
                    </th>

                </tr>

                </thead>


                <tbody>


                <?php if (
                    empty(
                        $hadirDataProvider->getModels()
                    )
                ): ?>


                    <tr>

                        <td
                            colspan="6"
                            class="text-center text-secondary py-4">

                            Tidak ada data peserta yang cocok
                            dengan filter saat ini.

                        </td>

                    </tr>


                <?php else: ?>


                    <?php foreach (
                        $hadirDataProvider->getModels()
                        as $row
                    ): ?>


                        <?php

                        $hadir =
                            $row['absensi_id'] !== null;

                        ?>


                        <tr>


                            <!-- NIK/NIM -->

                            <td>

                                <?= Html::encode(
                                    $row['identitas_number']
                                    ?: '-'
                                ) ?>

                            </td>


                            <!-- Nama -->

                            <td class="dh-name">

                                <?= Html::encode(
                                    $row['nama']
                                ) ?>

                            </td>


                            <!-- Instansi -->

                            <td>

                                <?= Html::encode(
                                    $row['instansi']
                                    ?: '-'
                                ) ?>

                            </td>


                            <!-- Waktu -->

                            <td>

                                <?= $hadir

                                    ? Html::encode(
                                        Yii::$app
                                            ->formatter
                                            ->asTime(
                                                $row['waktu_scan']
                                            )
                                        . ' WIB'
                                    )

                                    : '-'

                                ?>

                            </td>


                            <!-- Status -->

                            <td>


                                <?php if ($hadir): ?>


                                    <span
                                        class="status-badge badge-hadir">

                                        &#10003; Hadir

                                    </span>


                                <?php else: ?>


                                    <span
                                        class="status-badge badge-tidak">

                                        &#10005; Tidak Hadir

                                    </span>


                                <?php endif; ?>


                            </td>


                            <!-- Aksi -->

                            <td class="text-end">


                                <?php
                                $signatureExists = $hadir
                                    && !empty($row['tanda_tangan_path'])
                                    && is_file(Yii::getAlias('@webroot/' . ltrim($row['tanda_tangan_path'], '/')));
                                ?>

                                <?php if ($signatureExists): ?>

                                    <?= Html::a('Lihat TTD', Url::to('@web/' . ltrim($row['tanda_tangan_path'], '/')), [
                                        'class' => 'btn btn-sm btn-outline-secondary',
                                        'target' => '_blank',
                                    ]) ?>

                                <?php elseif ($hadir): ?>

                                    <span class="text-muted" style="font-size:.78rem;">TTD tidak tersedia</span>

                                <?php endif; ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>


    <!-- =============================== -->
    <!-- FOOTER / PAGINATION -->
    <!-- =============================== -->

    <div class="dh-footer">


        <div>

            Menampilkan

            <?= count(
                $hadirDataProvider->getModels()
            ) ?>

            dari

            <?= (int) $totalPeserta ?>

            peserta

        </div>


        <?= LinkPager::widget([

            'pagination' =>
                $hadirDataProvider->getPagination(),

        ]) ?>


    </div>


</div>
