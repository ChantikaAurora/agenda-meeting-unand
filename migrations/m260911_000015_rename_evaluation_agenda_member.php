<?php

use yii\db\Migration;

class m260911_000015_rename_evaluation_agenda_member extends Migration
{
    public function safeUp()
    {
        $this->update('{{%member}}', [
            'nama' => 'Maya Sari, S.E.',
            'jabatan' => 'Kepala Subbagian Akademik',
            'email' => 'maya.sari@unand.ac.id',
        ], [
            'email' => 'dwi.wulan@unand.ac.id',
        ]);
    }

    public function safeDown()
    {
        $this->update('{{%member}}', [
            'nama' => 'Dwi Wulan Suci, S.E.',
            'jabatan' => 'Kepala Subbagian Akademik',
            'email' => 'dwi.wulan@unand.ac.id',
        ], [
            'email' => 'maya.sari@unand.ac.id',
        ]);
    }
}
