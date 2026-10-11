# FindHazard VR - Silent Casting Background Helper
# Automatically pushes the MQDH 'Casting' window behind the dashboard browser so it never blocks your screen.

Add-Type @"
using System;
using System.Runtime.InteropServices;
public class WinHelper {
    [DllImport("user32.dll")]
    public static extern bool SetWindowPos(IntPtr hWnd, IntPtr hWndInsertAfter, int X, int Y, int cx, int cy, uint uFlags);
    [DllImport("user32.dll")]
    public static extern bool SetForegroundWindow(IntPtr hWnd);
    [DllImport("user32.dll")]
    public static extern IntPtr GetForegroundWindow();

    public static readonly IntPtr HWND_BOTTOM = new IntPtr(1);
    public const uint SWP_NOSIZE = 0x0001;
    public const uint SWP_NOMOVE = 0x0002;
    public const uint SWP_NOACTIVATE = 0x0010;

    public static void PushToBack(IntPtr hWnd) {
        SetWindowPos(hWnd, HWND_BOTTOM, 0, 0, 0, 0, SWP_NOMOVE | SWP_NOSIZE | SWP_NOACTIVATE);
    }
}
"@

$lastPushedHwnd = [IntPtr]::Zero

while ($true) {
    try {
        # Find any window with 'Casting' in its title
        $castingProcs = Get-Process | Where-Object { 
            $_.MainWindowTitle -and ($_.MainWindowTitle -match 'Casting|Meta Quest Casting|Oculus Casting')
        }

        foreach ($p in $castingProcs) {
            $hwnd = $p.MainWindowHandle
            if ($hwnd -ne [IntPtr]::Zero -and $hwnd -ne $lastPushedHwnd) {
                # Push the casting window behind other windows
                [WinHelper]::PushToBack($hwnd)
                
                # Bring the browser dashboard to the front
                $browserProcs = Get-Process | Where-Object { 
                    $_.MainWindowTitle -and ($_.MainWindowTitle -match 'FindHazard|Supervisor|127.0.0.1:8000') 
                }
                foreach ($bp in $browserProcs) {
                    if ($bp.MainWindowHandle -ne [IntPtr]::Zero) {
                        [WinHelper]::SetForegroundWindow($bp.MainWindowHandle)
                    }
                }
                $lastPushedHwnd = $hwnd
            }
        }
    } catch {
        # Ignore errors
    }
    Start-Sleep -Milliseconds 800
}
