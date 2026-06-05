#!/bin/bash

# 🚀 Script de Inicialización de E-commerce Template
# Este script automatiza la configuración inicial del proyecto

set -e  # Salir si hay error

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funciones de utilidad
print_header() {
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# Banner
clear
echo -e "${BLUE}"
cat << "EOF"
 ___                                          
| __|__ ___ _ __  _ __  ___ _ _ __ ___       
| _|/ _/ _ \ '  \| '  \/ -_) '_/ _/ -_)      
|___\__\___/_|_|_|_|_|_\___|_| \__\___|      
  ___      _               _____                   _      _       
 / __| ___| |_ _  _ _ __  |_   _|__ _ __  _ __| |__ _| |_ ___ 
 \__ \/ -_)  _| || | '_ \   | |/ -_) '  \| '_ \ / _` |  _/ -_)
 |___/\___|\__|\_,_| .__/   |_|\___|_|_|_| .__/_\__,_|\__\___|
                   |_|                    |_|                   
EOF
echo -e "${NC}"
print_info "Configuración inicial para nuevo cliente"
echo ""

# Verificar requisitos
print_header "Verificando Requisitos"

command -v php >/dev/null 2>&1 || { print_error "PHP no está instalado"; exit 1; }
print_success "PHP instalado: $(php -v | head -n 1)"

command -v composer >/dev/null 2>&1 || { print_error "Composer no está instalado"; exit 1; }
print_success "Composer instalado: $(composer --version | head -n 1)"

command -v node >/dev/null 2>&1 || { print_error "Node.js no está instalado"; exit 1; }
print_success "Node.js instalado: $(node -v)"

command -v npm >/dev/null 2>&1 || { print_error "NPM no está instalado"; exit 1; }
print_success "NPM instalado: $(npm -v)"

echo ""

# Preguntar información del cliente
print_header "Configuración del Cliente"

read -p "Nombre del negocio/cliente: " APP_NAME
read -p "URL de la aplicación (ej: http://localhost:8000): " APP_URL
read -p "URL del frontend (ej: http://localhost:5173): " FRONTEND_URL
read -p "Nombre de la base de datos: " DB_NAME
read -p "Usuario de la base de datos [root]: " DB_USER
DB_USER=${DB_USER:-root}
read -sp "Contraseña de la base de datos: " DB_PASSWORD
echo ""
read -p "Email del administrador [admin@example.com]: " ADMIN_EMAIL
ADMIN_EMAIL=${ADMIN_EMAIL:-admin@example.com}
read -sp "Contraseña del administrador [123123]: " ADMIN_PASSWORD
ADMIN_PASSWORD=${ADMIN_PASSWORD:-123123}
echo ""

# Confirmar
echo ""
print_warning "Resumen de configuración:"
echo "  Nombre: $APP_NAME"
echo "  URL: $APP_URL"
echo "  Frontend: $FRONTEND_URL"
echo "  Base de datos: $DB_NAME"
echo "  Usuario DB: $DB_USER"
echo "  Email admin: $ADMIN_EMAIL"
echo ""
read -p "¿Continuar con esta configuración? (s/n) " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[SsYy]$ ]]
then
    print_error "Configuración cancelada"
    exit 1
fi

# Instalar dependencias de PHP
print_header "Instalando Dependencias de PHP"
composer install --no-interaction
print_success "Dependencias de PHP instaladas"

# Configurar .env
print_header "Configurando Archivo .env"
if [ ! -f .env ]; then
    cp .env.example .env
    print_success "Archivo .env creado desde .env.example"
else
    print_warning ".env ya existe, actualizando valores..."
fi

# Actualizar valores en .env
sed -i "s|APP_NAME=.*|APP_NAME=\"$APP_NAME\"|g" .env
sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g" .env
sed -i "s|FRONTEND_URL=.*|FRONTEND_URL=$FRONTEND_URL|g" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|g" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|g" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|g" .env
print_success "Archivo .env configurado"

# Generar claves
print_header "Generando Claves de Seguridad"
php artisan key:generate --no-interaction
print_success "APP_KEY generada"

if grep -q "JWT_SECRET" .env; then
    php artisan jwt:secret --force --no-interaction
    print_success "JWT_SECRET generada"
else
    print_warning "JWT_SECRET no configurado en .env"
fi

# Instalar dependencias de Node
print_header "Instalando Dependencias de Node.js"
npm install
print_success "Dependencias de Node.js instaladas"

# Configurar base de datos
print_header "Configurando Base de Datos"
read -p "¿Deseas crear la base de datos? (s/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[SsYy]$ ]]; then
    mysql -u "$DB_USER" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    print_success "Base de datos creada"
fi

# Ejecutar migraciones
print_header "Ejecutando Migraciones"
php artisan migrate --force --no-interaction
print_success "Migraciones ejecutadas"

# Ejecutar seeders
print_header "Creando Usuario Administrador"

# Actualizar el seeder con las credenciales proporcionadas
cat > database/seeders/DatabaseSeeder.php << EOF
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => '$ADMIN_EMAIL'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('$ADMIN_PASSWORD'),
            ]
        );
    }
}
EOF

php artisan db:seed --force --no-interaction
print_success "Usuario administrador creado"

# Storage link
print_header "Configurando Storage"
php artisan storage:link --no-interaction 2>/dev/null || print_warning "Storage link ya existe"
print_success "Storage configurado"

# Limpiar cachés
print_header "Limpiando Cachés"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
print_success "Cachés limpiados"

# Resumen final
print_header "¡Configuración Completada! 🎉"
echo ""
print_success "Tu e-commerce está listo para usar"
echo ""
print_info "Credenciales de acceso:"
echo "  📧 Email: $ADMIN_EMAIL"
echo "  🔑 Contraseña: $ADMIN_PASSWORD"
echo "  🌐 URL Admin: $APP_URL/admin"
echo ""
print_info "Para iniciar el proyecto:"
echo "  Backend:  php artisan serve"
echo "  Frontend: npm run dev"
echo ""
print_info "Consulta SETUP.md para más configuraciones"
echo ""
print_warning "Recuerda:"
echo "  - Configurar credenciales de email en .env"
echo "  - Configurar MercadoPago si usarás pagos"
echo "  - Personalizar logo y colores de marca"
echo "  - Revisar la página About.vue"
echo ""
print_success "¡Feliz desarrollo! 🚀"
