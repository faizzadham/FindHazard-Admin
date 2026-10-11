@echo off
title FindHazard VR - Silent Live Monitor Auto Launcher
cls
echo ====================================================================
echo   FindHazard VR - Live Session Monitor (Auto Stream Launcher)
echo ====================================================================
echo.

:: 1. Ensure USB reverse tunnel for Quest 3 trainee telemetry
echo [1/3] Setting USB reverse tunnel for Quest 3 (Port 8000)...
set ADB="C:\Users\Faiz Adham\AppData\Local\Android\Sdk\platform-tools\adb.exe"
if not exist %ADB% (
    set ADB="C:\Program Files\Unity\Hub\Editor\6000.3.19f1\Editor\Data\PlaybackEngines\AndroidPlayer\SDK\platform-tools\adb.exe"
)
if exist %ADB% (
    %ADB% reverse tcp:8000 tcp:8000 >nul 2>&1
)

:: 2. Launch background silent casting watcher
echo [2/3] Starting silent casting window watcher...
taskkill /F /FI "WINDOWTITLE eq SilentCastWatcher*" >nul 2>&1
start "SilentCastWatcher" /min powershell -WindowStyle Hidden -ExecutionPolicy Bypass -File "%~dp0silent_cast_watcher.ps1"

:: 3. Launch browser with auto-select casting flag (zero dialogs)
echo [3/3] Opening Live VR Monitor Dashboard...
set TARGET_URL=http://127.0.0.1:8000/monitor?autoconnect=1
set FLAGS=--auto-select-desktop-capture-source="Casting" --user-data-dir="%TEMP%\VRMonitorProfile" --no-first-run --no-default-browser-check --disable-fre --enable-usermedia-screen-capturing --auto-accept-camera-and-microphone-capture

:: 3a. Try Google Chrome
if exist "C:\Program Files\Google\Chrome\Application\chrome.exe" (
    echo Launching Google Chrome...
    start "" "C:\Program Files\Google\Chrome\Application\chrome.exe" %FLAGS% "%TARGET_URL%"
    goto :DONE
)

if exist "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" (
    echo Launching Google Chrome...
    start "" "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" %FLAGS% "%TARGET_URL%"
    goto :DONE
)

:: 3b. Try Microsoft Edge
if exist "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe" (
    echo Launching Microsoft Edge...
    start "" "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe" %FLAGS% "%TARGET_URL%"
    goto :DONE
)

if exist "C:\Program Files\Microsoft\Edge\Application\msedge.exe" (
    echo Launching Microsoft Edge...
    start "" "C:\Program Files\Microsoft\Edge\Application\msedge.exe" %FLAGS% "%TARGET_URL%"
    goto :DONE
)

:: 3c. Fallback
echo Opening default browser...
start "" "%TARGET_URL%"

:DONE
echo.
echo ====================================================================
echo   SUCCESS: Live Monitor Launched!
echo   * In MQDH, click Device Cast - Casting window stays in background.
echo   * Stream connects into the VR Monitor with ZERO dialog popups!
echo ====================================================================
ping 127.0.0.1 -n 3 >nul
exit /b
