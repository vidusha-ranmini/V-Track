<?php
$dbPath = __DIR__ . '/../writable/database/v-track.db';
if (!file_exists($dbPath)) { echo "DB file not found: $dbPath\n"; exit(1);} 
try {
    $db = new PDO('sqlite:' . $dbPath);
    $stmt = $db->query("SELECT * FROM migrations ORDER BY timestamp");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) { echo "No migrations rows\n"; } else { foreach ($rows as $r) { echo $r['version'] . " | " . ($r['timestamp'] ?? '') . "\n"; } }
} catch (Exception $e) { echo 'Error: ' . $e->getMessage() . "\n"; exit(2); }
