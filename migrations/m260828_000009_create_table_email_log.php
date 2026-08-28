<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%email_log}}`.
 * Has foreign keys to `{{%lampiran}}`, `{{%member}}`, `{{%users}}`.
 */
class m260828_000009_create_table_email_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%email_log}}', [
            'id' => $this->primaryKey(),
            'lampiran_id' => $this->integer()->notNull(),
            'member_id' => $this->integer()->null(),
            'nama' => $this->string(150)->notNull(),
            'email' => $this->string(150)->notNull(),
            'status' => "ENUM('terkirim','gagal') NOT NULL",
            'sent_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'sent_by' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_emaillog_lampiran', '{{%email_log}}', 'lampiran_id', '{{%lampiran}}', 'lampiran_id');
        $this->addForeignKey('fk_emaillog_member', '{{%email_log}}', 'member_id', '{{%member}}', 'member_id');
        $this->addForeignKey('fk_emaillog_sent_by', '{{%email_log}}', 'sent_by', '{{%users}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_emaillog_sent_by', '{{%email_log}}');
        $this->dropForeignKey('fk_emaillog_member', '{{%email_log}}');
        $this->dropForeignKey('fk_emaillog_lampiran', '{{%email_log}}');
        $this->dropTable('{{%email_log}}');
    }
}
