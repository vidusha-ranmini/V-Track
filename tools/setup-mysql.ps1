<#
PowerShell helper to configure MySQL for this project.
Actions:
 - Back up and enable mysqli and pdo_mysql extensions in XAMPP's php.ini
 - Prompt for MySQL root password and create database + user
 - Create/update .env with database.mysql settings and set defaultGroup = mysql
 - Run php spark migrate --all and php spark db:seed RoadsSeeder

Usage (run from project root):
    powershell -ExecutionPolicy Bypass -File .\tools\setup-mysql.ps1

Notes:
 - This script edits files on your machine. Review it before running.
 - It will attempt to find php.ini and mysql.exe in common XAMPP locations.
 - You will be prompted for the MySQL root password and the new app DB credentials.
#>

function Write-Ok($msg) { Write-Host "[OK]  " $msg -ForegroundColor Green }
function Write-Warn($msg) { Write-Host "[WARN]" $msg -ForegroundColor Yellow }
function Write-Err($msg) { Write-Host "[ERR] " $msg -ForegroundColor Red }

Push-Location $PSScriptRoot\.. | Out-Null
$projectRoot = (Get-Location).Path
Write-Host "Project root: $projectRoot"

# 1) Find php.ini
$phpIniCandidates = @(
    "C:\xampp\php\php.ini",
    "C:\Program Files\PHP\php.ini",
    "C:\Program Files (x86)\PHP\php.ini"
)
$phpIni = $null
foreach ($c in $phpIniCandidates) {
    if (Test-Path $c) { $phpIni = $c; break }
}
if (-not $phpIni) {
    $phpIni = Read-Host "Could not find php.ini automatically. Enter full path to php.ini (or press Enter to skip)"
    if (-not $phpIni) { Write-Warn "Skipping php.ini edits. Ensure mysqli/pdo_mysql are enabled manually." }
}

if ($phpIni -and (Test-Path $phpIni)) {
    $bak = "$phpIni.bak_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
    Copy-Item $phpIni $bak -Force
    Write-Host "Backed up php.ini to: $bak"

    $content = Get-Content $phpIni -Raw
    $updated = $content
    # try to uncomment common extension names
    $updated = $updated -replace '^[;\s]*extension=php_mysqli.dll','extension=php_mysqli.dll' -replace '(?m)^[;\s]*extension=mysqli','extension=mysqli' -replace '^[;\s]*extension=php_pdo_mysql.dll','extension=php_pdo_mysql.dll' -replace '(?m)^[;\s]*extension=pdo_mysql','extension=pdo_mysql'

    if ($updated -ne $content) {
        Set-Content -Path $phpIni -Value $updated -Force
        Write-Ok "Updated php.ini to enable mysqli / pdo_mysql (you must restart Apache/XAMPP for changes to take effect)."
    } else {
        Write-Warn "php.ini already contained enabled mysqli/pdo_mysql or the patterns were not found."
    }
} else {
    Write-Warn "php.ini not modified.";
}

# 2) Locate mysql.exe
function Find-MySqlExe {
    $names = @('mysql.exe','mysql')
    foreach ($n in $names) {
        $p = (Get-Command $n -ErrorAction SilentlyContinue)?.Path
        if ($p) { return $p }
    }
    $candidates = @(
        "C:\xampp\mysql\bin\mysql.exe",
        "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe",
        "C:\Program Files (x86)\MySQL\MySQL Server 5.7\bin\mysql.exe"
    )
    foreach ($c in $candidates) { if (Test-Path $c) { return $c } }
    return $null
}

$mysqlExe = Find-MySqlExe
if (-not $mysqlExe) {
    $mysqlExe = Read-Host "mysql client not found automatically. Enter full path to mysql.exe (or press Enter to skip DB creation)"
}

