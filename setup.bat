@echo off
REM E-commerce Template Setup Script for Windows
REM Este script automatiza la configuracion inicial del proyecto

echo ========================================
echo    E-commerce Setup Template
echo    Configuracion inicial
echo ========================================
echo.

REM Verificar requisitos
echo [*] Verificando requisitos...

where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP no esta instalado
    pause
    exit /b 1
)
echo [OK] PHP instalado

where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer no esta instalado
    pause
    exit /b 1
)
echo [OK] Composer instalado

where node >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Node.js no esta instalado
    pause
    exit /b 1
)
echo [OK] Node.js instalado

where npm >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] NPM no esta instalado
    pause
    exit /b 1
)
echo [OK] NPM instalado
echo.

REM Solicitar informacion
echo ========================================
echo    Configuracion del Cliente
echo ========================================
echo.

set /p APP_NAME="Nombre del negocio/cliente: "
set /p APP_URL="URL de la aplicacion (ej: http://localhost:8000): "
set /p FRONTEND_URL="URL del frontend (ej: http://localhost:5173): "
set /p DB_NAME="Nombre de la base de datos: "
set /p DB_USER="Usuario de la base de datos [root]: "
if "%DB_USER%"=="" set DB_USER=root
set /p DB_PASSWORD="Contrasena de la base de datos: "
set /p ADMIN_EMAIL="Email del administrador [admin@example.com]: "
if "%ADMIN_EMAIL%"=="" set ADMIN_EMAIL=admin@example.com
set /p ADMIN_PASSWORD="Contrasena del administrador [123123]: "
if "%ADMIN_PASSWORD%"=="" set ADMIN_PASSWORD=123123
echo.

REM Confirmar
echo ========================================
echo    Resumen de configuracion
echo ========================================
echo   Nombre: %APP_NAME%
echo   URL: %APP_URL%
echo   Frontend: %FRONTEND_URL%
echo   Base de datos: %DB_NAME%
echo   Usuario DB: %DB_USER%
echo   Email admin: %ADMIN_EMAIL%
echo ========================================
echo.
set /p CONFIRM="Continuar con esta configuracion? (s/n): "
if /i not "%CONFIRM%"=="s" (
    echo Configuracion cancelada
    pause
    exit /b 1
)
echo.

REM Instalar dependencias PHP
echo ========================================
echo    Instalando Dependencias PHP
echo ========================================
call composer install --no-interaction
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Fallo la instalacion de dependencias PHP
    pause
    exit /b 1
)
echo [OK] Dependencias PHP instaladas
echo.

REM Configurar .env
echo ========================================
echo    Configurando .env
echo ========================================
if not exist .env (
    copy .env.example .env
    echo [OK] Archivo .env creado
) else (
    echo [!] .env ya existe, actualizando...
)

REM Nota: En Windows, necesitarias un metodo mas robusto para editar .env
REM Por simplicidad, se muestra un mensaje al usuario
echo [!] IMPORTANTE: Edita manualmente el archivo .env con estos valores:
echo     APP_NAME="%APP_NAME%"
echo     APP_URL=%APP_URL%
echo     FRONTEND_URL=%FRONTEND_URL%
echo     DB_DATABASE=%DB_NAME%
echo     DB_USERNAME=%DB_USER%
echo     DB_PASSWORD=%DB_PASSWORD%
echo.
pause

REM Generar claves
echo ========================================
echo    Generando Claves de Seguridad
echo ========================================
php artisan key:generate --no-interaction
echo [OK] APP_KEY generada

php artisan jwt:secret --force --no-interaction 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] JWT_SECRET generada
) else (
    echo [!] JWT_SECRET no configurado
)
echo.

REM Instalar dependencias Node
echo ========================================
echo    Instalando Dependencias Node.js
echo ========================================
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Fallo la instalacion de dependencias Node.js
    pause
    exit /b 1
)
echo [OK] Dependencias Node.js instaladas
echo.

REM Ejecutar migraciones
echo ========================================
echo    Ejecutando Migraciones
echo ========================================
php artisan migrate --force --no-interaction
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Fallo la ejecucion de migraciones
    pause
    exit /b 1
)
echo [OK] Migraciones ejecutadas
echo.

REM Ejecutar seeders
echo ========================================
echo    Creando Usuario Administrador
echo ========================================
php artisan db:seed --force --no-interaction
echo [OK] Usuario administrador creado
echo.

REM Storage link
echo ========================================
echo    Configurando Storage
echo ========================================
php artisan storage:link --no-interaction
echo [OK] Storage configurado
echo.

REM Limpiar caches
echo ========================================
echo    Limpiando Caches
echo ========================================
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo [OK] Caches limpiados
echo.

REM Resumen final
echo ========================================
echo    Configuracion Completada!
echo ========================================
echo.
echo [OK] Tu e-commerce esta listo para usar
echo.
echo Credenciales de acceso:
echo   Email: %ADMIN_EMAIL%
echo   Contrasena: %ADMIN_PASSWORD%
echo   URL Admin: %APP_URL%/admin
echo.
echo Para iniciar el proyecto:
echo   Backend:  php artisan serve
echo   Frontend: npm run dev
echo.
echo Consulta SETUP.md para mas configuraciones
echo.
echo Recuerda:
echo   - Configurar credenciales de email en .env
echo   - Configurar MercadoPago si usaras pagos
echo   - Personalizar logo y colores de marca
echo.
echo Feliz desarrollo!
echo.
pause
