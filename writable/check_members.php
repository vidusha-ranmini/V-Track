<?php
try {
    if (!defined('WRITEPATH')) {
        define('WRITEPATH', __DIR__ . DIRECTORY_SEPARATOR);
    }
    $dbfile = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'v-track.db') ?: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'v-track.db';
    echo "dbfile: " . $dbfile . PHP_EOL;
    $db = new PDO('sqlite:' . $dbfile);
    $q = $db->query('SELECT id, full_name FROM members ORDER BY id');
    if ($q === false) {
        echo "NO_MEMBERS_TABLE\n";
        exit(0);
    }
    $rows = $q->fetchAll(PDO::FETCH_ASSOC);
    echo "members_count:" . count($rows) . PHP_EOL;
    foreach ($rows as $r) {
        echo "id=" . $r['id'] . " name=" . $r['full_name'] . PHP_EOL;
    }
} catch (Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . PHP_EOL;
    exit(2);
}
