@echo off

:: Default port
set PORT=1111

:: Check if a port argument is provided
if "%1" NEQ "" (
    set PORT=%1
)

:: Start the PHP built-in server
echo Starting PHP server at http://localhost:%PORT%
php -S localhost:%PORT%
pause
