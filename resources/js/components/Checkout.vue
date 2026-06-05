<template>
  <div class="checkout max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-3xl font-bold mb-8">Finalizar Compra</h2>

    <form @submit.prevent="processOrder">
      <!-- Información Personal -->
      <div class="mb-8">
        <h3 class="text-xl font-semibold mb-4">Información Personal</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Nombre</label>
            <input
              v-model="form.firstName"
              type="text"
              required
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Apellido</label>
            <input
              v-model="form.lastName"
              type="text"
              required
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium mb-2">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium mb-2">Teléfono</label>
          <input
            v-model="form.phone"
            type="tel"
            required
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>
      </div>

      <!-- Dirección de Envío -->
      <div class="mb-8">
        <h3 class="text-xl font-semibold mb-4">Dirección de Envío</h3>
        <div>
          <label class="block text-sm font-medium mb-2">Dirección</label>
          <input
            v-model="form.address"
            type="text"
            required
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="grid md:grid-cols-3 gap-4 mt-4">
          <div>
            <label class="block text-sm font-medium mb-2">Ciudad</label>
            <input
              v-model="form.city"
              type="text"
              required
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Estado</label>
            <input
              v-model="form.state"
              type="text"
              required
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Código Postal</label>
            <input
              v-model="form.zipCode"
              type="text"
              required
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>
      </div>

      <!-- Método de Pago -->
      <div class="mb-8">
        <h3 class="text-xl font-semibold mb-4">Método de Pago</h3>
        <div class="space-y-3">
          <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
            <input
              v-model="form.paymentMethod"
              type="radio"
              value="card"
              class="w-4 h-4"
            />
            <span>💳 Tarjeta de Crédito/Débito</span>
          </label>
          <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
            <input
              v-model="form.paymentMethod"
              type="radio"
              value="paypal"
              class="w-4 h-4"
            />
            <span>🅿️ PayPal</span>
          </label>
          <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
            <input
              v-model="form.paymentMethod"
              type="radio"
              value="cash"
              class="w-4 h-4"
            />
            <span>💵 Pago contra entrega</span>
          </label>
        </div>
      </div>

      <!-- Resumen del Pedido -->
      <div class="bg-gray-50 rounded-lg p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4">Resumen del Pedido</h3>
        <div class="space-y-2">
          <div class="flex justify-between">
            <span>Subtotal:</span>
            <span>${{ subtotal.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between">
            <span>Envío:</span>
            <span>${{ shipping.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between">
            <span>Impuestos:</span>
            <span>${{ tax.toFixed(2) }}</span>
          </div>
          <div class="border-t pt-2 flex justify-between font-bold text-lg">
            <span>Total:</span>
            <span>${{ total.toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <!-- Botones -->
      <div class="flex gap-4">
        <button
          type="button"
          @click="$emit('back')"
          class="flex-1 px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Volver
        </button>
        <button
          type="submit"
          class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold"
        >
          Confirmar Pedido
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  cartTotal: {
    type: Number,
    default: 0
  }
});

const emit = defineEmits(['confirm-order', 'back']);

const form = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  zipCode: '',
  paymentMethod: 'card'
});

const subtotal = computed(() => props.cartTotal);
const shipping = computed(() => subtotal.value > 50 ? 0 : 10);
const tax = computed(() => subtotal.value * 0.16); // 16% IVA
const total = computed(() => subtotal.value + shipping.value + tax.value);

const processOrder = () => {
  emit('confirm-order', {
    form: form.value,
    total: total.value
  });
};
</script>
