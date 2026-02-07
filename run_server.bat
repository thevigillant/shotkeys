@echo off
setlocal
echo ==========================================
echo    Iniciando Servidor ShotKeys (Porta 8080)
echo ==========================================
echo.

REM Tenta encontrar o PHP no PATH
where php >nul 2>nul
if %errorlevel% equ 0 (
    echo [OK] PHP encontrado no sistema.
    goto :START_SERVER
)

echo [INFO] PHP nao encontrado no PATH. Procurando em pastas padrao...

REM Lista de pastas comuns para verificar
set "PHP_DIRS=C:\xampp\php C:\wamp64\bin\php\php8.2.12 C:\wamp64\bin\php\php8.1.0 C:\wamp64\bin\php\php8.0.0 C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64 C:\tools\php C:\Program Files\php"

for %%d in (%PHP_DIRS%) do (
    if exist "%%d\php.exe" (
        echo [OK] PHP encontrado em: %%d
        set "PATH=%%d;%PATH%"
        goto :START_SERVER
    )
)

echo.
echo [ERRO] NAO FOI POSSIVEL ENCONTRAR O PHP!
echo.
echo Por favor, certifique-se de que o XAMPP, WAMP ou PHP esta instalado.
echo Se voce usa XAMPP, verifique se a pasta C:\xampp\php existe.
echo.
pause
exit /b 1

:START_SERVER
echo.
echo Acesse no navegador: http://127.0.0.1:8080/install.php
echo.
echo Pressione Ctrl+C para encerrar.
php -S 127.0.0.1:8080
pause
