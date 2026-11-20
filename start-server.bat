@echo off
title E-TRANSKRIP UINSSC - SERVER AKTIF (HP & LAPTOP)
color 0A
cls
echo ======================================================================
echo       SERVER E-TRANSKRIP AKADEMIK - UIN SIBER SYEKH NURJATI
echo ======================================================================
echo.
echo   [1/2] Membuka izin firewall port 8000...
netsh advfirewall firewall add rule name="Laravel E-Transkrip 8000" dir=in action=allow protocol=TCP localport=8000 >nul 2>&1
echo   [2/2] Menjalankan Server Laravel (Host: 0.0.0.0, Port: 8000)...
echo.
echo   ------------------------------------------------------------------
echo    PANDUAN AKSES:
echo    - Di Laptop : Buka browser http://127.0.0.1:8000
echo    - Di HP     : Hubungkan HP ke Wi-Fi yang sama, lalu scan QR Code
echo                  atau buka http://192.168.1.34:8000
echo   ------------------------------------------------------------------
echo.
echo   PENTING:
echo   1. JANGAN TUTUP JENDELA INI selama Anda memakai aplikasi!
echo   2. Pastikan HP tersambung ke Wi-Fi (matikan data 4G HP saat tes).
echo.
echo ======================================================================
php artisan serve --host=0.0.0.0 --port=8000
pause

