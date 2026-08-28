<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%activity_log}}`.
 * Has foreign key to `{{%users}}`.
 */
class m260828_000010_create_table_activity_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%activity_log}}', [
            'log_id' => $this->primaryKey(),
            'table_name' => $this->string(100)->notNull(),
            'record_id' => $this->integer()->notNull(),
            'action' => $this->string(50)->notNull(),
            'performed_by' => $this->integer()->notNull(),
            'performed_role' => $this->string(50)->notNull(),
            'old_value' => $this->text()->null(),
            'new_value' => $this->text()->null(),
            'ip_address' => $this->string(45)->null(),
            'user_agent' => $this->string(255)->null(),
            'description' => $this->string(255)->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addForeignKey('fk_activitylog_performed_by', '{{%activity_log}}', 'performed_by', '{{%users}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_activitylog_performed_by', '{{%activity_log}}');
        $this->dropTable('{{%activity_log}}');
    }
}
