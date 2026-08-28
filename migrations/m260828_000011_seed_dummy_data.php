<?php

use yii\db\Migration;

/**
 * Seeder data dummy untuk keperluan development & testing.
 * Data disusun urut sesuai dependency FK (Tier 1 -> Tier 2 -> Tier 3 -> cross-cutting).
 */
class m260828_000011_seed_dummy_data extends Migration
{
    public function safeUp()
    {
        // ================= USERS =================
        $this->insert('{{%users}}', [
            'nama' => 'Chantika Aurora Akmal',
            'email' => 'aurora.admin@unand.ac.id',
            'username' => 'admin',
            'password' => Yii::$app->security->generatePasswordHash('admin123'),
            'role' => 'administrasi',
        ]);
        $adminId = (int) $this->db->getLastInsertID();

        $this->insert('{{%users}}', [
            'nama' => 'Dwi Wulan Suci',
            'email' => 'wulan.notulen@unand.ac.id',
            'username' => 'notulen1',
            'password' => Yii::$app->security->generatePasswordHash('notulen123'),
            'role' => 'notulen',
        ]);
        $notulenId = (int) $this->db->getLastInsertID();

        // ================= UNIT =================
        $this->insert('{{%unit}}', [
            'nama_unit' => 'Fakultas Teknik',
            'kategori_unit' => 'fakultas',
            'created_by' => $adminId,
        ]);
        $unitFT = (int) $this->db->getLastInsertID();

        $this->insert('{{%unit}}', [
            'nama_unit' => 'Direktorat Akademik',
            'kategori_unit' => 'direktorat',
            'created_by' => $adminId,
        ]);
        $unitDirektorat = (int) $this->db->getLastInsertID();

        $this->insert('{{%unit}}', [
            'nama_unit' => 'UPT Perpustakaan',
            'kategori_unit' => 'upt',
            'created_by' => $adminId,
        ]);
        $unitUPT = (int) $this->db->getLastInsertID();

        // ================= LOKASI =================
        $this->insert('{{%lokasi}}', [
            'unit_id' => $unitFT,
            'kategori_lokasi' => 'Ruang Sidang',
            'lokasi' => 'Ruang Sidang Dekanat FT Lantai 2',
            'created_by' => $adminId,
        ]);
        $lokasiFT = (int) $this->db->getLastInsertID();

        $this->insert('{{%lokasi}}', [
            'unit_id' => $unitDirektorat,
            'kategori_lokasi' => 'Aula',
            'lokasi' => 'Aula Rektorat Lantai 3',
            'created_by' => $adminId,
        ]);
        $lokasiAula = (int) $this->db->getLastInsertID();

        // ================= AGENDA =================
        $this->insert('{{%agenda}}', [
            'nomor_surat' => '001/UN16/RPT/2026',
            'pembahasan' => 'Rapat Koordinasi Kurikulum Prodi TRPL',
            'deskripsi' => 'Pembahasan penyesuaian kurikulum semester ganjil 2026/2027.',
            'tanggal' => '2026-09-10',
            'tahun_akademik' => '2026/2027',
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '11:00:00',
            'lokasi_id' => $lokasiFT,
            'qr_code_value' => 'AGD-' . bin2hex(random_bytes(8)),
            'status' => 'terjadwal',
            'created_by' => $adminId,
        ]);
        $agenda1 = (int) $this->db->getLastInsertID();

        $this->insert('{{%agenda}}', [
            'nomor_surat' => '002/UN16/RPT/2026',
            'pembahasan' => 'Rapat Evaluasi Kinerja Semester Ganjil',
            'deskripsi' => 'Evaluasi capaian kinerja unit kerja semester ganjil.',
            'tanggal' => '2026-09-15',
            'tahun_akademik' => '2026/2027',
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '15:00:00',
            'lokasi_id' => $lokasiAula,
            'qr_code_value' => 'AGD-' . bin2hex(random_bytes(8)),
            'status' => 'terjadwal',
            'created_by' => $adminId,
        ]);
        $agenda2 = (int) $this->db->getLastInsertID();

        // ================= MEMBER =================
        $this->insert('{{%member}}', [
            'nama' => 'Dr. Budi Santoso, M.T.',
            'jabatan' => 'Ketua Program Studi',
            'instansi' => 'Fakultas Teknik Unand',
            'tipe_identitas' => 'NIP',
            'identitas_number' => '198005152005011002',
            'email' => 'budi.santoso@unand.ac.id',
            'created_by' => $adminId,
        ]);
        $member1 = (int) $this->db->getLastInsertID();

        $this->insert('{{%member}}', [
            'nama' => 'Siti Rahayu, S.Kom., M.Kom.',
            'jabatan' => 'Sekretaris Prodi',
            'instansi' => 'Fakultas Teknik Unand',
            'tipe_identitas' => 'NIP',
            'identitas_number' => '198709232014042001',
            'email' => 'siti.rahayu@unand.ac.id',
            'created_by' => $adminId,
        ]);
        $member2 = (int) $this->db->getLastInsertID();

        $this->insert('{{%member}}', [
            'nama' => 'Ahmad Fauzi',
            'jabatan' => 'Staff Akademik',
            'instansi' => 'Direktorat Akademik Unand',
            'tipe_identitas' => 'NIP',
            'identitas_number' => '199203102019031005',
            'email' => 'ahmad.fauzi@unand.ac.id',
            'created_by' => $adminId,
        ]);
        $member3 = (int) $this->db->getLastInsertID();

        // ================= AGENDA_MEMBER =================
        $this->insert('{{%agenda_member}}', [
            'agenda_id' => $agenda1,
            'member_id' => $member1,
            'peran' => 'narasumber',
            'created_by' => $adminId,
        ]);
        $this->insert('{{%agenda_member}}', [
            'agenda_id' => $agenda1,
            'member_id' => $member2,
            'peran' => 'moderator',
            'created_by' => $adminId,
        ]);
        $this->insert('{{%agenda_member}}', [
            'agenda_id' => $agenda2,
            'member_id' => $member3,
            'peran' => 'peserta',
            'created_by' => $adminId,
        ]);

        // ================= LAMPIRAN =================
        $this->insert('{{%lampiran}}', [
            'agenda_id' => $agenda1,
            'jenis_lampiran' => 'notulen',
            'ringkasan' => 'Notulen hasil rapat koordinasi kurikulum Prodi TRPL.',
            'file_path' => 'uploads/notulen/notulen-agenda-1.pdf',
            'status' => 'draft',
            'uploaded_by' => $notulenId,
        ]);
        $lampiran1 = (int) $this->db->getLastInsertID();

        // ================= ABSENSI =================
        $this->insert('{{%absensi}}', [
            'agenda_id' => $agenda1,
            'member_id' => $member1,
            'tipe_identitas' => 'NIP',
            'identitas_number' => '198005152005011002',
            'jabatan' => 'Ketua Program Studi',
            'instansi' => 'Fakultas Teknik Unand',
            'nama' => 'Dr. Budi Santoso, M.T.',
            'email' => 'budi.santoso@unand.ac.id',
            'tanda_tangan_path' => 'uploads/ttd/ttd-absensi-1.png',
            'ip_address' => '127.0.0.1',
            'device_info' => 'Seeder dummy data',
        ]);

        // ================= EMAIL_LOG =================
        $this->insert('{{%email_log}}', [
            'lampiran_id' => $lampiran1,
            'member_id' => $member1,
            'nama' => 'Dr. Budi Santoso, M.T.',
            'email' => 'budi.santoso@unand.ac.id',
            'status' => 'terkirim',
            'sent_by' => $notulenId,
        ]);

        // ================= ACTIVITY_LOG =================
        $this->insert('{{%activity_log}}', [
            'table_name' => 'agenda',
            'record_id' => $agenda1,
            'action' => 'create',
            'performed_by' => $adminId,
            'performed_role' => 'administrasi',
            'new_value' => json_encode(['pembahasan' => 'Rapat Koordinasi Kurikulum Prodi TRPL']),
            'ip_address' => '127.0.0.1',
            'description' => 'Seeder dummy data: pembuatan agenda awal',
        ]);
    }

    public function safeDown()
    {
        // Hapus urut terbalik supaya tidak melanggar FK constraint
        $this->delete('{{%activity_log}}');
        $this->delete('{{%email_log}}');
        $this->delete('{{%absensi}}');
        $this->delete('{{%lampiran}}');
        $this->delete('{{%agenda_member}}');
        $this->delete('{{%member}}');
        $this->delete('{{%agenda}}');
        $this->delete('{{%lokasi}}');
        $this->delete('{{%unit}}');
        $this->delete('{{%users}}');
    }
}
