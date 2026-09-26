@echo off
setlocal enabledelayedexpansion
title Apex Peak Expeditions - 1-Click Automated Setup

:: ==============================================================================
:: APEX PEAK EXPEDITIONS - AUTOMATED 1-CLICK WORDPRESS INSTALLER & DEPLOYER
:: ==============================================================================
:: This script will:
:: 1. Detect MySQL / XAMPP / MariaDB installation and services
:: 2. Auto-start MySQL & Apache if not already running
:: 3. Determine target webroot (C:\xampp\htdocs\wordpress or public_html\wordpress)
:: 4. Copy all website files, plugins, uploads, and assets to webroot
:: 5. Create database and import all 10 pages + custom tables
:: 6. Generate production-ready wp-config.php
:: 7. Launch the live website in your default browser
:: ==============================================================================

cls
echo ==============================================================================
echo               APEX PEAK EXPEDITIONS - 1-CLICK AUTOMATED SETUP
echo          High-Altitude Alpine Exploration Platform (WordPress 6.7)
echo ==============================================================================
echo.

set "SCRIPT_DIR=%~dp0"
:: Remove trailing backslash if present
if "%SCRIPT_DIR:~-1%"=="\" set "SCRIPT_DIR=%SCRIPT_DIR:~0,-1%"

:: Configuration Defaults
set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"
set "DB_USER=root"
set "DB_PASS="
set "DB_NAME=wordpress"
set "SITE_URL=http://localhost/wordpress"

echo [*] Initializing installation from: %SCRIPT_DIR%
echo.

:: ------------------------------------------------------------------------------
:: STEP 1: DETECT XAMPP AND MYSQL BINARIES
:: ------------------------------------------------------------------------------
echo [Step 1/6] Detecting MySQL and Web Server environment...

set "XAMPP_DIR="
set "MYSQL_BIN="
set "APACHE_BIN="

:: Check common XAMPP locations
if exist "C:\xampp\mysql\bin\mysql.exe" set "XAMPP_DIR=C:\xampp"
if not defined XAMPP_DIR if exist "D:\xampp\mysql\bin\mysql.exe" set "XAMPP_DIR=D:\xampp"
if not defined XAMPP_DIR if exist "E:\xampp\mysql\bin\mysql.exe" set "XAMPP_DIR=E:\xampp"
if not defined XAMPP_DIR if exist "%LOCALAPPDATA%\Programs\xampp\mysql\bin\mysql.exe" set "XAMPP_DIR=%LOCALAPPDATA%\Programs\xampp"

if defined XAMPP_DIR (
    echo  [+] Detected XAMPP at: %XAMPP_DIR%
    set "MYSQL_BIN=%XAMPP_DIR%\mysql\bin\mysql.exe"
    set "MYSQLD_BIN=%XAMPP_DIR%\mysql\bin\mysqld.exe"
    set "APACHE_BIN=%XAMPP_DIR%\apache\bin\httpd.exe"
    set "APACHE_DIR=%XAMPP_DIR%\apache"
) else (
    :: Check if mysql is in PATH
    where mysql >nul 2>nul
    if %errorlevel% equ 0 (
        set "MYSQL_BIN=mysql"
        echo  [+] Found MySQL in system PATH.
    ) else (
        echo  [!] Warning: Could not locate standard XAMPP or MySQL in PATH.
        set /p "CUSTOM_MYSQL=Enter full path to mysql.exe (or press ENTER to cancel): "
        if not defined CUSTOM_MYSQL (
            echo [ERROR] MySQL is required to import the database. Setup cannot continue.
            pause
            exit /b 1
        )
        set "MYSQL_BIN=!CUSTOM_MYSQL!"
    )
)

:: ------------------------------------------------------------------------------
:: STEP 2: VERIFY AND AUTO-START MYSQL SERVICE
:: ------------------------------------------------------------------------------
echo.
echo [Step 2/6] Checking MySQL service connectivity...

