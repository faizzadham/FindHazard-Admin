@echo off
title FindHazard VR - Live Monitor Auto Launcher
cls
echo ====================================================================
echo   FindHazard VR - Live Session Monitor (Auto Stream Launcher)
echo ====================================================================
echo.
echo [1/2] Checking browser environment...

set TARGET_URL=http://127.0.0.1:8000/monitor
set FLAGS=--auto-select-desktop-capture-source="Casting" --enable-usermedia-screen-capturing --auto-accept-camera-and-microphone-capture

:: 1. Try Google Chrome
if exist "C:\Program Files\Google\Chrome\Application\chrome.exe" (
    echo [2/2] Launching Google Chrome with Auto-Select Casting flag...
    start "" "C:\Program Files\Google\Chrome\Application\chrome.exe" %FLAGS% "%TARGET_URL%"
    goto :DONE
)

if exist "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" (
    echo [2/2] Launching Google Chrome (x86) with Auto-Select Casting flag...
    start "" "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" %FLAGS% "%TARGET_URL%"
    goto :DONE
)

:: 2. Try Microsoft Edge
if exist "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe" (
    echo [2/2] Launching Microsoft Edge with Auto-Select Casting flag...
    start "" "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe" %FLAGS% "%TARGET_URL%"
    goto :DONE
)

if exist "C:\Program Files\Microsoft\Edge\Application\msedge.exe" (
    echo [2/2] Launching Microsoft Edge with Auto-Select Casting flag...
    start "" "C:\Program Files\Microsoft\Edge\Application\msedge.exe" %FLAGS% "%TARGET_URL%"
    goto :DONE
)

:: 3. Fallback
echo [2/2] Opening default browser...
start "" "%TARGET_URL%"

:DONE
echo.
echo ====================================================================
echo   SUCCESS: Live Monitor opened!
echo   * Ensure Meta Quest Developer Hub (Device Cast) is open.
echo   * The stream will automatically connect without window prompts.
echo ====================================================================
timeout /t 3 >nul
exit /b
