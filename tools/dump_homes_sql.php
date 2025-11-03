<?php
$dbPath = __DIR__ . '/../writable/database/v-track.db';
if (!file_exists($dbPath)) { echo "DB not found\n"; exit(1);} 
try {
    $db = new PDO('sqlite:'.$dbPath);
    $stmt = $db->query("SELECT sql FROM sqlite_master WHERE name='homes'");
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($r);
} catch (Exception $e) { echo $e->getMessage(); }
