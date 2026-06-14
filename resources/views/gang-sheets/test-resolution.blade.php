<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gang Sheet - Prueba de Resolución</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .spinner {
            border: 4px solid rgba(0,0,0,0.1);
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .image-preview {
            max-height: 600px;
            overflow-y: auto;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
        }
        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 16px;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div id="app">
        <div class="container mx-auto p-8">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">🎨 Gang Sheet - Prueba de Resolución</h1>
                    <p class="text-gray-600">Prueba la generación de imágenes PNG de alta resolución (300 DPI) sin necesidad de pagar</p>
                </div>

                <!-- Step 1: Información -->
                <div class="info-box mb-6">
                    <h2 class="font-bold text-lg mb-2">📋 Proceso:</h2>
                    <ol class="list-decimal list-inside space-y-1 text-sm">
                        <li><strong>Paso 1:</strong> Si ya tienes un design guardado, ingresa su ID. Si no, crearemos uno nuevo.</li>
                        <li><strong>Paso 2:</strong> Haz click en "Generar Imagen" para crear PNG de 300 DPI en backend</li>
                        <li><strong>Paso 3:</strong> Visualiza la imagen en navegador o descárgala</li>
                        <li><strong>Nota:</strong> Imágenes grandes (22'×10') pueden tardar 5-10 segundos</li>
                    </ol>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: Controls -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">🎛️ Controles</h2>

                        <!-- Input Gang Sheet ID -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Gang Sheet ID
                            </label>
                            <input 
                                v-model="gangSheetId" 
                                type="number" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="ej: 1, 2, 3..."
                            >
                            <p class="text-xs text-gray-500 mt-2">Deja vacío para crear uno nuevo de prueba</p>
                        </div>

                        <!-- Sheet Dimensions (si es nuevo) -->
                        <div v-if="!gangSheetId" class="space-y-4 mb-6 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-gray-900">Dimensiones (nuevo design):</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ancho (pies)
                                </label>
                                <input 
                                    v-model.number="newDesign.width" 
                                    type="number" 
                                    min="1"
                                    max="100"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alto (pies)
                                </label>
                                <input 
                                    v-model.number="newDesign.height" 
                                    type="number" 
                                    min="1"
                                    max="100"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                >
                            </div>

                            <div class="p-3 bg-white rounded text-sm text-gray-700">
                                <strong>Resultado:</strong> 
                                {{ (newDesign.width * 12 * 300).toLocaleString() }} × 
                                {{ (newDesign.height * 12 * 300).toLocaleString() }} px
                                <br>
                                <strong>Color:</strong> Blanco (sin imágenes)
                            </div>
                        </div>

                        <!-- Status Info -->
                        <div v-if="currentGangSheet" class="p-4 bg-gray-100 rounded-lg mb-6 text-sm">
                            <p><strong>Ancho:</strong> {{ currentGangSheet.width }} {{ currentGangSheet.unit }}</p>
                            <p><strong>Alto:</strong> {{ currentGangSheet.height }} {{ currentGangSheet.unit }}</p>
                            <p><strong>Imágenes:</strong> {{ currentGangSheet.image_count }} </p>
                            <p><strong>Estado:</strong> <span class="font-semibold">{{ currentGangSheet.status }}</span></p>
                            <p v-if="currentGangSheet.final_path" class="mt-2 text-green-700 font-semibold">
                                ✅ Imagen lista: {{ currentGangSheet.final_path }}
                            </p>
                        </div>

                        <!-- Buttons -->
                        <div class="space-y-3">
                            <button 
                                v-if="!gangSheetId"
                                @click="createNewDesign"
                                :disabled="loading"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg disabled:opacity-50 transition"
                            >
                                <span v-if="loading" class="flex items-center justify-center gap-2">
                                    <div class="spinner"></div> Creando...
                                </span>
                                <span v-else>✨ Crear Design de Prueba</span>
                            </button>

                            <button 
                                @click="generateImage"
                                :disabled="!gangSheetId || loading"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg disabled:opacity-50 transition"
                            >
                                <span v-if="loading" class="flex items-center justify-center gap-2">
                                    <div class="spinner"></div> Generando (esto puede tardar)...
                                </span>
                                <span v-else>🎨 Generar Imagen 300 DPI</span>
                            </button>

                            <button 
                                v-if="currentGangSheet?.final_path"
                                @click="downloadImage"
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg transition"
                            >
                                📥 Descargar PNG
                            </button>

                            <button 
                                v-if="currentGangSheet?.final_path"
                                @click="viewImage"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-lg transition"
                            >
                                👁️ Ver en Navegador
                            </button>
                        </div>

                        <!-- Error/Success Messages -->
                        <div v-if="error" class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            <strong>Error:</strong> {{ error }}
                        </div>
                        <div v-if="success" class="mt-4 p-4 bg-green-100 text-green-700 rounded-lg">
                            <strong>✅ Éxito:</strong> {{ success }}
                        </div>
                    </div>

                    <!-- Right Column: Preview/Info -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 Información</h2>

                        <!-- Resolution Calculator -->
                        <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg mb-6">
                            <h3 class="font-bold text-lg text-gray-900 mb-3">📐 Cálculo de Resolución</h3>
                            <div v-if="gangSheetId && currentGangSheet" class="space-y-2 text-sm">
                                <p><strong>Dimensión:</strong> {{ currentGangSheet.width }}' × {{ currentGangSheet.height }}'</p>
                                <p class="text-gray-600">= {{ currentGangSheet.width * 12 }}\" × {{ currentGangSheet.height * 12 }}\"</p>
                                <p class="text-gray-600 text-xs">× 300 DPI = </p>
                                <p class="text-lg font-bold text-blue-700">
                                    {{ (currentGangSheet.width * 12 * 300).toLocaleString() }} × 
                                    {{ (currentGangSheet.height * 12 * 300).toLocaleString() }} px
                                </p>
                                <p class="text-xs text-gray-600 mt-3">
                                    Tamaño estimado: <span class="font-bold">{{ estimatedSize }}</span> MB
                                </p>
                            </div>
                            <div v-else class="text-gray-600 text-sm">
                                Ingresa o crea un design para ver el cálculo
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div class="mb-6">
                            <h3 class="font-bold text-lg text-gray-900 mb-3">👁️ Vista Previa</h3>
                            <div v-if="imagePreviewUrl" class="image-preview bg-white">
                                <img :src="imagePreviewUrl" class="w-full" alt="Gang Sheet Preview">
                            </div>
                            <div v-else class="p-8 bg-gray-100 rounded-lg text-center text-gray-500">
                                Genera una imagen para ver vista previa
                            </div>
                        </div>

                        <!-- Specs -->
                        <div class="p-4 bg-amber-50 rounded-lg">
                            <h3 class="font-bold text-gray-900 mb-3">📋 Especificaciones</h3>
                            <ul class="text-sm space-y-1 text-gray-700">
                                <li>✓ Formato: <strong>PNG</strong></li>
                                <li>✓ Resolución: <strong>300 DPI</strong> (profesional para DTF)</li>
                                <li>✓ Compresión: <strong>Máxima</strong> (menor tamaño)</li>
                                <li>✓ Fondo: <strong>Blanco</strong></li>
                                <li>✓ Backend: <strong>Imagick/GD</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        const { createApp, ref, computed } = Vue;

        createApp({
            setup() {
                const gangSheetId = ref(null);
                const loading = ref(false);
                const error = ref(null);
                const success = ref(null);
                const currentGangSheet = ref(null);
                const imagePreviewUrl = ref(null);

                const newDesign = ref({
                    width: 22,
                    height: 10,
                });

                const estimatedSize = computed(() => {
                    if (!currentGangSheet.value) return 'N/A';
                    const width = currentGangSheet.value.width * 12 * 300;
                    const height = currentGangSheet.value.height * 12 * 300;
                    const bytes = width * height * 1.5; // 1.5 bytes por pixel aprox
                    return (bytes / 1024 / 1024).toFixed(2);
                });

                const createNewDesign = async () => {
                    loading.value = true;
                    error.value = null;
                    success.value = null;

                    try {
                        const formData = new FormData();
                        formData.append('width', newDesign.value.width);
                        formData.append('height', newDesign.value.height);
                        formData.append('unit', 'feet');
                        formData.append('name', `Test Design ${newDesign.value.width}x${newDesign.value.height}ft`);
                        formData.append('images', JSON.stringify([])); // Sin imágenes

                        const response = await fetch('/api/gang-sheets/save', {
                            method: 'POST',
                            body: formData,
                        });

                        if (!response.ok) {
                            throw new Error('Error creating design');
                        }

                        const data = await response.json();
                        gangSheetId.value = data.data.id;
                        currentGangSheet.value = data.data;
                        success.value = `✅ Design creado con ID: ${data.data.id}`;
                    } catch (err) {
                        error.value = err.message;
                    } finally {
                        loading.value = false;
                    }
                };

                const generateImage = async () => {
                    loading.value = true;
                    error.value = null;
                    success.value = null;
                    imagePreviewUrl.value = null;

                    try {
                        const response = await fetch(`/api/gang-sheets/${gangSheetId.value}/test-generate`);
                        
                        if (!response.ok) {
                            throw new Error('Error generating image');
                        }

                        const data = await response.json();
                        
                        // Actualizar info
                        currentGangSheet.value = {
                            ...currentGangSheet.value,
                            final_path: data.file_path,
                            status: 'completed'
                        };

                        // Mostrar preview
                        imagePreviewUrl.value = data.download_url;
                        success.value = `✅ Imagen generada (${data.file_size_mb} MB)`;
                    } catch (err) {
                        error.value = err.message;
                    } finally {
                        loading.value = false;
                    }
                };

                const downloadImage = () => {
                    if (currentGangSheet.value?.final_path) {
                        window.location.href = `/api/gang-sheets/${gangSheetId.value}/download`;
                    }
                };

                const viewImage = () => {
                    if (imagePreviewUrl.value) {
                        window.open(imagePreviewUrl.value, '_blank');
                    }
                };

                return {
                    gangSheetId,
                    loading,
                    error,
                    success,
                    currentGangSheet,
                    imagePreviewUrl,
                    newDesign,
                    estimatedSize,
                    createNewDesign,
                    generateImage,
                    downloadImage,
                    viewImage,
                };
            }
        }).mount('#app');
    </script>
</body>
</html>
