<template>
  <div class="gang-sheet-builder-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">DTF Gang Sheet Builder</h1>
        <p class="text-lg text-gray-600">
          Create your custom DTF transfer gang sheets. Upload your designs, arrange them, and we'll print them for you!
        </p>
      </div>

      <!-- Instructions -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-blue-900 mb-3 flex items-center">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          How it works
        </h2>
        <ol class="list-decimal list-inside space-y-2 text-gray-700">
          <li>Select your measurement unit (Feet or Inches)</li>
          <li>Choose your desired sheet size from the available options</li>
          <li>Upload your design images (PNG with transparent background recommended)</li>
          <li>Drag and resize images on the canvas or use Auto Build to arrange automatically</li>
          <li>Review coverage and pricing</li>
          <li>Save and add to cart or download for approval</li>
        </ol>
      </div>

      <!-- Unit Selector - Radio Buttons -->
      <div class="mb-8">
        <div class="flex items-center gap-8 bg-white p-6 rounded-lg border">
          <label class="flex items-center cursor-pointer gap-3">
            <input 
              type="radio" 
              v-model="activeTab" 
              value="feet"
              class="w-4 h-4 accent-blue-600"
            />
            <span class="text-lg font-medium text-gray-700">Feet (Pies)</span>
          </label>
          
          <label class="flex items-center cursor-pointer gap-3">
            <input 
              type="radio" 
              v-model="activeTab" 
              value="inches"
              class="w-4 h-4 accent-blue-600"
            />
            <span class="text-lg font-medium text-gray-700">Inches (Pulgadas)</span>
          </label>
        </div>
      </div>

      <!-- Pricing Info - Feet -->
      <div v-if="activeTab === 'feet'" class="grid md:grid-cols-3 gap-4 mb-8 animate-fadeIn">
        <div class="bg-white rounded-lg shadow-sm border p-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold text-gray-700">22' × 10' Sheet</h3>
            <span class="text-2xl font-bold text-green-600">$165</span>
          </div>
          <p class="text-sm text-gray-500">Most popular size</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border p-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold text-gray-700">22' × 5' Sheet</h3>
            <span class="text-2xl font-bold text-green-600">$95</span>
          </div>
          <p class="text-sm text-gray-500">Good for smaller orders</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border p-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold text-gray-700">11' × 5' Sheet</h3>
            <span class="text-2xl font-bold text-green-600">$65</span>
          </div>
          <p class="text-sm text-gray-500">Compact option</p>
        </div>
      </div>

      <!-- Pricing Info - Inches -->
      <div v-if="activeTab === 'inches'" class="grid md:grid-cols-4 gap-4 mb-8 animate-fadeIn">
        <div class="bg-white rounded-lg shadow-sm border p-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold text-gray-700">22" × 120"</h3>
            <span class="text-2xl font-bold text-green-600">$165</span>
          </div>
          <p class="text-sm text-gray-500">10 feet long</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border p-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold text-gray-700">22" × 60"</h3>
            <span class="text-2xl font-bold text-green-600">$95</span>
          </div>
          <p class="text-sm text-gray-500">5 feet long</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border p-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold text-gray-700">13" × 19"</h3>
            <span class="text-2xl font-bold text-green-600">$28</span>
          </div>
          <p class="text-sm text-gray-500">Testing designs</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold text-gray-700">11" × 17"</h3>
            <span class="text-2xl font-bold text-green-600">$22</span>
          </div>
          <p class="text-sm text-gray-500">Small format</p>
        </div>
      </div>

      <!-- Editor Component - Feet Tab -->
      <div v-if="activeTab === 'feet'">
        <GangSheetEditorFeet />
      </div>

      <!-- Editor Component - Inches Tab -->
      <div v-if="activeTab === 'inches'">
        <GangSheetEditorInches />
      </div>

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
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import GangSheetEditorFeet from '../components/GangSheetEditorFeet.vue';
import GangSheetEditorInches from '../components/GangSheetEditorInches.vue';

export default {
  name: 'GangSheetBuilder',
  components: {
    GangSheetEditorFeet,
    GangSheetEditorInches,
  },
  setup() {
    const activeTab = ref('inches'); // Default to Inches tab

    return {
      activeTab,
    };
  },
};
</script>

<style scoped>
.gang-sheet-builder-page {
  min-height: 100vh;
  background: linear-gradient(to bottom, #f9fafb, #ffffff);
  margin-top: 80px;
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
