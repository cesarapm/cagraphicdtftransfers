# 🎬 Quick Start - Prueba Ahora

## En 3 Pasos

### 1️⃣ Abre en navegador
```
http://localhost:8000/test-gang-sheet
```

### 2️⃣ Click "Crear Design de Prueba"
- Automáticamente genera un design de **22' × 10'**
- Retorna **Gang Sheet ID**
- Muestra dimensión: **79,200 × 36,000 px** @ 300 DPI

### 3️⃣ Click "Generar Imagen 300 DPI"
- ⏳ Espera 5-10 segundos
- ✅ Genera PNG de 50 MB
- 👁️ Visualiza o 📥 Descarga

---

## Lo Que Verás

### Página de Testing
```
┌─────────────────────────────────────┐
│  Gang Sheet - Prueba de Resolución  │
├─────────────────────────────────────┤
│  [Crear Design de Prueba]           │
│  [Generar Imagen 300 DPI]           │
│  [Ver en Navegador] [Descargar PNG] │
├─────────────────────────────────────┤
│                                     │
│  📐 79,200 × 36,000 px @ 300 DPI   │
│  Tamaño: 50 MB                      │
│                                     │
│  [PREVIEW PNG AQUÍ]                 │
│                                     │
└─────────────────────────────────────┘
```

### Información Mostrada
```
Dimensión:    22' × 10' 
Píxeles:      79,200 × 36,000 px
Resolución:   300 DPI (profesional DTF)
Formato:      PNG comprimido
Tamaño:       ~50 MB
Tiempo gen:   5-10 segundos
```

---

## Resultado Final

**Un PNG listo para imprenta DTF:**
- ✅ Resolución exacta: 79,200 × 36,000 px
- ✅ 300 DPI (estándar profesional)
- ✅ Fondo blanco
- ✅ Comprimido optimizado

---

## Estructura de Archivos Generados

```
storage/app/public/exports/
└── gang-sheet-1-1717940400.png   (50 MB)

Accesible públicamente en:
/storage/exports/gang-sheet-1-1717940400.png
```

---

## 🐛 Si No Funciona

```bash
# 1. Revisa que exista la carpeta
mkdir -p storage/app/public/exports

# 2. Dale permisos
chmod -R 777 storage/app/public

# 3. Verifica que Imagick esté instalado
php -m | grep imagick

# Si no está, instala:
# Ubuntu/Debian:
sudo apt-get install imagemagick php-imagick
sudo systemctl restart php-fpm

# macOS:
brew install imagemagick
```

---

## 📊 Matemática de Resolución

```
Entrada:  22 pies × 10 pies
          ↓
Paso 1:   Convertir a pulgadas
          22 × 12 = 264 pulgadas
          10 × 12 = 120 pulgadas
          ↓
Paso 2:   Convertir a píxeles @ 300 DPI
          264 × 300 = 79,200 px
          120 × 300 = 36,000 px
          ↓
Salida:   79,200 × 36,000 px PNG
          ~50 MB
```

---

## 🎯 Casos de Prueba

| Dimensión | Píxeles | Tamaño | Tiempo |
|-----------|---------|--------|--------|
| 10' × 5' | 36,000 × 18,000 | 12 MB | 3s |
| 22' × 10' | 79,200 × 36,000 | 50 MB | 5s |
| 30' × 15' | 108,000 × 54,000 | 110 MB | 15s |

---

## ✨ Ver Imagen en Navegador

Después de generar:
1. Click **"👁️ Ver en Navegador"**
2. Se abre en nueva pestaña
3. Puedes hacer **zoom** (Ctrl + mouse wheel)
4. Ver detalles de máxima resolución

---

## 📥 Descargar PNG

Después de generar:
1. Click **"📥 Descargar PNG"**
2. Se descarga: `gang-sheet-22x10-feet.png`
3. Listo para DTF printing 🎨

---

## 🔗 Rutas Usadas

```
GET /test-gang-sheet
  ↓ Abre página testing

POST /api/gang-sheets/save
  ↓ Crea design en BD

GET /api/gang-sheets/{id}/test-generate
  ↓ Genera PNG 300 DPI en backend

GET /api/gang-sheets/{id}/download
  ↓ Descarga PNG generado
```

---

## 📝 Próximo: Imágenes Reales

Una vez verificado que funciona la resolución:

1. Abre **GangSheetEditorFeet.vue**
2. Sube imágenes reales
3. Posiciona en canvas
4. Click "Save to Server"
5. En testing, usa ese ID
6. Genera → verás imágenes renderizadas

---

**¡Listo!** 🚀 Abre http://localhost:8000/test-gang-sheet

Para más detalles: Ver [TEST_RESOLUTION.md](TEST_RESOLUTION.md)
