# 🎨 Plantilla E-commerce - Guía de Uso

Esta es una plantilla completa de e-commerce lista para usar. Está diseñada para ser clonada y personalizada para cada nuevo cliente.

## 🎯 ¿Para qué sirve esta plantilla?

Esta plantilla te permite iniciar un proyecto de e-commerce completamente funcional en **menos de 10 minutos**, con:

- Panel de administración profesional
- Frontend moderno y responsive
- Sistema de pagos integrado
- Emails transaccionales
- Gestión completa de productos y órdenes

## 🚀 Inicio Rápido

### Opción 1: Configuración Automática (Recomendada)

#### Linux/Mac:
```bash
./setup.sh
```

#### Windows:
```bash
setup.bat
```

El script te pedirá la información necesaria y configurará todo automáticamente.

### Opción 2: Configuración Manual

Si prefieres hacerlo manualmente, sigue los pasos en [SETUP.md](SETUP.md).

## 📁 Estructura del Proyecto

```
ecommerce/
├── app/                    # Lógica de la aplicación
│   ├── Filament/          # Panel de administración
│   ├── Http/              # Controllers y Middleware
│   ├── Mail/              # Templates de email
│   ├── Models/            # Modelos Eloquent
│   └── Services/          # Servicios (MercadoPago, etc.)
│
├── database/
│   ├── migrations/        # Esquema de base de datos
│   └── seeders/          # Datos iniciales
│
├── resources/
│   ├── js/               # Aplicación Vue.js
│   │   ├── components/   # Componentes reutilizables
│   │   ├── pages/        # Páginas de la aplicación
│   │   └── App.vue       # Componente raíz
│   ├── css/              # Estilos globales
│   └── views/            # Vistas Blade (mínimas)
│
├── public/               # Archivos públicos
│   ├── images/          # Imágenes del sitio
│   └── build/           # Assets compilados
│
├── routes/
│   ├── web.php          # Rutas web
│   └── api.php          # Rutas API
│
├── .env.example         # Variables de entorno ejemplo
├── setup.sh            # Script de setup (Linux/Mac)
├── setup.bat           # Script de setup (Windows)
├── README.md           # Documentación principal
├── SETUP.md            # Guía de configuración detallada
└── TEMPLATE.md         # Este archivo
```

## 🎨 Personalización Básica

### 1. Branding (Logo y Nombre)

**Nombre de la tienda:**
- Editar: `resources/js/App.vue` línea ~17-18

```vue
<span class="brand-wordmark">Nombre del Cliente</span>
<span class="brand-note">Eslogan aquí</span>
```

**Logo:**
- Reemplazar: `public/images/logo.png` (o tu formato)

### 2. Colores de Marca

**Tailwind:**
- Editar: `tailwind.config.js`

```javascript
theme: {
  extend: {
    colors: {
      primary: '#tu-color-primario',
      secondary: '#tu-color-secundario',
    }
  }
}
```

**Vuetify:**
- Editar: `resources/js/main.js` o donde configures Vuetify

### 3. Página "Nosotros"

- Editar: `resources/js/pages/About.vue`
- Personaliza con la historia y valores del cliente

### 4. Información de Contacto

- Editar: `resources/js/pages/Contact.vue`
- Actualizar dirección, teléfono, email, etc.

## 🛍️ Configuración de Productos

### Desde el Panel Admin

1. Accede a `/admin`
2. Ve a "Products"
3. Click en "New Product"
4. Completa los campos:
   - Nombre
   - Código (SKU)
   - Descripción
   - Precio
   - Stock
   - Categoría
   - Imagen principal
   - Galería de imágenes

### Campos Disponibles

Los productos incluyen:
- `name` - Nombre del producto
- `codigo` - Código/SKU único
- `description` - Descripción detallada
- `price` - Precio (decimal)
- `stock` - Cantidad disponible
- `category` - Categoría del producto
- `image` - Imagen principal
- `galeria_imagenes` - Array de imágenes adicionales
- `collection` - Colección (opcional)
- `material` - Material (opcional)
- `peso` - Peso (opcional)
- `dimensiones` - Dimensiones (opcional)
- `destacado` - Boolean (producto destacado)
- `is_active` - Boolean (activo/inactivo)

### Agregar Campos Personalizados

Si necesitas campos adicionales:

1. **Crear migración:**
```bash
php artisan make:migration add_campo_to_products_table
```

2. **Editar el modelo:**
`app/Models/Product.php` - agregar a `$fillable`

3. **Actualizar el recurso de Filament:**
`app/Filament/Resources/ProductResource.php`

