<template>
  <v-container class="my-8 cart-page">
    <h1 class="text-h3 mb-8">Carrito de Compras</h1>

    <v-row>
      <v-col cols="12" md="8">
        <v-card v-if="cartItems.length === 0" class="pa-12 text-center">
          <v-icon size="64" color="grey">mdi-cart-outline</v-icon>
          <p class="text-h6 mt-4">Tu carrito está vacío</p>
          <v-btn color="primary" class="mt-4" :to="{ name: 'ProductList' }">
            Ver Productos
          </v-btn>
        </v-card>

        <v-card v-else>
          <v-list lines="two">
            <template v-for="(item, index) in cartItems" :key="item.id">
              <v-list-item>
                <template v-slot:prepend>
                  <div class="product-thumb d-flex align-center justify-center mr-4">
                    <v-img
                      v-if="item.image"
                      :src="item.image"
                      :alt="item.name"
                      cover
                    />
                    <span v-else style="font-size: 40px;">🧢</span>
                  </div>
                </template>

                <v-list-item-title>{{ item.name }}</v-list-item-title>
                <v-list-item-subtitle>${{ item.price }}</v-list-item-subtitle>

                <template v-slot:append>
                  <div class="d-flex align-center">
                    <v-btn
                      icon="mdi-minus"
                      size="small"
                      variant="outlined"
                      @click="updateQuantity(item.id, item.quantity - 1)"
                    ></v-btn>
                    <span class="mx-3">{{ item.quantity }}</span>
                    <v-btn
                      icon="mdi-plus"
                      size="small"
                      variant="outlined"
                      @click="updateQuantity(item.id, item.quantity + 1)"
                    ></v-btn>
                    <v-btn
                      icon="mdi-delete"
                      size="small"
                      color="error"
                      variant="text"
                      class="ml-4"
                      @click="removeFromCart(item.id)"
                    ></v-btn>
                  </div>
                </template>
              </v-list-item>
              <v-divider v-if="index < cartItems.length - 1"></v-divider>
            </template>
          </v-list>
        </v-card>
      </v-col>

      <v-col cols="12" md="4" v-if="cartItems.length > 0">
        <v-card>
          <v-card-title>Resumen del Pedido</v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <div class="d-flex justify-space-between mb-2">
              <span>Subtotal:</span>
              <span>${{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="d-flex justify-space-between mb-2">
              <span>Envío:</span>
              <span>{{ subtotal > 50 ? 'Gratis' : '$10.00' }}</span>
            </div>
            <v-divider class="my-3"></v-divider>
            <div class="d-flex justify-space-between">
              <span class="font-weight-bold">Total:</span>
              <span class="font-weight-bold text-h6">
                ${{ (subtotal + (subtotal > 50 ? 0 : 10)).toFixed(2) }}
              </span>
            </div>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn
              color="success"
              block
              size="large"
              :to="{ name: 'Checkout' }"
            >
              Continuar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useCart } from '../composables/useCart';

const { cartItems, removeFromCart, updateQuantity, subtotal } = useCart();
</script>

<style scoped>
.product-thumb {

  width: 80px;
  height: 80px;
  border-radius: 8px;
}
.cart-page{
margin-top: 150px !important;
}
@media (max-width: 600px) {
 .cart-page{
margin-top: 120px !important;
}
}
</style>
