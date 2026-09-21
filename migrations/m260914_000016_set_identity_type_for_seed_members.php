<?php

use yii\db\Migration;

class m260914_000016_set_identity_type_for_seed_members extends Migration
{
    public function safeUp()
    {
        $this->update(
            '{{%member}}',
            ['tipe_identitas' => 'NIP'],
            [
                'email' => [
                    'maya.sari@unand.ac.id',
                    'rina.marlina@unand.ac.id',
                    'fajar.hidayat@unand.ac.id',
                    'nadia.putri@unand.ac.id',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->update(
            '{{%member}}',
            ['tipe_identitas' => null],
            [
                'email' => [
                    'maya.sari@unand.ac.id',
                    'rina.marlina@unand.ac.id',
                    'fajar.hidayat@unand.ac.id',
                    'nadia.putri@unand.ac.id',
                ],
            ]
        );
    }
}