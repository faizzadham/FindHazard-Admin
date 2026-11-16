@echo off
title FindHazard VR - PC Bridge & Telemetry Server
color 0A

echo ================================================================
echo         FINDHAZARD VR ^<--^> SUPERVISOR DASHBOARD BRIDGE
echo ================================================================
echo.

:: 1. Detect Local Wi-Fi IPv4 Address
echo [1/3] Detecting Current PC Local IP Address...
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set LOCAL_IP=%%a
    goto :found_ip
)
:found_ip
set LOCAL_IP=%LOCAL_IP: =%
echo       Current PC IP: %LOCAL_IP%
echo.

:: 2. Set up ADB Reverse Tunnel for USB Cable Testing
echo [2/3] Setting up USB Cable Reverse Tunnel (Port 8000)...
"%LOCALAPPDATA%\Android\Sdk\platform-tools\adb.exe" reverse tcp:8000 tcp:8000 >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo       [SUCCESS] USB Tunnel Active: Quest localhost:8000 --^> PC:8000
) else (
    echo       [NOTE] No USB Quest detected or ADB not in default path.
    echo              If testing via USB, plug in your Quest and re-run.
)
echo.

:: 3. Check / Start Laravel on 0.0.0.0:8000
echo [3/3] Starting Laravel Server on 0.0.0.0:8000...
echo.
echo ================================================================
echo   READY! Open your browser:
echo   - Local:    http://localhost:8000/monitor
echo   - Over LAN: http://%LOCAL_IP%:8000/monitor
echo ================================================================
echo.

cd /d "c:\xampp\htdocs\laravel\FindHazardAdmin"
php artisan serve --host=0.0.0.0 --port=8000
pause
