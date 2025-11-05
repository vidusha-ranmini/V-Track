# Quick script to enable openssl extension in php.ini
$phpIni = "C:\xampp\php\php.ini"

if (-not (Test-Path $phpIni)) {
    Write-Host "[ERROR] php.ini not found at: $phpIni" -ForegroundColor Red
    exit 1
}

# Backup php.ini
$backup = "$phpIni.bak_openssl_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Copy-Item $phpIni $backup -Force
Write-Host "[OK] Backed up php.ini to: $backup" -ForegroundColor Green

# Read and update
$content = Get-Content $phpIni -Raw

if ($content -match '(?m)^extension=openssl') {
    Write-Host "[INFO] openssl extension is already enabled." -ForegroundColor Yellow
} elseif ($content -match '(?m)^;extension=openssl') {
    $content = $content -replace '(?m)^;extension=openssl', 'extension=openssl'
    Set-Content -Path $phpIni -Value $content -Force
    Write-Host "[OK] Enabled openssl extension in php.ini" -ForegroundColor Green
    Write-Host "[INFO] You must restart Apache/XAMPP for changes to take effect." -ForegroundColor Cyan
} else {
    # Add it if it doesn't exist
    $content += "`nextension=openssl`n"
    Set-Content -Path $phpIni -Value $content -Force
    Write-Host "[OK] Added openssl extension to php.ini" -ForegroundColor Green
    Write-Host "[INFO] You must restart Apache/XAMPP for changes to take effect." -ForegroundColor Cyan
}

Write-Host "`nVerify after restart with: php -m | Select-String openssl" -ForegroundColor White
