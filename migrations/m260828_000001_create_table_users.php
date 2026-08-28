<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m260828_000001_create_table_users extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'user_id' => $this->primaryKey(),
            'nama' => $this->string(150)->notNull(),
            'email' => $this->string(150)->notNull()->unique(),
            'username' => $this->string(100)->notNull()->unique(),
            'password' => $this->string(255)->notNull(),
            'role' => $this->string(50)->notNull(),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions);

        // self-referencing FK, aman ditambahkan setelah tabel dibuat
        $this->addForeignKey('fk_users_created_by', '{{%users}}', 'created_by', '{{%users}}', 'user_id');
        $this->addForeignKey('fk_users_updated_by', '{{%users}}', 'updated_by', '{{%users}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_users_updated_by', '{{%users}}');
        $this->dropForeignKey('fk_users_created_by', '{{%users}}');
        $this->dropTable('{{%users}}');
    }
}
