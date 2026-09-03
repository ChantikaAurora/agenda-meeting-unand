<?php

use app\models\Member;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var yii\data\ArrayDataProvider $hadirDataProvider */
/** @var app\models\Agenda[] $agendaList */
/** @var string $agendaId */
/** @var string $status */
/** @var string $q */
/** @var int $totalPeserta */
/** @var int $totalHadir */
/** @var int $totalTidakHadir */

$this->title = 'Kelola Peserta & Member';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="member-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Kelola data member dan pantau kehadiran rapat.
    </p>


    <!-- ========================= -->
    <!-- TAB -->
    <!-- ========================= -->

    <ul class="nav nav-tabs" id="pesertaTab" role="tablist">

        <!-- TAB DAFTAR HADIR -->
        <li class="nav-item" role="presentation">

            <button
                class="nav-link"
                id="hadir-tab"
                data-bs-toggle="tab"
                data-bs-target="#hadir-pane"
                type="button"
                role="tab"
                aria-controls="hadir-pane"
                aria-selected="false">

                Daftar Hadir

            </button>

        </li>


        <!-- TAB DATA MEMBER -->
        <li class="nav-item" role="presentation">

            <button
                class="nav-link active"
                id="member-tab"
                data-bs-toggle="tab"
                data-bs-target="#member-pane"
                type="button"
                role="tab"
                aria-controls="member-pane"
                aria-selected="true">

                Data Member

            </button>

        </li>

    </ul>


    <!-- ========================= -->
    <!-- ISI TAB -->
    <!-- ========================= -->

    <div
        class="tab-content"
        id="pesertaTabContent"
        style="margin-top: 15px;">


        <!-- ====================================== -->
        <!-- TAB DAFTAR HADIR -->
        <!-- ====================================== -->

        <div
            class="tab-pane fade"
            id="hadir-pane"
            role="tabpanel"
            aria-labelledby="hadir-tab">

            <?= $this->render('daftar-hadir', [

                'hadirDataProvider' => $hadirDataProvider,

                'agendaList' => $agendaList,

                'agendaId' => $agendaId,

                'status' => $status,

                'q' => $q,

                'totalPeserta' => $totalPeserta,

                'totalHadir' => $totalHadir,

                'totalTidakHadir' => $totalTidakHadir,

            ]) ?>

        </div>


        <!-- ====================================== -->
        <!-- TAB DATA MEMBER -->
        <!-- ====================================== -->

        <div
            class="tab-pane fade show active"
            id="member-pane"
            role="tabpanel"
            aria-labelledby="member-tab">


            <p>

                <?= Html::a(
                    'Tambah Member',
                    ['create'],
                    [
                        'class' => 'btn btn-success'
                    ]
                ) ?>

            </p>


            <?= GridView::widget([

                'dataProvider' => $dataProvider,

                'columns' => [

                    [
                        'class' => 'yii\grid\SerialColumn'
                    ],

                    'nama',

                    'jabatan',

                    'instansi',

                    'tipe_identitas',

                    [

                        'class' => ActionColumn::className(),

                        'urlCreator' => function (
                            $action,
                            Member $model,
                            $key,
                            $index,
                            $column
                        ) {

                            return Url::toRoute([
                                $action,
                                'member_id' => $model->member_id
                            ]);

                        }

                    ],

                ],

            ]); ?>


        </div>

    </div>

</div>


<?php

/*
 * ==========================================
 * AGAR TAB DAFTAR HADIR BISA DIBUKA
 * KETIKA ADA FILTER DARI DAFTAR HADIR
 * ==========================================
 */

if (
    $agendaId !== '' ||
    $status !== '' ||
    $q !== ''
) {

    $this->registerJs(<<<JS

        document.addEventListener('DOMContentLoaded', function () {

            var hadirTab =
                document.getElementById('hadir-tab');

            if (hadirTab) {

                var tab =
                    new bootstrap.Tab(hadirTab);

                tab.show();

            }

        });

    JS
    );

}

?>
