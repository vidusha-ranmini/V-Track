<?php
$dbPath = __DIR__ . '/../writable/database/v-track.db';
if (!file_exists($dbPath)) { echo "DB file not found\n"; exit(1);} try { $db = new PDO('sqlite:'.$dbPath); $cols = $db->query('PRAGMA table_info(homes)')->fetchAll(PDO::FETCH_ASSOC); if (!$cols) { echo "no homes table or no cols\n"; exit; } foreach ($cols as $c) { echo $c['cid'].' '. $c['name'] .' '. $c['type'] . PHP_EOL; } } catch (Exception $e) { echo $e->getMessage(); }
