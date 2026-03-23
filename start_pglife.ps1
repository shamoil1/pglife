# PGLife Native Starter Script
# This script starts the local MariaDB and PHP servers for your project

$CurrentDir = $PSScriptRoot
if (!$CurrentDir) { $CurrentDir = Get-Location }

$PhpDir = "$CurrentDir\local_env\php"
# Updated path based on actual folder structure
$DbBaseDir = "$CurrentDir\local_env\mariadb\mariadb-10.6.15-winx64"

Write-Host "--- Starting PGLife Local Servers ---" -ForegroundColor Cyan

# 1. Start MariaDB in Background
if (Test-Path "$DbBaseDir\bin\mysqld.exe") {
    Write-Host "[1/2] Starting MariaDB Database Server..." -ForegroundColor Yellow
    # Using --datadir relative to the bin location or finding the data folder
    $DataDir = "$DbBaseDir\data"
    if (!(Test-Path $DataDir)) {
        Write-Host "Creating data directory..." -ForegroundColor Gray
        New-Item -ItemType Directory -Path $DataDir -ErrorAction SilentlyContinue
    }
    
    Start-Process -FilePath "$DbBaseDir\bin\mysqld.exe" -ArgumentList "--datadir=$DataDir", "--console" -WindowStyle Hidden
} else {
    Write-Host "Error: MariaDB not found. Please check local_env contents." -ForegroundColor Red
}

# 2. Wait a moment for DB to initialize
Start-Sleep -Seconds 3

# 3. Start PHP Built-in Server
if (Test-Path "$PhpDir\php.exe") {
    Write-Host "[2/2] Starting PHP Web Server at http://localhost:8080" -ForegroundColor Green
    Write-Host "Keep this window open to keep the website running!" -ForegroundColor Cyan
    & "$PhpDir\php.exe" -S localhost:8080
} else {
    Write-Host "Error: PHP not found in $PhpDir" -ForegroundColor Red
}
