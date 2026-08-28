<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 * Has foreign keys to `{{%users}}`.
 */
class m260828_000005_create_table_member extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%member}}', [
            'member_id' => $this->primaryKey(),
            'nama' => $this->string(150)->notNull(),
            'jabatan' => $this->string(100)->null(),
            'instansi' => $this->string(150)->null(),
            'tipe_identitas' => $this->string(50)->null(),
            'identitas_number' => $this->string(100)->null(),
            'email' => $this->string(150)->null(),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions);

        $this->addForeignKey('fk_member_created_by', '{{%member}}', 'created_by', '{{%users}}', 'user_id');
        $this->addForeignKey('fk_member_updated_by', '{{%member}}', 'updated_by', '{{%users}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_member_updated_by', '{{%member}}');
        $this->dropForeignKey('fk_member_created_by', '{{%member}}');
        $this->dropTable('{{%member}}');
    }
}
