# Test phpMyAdmin database connection and diagnose loading issues
$testUrl = "http://localhost:8080/phpmyadmin/index.php"

Write-Host "Testing phpMyAdmin connection..." -ForegroundColor Cyan

try {
    $response = Invoke-WebRequest -Uri $testUrl -UseBasicParsing -TimeoutSec 10 -ErrorAction Stop
    Write-Host "[OK] phpMyAdmin responds with status: $($response.StatusCode)" -ForegroundColor Green
    
    if ($response.Content -match "error|fatal|warning") {
        Write-Host "[WARN] Response contains error keywords" -ForegroundColor Yellow
    }
    
    # Check if response is actually loading
    if ($response.Content.Length -lt 500) {
        Write-Host "[WARN] Response is unusually small ($($response.Content.Length) bytes)" -ForegroundColor Yellow
    } else {
        Write-Host "[OK] Response size: $($response.Content.Length) bytes" -ForegroundColor Green
    }
    
} catch {
    Write-Host "[ERROR] Failed to connect: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`nChecking MySQL connection from PHP..." -ForegroundColor Cyan
$phpTest = @"
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP Version: " . PHP_VERSION . "\n";
echo "mysqli loaded: " . (extension_loaded('mysqli') ? 'YES' : 'NO') . "\n";
echo "session.save_path: " . ini_get('session.save_path') . "\n";

try {
    `$conn = new mysqli('127.0.0.1', 'root', '', 'v_track', 3306);
    if (`$conn->connect_error) {
        echo "MySQL Connection FAILED: " . `$conn->connect_error . "\n";
    } else {
        echo "MySQL Connection: OK\n";
        echo "MySQL Version: " . `$conn->server_info . "\n";
        `$conn->close();
    }
} catch (Exception `$e) {
    echo "MySQL Exception: " . `$e->getMessage() . "\n";
}

echo "\nChecking phpMyAdmin config...\n";
if (file_exists('C:/xampp/phpMyAdmin/config.inc.php')) {
    echo "config.inc.php: EXISTS\n";
} else {
    echo "config.inc.php: MISSING (using config.sample.inc.php)\n";
}
?>
"@

Set-Content -Path "C:\xampp\htdocs\test_pma.php" -Value $phpTest
Write-Host "[OK] Created test script at: http://localhost:8080/test_pma.php" -ForegroundColor Green
Write-Host "Open this URL in your browser to see detailed diagnostics." -ForegroundColor White