## 💳 Configuración de Pagos

### MercadoPago

1. Obtén credenciales en [MercadoPago Developers](https://www.mercadopago.com.mx/developers)

2. Agrega en `.env`:
```env
MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
```

3. El servicio ya está configurado en `app/Services/MercadoPagoConfig.php`

### Otros Métodos de Pago

Para agregar otros procesadores:
1. Crear servicio similar en `app/Services/`
2. Integrar en el proceso de checkout
3. Actualizar `app/Models/Pay.php` si es necesario

## 📧 Emails

### Plantillas Disponibles

- `CustomerOrdersAccessCode.php` - Código de acceso para clientes
- `OrdenAprobada.php` - Confirmación de orden aprobada
- `OrdenClienteAprobada.php` - Notificación al cliente

### Personalizar Emails

Editar las clases en `app/Mail/` y crear vistas en `resources/views/emails/`

### Configuración SMTP

Para producción, configura en `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.tu-proveedor.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@dominio.com
MAIL_PASSWORD=tu-contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tu-dominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🔐 Usuarios y Roles

### Usuario Admin por Defecto

Creado por el seeder:
- Email: `admin@example.com`
- Password: `123123`

**⚠️ IMPORTANTE:** Cambia estas credenciales en producción.

### Agregar Más Usuarios

Desde el panel de Filament en `/admin/users`

## 📱 Páginas del Frontend

### Páginas Incluidas

- `/` - Home (página principal)
- `/products` - Listado de productos
- `/product/:id` - Detalle de producto
- `/cart` - Carrito de compras
- `/checkout` - Proceso de compra
- `/about` - Sobre nosotros
- `/contact` - Contacto
- `/order-tracking` - Rastreo de orden
- `/customer-orders` - Órdenes del cliente

### Agregar Nueva Página

1. Crear componente en `resources/js/pages/NuevaPagina.vue`
2. Agregar ruta en `resources/js/router.js`
3. Agregar link en el navbar si es necesario

## 🎨 Componentes Reutilizables

En `resources/js/components/` encontrarás:
- ProductCard
- CartItem
- Navbar
- Footer
- Etc.

Úsalos y personalízalos según necesites.

## 🗄️ Base de Datos

### Modelos Principales

- **Product** - Productos del catálogo
- **Order** - Órdenes de compra
- **OrderItem** - Items individuales de cada orden
- **Customer** - Clientes registrados
- **User** - Usuarios administradores
- **Pay** - Pagos realizados
- **Setting** - Configuraciones del sistema

### Relaciones

- Order → Customer (belongsTo)
- Order → OrderItems (hasMany)
- OrderItem → Product (belongsTo)
- Customer → Orders (hasMany)

## 🚀 Deployment

### Producción

Ver [SETUP.md](SETUP.md) sección de deployment para:
- Configuración de VPS
- Optimización de Laravel
- Configuración de Nginx/Apache
- SSL/HTTPS
- Backups

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Con coverage
php artisan test --coverage
```

## 📚 Recursos y Documentación

- [Laravel Docs](https://laravel.com/docs)
- [Filament Docs](https://filamentphp.com/docs)
- [Vue.js Docs](https://vuejs.org/)
- [Vuetify Docs](https://vuetifyjs.com/)
- [MercadoPago Docs](https://www.mercadopago.com.mx/developers)

## 💡 Tips y Mejores Prácticas

1. **Git:** Haz commits frecuentes con mensajes descriptivos
2. **Backup:** Configura backups automáticos de la base de datos
3. **Logs:** Revisa regularmente `storage/logs/`
4. **Actualizaciones:** Mantén las dependencias actualizadas
5. **Testing:** Escribe tests para funcionalidades críticas
6. **Seguridad:** Nunca commites el archivo `.env`
7. **Performance:** Usa caché de Laravel en producción
8. **SEO:** Configura meta tags y sitemap.xml

## 🆘 Problemas Comunes

### "No application encryption key"
```bash
php artisan key:generate
```

### Assets no cargan
```bash
npm run build
```

### Permisos de storage
```bash
chmod -R 755 storage bootstrap/cache
```

### Caché problemática
```bash
php artisan optimize:clear
```

## 📞 Soporte

Para dudas o problemas:
1. Revisa [SETUP.md](SETUP.md)
2. Consulta la documentación oficial
3. Revisa los logs en `storage/logs/`

## 📄 Licencia

Esta plantilla es de código abierto y puede ser usada libremente para proyectos comerciales.

---

**¡Feliz desarrollo!** 🚀

Última actualización: Mayo 2026
Versión: 1.0.0
