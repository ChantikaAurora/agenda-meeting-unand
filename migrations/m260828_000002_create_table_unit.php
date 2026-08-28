<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%unit}}`.
 * Has foreign keys to `{{%users}}`.
 */
class m260828_000002_create_table_unit extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%unit}}', [
            'unit_id' => $this->primaryKey(),
            'nama_unit' => $this->string(150)->notNull(),
            'kategori_unit' => "ENUM('fakultas','direktorat','lembaga','upt') NOT NULL",
            'created_by' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions);

        $this->addForeignKey('fk_unit_created_by', '{{%unit}}', 'created_by', '{{%users}}', 'user_id');
        $this->addForeignKey('fk_unit_updated_by', '{{%unit}}', 'updated_by', '{{%users}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_unit_updated_by', '{{%unit}}');
        $this->dropForeignKey('fk_unit_created_by', '{{%unit}}');
        $this->dropTable('{{%unit}}');
    }
}
