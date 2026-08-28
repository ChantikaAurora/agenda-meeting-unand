<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%absensi}}`.
 * Has foreign keys to `{{%agenda}}` and `{{%member}}`.
 */
class m260828_000008_create_table_absensi extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%absensi}}', [
            'absensi_id' => $this->primaryKey(),
            'agenda_id' => $this->integer()->notNull(),
            'member_id' => $this->integer()->null(),
            'tipe_identitas' => $this->string(50)->notNull(),
            'identitas_number' => $this->string(100)->notNull(),
            'jabatan' => $this->string(100)->null(),
            'instansi' => $this->string(150)->null(),
            'nama' => $this->string(150)->notNull(),
            'email' => $this->string(150)->null(),
            'data_tambahan' => $this->string(255)->null(),
            'tanda_tangan_path' => $this->string(255)->notNull(),
            'waktu_scan' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'ip_address' => $this->string(45)->null(),
            'device_info' => $this->string(255)->null(),
            'deleted_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey('fk_absensi_agenda', '{{%absensi}}', 'agenda_id', '{{%agenda}}', 'agenda_id');
        $this->addForeignKey('fk_absensi_member', '{{%absensi}}', 'member_id', '{{%member}}', 'member_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_absensi_member', '{{%absensi}}');
        $this->dropForeignKey('fk_absensi_agenda', '{{%absensi}}');
        $this->dropTable('{{%absensi}}');
    }
}
