<template>
  <div class="product-list">
    <h2 class="text-3xl font-bold mb-6">Todos los Productos</h2>

    <!-- Filtros -->
    <div class="mb-6 flex gap-4">
      <select
        v-model="selectedCategory"
        class="px-4 py-2 border rounded-lg"
      >
        <option value="">Todas las categorías</option>
        <option value="clasica">Clásicas</option>
        <option value="deportiva">Deportivas</option>
        <option value="vintage">Vintage</option>
      </select>

      <select
        v-model="sortBy"
        class="px-4 py-2 border rounded-lg"
      >
        <option value="name">Nombre</option>
        <option value="price-asc">Precio: Menor a Mayor</option>
        <option value="price-desc">Precio: Mayor a Menor</option>
      </select>
    </div>

    <!-- Grid de productos -->
    <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition"
      >
        <div class="h-48 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
          <span class="text-6xl">{{ product.emoji }}</span>
        </div>
        <h3 class="text-lg font-semibold mb-2">{{ product.name }}</h3>
        <p class="text-gray-600 text-sm mb-3">{{ product.description }}</p>
        <div class="flex items-center justify-between">
          <span class="text-xl font-bold">${{ product.price }}</span>
          <button
            @click="$emit('add-to-cart', product)"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Agregar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const selectedCategory = ref('');
const sortBy = ref('name');

const products = ref([
  { id: 1, name: 'Gorra Clásica Negra', category: 'clasica', price: 29.99, emoji: '🧢', description: 'Estilo atemporal' },
  { id: 2, name: 'Gorra Deportiva Pro', category: 'deportiva', price: 34.99, emoji: '⚡', description: 'Alto rendimiento' },
  { id: 3, name: 'Gorra Vintage Retro', category: 'vintage', price: 39.99, emoji: '🎨', description: 'Diseño retro' },
  { id: 4, name: 'Gorra Premium Gold', category: 'clasica', price: 49.99, emoji: '👑', description: 'Lujo premium' },
  { id: 5, name: 'Gorra Runner', category: 'deportiva', price: 32.99, emoji: '🏃', description: 'Para corredores' },
  { id: 6, name: 'Gorra Old School', category: 'vintage', price: 37.99, emoji: '📻', description: 'Estilo clásico' },
]);

const filteredProducts = computed(() => {
  let result = products.value;

  // Filtrar por categoría
  if (selectedCategory.value) {
    result = result.filter(p => p.category === selectedCategory.value);
  }

  // Ordenar
  if (sortBy.value === 'price-asc') {
    result = [...result].sort((a, b) => a.price - b.price);
  } else if (sortBy.value === 'price-desc') {
    result = [...result].sort((a, b) => b.price - a.price);
  } else {
    result = [...result].sort((a, b) => a.name.localeCompare(b.name));
  }

  return result;
});
</script>
