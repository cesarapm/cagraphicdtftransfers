<template>
  <div class="gang-sheet-builder-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center min-h-screen">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-gray-600 text-lg">Loading sheet information...</p>
        </div>
      </div>

      <!-- Page Header -->
      <div v-else class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">DTF Gang Sheet Builder</h1>
        <p class="text-lg text-gray-600">
          Create your custom DTF transfer gang sheets. Upload your designs, arrange them, and we'll print them for you!
        </p>

        <!-- Selected Sheet Info -->
        <div v-if="selectedSheet" class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
          <p class="text-green-800">
            <strong>📋 Selected Sheet:</strong> {{ selectedSheet.name }} 
            <span class="ml-2 text-gray-600">({{ selectedSheet.width }} × {{ selectedSheet.height }} {{ selectedSheet.unit === 'feet' ? 'ft' : 'in' }})</span>
            <span class="ml-2 font-bold">
              <!-- Si hay descuento activo, mostrar precio tachado y precio final -->
              <span v-if="selectedSheet.promotion && selectedSheet.promotion.final_price">
                <span class="line-through text-red-600">${{ parseFloat(selectedSheet.price).toFixed(2) }}</span>
                <span class="text-green-700 ml-2">💚 ${{ parseFloat(selectedSheet.promotion.final_price).toFixed(2) }}</span>
                <span class="text-xs text-green-600 ml-2">({{ selectedSheet.promotion.discount_type === 'percentage' ? selectedSheet.promotion.discount_value + '%' : '$' + selectedSheet.promotion.discount_value }} OFF)</span>
              </span>
              <!-- Si no hay descuento, mostrar solo el precio -->
              <span v-else class="text-green-700">${{ parseFloat(selectedSheet.price).toFixed(2) }}</span>
            </span>
          </p>
          <p class="text-xs text-gray-500 mt-2">ID: {{ selectedSheet.id }} | Unit: {{ selectedSheet.unit }}</p>
        </div>
        <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
          <p class="text-yellow-800">⏳ Loading sheet data...</p>
        </div>
      </div>

      <template v-if="!loading">

      <!-- Instructions -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-blue-900 mb-3 flex items-center">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          How it works
        </h2>
        <ol class="list-decimal list-inside space-y-2 text-gray-700">
          <li>Upload your design images (PNG with transparent background recommended)</li>
          <li>Drag and resize images on the canvas or use Auto Build to arrange automatically</li>
          <li>Review coverage and pricing</li>
          <li>Save and add to cart when ready</li>
        </ol>
      </div>

      <!-- Editor Component - Inches -->
      <GangSheetEditorInches v-if="selectedSheet" :sheetData="selectedSheet" />

      <!-- Features -->
      <div class="mt-12 grid md:grid-cols-4 gap-6">
        <div class="text-center">
          <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <h3 class="font-semibold mb-1">Fast Turnaround</h3>
          <p class="text-sm text-gray-600">2-3 business days production</p>
        </div>
        
        <div class="text-center">
          <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="font-semibold mb-1">High Quality</h3>
          <p class="text-sm text-gray-600">300 DPI premium prints</p>
        </div>
        
        <div class="text-center">
          <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="font-semibold mb-1">Best Pricing</h3>
          <p class="text-sm text-gray-600">Maximize your sheet space</p>
        </div>
        
        <div class="text-center">
          <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <h3 class="font-semibold mb-1">Easy to Use</h3>
          <p class="text-sm text-gray-600">Drag, drop, and done!</p>
        </div>
      </div>

      <!-- FAQ Section -->
      <div class="mt-12 bg-white rounded-lg shadow-sm border p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
        
        <div class="space-y-4">
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">What's the difference between Feet and Inches?</h3>
            <p class="text-gray-600">Both use the same printing technology. Choose based on your preferred measurement unit - Feet for larger format designs, Inches for more precise measurements.</p>
          </div>
          
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">What file format should I upload?</h3>
            <p class="text-gray-600">PNG files with transparent backgrounds work best. We also accept JPG, JPEG, and SVG formats.</p>
          </div>
          
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">What resolution do I need?</h3>
            <p class="text-gray-600">For best results, upload images at 300 DPI. Minimum resolution should be at least 150 DPI at your desired print size.</p>
          </div>
          
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">Can I edit my design after saving?</h3>
            <p class="text-gray-600">Yes! You can save your gang sheet as a draft and come back to edit it before final submission.</p>
          </div>
          
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">How does Auto Build work?</h3>
            <p class="text-gray-600">Auto Build uses an intelligent algorithm to arrange your images efficiently on the sheet, maximizing space usage and minimizing waste.</p>
          </div>
          
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">What's the turnaround time?</h3>
            <p class="text-gray-600">Standard production is 2-3 business days. Rush options are available for an additional fee.</p>
          </div>
        </div>
      </div>
      </template>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import GangSheetEditorFeet from '../components/GangSheetEditorFeet.vue';
