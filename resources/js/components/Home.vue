<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header/Navbar -->
    <header class="bg-white shadow-md sticky top-0 z-50">
      <nav class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="text-2xl font-bold text-gray-800">
            🧢 Gorras Store
          </div>

          <div class="hidden md:flex space-x-8">
            <a href="#" class="text-gray-600 hover:text-gray-900 transition">Inicio</a>
            <a href="#productos" class="text-gray-600 hover:text-gray-900 transition">Productos</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 transition">Categorías</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 transition">Contacto</a>
          </div>

          <div class="flex items-center space-x-4">
            <button class="relative p-2 text-gray-600 hover:text-gray-900">
              🛒
              <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ cartCount }}
              </span>
            </button>
          </div>
        </div>
      </nav>
    </header>

    <!-- Hero Section -->
    <section class="container mx-auto px-6 py-20">
      <div class="text-center">
        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
          Encuentra tu Estilo Perfecto
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
          Descubre nuestra colección exclusiva de gorras premium. Calidad y estilo en cada detalle.
        </p>
        <button
          @click="scrollToProducts"
          class="bg-gray-900 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105"
        >
          Ver Colección
        </button>
      </div>
    </section>

    <!-- Características -->
    <section class="bg-white py-16">
      <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-3 gap-8">
          <div class="text-center p-6">
            <div class="text-4xl mb-4">🚚</div>
            <h3 class="text-xl font-semibold mb-2">Envío Gratis</h3>
            <p class="text-gray-600">En compras mayores a $50</p>
          </div>
          <div class="text-center p-6">
            <div class="text-4xl mb-4">✨</div>
            <h3 class="text-xl font-semibold mb-2">Calidad Premium</h3>
            <p class="text-gray-600">Materiales de primera calidad</p>
          </div>
          <div class="text-center p-6">
            <div class="text-4xl mb-4">🔄</div>
            <h3 class="text-xl font-semibold mb-2">Devolución Fácil</h3>
            <p class="text-gray-600">30 días de garantía</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Productos Destacados -->
    <section id="productos" class="container mx-auto px-6 py-20">
      <h2 class="text-4xl font-bold text-center mb-12">Productos Destacados</h2>

      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div
          v-for="product in featuredProducts"
          :key="product.id"
          class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2"
        >
          <div class="h-64 bg-gray-200 flex items-center justify-center">
            <span class="text-6xl">{{ product.emoji }}</span>
          </div>
          <div class="p-6">
            <h3 class="text-xl font-semibold mb-2">{{ product.name }}</h3>
            <p class="text-gray-600 mb-4">{{ product.description }}</p>
            <div class="flex items-center justify-between">
              <span class="text-2xl font-bold text-gray-900">${{ product.price }}</span>
              <button
                @click="addToCart(product)"
                class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition"
              >
                Agregar
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
      <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-8">
          <div>
            <h4 class="text-xl font-bold mb-4">Gorras Store</h4>
            <p class="text-gray-400">Tu tienda de gorras premium</p>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Enlaces</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white">Sobre Nosotros</a></li>
              <li><a href="#" class="hover:text-white">Productos</a></li>
              <li><a href="#" class="hover:text-white">Blog</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Ayuda</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white">FAQ</a></li>
              <li><a href="#" class="hover:text-white">Envíos</a></li>
              <li><a href="#" class="hover:text-white">Devoluciones</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Contacto</h4>
            <ul class="space-y-2 text-gray-400">
              <li>📧 info@gorras.com</li>
              <li>📱 +1 234 567 890</li>
            </ul>
          </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2026 Gorras Store. Todos los derechos reservados.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const cartCount = ref(0);

const featuredProducts = ref([
  {
    id: 1,
    name: 'Gorra Clásica',
    description: 'Estilo atemporal y elegante',
    price: 29.99,
    emoji: '🧢'
  },
  {
    id: 2,
    name: 'Gorra Deportiva',
    description: 'Perfecta para tus entrenamientos',
    price: 34.99,
    emoji: '⚡'
  },
  {
    id: 3,
    name: 'Gorra Vintage',
    description: 'Diseño retro exclusivo',
    price: 39.99,
    emoji: '🎨'
  },
  {
    id: 4,
    name: 'Gorra Premium',
    description: 'Lujo y comodidad',
    price: 49.99,
    emoji: '👑'
  }
]);

const addToCart = (product) => {
  cartCount.value++;
  alert(`${product.name} agregado al carrito!`);
};

const scrollToProducts = () => {
  document.getElementById('productos').scrollIntoView({ behavior: 'smooth' });
};
</script>

<style scoped>
/* Estilos específicos del componente Home */
</style>
