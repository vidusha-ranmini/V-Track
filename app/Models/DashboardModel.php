<?php
namespace App\Models;

use CodeIgniter\Model;

class DashboardModel
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function getOccupationCounts(): array
    {
        $builder = $this->db->table('members');
        $rows = $builder->select('occupation, COUNT(*) AS cnt')
            ->groupBy('occupation')
            ->orderBy('cnt', 'DESC')
            ->get()
            ->getResultArray();

        $labels = [];
        $data = [];
        foreach ($rows as $r) {
            $labels[] = $r['occupation'] ?: 'Unknown';
            $data[] = (int) $r['cnt'];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getAgeBuckets(): array
    {
        // Use aggregate SUM(CASE ...) to get counts per bucket
        $sql = "SELECT
            SUM(CASE WHEN age BETWEEN 0 AND 5 THEN 1 ELSE 0 END) AS a0_5,
            SUM(CASE WHEN age BETWEEN 6 AND 18 THEN 1 ELSE 0 END) AS a6_18,
            SUM(CASE WHEN age BETWEEN 19 AND 30 THEN 1 ELSE 0 END) AS a19_30,
            SUM(CASE WHEN age BETWEEN 31 AND 50 THEN 1 ELSE 0 END) AS a31_50,
            SUM(CASE WHEN age BETWEEN 51 AND 70 THEN 1 ELSE 0 END) AS a51_70,
            SUM(CASE WHEN age >= 71 THEN 1 ELSE 0 END) AS a71
            FROM members";

        $row = $this->db->query($sql)->getRowArray();

        $labels = ['0-5','6-18','19-30','31-50','51-70','71+'];
        $data = [
            (int) ($row['a0_5'] ?? 0),
            (int) ($row['a6_18'] ?? 0),
            (int) ($row['a19_30'] ?? 0),
            (int) ($row['a31_50'] ?? 0),
            (int) ($row['a51_70'] ?? 0),
            (int) ($row['a71'] ?? 0),
        ];

        return ['labels' => $labels, 'data' => $data];
    }

    public function getResidentCounts(): array
    {
        $rows = $this->db->table('homes')
            ->select('resident_type, COUNT(*) AS cnt')
            ->groupBy('resident_type')
            ->get()
            ->getResultArray();

        $labels = [];$data = [];
        foreach ($rows as $r) { $labels[] = $r['resident_type']; $data[] = (int)$r['cnt']; }
        return ['labels' => $labels, 'data' => $data];
    }

    public function getWasteCounts(): array
    {
        $rows = $this->db->table('homes')
            ->select('waste_disposal, COUNT(*) AS cnt')
            ->groupBy('waste_disposal')
            ->get()
            ->getResultArray();

        $labels = [];$data = [];
        foreach ($rows as $r) { $labels[] = $r['waste_disposal']; $data[] = (int)$r['cnt']; }
        return ['labels' => $labels, 'data' => $data];
    }

    public function getOfferCounts(): array
    {
        $rows = $this->db->table('member_offers')
            ->select('offer, COUNT(*) AS cnt')
            ->groupBy('offer')
            ->get()
            ->getResultArray();

        $labels = [];$data = [];
        foreach ($rows as $r) { $labels[] = $r['offer']; $data[] = (int)$r['cnt']; }
        return ['labels' => $labels, 'data' => $data];
    }

    public function getDisabledCounts(): array
    {
        $rows = $this->db->table('members')
            ->select('disabled, COUNT(*) AS cnt')
            ->groupBy('disabled')
            ->get()
            ->getResultArray();

        $labels = [];$data = [];
        foreach ($rows as $r) { $labels[] = $r['disabled']; $data[] = (int)$r['cnt']; }
        return ['labels' => $labels, 'data' => $data];
    }
}