import GangSheetEditorInches from '../components/GangSheetEditorInches.vue';

export default {
  name: 'GangSheetBuilder',
  components: {
    GangSheetEditorFeet,
    GangSheetEditorInches,
  },
  props: {
    sheetId: {
      type: [String, Number],
      required: true
    }
  },
  setup(props) {
    const route = useRoute();
    const selectedSheet = ref(null);
    const loading = ref(true);

    // Load sheet data by ID
    const loadSheetData = async () => {
      try {
        loading.value = true;
        // console.log('🔍 Fetching sheet with ID:', props.sheetId);
        // console.log('📍 URL:', `/api/sheet-sizes/${props.sheetId}`);
        
        const response = await axios.get(`/api/sheet-sizes/${props.sheetId}`);
        
        // console.log('✅ API Response status:', response.status);
        // console.log('📊 COMPLETE Response object:', response);
        // console.log('📊 response.data:', response.data);
        // console.log('📊 response.data.data:', response.data.data);
        // console.log('📊 JSON stringify:', JSON.stringify(response.data, null, 2));
        
        // Intentar obtener los datos de diferentes estructuras posibles
        let sheetData = response.data;
        
        // Si está envuelto en un objeto "data"
        if (response.data.data && !response.data.width) {
          sheetData = response.data.data;
          // console.log('📦 Datos encontrados en response.data.data');
        }
        
        // console.log('📋 Sheet data final:', sheetData);
        // console.log('   - id:', sheetData.id);
        // console.log('   - width:', sheetData.width);
        // console.log('   - height:', sheetData.height);
        // console.log('   - name:', sheetData.name);
        // console.log('   - unit:', sheetData.unit);
        // console.log('   - price:', sheetData.price);
        
        selectedSheet.value = sheetData;
        // console.log('✅ selectedSheet ref updated:', selectedSheet.value);
      } catch (error) {
        console.error('❌ Error loading sheet:', error);
        console.error('   Error message:', error.message);
        console.error('   Error response:', error.response?.status, error.response?.data);
        console.error('   Full error:', error);
        alert('Could not load sheet information. Please try again.');
      } finally {
        loading.value = false;
      }
    };

    // SEO Meta Tags
    onMounted(() => {
      document.title = 'DTF Gang Sheet Builder - Create Custom Transfer Sheets | CA Graphic DTF';
      
      const updateMeta = (name, content, isProperty = false) => {
        const selector = isProperty ? `meta[property="${name}"]` : `meta[name="${name}"]`;
        let meta = document.querySelector(selector);
        if (meta) meta.remove();
        
        const newMeta = document.createElement('meta');
        if (isProperty) {
          newMeta.setAttribute('property', name);
        } else {
          newMeta.setAttribute('name', name);
        }
        newMeta.content = content;
        document.head.appendChild(newMeta);
      };
      
      updateMeta('description', 'Build custom DTF gang sheets online. Upload designs, arrange them, and order press-ready sheets. No minimums, no setup fees.');
      updateMeta('keywords', 'DTF gang sheet builder, custom transfer sheets, DTF printing, direct-to-film transfers');
      updateMeta('og:title', 'DTF Gang Sheet Builder - Create Custom Transfer Sheets', true);
      updateMeta('og:description', 'Build custom DTF gang sheets online. Upload designs, arrange them, and order press-ready sheets.', true);
      updateMeta('og:url', 'https://cagraphicdtftransfers.com/build', true);

      // Load sheet data
      // console.log('🚀 GangSheetBuilderCanvas onMounted - calling loadSheetData');
      // console.log('   props.sheetId:', props.sheetId);
      loadSheetData();
    });

    return {
      selectedSheet,
      loading,
    };
  },
};
</script>

<style scoped>
.gang-sheet-builder-page {
  min-height: 100vh;
  background: linear-gradient(to bottom, #f9fafb, #ffffff);
  margin-top: 10px;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-in-out;
}
</style>
