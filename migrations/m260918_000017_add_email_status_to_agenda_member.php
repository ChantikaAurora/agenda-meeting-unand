<?php

use yii\db\Migration;

/**
 * Menambahkan pelacakan status pengiriman email undangan langsung di
 * `agenda_member` -- SENGAJA tidak memakai tabel `email_log` yang sudah ada,
 * karena kolom `email_log.lampiran_id` bersifat NOT NULL (dirancang khusus
 * untuk fitur kirim lampiran/notulen, lihat LampiranController & fitur
 * "Kirim Email Lampiran"). Undangan rapat biasanya dikirim SEBELUM lampiran
 * ada, jadi tidak bisa dan tidak seharusnya memakai tabel yang sama.
 */
class m260918_000017_add_email_status_to_agenda_member extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            '{{%agenda_member}}',
            'email_status',
            "ENUM('belum_terkirim','terkirim','gagal') NOT NULL DEFAULT 'belum_terkirim' AFTER peran"
        );
        $this->addColumn(
            '{{%agenda_member}}',
            'email_sent_at',
            $this->dateTime()->null()->after('email_status')
        );
    }

    public function safeDown()
    {
        $this->dropColumn('{{%agenda_member}}', 'email_sent_at');
        $this->dropColumn('{{%agenda_member}}', 'email_status');
    }
}
