<?php
// Simple SQLite -> MySQL migration script
// Edit the $mysqlConfig below with your MySQL credentials before running.
// Usage: php migrate_sqlite_to_mysql.php

$mysqlConfig = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'user' => 'root',
    'pass' => '',
    'db'   => 'v_track',
    'charset' => 'utf8mb4',
];

// Path to the SQLite DB file used by the app
$sqlitePath = __DIR__ . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'v-track.db';

if (!file_exists($sqlitePath)) {
    echo "SQLite DB not found at: $sqlitePath\n";
    exit(1);
}

// Ensure extensions exist
if (!class_exists('SQLite3')) {
    echo "SQLite3 extension not available in PHP.\n";
    exit(1);
}
if (!function_exists('mysqli_connect')) {
    echo "mysqli extension not available in PHP CLI. Enable mysqli/pdo_mysql in php.ini.\n";
    exit(1);
}

// Connect to SQLite
$sqlite = new SQLite3($sqlitePath, SQLITE3_OPEN_READONLY);
if (!$sqlite) {
    echo "Failed to open SQLite DB.\n";
    exit(1);
}

// Connect to MySQL
$mysqli = new mysqli($mysqlConfig['host'], $mysqlConfig['user'], $mysqlConfig['pass'], $mysqlConfig['db'], $mysqlConfig['port']);
if ($mysqli->connect_errno) {
    echo "MySQL connection failed: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n";
    exit(1);
}
$mysqli->set_charset($mysqlConfig['charset']);

echo "Connected to MySQL {$mysqlConfig['db']}@{$mysqlConfig['host']}:{$mysqlConfig['port']}\n";

// Get tables from SQLite
$tablesRes = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
$tables = [];
while ($row = $tablesRes->fetchArray(SQLITE3_ASSOC)) {
    $tables[] = $row['name'];
}

if (empty($tables)) {
    echo "No user tables found in SQLite DB. Nothing to migrate.\n";
    exit(0);
}

echo "Tables found in SQLite: " . implode(', ', $tables) . "\n";

foreach ($tables as $table) {
    echo "\n--- Migrating table: $table\n";

    // Get columns from SQLite
    $colRes = $sqlite->query("PRAGMA table_info('" . SQLite3::escapeString($table) . "')");
    $cols = [];
    while ($c = $colRes->fetchArray(SQLITE3_ASSOC)) {
        $cols[] = $c['name'];
    }
    if (empty($cols)) {
        echo "  No columns found for $table, skipping.\n";
        continue;
    }

    // Check if table exists in MySQL
    $escapedTable = $mysqli->real_escape_string($table);
    $chk = $mysqli->query("SHOW TABLES LIKE '{$escapedTable}'");
    if (!$chk || $chk->num_rows == 0) {
        echo "  Table '$table' does not exist in MySQL database '{$mysqlConfig['db']}', skipping.\n";
        continue;
    }

    // Build INSERT statement for MySQL using the intersection of columns that exist in MySQL
    $mysqlColsRes = $mysqli->query("SHOW COLUMNS FROM `{$escapedTable}`");
    $mysqlCols = [];
    while ($mc = $mysqlColsRes->fetch_assoc()) {
        $mysqlCols[] = $mc['Field'];
    }
    $intersectCols = array_values(array_intersect($cols, $mysqlCols));
    if (empty($intersectCols)) {
        echo "  No matching columns between SQLite and MySQL for table '$table', skipping.\n";
        continue;
    }

    $colList = implode('`,`', array_map(function($c){return $c;}, $intersectCols));
    $placeholders = implode(',', array_fill(0, count($intersectCols), '?'));
    $insertSql = "INSERT INTO `{$escapedTable}` (`{$colList}`) VALUES ({$placeholders})";

    echo "  Columns to copy: " . implode(', ', $intersectCols) . "\n";
    echo "  Using insert: $insertSql\n";

    // Prepare statement
    $stmt = $mysqli->prepare($insertSql);
    if (!$stmt) {
        echo "  Failed to prepare insert statement: {$mysqli->error}\n";
        continue;
    }

    // Determine types for bind_param (all strings by default 's')
    $types = str_repeat('s', count($intersectCols));

    // Read rows from SQLite and insert into MySQL in a transaction
    $sqliteCountRes = $sqlite->querySingle("SELECT COUNT(*) AS cnt FROM \"$table\"", true);
    $rowCount = is_array($sqliteCountRes) ? (int)$sqliteCountRes['cnt'] : (int)$sqliteCountRes;
    echo "  Rows to migrate: {$rowCount}\n";

    $batch = 0;
    $migrated = 0;

    $mysqli->begin_transaction();
    try {
        $q = $sqlite->query('SELECT * FROM "' . SQLite3::escapeString($table) . '"');
        while ($r = $q->fetchArray(SQLITE3_ASSOC)) {
            // Build ordered values for intersectCols
            $vals = [];
            foreach ($intersectCols as $c) {
                $vals[] = isset($r[$c]) ? $r[$c] : null;
            }
            // bind params dynamically
            $bindNames = [];
            $bindNames[] = $types;
            for ($i=0;$i<count($vals);$i++) {
                $bindNames[] = &$vals[$i];
            }
            // call_user_func_array requires references
            call_user_func_array(array($stmt, 'bind_param'), $bindNames);
            if (!$stmt->execute()) {
                // on duplicate/other errors, print and continue
                echo "    Insert failed: ({$stmt->errno}) {$stmt->error}\n";
                continue;
            }
            $migrated++;
            $batch++;
            if ($batch % 100 == 0) echo "    Migrated {$migrated} rows...\n";
        }
        $mysqli->commit();
        echo "  Finished migrating table '$table'. Migrated: {$migrated} rows.\n";
    } catch (Exception $ex) {
        $mysqli->rollback();
        echo "  Exception during migration: {$ex->getMessage()}\n";
    }

    $stmt->close();
}

echo "\nMigration complete.\n";

$sqlite->close();
$mysqli->close();

return 0;
