<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lokasi}}`.
 * Has foreign keys to `{{%unit}}` and `{{%users}}`.
 */
class m260828_000003_create_table_lokasi extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lokasi}}', [
            'lokasi_id' => $this->primaryKey(),
            'unit_id' => $this->integer()->notNull(),
            'kategori_lokasi' => $this->string(100)->notNull(),
            'lokasi' => $this->string(150)->notNull(),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions);

        $this->addForeignKey('fk_lokasi_unit', '{{%lokasi}}', 'unit_id', '{{%unit}}', 'unit_id');
        $this->addForeignKey('fk_lokasi_created_by', '{{%lokasi}}', 'created_by', '{{%users}}', 'user_id');
        $this->addForeignKey('fk_lokasi_updated_by', '{{%lokasi}}', 'updated_by', '{{%users}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_lokasi_updated_by', '{{%lokasi}}');
        $this->dropForeignKey('fk_lokasi_created_by', '{{%lokasi}}');
        $this->dropForeignKey('fk_lokasi_unit', '{{%lokasi}}');
        $this->dropTable('{{%lokasi}}');
    }
}
