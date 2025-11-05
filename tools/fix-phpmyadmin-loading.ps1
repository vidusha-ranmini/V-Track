# Fix common phpMyAdmin loading/hanging issues
Write-Host "Fixing phpMyAdmin loading issues..." -ForegroundColor Cyan

# 1. Check and fix phpMyAdmin config
$configPath = "C:\xampp\phpMyAdmin\config.inc.php"
$samplePath = "C:\xampp\phpMyAdmin\config.sample.inc.php"

if (-not (Test-Path $configPath)) {
    if (Test-Path $samplePath) {
        Copy-Item $samplePath $configPath
        Write-Host "[OK] Created config.inc.php from sample" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] Neither config.inc.php nor config.sample.inc.php found!" -ForegroundColor Red
        exit 1
    }
}

# 2. Backup config
$backup = "$configPath.bak_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Copy-Item $configPath $backup -Force
Write-Host "[OK] Backed up config to: $backup" -ForegroundColor Green

# 3. Read and update config
$config = Get-Content $configPath -Raw

# Set a blowfish secret if missing or default
if ($config -match "\['blowfish_secret'\]\s*=\s*'';") {
    $secret = -join ((65..90) + (97..122) + (48..57) | Get-Random -Count 32 | ForEach-Object {[char]$_})
    $config = $config -replace "(\['blowfish_secret'\]\s*=\s*)'';", "`$1'$secret';"
    Write-Host "[OK] Set blowfish_secret" -ForegroundColor Green
}

# Disable zero configuration (can cause hangs)
if ($config -notmatch "\['AllowNoPassword'\]") {
    $config = $config -replace "(\\\$cfg\['Servers'\]\[\\\$i\]\['auth_type'\])", "`$1`n`$cfg['Servers'][`$i]['AllowNoPassword'] = true;"
    Write-Host "[OK] Enabled AllowNoPassword" -ForegroundColor Green
}

# Set short timeout for performance
if ($config -notmatch "LoginCookieValidity") {
    $config += "`n`$cfg['LoginCookieValidity'] = 1440;`n"
    Write-Host "[OK] Set LoginCookieValidity" -ForegroundColor Green
}

# Disable some heavy features that can cause loading issues
if ($config -notmatch "MaxNavigationItems") {
    $config += "`$cfg['MaxNavigationItems'] = 50;`n"
    $config += "`$cfg['NavigationTreeEnableGrouping'] = false;`n"
    $config += "`$cfg['NavigationTreeEnableExpansion'] = false;`n"
    Write-Host "[OK] Optimized navigation settings" -ForegroundColor Green
}

Set-Content -Path $configPath -Value $config -Force

# 4. Clear phpMyAdmin tmp/cache
$tmpPath = "C:\xampp\phpMyAdmin\tmp"
if (Test-Path $tmpPath) {
    Get-ChildItem $tmpPath -File | Remove-Item -Force -ErrorAction SilentlyContinue
    Write-Host "[OK] Cleared phpMyAdmin tmp directory" -ForegroundColor Green
}

Write-Host "`n[NEXT STEPS]" -ForegroundColor Yellow
Write-Host "1. Clear your browser cache and cookies for localhost" -ForegroundColor White
Write-Host "2. Close and reopen your browser" -ForegroundColor White
Write-Host "3. Access phpMyAdmin: http://localhost:8080/phpmyadmin/" -ForegroundColor White
Write-Host "4. If still loading, open browser DevTools (F12) > Console tab to see JavaScript errors" -ForegroundColor White
