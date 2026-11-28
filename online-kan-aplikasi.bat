@echo off
title E-TRANSKRIP UINSSC - ONLINE PUBLIC TUNNEL
color 0A
cls
echo ======================================================================
echo    E-TRANSKRIP AKADEMIK ONLINE - UIN SIBER SYEKH NURJATI CIREBON
echo ======================================================================
echo.
echo  Menghubungkan aplikasi laptop ke internet publik...
echo  (Bisa dibuka dari HP, Dosen, atau Siapa Saja di mana pun tanpa biaya)
echo.
echo  [1/2] Menyalakan Server Laravel di jendela baru...
start "Laravel Server (Jangan Ditutup)" cmd /k "cd /d D:\e-transkip && php artisan serve --port=8000"
timeout /t 3 >nul
echo  [2/2] Membuka Terowongan Online HTTPS (Pinggy)...
echo.
echo  ------------------------------------------------------------------
echo   PETUNJUK:
echo   - Salin / Buka Link HTTPS (https://xxxx.a.pinggy.link) yang muncul
echo   - Bagikan link tersebut ke HP atau ke Dosen/Penguji
echo   - JANGAN TUTUP jendela ini selama pengujian berlangsung
echo  ------------------------------------------------------------------
echo.
ssh -p 443 -o StrictHostKeyChecking=no -R0:localhost:8000 a.pinggy.io
pause

