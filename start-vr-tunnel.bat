@echo off
echo ========================================================
echo   FindHazard VR - ADB USB Reverse Port Forwarding
echo ========================================================
echo.

set ADB="C:\Program Files\Unity\Hub\Editor\6000.3.19f1\Editor\Data\PlaybackEngines\AndroidPlayer\SDK\platform-tools\adb.exe"

if not exist %ADB% (
    echo [ERROR] ADB not found at %ADB%
    pause
    exit /b 1
)

echo Checking for connected Meta Quest 3...
%ADB% devices

echo.
echo Forwarding Quest 3 port 8000 -> PC port 8000 (artisan serve)...
%ADB% reverse tcp:8000 tcp:8000

echo Forwarding Quest 3 port 80 -> PC port 80 (Apache)...
%ADB% reverse tcp:80 tcp:80

echo.
echo Active Reverse Tunnels:
%ADB% reverse --list

echo.
echo [SUCCESS] USB Reverse Tunnel is active! 
echo The Quest 3 can now access http://127.0.0.1:8000 directly over USB.
echo ========================================================
pause
