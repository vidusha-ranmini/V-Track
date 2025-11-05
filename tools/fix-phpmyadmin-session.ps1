# Fix phpMyAdmin session errors and E_STRICT deprecation issues
$phpIni = "C:\xampp\php\php.ini"

if (-not (Test-Path $phpIni)) {
    Write-Host "[ERROR] php.ini not found at: $phpIni" -ForegroundColor Red
    exit 1
}

# Backup
$backup = "$phpIni.bak_session_fix_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Copy-Item $phpIni $backup -Force
Write-Host "[OK] Backed up php.ini to: $backup" -ForegroundColor Green

# Read content
$content = Get-Content $phpIni -Raw

$changed = $false

# 1. Enable and set session.save_path to XAMPP's tmp folder
if ($content -match '(?m)^;session\.save_path\s*=\s*"/tmp"') {
    $content = $content -replace '(?m)^;session\.save_path\s*=\s*"/tmp"', 'session.save_path = "C:/xampp/tmp"'
    Write-Host "[OK] Enabled session.save_path = C:/xampp/tmp" -ForegroundColor Green
    $changed = $true
} elseif ($content -notmatch '(?m)^session\.save_path\s*=') {
    # Add it if missing
    $content = $content -replace '(?m)(^\[Session\])', "`$1`nsession.save_path = `"C:/xampp/tmp`""
    Write-Host "[OK] Added session.save_path = C:/xampp/tmp" -ForegroundColor Green
    $changed = $true
}

# 2. Ensure error_reporting excludes E_DEPRECATED and E_STRICT
if ($content -notmatch '(?m)^error_reporting\s*=.*~E_DEPRECATED') {
    $content = $content -replace '(?m)^error_reporting\s*=.*$', 'error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT'
    Write-Host "[OK] Updated error_reporting to exclude deprecations" -ForegroundColor Green
    $changed = $true
}

if ($changed) {
    Set-Content -Path $phpIni -Value $content -Force
    Write-Host "[INFO] php.ini updated. Restart Apache for changes to take effect." -ForegroundColor Yellow
} else {
    Write-Host "[INFO] No changes needed." -ForegroundColor Cyan
}

# Ensure tmp directory exists
$tmpDir = "C:\xampp\tmp"
if (-not (Test-Path $tmpDir)) {
    New-Item -ItemType Directory -Path $tmpDir -Force | Out-Null
    Write-Host "[OK] Created directory: $tmpDir" -ForegroundColor Green
}

Write-Host "`n[NEXT STEPS]" -ForegroundColor Cyan
Write-Host "1. Restart Apache in XAMPP Control Panel" -ForegroundColor White
Write-Host "2. Clear browser cookies for localhost" -ForegroundColor White
Write-Host "3. Access phpMyAdmin at: http://localhost:8080/phpmyadmin/" -ForegroundColor White
