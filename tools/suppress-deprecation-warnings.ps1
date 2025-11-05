# Script to suppress PHP deprecation warnings (for phpMyAdmin compatibility with PHP 8.4)
$phpIni = "C:\xampp\php\php.ini"

if (-not (Test-Path $phpIni)) {
    Write-Host "[ERROR] php.ini not found at: $phpIni" -ForegroundColor Red
    exit 1
}

# Backup
$backup = "$phpIni.bak_error_reporting_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Copy-Item $phpIni $backup -Force
Write-Host "[OK] Backed up php.ini to: $backup" -ForegroundColor Green

# Read content
$content = Get-Content $phpIni -Raw

# Change error_reporting to exclude deprecation warnings
# E_ALL & ~E_DEPRECATED & ~E_STRICT
if ($content -match '(?m)^error_reporting\s*=\s*E_ALL\s*$') {
    $content = $content -replace '(?m)^error_reporting\s*=\s*E_ALL\s*$', 'error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT'
    Set-Content -Path $phpIni -Value $content -Force
    Write-Host "[OK] Changed error_reporting to suppress deprecation warnings" -ForegroundColor Green
    Write-Host "[INFO] New setting: error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT" -ForegroundColor Cyan
    Write-Host "[INFO] You must restart Apache for changes to take effect." -ForegroundColor Yellow
} else {
    Write-Host "[INFO] error_reporting is not set to E_ALL, manual review recommended" -ForegroundColor Yellow
    Write-Host "Current setting: $(Get-Content C:\xampp\php\php.ini | Select-String '^error_reporting')" -ForegroundColor White
}
