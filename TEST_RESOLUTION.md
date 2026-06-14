# 🧪 Pruebas de Resolución Gang Sheet

## 🚀 Inicio Rápido

### 1. Acceder a la página de testing
```
http://localhost:8000/test-gang-sheet
```

### 2. Crear un design de prueba
- No necesita ingresar ID (vacío)
- Ingresa dimensiones:
  - **Ancho:** 22 pies (por defecto)
  - **Alto:** 10 pies (por defecto)
- Click en **"✨ Crear Design de Prueba"**
  - Respuesta: Se crea en BD con estado "draft"
  - Retorna: **Gang Sheet ID** (ej: 1, 2, 3...)

### 3. Generar imagen PNG 300 DPI
- Click en **"🎨 Generar Imagen 300 DPI"**
  - Comienza a procesar en backend
  - ⏳ **Espera 5-10 segundos** (imágenes grandes)
  - Status cambió a "processing" → "completed"
  - Guarda PNG en: `storage/app/public/exports/`

### 4. Visualizar resultados
Dos opciones:

**Opción A: Ver en navegador**
- Click **"👁️ Ver en Navegador"**
- Se abre en pestaña nueva
- ✅ Puedes hacer zoom/pan para ver detalles

**Opción B: Descargar PNG**
- Click **"📥 Descargar PNG"**
- Se descarga a tu carpeta de descargas
- Puedes abrir con cualquier visor de imágenes

---

## 📊 Qué Esperar

### Dimensiones Generadas
```
Input: 22' × 10' (pies)
↓
Conversión: 22×12 × 10×12 = 264" × 120" (pulgadas)
↓
A 300 DPI: 264×300 × 120×300 = 79,200 × 36,000 píxeles
↓
Tamaño archivo: ~50 MB (PNG comprimido)
```

### Tiempo de Generación
| Método | Tamaño | Tiempo |
|--------|--------|--------|
| Imagick (preferido) | 79,200×36,000 | 2-5 segundos |
| GD (fallback) | 79,200×36,000 | 10-15 segundos |

### Archivos Generados
```
storage/app/public/exports/
├── gang-sheet-1-1717940400.png     (50 MB)
├── gang-sheet-2-1717940410.png     (50 MB)
└── ...
```

---

## 🎯 Casos de Prueba

### Prueba 1: Dimensiones Estándar (22' × 10')
```bash
# Dimensiones
Width: 22 pies
Height: 10 pies

# Resultado esperado
79,200 × 36,000 px
~50 MB
```

### Prueba 2: Dimensiones Pequeñas (10' × 5')
```bash
# Dimensiones
Width: 10 pies
Height: 5 pies

# Resultado esperado
36,000 × 18,000 px
~12 MB
```

### Prueba 3: Dimensiones Grandes (30' × 15')
```bash
# Dimensiones
Width: 30 pies
Height: 15 pies

# Resultado esperado
108,000 × 54,000 px
~110 MB
⚠️ LENTO: Puede tardar 15-30 segundos
```

### Prueba 4: Con imágenes (después)
```bash
# Una vez tengas un design generado:
1. Sube imágenes desde GangSheetEditor
2. Posiciona en canvas
3. Click "Save to Server"
4. Vuelve a generar
5. Verás las imágenes renderizadas en el PNG final
```

---

## 📋 Checklist de Verificación

- [ ] Acceso a http://localhost:8000/test-gang-sheet
- [ ] Crear design de prueba exitosamente
- [ ] Generar imagen sin errores
- [ ] Archivo PNG se crea en storage/app/public/exports/
- [ ] Ver en navegador muestra la imagen
- [ ] Descargar PNG funciona
- [ ] Imagen es completamente blanca (sin imágenes)
- [ ] Resolución es exacta: 79,200 × 36,000 px
- [ ] Archivo pesa ~50 MB (verificar propiedades)

---

## 🐛 Troubleshooting

### Error: "Gang Sheet not found"
**Causa:** ID inválido o no existe en BD  
**Solución:** Crea uno nuevo con "Crear Design de Prueba"

### Error: "Error generating image"
**Causa:** 
- Imagick/GD no configurados
- Permisos de escritura en storage/
- RAM insuficiente para imágenes grandes

**Solución:**
```bash
# Verificar permisos
chmod -R 777 storage/app/public/

# Verificar Imagick
php -m | grep imagick

# Aumentar memory_limit en php.ini
memory_limit=2048M
```

### Tiempo de generación muy lento
**Causa:** Usar GD en lugar de Imagick  
**Solución:**
```bash
# Instalar Imagick
sudo apt-get install imagemagick php-imagick
sudo systemctl restart php-fpm  # o apache
```

### Archivo PNG no se puede abrir
**Causa:** Corrupción durante generación (rara)  
**Solución:**
```bash
# Verificar integridad del PNG
file storage/app/public/exports/gang-sheet-*.png

# Regenerar
Intenta crear otro design y generar de nuevo
```

### No veo la imagen en navegador
**Causa:** 
- URL pública mal configurada
- storage/ no está linkeado

**Solución:**
```bash
# Crear link simbólico
php artisan storage:link

# Verificar que storage/app/public/exports/ sea accesible en:
http://localhost:8000/storage/exports/
```

---

## 📈 Métricas de Performance

### Local Testing (tu servidor)
Captura estas métricas:

```bash
# 1. Tiempo de generación
# (ver en console del navegador o logs de Laravel)

# 2. Tamaño de archivo
ls -lh storage/app/public/exports/

# 3. Calidad de imagen (visual)
# Abre el PNG y verifica:
# - Fondo blanco limpio
# - Sin artefactos
# - Sin pixelación (es PNG a 300 DPI, debe ser muy definido)

# 4. DPI real
# En Linux:
identify -verbose storage/app/public/exports/gang-sheet-*.png | grep Resolution

# En Mac:
sips -g DPIWidth -g DPIHeight storage/app/public/exports/gang-sheet-*.png
```

---

## 🔗 URLs Importantes

| Recurso | URL |
|---------|-----|
| Página Testing | http://localhost:8000/test-gang-sheet |
| API Create | POST `/api/gang-sheets/save` |
| API Generate | GET `/api/gang-sheets/{id}/test-generate` |
| API Download | GET `/api/gang-sheets/{id}/download` |
| Storage Public | `/storage/exports/` |

---

## ✅ Próximos Pasos

Después de verificar que las imágenes se generan correctamente:

1. **Integrar imágenes reales** - Sube imágenes en GangSheetEditor y genera con contenido
2. **Testing con pago** - Implementar flujo Stripe completo
3. **Producción** - Instalar Imagick en servidor, configurar webhooks
4. **Optimización** - Implementar queue worker para generar en background

---

## 📞 Debugging

Si algo falla, revisa estos logs:

```bash
# Laravel log
tail -f storage/logs/laravel.log

# Ver si Imagick está disponible
php -r "echo extension_loaded('imagick') ? 'YES' : 'NO';"

# Ver memoria disponible
php -r "echo round(memory_get_usage() / 1024 / 1024) . 'MB';"

# Ver permiso de storage
ls -la storage/app/public/exports/
```

---

**¿Preguntas?** Revisa IMPLEMENTATION_GUIDE.md para documentación técnica completa.
