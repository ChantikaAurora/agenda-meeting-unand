<?php

use yii\db\Migration;

class m260828_000015_finalize_existing_lampiran extends Migration
{
    public function safeUp()
    {
        $this->update('{{%lampiran}}', ['status' => 'final'], ['and', ['<>', 'file_path', ''], ['not', ['file_path' => null]]]);
    }

    public function safeDown()
    {
        $this->update('{{%lampiran}}', ['status' => 'draft'], ['status' => 'final']);
    }
}