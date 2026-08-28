<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%agenda}}`.
 * Has foreign keys to `{{%lokasi}}` and `{{%users}}`.
 */
class m260828_000004_create_table_agenda extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%agenda}}', [
            'agenda_id' => $this->primaryKey(),
            'nomor_surat' => $this->string(100)->null(),
            'pembahasan' => $this->string(255)->notNull(),
            'deskripsi' => $this->text()->null(),
            'tanggal' => $this->date()->notNull(),
            'tahun_akademik' => $this->string(20)->notNull(),
            'waktu_mulai' => $this->time()->notNull(),
            'waktu_selesai' => $this->time()->notNull(),
            'lokasi_id' => $this->integer()->notNull(),
            'qr_code_value' => $this->string(255)->null(),
            'qr_code_path' => $this->string(255)->null(),
            'status' => $this->string(50)->notNull(),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey('fk_agenda_lokasi', '{{%agenda}}', 'lokasi_id', '{{%lokasi}}', 'lokasi_id');
        $this->addForeignKey('fk_agenda_created_by', '{{%agenda}}', 'created_by', '{{%users}}', 'user_id');
        $this->addForeignKey('fk_agenda_updated_by', '{{%agenda}}', 'updated_by', '{{%users}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_agenda_updated_by', '{{%agenda}}');
        $this->dropForeignKey('fk_agenda_created_by', '{{%agenda}}');
        $this->dropForeignKey('fk_agenda_lokasi', '{{%agenda}}');
        $this->dropTable('{{%agenda}}');
    }
}
