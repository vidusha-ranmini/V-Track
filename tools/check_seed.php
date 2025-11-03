<?php
$dbPath = __DIR__ . '/../writable/database/v-track.db';
if (!file_exists($dbPath)) { echo "DB file not found: $dbPath\n"; exit(1);} 
try {
    $db = new PDO('sqlite:' . $dbPath);
    $tables = ['roads','sub_roads','addresses'];
    foreach ($tables as $t) {
        $cnt = $db->query("SELECT COUNT(*) as c FROM $t")->fetch(PDO::FETCH_ASSOC);
        echo "$t: " . ($cnt['c'] ?? 0) . " rows\n";
    }
} catch (Exception $e) { echo 'Error: ' . $e->getMessage() . "\n"; exit(2); }