# 3) Ask user for DB creation info
if ($mysqlExe -and (Test-Path $mysqlExe)) {
    $rootUser = Read-Host "MySQL root username (default: root)" -Default 'root'
    $rootPass = Read-Host -AsSecureString "MySQL root password (input hidden)"
    $rootPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($rootPass))

    $appDb = Read-Host "Name of application database to create (default: v_track)" -Default 'v_track'
    $appUser = Read-Host "App DB username to create (default: vtrack)" -Default 'vtrack'
    $appPass = Read-Host -AsSecureString "App DB user password (input hidden)"
    $appPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($appPass))

    Write-Host "Creating database and user..."
    $cmdCreate = "CREATE DATABASE IF NOT EXISTS `{0}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;" -f $appDb
    $cmdUser = "CREATE USER IF NOT EXISTS '{0}'@'127.0.0.1' IDENTIFIED BY '{1}'; GRANT ALL PRIVILEGES ON `{2}`.* TO '{0}'@'127.0.0.1'; FLUSH PRIVILEGES;" -f $appUser, $appPassPlain, $appDb

    $tempSql = [IO.Path]::GetTempFileName()
    Set-Content -Path $tempSql -Value $cmdCreate + "`n" + $cmdUser

    $quoted = "`"$mysqlExe`" -u$rootUser -p$rootPassPlain < `"$tempSql`""
    try {
        & $mysqlExe -u$rootUser -p$rootPassPlain < $tempSql
        Write-Ok "Database and user created (or already existed)."
    } catch {
        Write-Err "MySQL command failed: $_. Exception. You may need to run the commands manually."
        Write-Host "SQL that was attempted:\n$cmdCreate`n$cmdUser"
    }
    Remove-Item $tempSql -ErrorAction SilentlyContinue
} else {
    Write-Warn "Skipping DB creation because mysql client was not found. You must create DB and user manually."
}

# 4) Update .env
$envFile = Join-Path $projectRoot '.env'
if (-not (Test-Path $envFile)) {
    Copy-Item -Path (Join-Path $projectRoot 'env') -Destination $envFile -Force -ErrorAction SilentlyContinue
    Write-Host ".env created from env template (if present)."
}

# Read existing .env or create empty
$envContent = ''
if (Test-Path $envFile) { $envContent = Get-Content $envFile -Raw }

# Helper to set or replace key
function Set-EnvKey($key, $value) {
    param($key, $value)
    $pattern = "(?m)^$([regex]::Escape($key))\s*=.*$"
    if ($envContent -match $pattern) {
        $GLOBALS:envContent = [regex]::Replace($GLOBALS:envContent, $pattern, "$key = $value")
    } else {
        if ($GLOBALS:envContent -ne '' -and -not $GLOBALS:envContent.TrimEnd().EndsWith("`n")) { $GLOBALS:envContent += "`n" }
        $GLOBALS:envContent += "$key = $value`n"
    }
}

Set-EnvKey 'database.defaultGroup' 'mysql'
Set-EnvKey 'database.mysql.hostname' '127.0.0.1'
Set-EnvKey 'database.mysql.database' ($appDb -ne $null ? $appDb : 'v_track')
Set-EnvKey 'database.mysql.username' ($appUser -ne $null ? $appUser : 'vtrack')
if ($appPassPlain) { Set-EnvKey 'database.mysql.password' $appPassPlain } else { Set-EnvKey 'database.mysql.password' '' }
Set-EnvKey 'database.mysql.DBDriver' 'MySQLi'
Set-Content -Path $envFile -Value $envContent -Force
Write-Ok ".env updated with DB settings."

# 5) Run migrations & seeders
Write-Host "Running migrations..."
try {
    & php "spark" migrate --all
    Write-Ok "Migrations applied."
} catch {
    Write-Err "Migrations failed: $_"
}

Write-Host "Seeding roads..."
try {
    & php "spark" db:seed RoadsSeeder
    Write-Ok "RoadsSeeder ran (if configured)."
} catch {
    Write-Err "Seeder failed: $_"
}

Write-Host "Setup script finished. If you edited php.ini, restart Apache/XAMPP now and re-open a new PowerShell window before running the app." 

Pop-Location | Out-Null
