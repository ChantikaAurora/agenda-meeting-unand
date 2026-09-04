<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Member $model */

$this->title = 'Ubah Member';

?>

<div class="member-update">

    <!-- ==========================================
         BREADCRUMB
         ========================================== -->

    <div class="member-breadcrumb">

        <?= Html::a(
            'Dashboard',
            ['site/index'],
            ['class' => 'breadcrumb-link']
        ) ?>

        <span class="breadcrumb-separator">›</span>

        <?= Html::a(
            'Kelola Member',
            ['index'],
            ['class' => 'breadcrumb-link']
        ) ?>

        <span class="breadcrumb-separator">›</span>

        <span class="breadcrumb-current">
            Ubah Member
        </span>

    </div>


    <!-- ==========================================
         HEADER
         ========================================== -->

    <div class="member-update-header">

        <div>

            <h1>
                Ubah Data Member
            </h1>

            <p>
                Perbarui informasi member sesuai data yang diperlukan.
            </p>

        </div>

    </div>


    <!-- ==========================================
         FORM CARD
         ========================================== -->

    <div class="member-form-card">

        <?php $form = ActiveForm::begin([
            'options' => [
                'class' => 'member-form'
            ]
        ]); ?>


        <!-- ==========================================
             INFORMASI MEMBER
             ========================================== -->

        <div class="form-section">

            <div class="form-section-title">
                Informasi Member
            </div>

            <div class="form-section-description">
                Lengkapi informasi dasar member.
            </div>


            <div class="form-grid">

                <!-- NAMA -->

                <div class="form-field form-field-full">

                    <?= $form->field($model, 'nama')
                        ->textInput([
                            'maxlength' => true,
                            'class' => 'form-control custom-input',
                            'placeholder' => 'Masukkan nama lengkap'
                        ])
                        ->label('Nama Lengkap') ?>

                </div>


                <!-- JABATAN -->

                <div class="form-field">

                    <?= $form->field($model, 'jabatan')
                        ->textInput([
                            'maxlength' => true,
                            'class' => 'form-control custom-input',
                            'placeholder' => 'Contoh: Ketua Program Studi'
                        ])
                        ->label('Jabatan') ?>

                </div>


                <!-- INSTANSI -->

                <div class="form-field">

                    <?= $form->field($model, 'instansi')
                        ->textInput([
                            'maxlength' => true,
                            'class' => 'form-control custom-input',
                            'placeholder' => 'Contoh: Fakultas Teknik Unand'
                        ])
                        ->label('Instansi') ?>

                </div>

            </div>

        </div>


        <!-- ==========================================
             IDENTITAS
             ========================================== -->

        <div class="form-section">

            <div class="form-section-title">
                Identitas Member
            </div>

            <div class="form-section-description">
                Informasi identitas digunakan untuk keperluan data member.
            </div>


            <div class="form-grid">

                <!-- TIPE IDENTITAS -->

                <div class="form-field">

                    <?= $form->field($model, 'tipe_identitas')
                        ->textInput([
                            'maxlength' => true,
                            'class' => 'form-control custom-input',
                            'placeholder' => 'Contoh: NIP'
                        ])
                        ->label('Tipe Identitas') ?>

                </div>


                <!-- NOMOR IDENTITAS -->

                <div class="form-field">

                    <?= $form->field($model, 'identitas_number')
                        ->textInput([
                            'maxlength' => true,
                            'class' => 'form-control custom-input',
                            'placeholder' => 'Masukkan nomor identitas'
                        ])
                        ->label('Nomor Identitas') ?>

                </div>


                <!-- EMAIL -->

                <div class="form-field form-field-full">

                    <?= $form->field($model, 'email')
                        ->input('email', [
                            'maxlength' => true,
                            'class' => 'form-control custom-input',
                            'placeholder' => 'contoh@email.com'
                        ])
                        ->label('Email') ?>

                </div>

            </div>

        </div>


        <!-- ==========================================
             FOOTER BUTTON
             ========================================== -->

        <div class="member-form-footer">

            <?= Html::a(
                'Batal',
                [
                    'view',
                    'member_id' => $model->member_id
                ],
                [
                    'class' => 'btn-member-cancel'
                ]
            ) ?>


            <?= Html::submitButton(
                'Simpan Perubahan',
                [
                    'class' => 'btn-member-save'
                ]
            ) ?>

        </div>


        <?php ActiveForm::end(); ?>

    </div>

</div>


<?php

