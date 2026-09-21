<?php

use yii\db\Migration;

/**
 * Dukungan untuk status agenda otomatis:
 *  - activity_log.performed_by boleh NULL, karena pelaku perubahan bisa sistem
 *    (cron), bukan manusia. Menuliskan user_id acak di sini akan merusak
 *    nilai jejak audit.
 *  - index pada agenda untuk mempercepat query kandidat sinkronisasi.
 */
class m260921_000018_support_auto_agenda_status extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey('fk_activitylog_performed_by', '{{%activity_log}}');
        $this->alterColumn('{{%activity_log}}', 'performed_by', $this->integer()->null());
        $this->addForeignKey(
            'fk_activitylog_performed_by',
            '{{%activity_log}}',
            'performed_by',
            '{{%users}}',
            'user_id'
        );

        $this->createIndex(
            'idx_agenda_status_jadwal',
            '{{%agenda}}',
            ['deleted_at', 'status', 'tanggal']
        );
    }

    public function safeDown()
    {
        $this->dropIndex('idx_agenda_status_jadwal', '{{%agenda}}');

        $this->dropForeignKey('fk_activitylog_performed_by', '{{%activity_log}}');
        $this->alterColumn('{{%activity_log}}', 'performed_by', $this->integer()->notNull());
        $this->addForeignKey(
            'fk_activitylog_performed_by',
            '{{%activity_log}}',
            'performed_by',
            '{{%users}}',
            'user_id'
        );
    }
}
