# School Enrollment System - First-time setup script
# Run: .\setup.ps1

Write-Host "Setting up School Enrollment System..." -ForegroundColor Cyan

# 1. Install PHP deps
Write-Host "`n[1/5] Installing PHP dependencies..." -ForegroundColor Yellow
composer install

# 2. Copy .env if missing
if (-not (Test-Path ".env")) {
    Write-Host "`n[2/5] Creating .env file..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
    php artisan key:generate
} else {
    Write-Host "`n[2/5] .env already exists, skipping." -ForegroundColor Gray
}

# 3. Install JS deps
Write-Host "`n[3/5] Installing JS dependencies..." -ForegroundColor Yellow
npm install

# 4. Create database
Write-Host "`n[4/5] Creating database 'school_enrollment_db'..." -ForegroundColor Yellow
$env_content = Get-Content ".env" | Where-Object { $_ -match "^DB_" }
$db_user = ($env_content | Where-Object { $_ -match "^DB_USERNAME" }) -replace "DB_USERNAME=", ""
$db_pass = ($env_content | Where-Object { $_ -match "^DB_PASSWORD" }) -replace "DB_PASSWORD=", ""

if ($db_pass -eq "") {
    mysql -u $db_user -e "CREATE DATABASE IF NOT EXISTS school_enrollment_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
} else {
    mysql -u $db_user -p$db_pass -e "CREATE DATABASE IF NOT EXISTS school_enrollment_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
}

# 5. Run migrations
Write-Host "`n[5/5] Running migrations..." -ForegroundColor Yellow
php artisan migrate

Write-Host "`nDone! Run these to start dev:" -ForegroundColor Green
Write-Host "  php artisan serve" -ForegroundColor White
Write-Host "  npm run dev" -ForegroundColor White
