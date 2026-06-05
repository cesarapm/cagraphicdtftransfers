# 🚀 Guía de Configuración para Nuevo Cliente

Esta guía te ayudará a configurar rápidamente esta plantilla de e-commerce para un nuevo cliente.

## 📝 Checklist de Configuración

### 1. Configuración Básica

#### Archivo `.env`
```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Generar secreto JWT
php artisan jwt:secret
```

Editar `.env` y actualizar:
```env
APP_NAME="Nombre del Cliente"
APP_URL=https://dominio-cliente.com
FRONTEND_URL=https://dominio-cliente.com

DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario_db
DB_PASSWORD=contraseña_db
```

#### Base de Datos
```bash
# Crear base de datos (desde MySQL)
mysql -u root -p
CREATE DATABASE nombre_base_datos;
exit;

# Ejecutar migraciones
php artisan migrate

# Crear usuario admin (edita DatabaseSeeder primero si necesitas)
php artisan db:seed
```

### 2. Personalización de Marca

#### Logo y Nombre
Editar `resources/js/App.vue`:
```vue
<span class="brand-wordmark">Nombre Cliente</span>
<span class="brand-note">Tu eslogan aquí</span>
```

Reemplazar logo en:
- `public/images/logo.png` (o el formato que uses)

#### Página About
Editar `resources/js/pages/About.vue` con la información del cliente.

#### Colores y Estilos
Editar `tailwind.config.js` para personalizar colores de marca.

### 3. Configuración de Email

#### Mailtrap (Desarrollo)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
```

#### SMTP Real (Producción)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com  # o tu proveedor
MAIL_PORT=587
MAIL_USERNAME=correo@cliente.com
MAIL_PASSWORD=contraseña_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=correo@cliente.com
MAIL_FROM_NAME="${APP_NAME}"
CONTACTO_EMAIL=contacto@cliente.com
```

### 4. Configuración de Pagos (MercadoPago)

Obtén las credenciales en [MercadoPago Developers](https://www.mercadopago.com.mx/developers)

```env
MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
```

### 5. Compilar Assets

```bash
# Instalar dependencias
npm install

# Desarrollo
npm run dev

# Producción
npm run build
```

### 6. Configuración de Productos

#### Opción 1: Panel de Administración
1. Accede a `/admin` con las credenciales creadas
2. Ve a "Products" y comienza a agregar productos

#### Opción 2: Importación Masiva
Puedes usar el Excel importer de Filament o crear un seeder personalizado.

### 7. Storage Links

```bash
# Crear enlace simbólico para archivos públicos
php artisan storage:link
```

### 8. Permisos (Servidor Linux)

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🎨 Personalizaciones Comunes

### Cambiar Categorías de Productos
Editar la migración o agregar campo personalizado en `app/Models/Product.php`

### Agregar Campos Personalizados
1. Crear migración: `php artisan make:migration add_campo_to_products_table`
2. Actualizar modelo `Product.php` agregando al `$fillable`
3. Actualizar `ProductResource.php` en Filament

### Personalizar Emails
Los templates de email están en `app/Mail/`

## 🔒 Seguridad para Producción

### Checklist de Seguridad
- [ ] Cambiar `APP_KEY` y `JWT_SECRET`
- [ ] Configurar `APP_ENV=production`
- [ ] Deshabilitar `APP_DEBUG=false`
- [ ] Configurar CORS correctamente
- [ ] Usar HTTPS (SSL/TLS)
- [ ] Configurar firewall
- [ ] Actualizar contraseña del admin
- [ ] Configurar backups automáticos

### CORS
Editar `config/cors.php` según tus necesidades:
```php
'allowed_origins' => [
    'https://dominio-cliente.com',
],
```

## 📦 Deployment

### Servidor VPS/Dedicado
```bash
# Actualizar Composer
composer install --optimize-autoloader --no-dev

# Compilar assets
npm run build

# Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrar base de datos
php artisan migrate --force
```

### Shared Hosting
Consulta la documentación de tu proveedor para Laravel.

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Con coverage
php artisan test --coverage
```

## 📱 Funcionalidades Incluidas

- ✅ Panel de administración con Filament
- ✅ Gestión de productos (CRUD completo)
- ✅ Gestión de órdenes
- ✅ Gestión de clientes
- ✅ Carrito de compras
- ✅ Checkout
- ✅ Integración con MercadoPago
- ✅ Emails transaccionales
- ✅ Autenticación JWT
- ✅ Responsive design

## 🆘 Solución de Problemas

### Error: "No application encryption key"
```bash
php artisan key:generate
```

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error de permisos en storage
```bash
chmod -R 755 storage bootstrap/cache
```

### Vite no compila
```bash
rm -rf node_modules package-lock.json
npm install
npm run dev
```

## 📚 Recursos Adicionales

- [Documentación Laravel](https://laravel.com/docs)
- [Documentación Filament](https://filamentphp.com/docs)
- [Documentación Vue.js](https://vuejs.org/)
- [Documentación Vuetify](https://vuetifyjs.com/)

## 💡 Tips

1. **Mantén actualizado el .env.example** con las nuevas variables que agregues
2. **Documenta cambios importantes** en el código
3. **Usa migraciones** para todos los cambios de base de datos
4. **Haz commits frecuentes** en Git
5. **Prueba en local** antes de subir a producción

---

**¿Necesitas ayuda?** Consulta la documentación o contacta al equipo de desarrollo.
