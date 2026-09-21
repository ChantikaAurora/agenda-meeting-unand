<?php

use yii\db\Migration;

class m260911_000014_add_more_members_to_evaluation_agenda extends Migration
{
    public function safeUp()
    {
        $agendaId = (int) $this->db->createCommand(
            "SELECT agenda_id FROM {{%agenda}} WHERE nomor_surat = '002/UN16/RPT/2026' AND deleted_at IS NULL LIMIT 1"
        )->queryScalar();
        $adminId = (int) $this->db->createCommand(
            "SELECT user_id FROM {{%users}} WHERE username = 'admin' LIMIT 1"
        )->queryScalar();

        if ($agendaId === 0 || $adminId === 0) {
            return;
        }

        $members = [
            [
                'nama' => 'Dwi Wulan Suci, S.E.',
                'jabatan' => 'Kepala Subbagian Akademik',
                'instansi' => 'Direktorat Akademik Unand',
                'email' => 'dwi.wulan@unand.ac.id',
            ],
            [
                'nama' => 'Rina Marlina, S.Sos.',
                'jabatan' => 'Koordinator Administrasi',
                'instansi' => 'Direktorat Akademik Unand',
                'email' => 'rina.marlina@unand.ac.id',
            ],
            [
                'nama' => 'Fajar Hidayat, S.Kom.',
                'jabatan' => 'Analis Data Akademik',
                'instansi' => 'Direktorat Akademik Unand',
                'email' => 'fajar.hidayat@unand.ac.id',
            ],
            [
                'nama' => 'Nadia Putri, M.M.',
                'jabatan' => 'Staf Perencanaan',
                'instansi' => 'Direktorat Akademik Unand',
                'email' => 'nadia.putri@unand.ac.id',
            ],
        ];

        foreach ($members as $member) {
            $this->insert('{{%member}}', $member + [
                'created_by' => $adminId,
            ]);
            $memberId = (int) $this->db->getLastInsertID();

            $this->insert('{{%agenda_member}}', [
                'agenda_id' => $agendaId,
                'member_id' => $memberId,
                'peran' => 'peserta',
                'created_by' => $adminId,
            ]);
        }
    }

    public function safeDown()
    {
        $agendaId = (int) $this->db->createCommand(
            "SELECT agenda_id FROM {{%agenda}} WHERE nomor_surat = '002/UN16/RPT/2026' LIMIT 1"
        )->queryScalar();
        $emails = [
            'dwi.wulan@unand.ac.id',
            'rina.marlina@unand.ac.id',
            'fajar.hidayat@unand.ac.id',
            'nadia.putri@unand.ac.id',
        ];

        if ($agendaId === 0) {
            return;
        }

        $memberIds = (new \yii\db\Query())
            ->select('member_id')
            ->from('{{%member}}')
            ->where(['email' => $emails])
            ->column($this->db);

        $this->delete('{{%agenda_member}}', [
            'agenda_id' => $agendaId,
            'member_id' => $memberIds,
        ]);
        $this->delete('{{%member}}', ['member_id' => $memberIds]);
    }
}
