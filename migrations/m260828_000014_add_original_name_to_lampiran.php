<?php

use yii\db\Migration;

class m260828_000014_add_original_name_to_lampiran extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%lampiran}}', 'original_name', $this->string(255)->null()->after('file_path'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%lampiran}}', 'original_name');
    }
}