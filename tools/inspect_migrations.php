<?php
$dbPath = __DIR__ . '/../writable/database/v-track.db';
if (!file_exists($dbPath)) { echo "DB file not found: $dbPath\n"; exit(1);} 
try {
    $db = new PDO('sqlite:' . $dbPath);
    echo "=== migrations table info ===\n";
    $cols = $db->query("PRAGMA table_info(migrations)")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) echo $c['cid'] . ' ' . $c['name'] . ' ' . $c['type'] . "\n";
    echo "\n=== migrations rows ===\n";
    $rows = $db->query("SELECT * FROM migrations")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) { print_r($r); echo "\n"; }
} catch (Exception $e) { echo 'Error: ' . $e->getMessage() . "\n"; exit(2); }
