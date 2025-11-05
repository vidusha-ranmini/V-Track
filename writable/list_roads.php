<?php
try {
    $dbfile = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'v-track.db');
    if (!$dbfile) {
        echo "DB file not found\n"; exit(1);
    }
    $db = new PDO('sqlite:' . $dbfile);
    $roads = $db->query('SELECT id, name FROM roads ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
    $subroads = $db->query('SELECT id, road_id, name FROM sub_roads ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
    echo "roads: " . count($roads) . PHP_EOL;
    foreach ($roads as $r) echo "id={$r['id']} name={$r['name']}\n";
    echo "subroads: " . count($subroads) . PHP_EOL;
    foreach ($subroads as $s) echo "id={$s['id']} road_id={$s['road_id']} name={$s['name']}\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    exit(2);
}
