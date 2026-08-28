<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%agenda_member}}`.
 * Has foreign keys to `{{%agenda}}`, `{{%member}}`, `{{%users}}`.
 */
class m260828_000006_create_table_agenda_member extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%agenda_member}}', [
            'id' => $this->primaryKey(),
            'agenda_id' => $this->integer()->notNull(),
            'member_id' => $this->integer()->notNull(),
            'peran' => "ENUM('peserta','narasumber','moderator') NOT NULL DEFAULT 'peserta'",
            'created_by' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey('fk_agendamember_agenda', '{{%agenda_member}}', 'agenda_id', '{{%agenda}}', 'agenda_id');
        $this->addForeignKey('fk_agendamember_member', '{{%agenda_member}}', 'member_id', '{{%member}}', 'member_id');
        $this->addForeignKey('fk_agendamember_created_by', '{{%agenda_member}}', 'created_by', '{{%users}}', 'user_id');
        $this->createIndex('uq_agenda_member', '{{%agenda_member}}', ['agenda_id', 'member_id'], true);
    }

    public function safeDown()
    {
        $this->dropIndex('uq_agenda_member', '{{%agenda_member}}');
        $this->dropForeignKey('fk_agendamember_created_by', '{{%agenda_member}}');
        $this->dropForeignKey('fk_agendamember_member', '{{%agenda_member}}');
        $this->dropForeignKey('fk_agendamember_agenda', '{{%agenda_member}}');
        $this->dropTable('{{%agenda_member}}');
    }
}