$this->registerCss(<<<CSS

/* ==========================================
   MEMBER UPDATE
   ========================================== */

.member-update {
    padding: 0;
}


/* ==========================================
   BREADCRUMB
   ========================================== */

.member-breadcrumb {

    display: flex;

    align-items: center;

    gap: 8px;

    margin-bottom: 14px;

    font-size: 11px;

    line-height: 18px;
}


.breadcrumb-link {

    color: #718096;

    text-decoration: none;

    transition: .2s;
}


.breadcrumb-link:hover {

    color: #185c37;

    text-decoration: none;
}


.breadcrumb-separator {

    color: #9ca3af;

    font-size: 13px;
}


.breadcrumb-current {

    color: #185c37;

    font-weight: 600;
}


/* ==========================================
   HEADER
   ========================================== */

.member-update-header {

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 12px;

    padding: 20px 20px;

    margin-bottom: 18px;
}


.member-update-header h1 {

    margin: 0 0 5px 0;

    font-size: 21px;

    font-weight: 700;

    color: #111827;
}


.member-update-header p {

    margin: 0;

    font-size: 12px;

    color: #6b7280;
}


/* ==========================================
   FORM CARD
   ========================================== */

.member-form-card {

    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 12px;

    overflow: hidden;
}


/* ==========================================
   FORM SECTION
   ========================================== */

.form-section {

    padding: 22px 22px 24px 22px;

    border-bottom: 1px solid #eeeeee;
}


.form-section-title {

    font-size: 14px;

    font-weight: 700;

    color: #111827;

    margin-bottom: 3px;
}


.form-section-description {

    font-size: 11px;

    color: #8a94a6;

    margin-bottom: 18px;
}


/* ==========================================
   FORM GRID
   ========================================== */

.form-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    column-gap: 22px;

    row-gap: 4px;
}


.form-field {

    min-width: 0;
}


.form-field-full {

    grid-column: 1 / -1;
}


/* ==========================================
   LABEL
   ========================================== */

.member-form .control-label {

    display: block;

    margin-bottom: 6px;

    font-size: 10px;

    font-weight: 600;

    color: #6b7280;

    text-transform: uppercase;

    letter-spacing: .35px;
}


/* ==========================================
   INPUT
   ========================================== */

.member-form .custom-input {

    height: 38px;

    border: 1px solid #dfe3e8;

    border-radius: 7px;

    background: #ffffff;

    color: #111827;

    font-size: 13px;

    padding: 8px 11px;

    box-shadow: none;

    transition: all .2s ease;
}


.member-form .custom-input::placeholder {

    color: #b0b7c3;
}


.member-form .custom-input:focus {

    border-color: #185c37;

    box-shadow: 0 0 0 2px rgba(24, 92, 55, .08);

    outline: none;
}


/* ==========================================
   VALIDATION
   ========================================== */

.member-form .help-block {

    font-size: 11px;

    margin-top: 5px;

    color: #dc2626;
}


.member-form .has-error .custom-input {

    border-color: #dc2626;
}


/* ==========================================
   FOOTER
   ========================================== */

.member-form-footer {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 9px;

    padding: 16px 22px;

    background: #ffffff;
}


/* ==========================================
   BUTTON BATAL
   ========================================== */

.btn-member-cancel {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    height: 35px;

    padding: 0 17px;

    border: 1px solid #e5e7eb;

    border-radius: 7px;

    background: #ffffff;

    color: #6b7280 !important;

    font-size: 12px;

    font-weight: 600;

    text-decoration: none !important;

    transition: all .2s ease;
}


.btn-member-cancel:hover {

    background: #f9fafb;

    border-color: #d1d5db;

    color: #374151 !important;
}


/* ==========================================
   BUTTON SIMPAN
   ========================================== */

.btn-member-save {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    height: 35px;

    padding: 0 19px;

    border: 1px solid #185c37;

    border-radius: 7px;

    background: #185c37;

    color: #ffffff;

    font-size: 12px;

    font-weight: 600;

    cursor: pointer;

    transition: all .2s ease;
}


.btn-member-save:hover {

    background: #12482b;

    border-color: #12482b;

    color: #ffffff;
}


/* ==========================================
   RESPONSIVE
   ========================================== */

@media (max-width: 700px) {

    .form-grid {

        grid-template-columns: 1fr;
    }


    .form-field-full {

        grid-column: auto;
    }


    .member-form-footer {

        justify-content: stretch;
    }


    .btn-member-cancel,
    .btn-member-save {

        flex: 1;

        text-align: center;
    }

}

CSS

);

?>