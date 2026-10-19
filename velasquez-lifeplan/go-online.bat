@echo off
title Velasquez - Go Online (public URL)
color 0F
echo ============================================
echo  Velasquez Funeral Service - Go Online
echo ============================================
echo.
echo  This gives you a PUBLIC link anyone can open:
echo  https://velasquez.loca.lt
echo.
echo  Requirements: Node.js installed + internet on THIS pc,
echo  XAMPP Apache + MySQL running.
echo.
echo  Keep this window OPEN. Closing it takes the site offline.
echo  If the name "velasquez" is taken, edit this file and
echo  change --subdomain to another name.
echo.
echo  Starting tunnel...
echo.
npx -y localtunnel --port 80 --subdomain velasquez
pause