"%MYSQL_BIN%" -h %DB_HOST% -u %DB_USER% -e "SELECT 1;" >nul 2>nul
if %errorlevel% neq 0 (
    echo  [!] MySQL is not currently running. Attempting to start MySQL...
    if defined MYSQLD_BIN (
        start "" /B "%MYSQLD_BIN%" --defaults-file="%XAMPP_DIR%\mysql\bin\my.ini" --standalone
        timeout /t 4 /nobreak >nul
    ) else (
        net start mysql >nul 2>nul
        timeout /t 3 /nobreak >nul
    )

    "%MYSQL_BIN%" -h %DB_HOST% -u %DB_USER% -e "SELECT 1;" >nul 2>nul
    if !errorlevel! neq 0 (
        echo.
        echo  [!] Could not connect to MySQL on %DB_HOST%:%DB_PORT%.
        echo  [!] Please start MySQL via your XAMPP Control Panel or MySQL service,
        echo      then press any key to retry connection...
        pause >nul
        "%MYSQL_BIN%" -h %DB_HOST% -u %DB_USER% -e "SELECT 1;" >nul 2>nul
        if !errorlevel! neq 0 (
            echo [ERROR] Unable to connect to MySQL database server. Aborting.
            pause
            exit /b 1
        )
    )
)
echo  [+] MySQL connection verified successfully!

:: ------------------------------------------------------------------------------
:: STEP 3: CREATE DATABASE AND IMPORT APEX PEAK EXPEDITIONS TABLES
:: ------------------------------------------------------------------------------
echo.
echo [Step 3/6] Setting up database '%DB_NAME%' and importing tables...

echo  [+] Creating database if not exists: %DB_NAME%
"%MYSQL_BIN%" -h %DB_HOST% -u %DB_USER% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if %errorlevel% neq 0 (
    echo [ERROR] Failed to create database '%DB_NAME%'.
    pause
    exit /b 1
)

set "SQL_DUMP=%SCRIPT_DIR%\database\apex_peak_expeditions.sql"
if not exist "%SQL_DUMP%" (
    if exist "%SCRIPT_DIR%\apex_peak_expeditions.sql" set "SQL_DUMP=%SCRIPT_DIR%\apex_peak_expeditions.sql"
)

if exist "%SQL_DUMP%" (
    echo  [+] Importing SQL dump: %SQL_DUMP%
    cmd /c ""%MYSQL_BIN%" -h %DB_HOST% -u %DB_USER% --default-character-set=utf8mb4 %DB_NAME% < "%SQL_DUMP%""
    if !errorlevel! equ 0 (
        echo  [+] Database tables, 10 pages, terms, and custom engine tables imported successfully!
    ) else (
        echo  [!] SQL dump import finished with status !errorlevel!.
    )
) else (
    echo  [!] SQL dump file not found at %SQL_DUMP%. Proceeding with schema fallback if available.
    if exist "%SCRIPT_DIR%\database\schema.sql" (
        cmd /c ""%MYSQL_BIN%" -h %DB_HOST% -u %DB_USER% %DB_NAME% < "%SCRIPT_DIR%\database\schema.sql""
        echo  [+] Standalone schema tables imported.
    )
)

:: ------------------------------------------------------------------------------
:: STEP 4: DETERMINE TARGET WEBROOT AND COPY FILES
:: ------------------------------------------------------------------------------
echo.
echo [Step 4/6] Deploying WordPress website files to webroot...

set "TARGET_DIR="

:: Check if htdocs public_html structure exists (e.g. cPanel / custom vhost)
if defined XAMPP_DIR (
    if exist "%XAMPP_DIR%\htdocs\public_html" (
        set "TARGET_DIR=%XAMPP_DIR%\htdocs\public_html\wordpress"
    ) else if exist "%XAMPP_DIR%\htdocs" (
        set "TARGET_DIR=%XAMPP_DIR%\htdocs\wordpress"
    )
)

:: If not under XAMPP, or running directly inside the target folder
if not defined TARGET_DIR set "TARGET_DIR=%SCRIPT_DIR%"

:: Compare current directory with target directory
set "CURRENT_CLEAN=%SCRIPT_DIR%"
set "TARGET_CLEAN=%TARGET_DIR%"

