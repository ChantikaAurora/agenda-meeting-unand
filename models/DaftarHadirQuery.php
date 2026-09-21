<?php

declare(strict_types=1);

namespace app\models;

use yii\db\Query;

class DaftarHadirQuery
{
    /**
     * @param array $filter ['agenda_id' => string, 'status' => string, 'q' => string]
     * @return array<int, array<string, mixed>>
     */
    public static function fetch(array $filter): array
    {
        $agendaId = (string) ($filter['agenda_id'] ?? '');
        $status = (string) ($filter['status'] ?? '');
        $q = trim((string) ($filter['q'] ?? ''));

        $rows = [];

        if ($status !== 'walk_in') {
            $rows = array_merge($rows, self::undanganRows($agendaId, $status, $q));
        }

        if ($status !== 'tidak_hadir') {
            $rows = array_merge($rows, self::walkInRows($agendaId, $q));
        }

        usort($rows, static function (array $a, array $b): int {
            return [$b['tanggal'], $a['nama']] <=> [$a['tanggal'], $b['nama']];
        });

        return $rows;
    }

    /**
     * Ringkasan angka untuk kartu statistik di atas tabel.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return array{total:int, hadir:int, tidak_hadir:int, walk_in:int}
     */
    public static function summarize(array $rows): array
    {
        $hadir = 0;
        $walkIn = 0;

        foreach ($rows as $row) {
            if ($row['absensi_id'] !== null) {
                $hadir++;
            }
            if ($row['sumber'] === 'walk_in') {
                $walkIn++;
            }
        }

        return [
            'total' => count($rows),
            'hadir' => $hadir,
            'tidak_hadir' => count($rows) - $hadir,
            'walk_in' => $walkIn,
        ];
    }

    private static function undanganRows(string $agendaId, string $status, string $q): array
    {
        $query = (new Query())
            ->select([
                'agenda_id' => 'am.agenda_id',
                'member_id' => 'am.member_id',
                'nama' => 'm.nama',
                'identitas_number' => 'm.identitas_number',
                'instansi' => 'm.instansi',
                'jabatan' => 'm.jabatan',
                'pembahasan' => 'ag.pembahasan',
                'tanggal' => 'ag.tanggal',
                'absensi_id' => 'ab.absensi_id',
                'waktu_scan' => 'ab.waktu_scan',
                'tanda_tangan_path' => 'ab.tanda_tangan_path',
                'sumber' => new \yii\db\Expression("'undangan'"),
            ])
            ->from(['am' => 'agenda_member'])
            ->innerJoin(['m' => 'member'], 'm.member_id = am.member_id')
            ->innerJoin(['ag' => 'agenda'], 'ag.agenda_id = am.agenda_id')
            ->leftJoin(
                ['ab' => 'absensi'],
                'ab.agenda_id = am.agenda_id
                 AND ab.deleted_at IS NULL
                 AND (
                     ab.member_id = am.member_id
                     OR (
                         m.identitas_number IS NOT NULL
                         AND m.identitas_number <> ""
                         AND ab.identitas_number = m.identitas_number
                     )
                 )'
            )
            ->where([
                'am.deleted_at' => null,
                'ag.deleted_at' => null,
            ]);

        if ($agendaId !== '') {
            $query->andWhere(['am.agenda_id' => $agendaId]);
        }

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'm.nama', $q],
                ['like', 'm.identitas_number', $q],
            ]);
        }

        if ($status === 'hadir') {
            $query->andWhere(['is not', 'ab.absensi_id', null]);
        } elseif ($status === 'tidak_hadir') {
            $query->andWhere(['ab.absensi_id' => null]);
        }

        return $query->all();
    }

    private static function walkInRows(string $agendaId, string $q): array
    {
        $query = (new Query())
            ->select([
                'agenda_id' => 'ab.agenda_id',
                'member_id' => 'ab.member_id',
                'nama' => 'ab.nama',
                'identitas_number' => 'ab.identitas_number',
                'instansi' => 'ab.instansi',
                'jabatan' => 'ab.jabatan',
                'pembahasan' => 'ag.pembahasan',
                'tanggal' => 'ag.tanggal',
                'absensi_id' => 'ab.absensi_id',
                'waktu_scan' => 'ab.waktu_scan',
                'tanda_tangan_path' => 'ab.tanda_tangan_path',
                'sumber' => new \yii\db\Expression("'walk_in'"),
            ])
            ->from(['ab' => 'absensi'])
            ->innerJoin(['ag' => 'agenda'], 'ag.agenda_id = ab.agenda_id')
            ->where([
                'ab.deleted_at' => null,
                'ag.deleted_at' => null,
            ])

            ->andWhere(
                'NOT EXISTS (
                    SELECT 1
                    FROM agenda_member am2
                    LEFT JOIN member m2 ON m2.member_id = am2.member_id
                    WHERE am2.agenda_id = ab.agenda_id
                      AND am2.deleted_at IS NULL
                      AND (
                          am2.member_id = ab.member_id
                          OR (
                              m2.identitas_number IS NOT NULL
                              AND m2.identitas_number <> ""
                              AND m2.identitas_number = ab.identitas_number
                          )
                      )
                )'
            );

        if ($agendaId !== '') {
            $query->andWhere(['ab.agenda_id' => $agendaId]);
        }

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'ab.nama', $q],
                ['like', 'ab.identitas_number', $q],
            ]);
        }

        return $query->all();
    }
}
