# 🛍️ E-commerce Starter Template

Una plantilla completa de e-commerce construida con Laravel, Filament, Vue.js y Tailwind CSS. Perfecta para comenzar proyectos de comercio electrónico rápidamente.

## ✨ Características

- **Panel de Administración** con Filament 3.x
- **Frontend moderno** con Vue 3 + Vite
- **Sistema de productos** completo con categorías e imágenes
- **Gestión de órdenes** y clientes
- **Integración de pagos** (MercadoPago)
- **Sistema de email** para notificaciones
- **Autenticación JWT**
- **Responsive design** con Tailwind CSS
- **Multiidioma** (Español por defecto)

## 📋 Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL/MariaDB
- Redis (opcional pero recomendado)

## 🚀 Instalación

1. **Clonar el repositorio**
```bash
git clone <tu-repositorio>
cd ecommerce
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
```

4. **Configurar el entorno**
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

5. **Configurar la base de datos en `.env`**
```env
DB_DATABASE=tu_base_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

6. **Ejecutar migraciones**
```bash
php artisan migrate
```

7. **Crear usuario administrador**
```bash
php artisan db:seed
```
Usuario por defecto: `admin@example.com` / Contraseña: `123123`

8. **Iniciar el proyecto**

Terminal 1 - Backend:
```bash
php artisan serve
```

Terminal 2 - Frontend:
```bash
npm run dev
```

## 📂 Estructura del Proyecto

```
app/
├── Filament/
│   └── Resources/      # Recursos de Filament
├── Http/
│   └── Controllers/    # Controladores API y Web
├── Mail/              # Clases de email
├── Models/            # Modelos Eloquent
└── Services/          # Servicios (MercadoPago, etc.)

resources/
├── css/               # Estilos
├── js/                # Aplicación Vue.js
│   ├── components/    # Componentes Vue
│   ├── pages/         # Páginas Vue
│   └── App.vue        # Componente principal
└── views/             # Vistas Blade

database/
├── migrations/        # Migraciones
└── seeders/          # Seeders
```

## 🎨 Personalización

### Cambiar nombre de la aplicación
Edita `.env`:
```env
APP_NAME="Tu Tienda"
```

### Configurar métodos de pago
Para MercadoPago, agrega en `.env`:
```env
MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
```

### Personalizar el frontend
Los componentes Vue están en `resources/js/`:
- `App.vue` - Layout principal
- `pages/` - Páginas de la aplicación
- `components/` - Componentes reutilizables

### Personalizar emails
Las plantillas de email están en `app/Mail/`

## 🔧 Comandos Útiles

```bash
# Limpiar caché
php artisan optimize:clear

# Crear nuevo recurso de Filament
php artisan make:filament-resource NombreModelo

# Compilar assets para producción
npm run build

# Ejecutar tests
php artisan test
```

## 📦 Modelos Principales

- **Product** - Productos del catálogo
- **Order** - Órdenes de compra
- **OrderItem** - Items de las órdenes
- **Customer** - Clientes
- **User** - Usuarios administradores

## 🛡️ Seguridad

- Cambiar `APP_KEY` y `JWT_SECRET` en producción
- Configurar CORS apropiadamente
- Usar HTTPS en producción
- Proteger rutas sensibles

## 📝 Licencia

Este proyecto es una plantilla de código abierto. Úsala libremente para tus proyectos comerciales.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, abre un issue o pull request.

---

**Hecho con ❤️ para la comunidad de desarrolladores**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