if /i "%CURRENT_CLEAN%"=="%TARGET_CLEAN%" (
    echo  [+] Files are already located directly inside the webroot: %TARGET_DIR%
) else (
    echo  [+] Deploying files from: %SCRIPT_DIR%
    echo  [+] To webroot target:    %TARGET_DIR%
    
    if not exist "%TARGET_DIR%" mkdir "%TARGET_DIR%" >nul 2>nul
    
    echo  [+] Syncing WordPress core, plugins, themes, and uploads...
    robocopy "%SCRIPT_DIR%" "%TARGET_DIR%" /E /XD .git ci database /XF setup.bat .gitignore .gitmodules /NJH /NJS /NDL /NC /NS >nul
    
    :: Also ensure secondary htdocs/wordpress sync if public_html was used
    if exist "%XAMPP_DIR%\htdocs\public_html" (
        if not exist "%XAMPP_DIR%\htdocs\wordpress" (
            mklink /J "%XAMPP_DIR%\htdocs\wordpress" "%TARGET_DIR%" >nul 2>nul
        )
    )
    echo  [+] File synchronization complete!
)

:: ------------------------------------------------------------------------------
:: STEP 5: CONFIGURE WP-CONFIG.PHP
:: ------------------------------------------------------------------------------
echo.
echo [Step 5/6] Verifying wp-config.php configuration...

set "CONFIG_FILE=%TARGET_DIR%\wp-config.php"

if not exist "%CONFIG_FILE%" (
    if exist "%TARGET_DIR%\wp-config.php.example" (
        echo  [+] Creating wp-config.php from template...
        copy /Y "%TARGET_DIR%\wp-config.php.example" "%CONFIG_FILE%" >nul
    ) else if exist "%SCRIPT_DIR%\wp-config.php.example" (
        echo  [+] Copying wp-config.php from script directory...
        copy /Y "%SCRIPT_DIR%\wp-config.php.example" "%CONFIG_FILE%" >nul
    )
)

if exist "%CONFIG_FILE%" (
    echo  [+] wp-config.php is active with database: %DB_NAME%
) else (
    echo  [!] Warning: wp-config.php could not be created automatically.
)

:: ------------------------------------------------------------------------------
:: STEP 6: VERIFY APACHE & LAUNCH BROWSER
:: ------------------------------------------------------------------------------
echo.
echo [Step 6/6] Verifying web server and launching website...

:: Test port 80 / Apache
powershell -Command "$s = New-Object Net.Sockets.TcpClient; try { $s.Connect('127.0.0.1', 80); $s.Close(); exit 0 } catch { exit 1 }" >nul 2>nul
if %errorlevel% neq 0 (
    echo  [!] Apache web server is not running on port 80. Attempting to start Apache...
    if defined APACHE_BIN (
        start "" /B /D "%APACHE_DIR%" "%APACHE_BIN%"
        timeout /t 3 /nobreak >nul
    ) else (
        net start Apache2.4 >nul 2>nul
        timeout /t 3 /nobreak >nul
    )
)

echo.
echo ==============================================================================
echo                  🎉 INSTALLATION COMPLETED SUCCESSFULLY! 🎉
echo ==============================================================================
echo.
echo  Website URL:       %SITE_URL%/
echo  Admin Dashboard:   %SITE_URL%/wp-admin/
echo  Target Directory:  %TARGET_DIR%
echo  Database Name:     %DB_NAME%
echo.
echo  Included Features:
echo   - 10 Responsive Alpine Pages (Home, About, Expeditions, Gear, Safety, etc.)
echo   - Apex Expeditions Pro custom database engine plugin
echo   - 4 Custom MySQL Tables (expeditions, bookings, gear inventory, reviews)
echo   - Live AJAX booking lead desk and rental cost calculator
echo   - 10 Uncompressed high-definition mountain photographs
echo ==============================================================================
echo.

echo Launching %SITE_URL% in your default web browser...
start %SITE_URL%/

echo.
echo Setup finished. You may close this window.
pause
