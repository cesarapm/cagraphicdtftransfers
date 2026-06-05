<template>
  <div class="cart bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-6">Carrito de Compras</h2>

    <div v-if="cartItems.length === 0" class="text-center py-8 text-gray-500">
      <p class="text-lg">Tu carrito está vacío</p>
      <p class="text-sm mt-2">¡Agrega algunos productos!</p>
    </div>

    <div v-else>
      <div
        v-for="item in cartItems"
        :key="item.id"
        class="flex items-center justify-between border-b py-4"
      >
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
            <span class="text-2xl">{{ item.emoji }}</span>
          </div>
          <div>
            <h3 class="font-semibold">{{ item.name }}</h3>
            <p class="text-sm text-gray-600">${{ item.price }}</p>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2">
            <button
              @click="decreaseQuantity(item.id)"
              class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300"
            >
              -
            </button>
            <span class="w-8 text-center">{{ item.quantity }}</span>
            <button
              @click="increaseQuantity(item.id)"
              class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300"
            >
              +
            </button>
          </div>

          <button
            @click="removeItem(item.id)"
            class="text-red-600 hover:text-red-800"
          >
            🗑️
          </button>
        </div>
      </div>

      <div class="mt-6 border-t pt-4">
        <div class="flex justify-between text-lg font-semibold mb-4">
          <span>Total:</span>
          <span>${{ total.toFixed(2) }}</span>
        </div>

        <button
          @click="$emit('checkout')"
          class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold"
        >
          Proceder al Pago
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update-cart', 'checkout']);

const cartItems = ref(props.items);

const total = computed(() => {
  return cartItems.value.reduce((sum, item) => {
    return sum + (item.price * item.quantity);
  }, 0);
});

const increaseQuantity = (id) => {
  const item = cartItems.value.find(i => i.id === id);
  if (item) {
    item.quantity++;
    emit('update-cart', cartItems.value);
  }
};

const decreaseQuantity = (id) => {
  const item = cartItems.value.find(i => i.id === id);
  if (item && item.quantity > 1) {
    item.quantity--;
    emit('update-cart', cartItems.value);
  }
};

const removeItem = (id) => {
  cartItems.value = cartItems.value.filter(i => i.id !== id);
  emit('update-cart', cartItems.value);
};
</script>
