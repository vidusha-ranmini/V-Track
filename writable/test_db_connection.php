<?php
// Quick DB connection test script - reads Database.php config manually without CI framework

// Read Database.php and extract mysql connection array
$dbConfigPath = dirname(__DIR__) . '/app/Config/Database.php';
$dbContent = file_get_contents($dbConfigPath);

// Extract mysql connection config using regex
preg_match('/public\s+array\s+\$mysql\s*=\s*\[(.*?)\];/s', $dbContent, $matches);
if (!isset($matches[1])) {
    echo "[FAIL] Could not parse mysql config from Database.php\n";
    exit(1);
}

// Parse the array content manually (simple approach)
$configText = $matches[1];
preg_match("/'hostname'\s*=>\s*'([^']*)'/",$configText, $h); $hostname = $h[1] ?? '127.0.0.1';
preg_match("/'username'\s*=>\s*'([^']*)'/",$configText, $u); $username = $u[1] ?? 'root';
preg_match("/'password'\s*=>\s*'([^']*)'/",$configText, $p); $password = $p[1] ?? '';
preg_match("/'database'\s*=>\s*'([^']*)'/",$configText, $d); $database = $d[1] ?? 'v_track';
preg_match("/'port'\s*=>\s*(\d+)/",$configText, $pt); $port = $pt[1] ?? 3306;

echo "Testing MySQL connection with:\n";
echo "  Host: {$hostname}:{$port}\n";
echo "  User: {$username}\n";
echo "  Database: {$database}\n";
echo "  Driver: MySQLi\n\n";

// Test connection using mysqli
try {
    $mysqli = new mysqli(
        $hostname,
        $username,
        $password,
        $database,
        $port
    );

    if ($mysqli->connect_errno) {
        echo "[FAIL] Connection failed: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n";
        exit(1);
    }

    echo "[OK] Connected to MySQL server successfully.\n";
    
    // Get server version
    $result = $mysqli->query("SELECT VERSION() as version");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "[OK] MySQL version: {$row['version']}\n";
    }

    // Check if database exists and list tables
    $result = $mysqli->query("SHOW TABLES");
    if ($result) {
        $tables = [];
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }
        if (empty($tables)) {
            echo "[INFO] Database '{$database}' exists but has no tables yet.\n";
            echo "[INFO] Run: php spark migrate --all\n";
        } else {
            echo "[OK] Database '{$database}' has " . count($tables) . " table(s): " . implode(', ', $tables) . "\n";
        }
    }

    $mysqli->close();
    echo "\n[SUCCESS] Database configuration is correct and working.\n";
    exit(0);

} catch (Exception $e) {
    echo "[FAIL] Exception: {$e->getMessage()}\n";
    exit(1);
}
