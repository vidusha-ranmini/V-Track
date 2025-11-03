<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoadsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Helper to insert road and return id
        $roads = [
            '979 Main road',
            '979 Side road',
            '223 Main road',
            '223 Side road',
            'Korala maima main road',
            'Korala maima side road',
            'Maddegoda polhena main road',
            'Maddegoda polhena side road',
            'Praja mandala para main road',
            'Praja mandala para side road',
            '327 Main road',
            '327 Side road',
        ];

        $roadIds = [];
        foreach ($roads as $r) {
            $db->table('roads')->insert(['name' => $r, 'created_at' => date('Y-m-d H:i:s')]);
            $roadIds[$r] = $db->insertID();
        }

        // Sub-roads for side roads
        $subRoads = [
            '979 Side road' => [
                '979 1st lane','979 2nd lane','979 3rd lane','Selinco Waththa','979 4th lane','Haritha uyana','979 5th lane','Sisla uyana','Jaya mawatha','979 6th Lane','979 7th lane','Seram lane','979 8th Lane','979 9th lane','Golad lane','pragathi mawatha','Sisira mawatha','Metro Niwas road','Green Lane','Ranawiru Chrandrakumara mawatha','979 10th lane','979 11 lane'
            ],
            '223 Side road' => [
                '223 1st lane','223 2nd lane','223 3rd lane','223 4th lane','Gorak gaha handiya para','Daham mawatha','suhada mawatha II','223 8th lane','223 9th lane','223 10th lane','223 11 lane'
            ],
            'Korala maima side road' => [
                'Welamada Para(Alubogahawaththa)','Korala maima 1st lane','Korala maima 2nd road','Annasiwaththa para','Pokuna para','Moragahalandha para','Korala maima 3rd lane','Korala maima 4th lane','rubber waththa para','Mudhaleege para'
            ],
            'Maddegoda polhena side road' => [
                'Ranawiru Kapila Bandara mawatha','Maddegoda 1st lane','Maddegoda 2nd lane','Dewala para','Alubogahawaththa para','Dunkolamaduwa Para','Maddegoda 3rd lane','Maddegoda 4th lane','Maddegoda 5th lane','Maddegoda 6th lane'
            ],
            'Praja mandala para side road' => [
                'Suhada Mawatha I','Mangala mawatha','prajamadala 2nd lane'
            ],
            '327 Side road' => [
                '327 1st lane','Kurudugaha waththa para'
            ],
        ];

        $subIds = [];
        foreach ($subRoads as $road => $subs) {
            $rid = $roadIds[$road] ?? null;
            if (!$rid) continue;
            foreach ($subs as $s) {
                $db->table('sub_roads')->insert(['road_id' => $rid, 'name' => $s, 'created_at' => date('Y-m-d H:i:s')]);
                $subIds[$s] = $db->insertID();
            }
        }

        // Addresses: sample addresses for roads and sub-roads
        $addresses = [];
        // Add sample addresses for main roads
        foreach (['979 Main road','223 Main road','Korala maima main road','Maddegoda polhena main road','Praja mandala para main road','327 Main road'] as $mr) {
            $rid = $roadIds[$mr] ?? null;
            if (!$rid) continue;
            for ($i=1;$i<=3;$i++) {
                $addresses[] = ['road_id' => $rid, 'sub_road_id' => null, 'address' => $mr . ' - Sample ' . $i, 'created_at' => date('Y-m-d H:i:s')];
            }
        }

        // Sample addresses for side roads and their sub-lanes
        foreach ($subIds as $subName => $sid) {
            for ($i=1;$i<=2;$i++) {
                $addresses[] = ['road_id' => null, 'sub_road_id' => $sid, 'address' => $subName . ' - House ' . ($i*10), 'created_at' => date('Y-m-d H:i:s')];
            }
        }

        // Also add a couple of addresses directly tied to side road parent
        foreach (['979 Side road','223 Side road','Korala maima side road','Maddegoda polhena side road','Praja mandala para side road','327 Side road'] as $sr) {
            $rid = $roadIds[$sr] ?? null;
            if (!$rid) continue;
            $addresses[] = ['road_id' => $rid, 'sub_road_id' => null, 'address' => $sr . ' - Block 1', 'created_at' => date('Y-m-d H:i:s')];
            $addresses[] = ['road_id' => $rid, 'sub_road_id' => null, 'address' => $sr . ' - Block 2', 'created_at' => date('Y-m-d H:i:s')];
        }

        // Insert all addresses
        if (!empty($addresses)) {
            $db->table('addresses')->insertBatch($addresses);
        }
    }
}
