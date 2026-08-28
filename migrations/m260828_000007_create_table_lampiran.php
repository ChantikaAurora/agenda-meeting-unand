<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lampiran}}`.
 * Has foreign keys to `{{%agenda}}` and `{{%users}}`.
 */
class m260828_000007_create_table_lampiran extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lampiran}}', [
            'lampiran_id' => $this->primaryKey(),
            'agenda_id' => $this->integer()->notNull(),
            'jenis_lampiran' => $this->string(50)->notNull(),
            'ringkasan' => $this->text()->null(),
            'file_path' => $this->string(255)->notNull(),
            'status' => "ENUM('draft','final') NOT NULL DEFAULT 'draft'",
            'email_sent_at' => $this->dateTime()->null(),
            'email_sent_by' => $this->integer()->null(),
            'uploaded_by' => $this->integer()->notNull(),
            'uploaded_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey('fk_lampiran_agenda', '{{%lampiran}}', 'agenda_id', '{{%agenda}}', 'agenda_id');
        $this->addForeignKey('fk_lampiran_email_sent_by', '{{%lampiran}}', 'email_sent_by', '{{%users}}', 'user_id');
        $this->addForeignKey('fk_lampiran_uploaded_by', '{{%lampiran}}', 'uploaded_by', '{{%users}}', 'user_id');
        $this->addForeignKey('fk_lampiran_created_by', '{{%lampiran}}', 'created_by', '{{%users}}', 'user_id');
        $this->addForeignKey('fk_lampiran_updated_by', '{{%lampiran}}', 'updated_by', '{{%users}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_lampiran_updated_by', '{{%lampiran}}');
        $this->dropForeignKey('fk_lampiran_created_by', '{{%lampiran}}');
        $this->dropForeignKey('fk_lampiran_uploaded_by', '{{%lampiran}}');
        $this->dropForeignKey('fk_lampiran_email_sent_by', '{{%lampiran}}');
        $this->dropForeignKey('fk_lampiran_agenda', '{{%lampiran}}');
        $this->dropTable('{{%lampiran}}');
    }
}
