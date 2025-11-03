<?php
$dbPath = __DIR__ . '/../writable/database/v-track.db';
if (!file_exists($dbPath)) {
    echo "DB file not found: $dbPath\n";
    exit(1);
}
try {
    $db = new PDO('sqlite:' . $dbPath);
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!$tables) {
        echo "No tables found\n";
    } else {
        foreach ($tables as $t) echo $t . "\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(2);
}
