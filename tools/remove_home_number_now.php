<?php
$dbPath = __DIR__ . '/../writable/database/v-track.db';
if (!file_exists($dbPath)) { echo "DB not found\n"; exit(1);} 
try {
    $db = new PDO('sqlite:'.$dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Starting removal of home_number...\n";
    $sql = <<<'SQL'
BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS homes_new (
    id INTEGER PRIMARY KEY,
    address TEXT NOT NULL,
    road_id INTEGER NULL,
    sub_road_id INTEGER NULL,
    address_id INTEGER NULL,
    no_of_members INTEGER,
    has_assessment TEXT,
    assessment_number TEXT,
    resident_type TEXT,
    waste_disposal TEXT
);
INSERT INTO homes_new (id, address, road_id, sub_road_id, address_id, no_of_members, has_assessment, assessment_number, resident_type, waste_disposal)
    SELECT id, address, road_id, sub_road_id, address_id, no_of_members, has_assessment, assessment_number, resident_type, waste_disposal FROM homes;
DROP TABLE homes;
ALTER TABLE homes_new RENAME TO homes;
COMMIT;
SQL;
    $db->exec($sql);
    echo "Done.\n";
    $stmt = $db->query("SELECT sql FROM sqlite_master WHERE name='homes'");
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "New homes create SQL:\n" . $r['sql'] . "\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
