<template>
  <div class="payment-state payment-state--failure">
    <section class="payment-state__hero">
      <v-container class="py-16">
        <v-row justify="center">
          <v-col cols="12" lg="9">
            <div class="payment-state__shell">
              <span class="payment-state__eyebrow">Payment Not Completed</span>
              <div class="payment-state__seal payment-state__seal--failure">X</div>
              <h1>Your order is waiting for your next attempt.</h1>
              <p>
                The payment was not completed. You can review your details and return to checkout to try again,
                or contact us if you need assistance with your purchase.
              </p>

              <v-alert 
                v-if="orderId && cancelledOrder" 
                type="success" 
                variant="tonal"
                class="my-4"
              >
                ✓ Order #{{ orderId }} cancelled successfully
              </v-alert>

              <v-alert 
                v-if="cancelError" 
                type="error" 
                variant="tonal"
                class="my-4"
              >
                Error cancelling: {{ cancelError }}
              </v-alert>

              <div class="payment-state__actions">
                <v-btn 
                  class="payment-state__btn payment-state__btn--solid" 
                  :to="{ name: 'Cart' }" 
                  elevation="0"
                >
                  Back to Cart
                </v-btn>
                <v-btn 
                  v-if="orderId && !cancelledOrder"
                  class="payment-state__btn payment-state__btn--cancel" 
                  variant="outlined"
                  :loading="cancelling"
                  @click="handleCancelOrder"
                >
                  Cancel Order #{{ orderId }}
                </v-btn>
                <v-btn class="payment-state__btn" variant="outlined" :to="{ name: 'Contact' }">
                  Contact Support
                </v-btn>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const orderId = ref(null);
const cancelling = ref(false);
const cancelledOrder = ref(false);
const cancelError = ref(null);
const lastMercadoPagoOrderKey = 'last_mercado_pago_order_id';

const cancelFailedMercadoPagoOrder = async (id) => {
  try {
    cancelling.value = true;
    cancelError.value = null;

    // 🗑️ BORRAR GANG SHEETS Y LIMPIAR CARRITO COMPLETAMENTE
    const token = localStorage.getItem('auth_token');
    
    // Limpiar desde sessionStorage
    const cartKey = 'dtf_cart';
    const dtfCartData = sessionStorage.getItem(cartKey);
    
    if (dtfCartData) {
      try {
        const cartData = JSON.parse(dtfCartData);
        const dtfCartItems = cartData.dtf_items || [];
        
        // Eliminar gang sheets del servidor
        for (const item of dtfCartItems) {
          if (item.type === 'gang_sheet' && item.gangSheetid) {
            try {
              await fetch(`/api/gang-sheets/${item.gangSheetid}`, {
                method: 'DELETE',
                headers: token ? { Authorization: `Bearer ${token}` } : {}
              });
            } catch (err) {
              console.warn('Failed to delete gang sheet:', item.gangSheetid, err);
            }
          }
        }
      } catch (err) {
        console.warn('Error processing sessionStorage cart:', err);
      }
    }
    
    // Limpiar COMPLETAMENTE ambos storages
    sessionStorage.removeItem(cartKey);
    localStorage.removeItem('dtf_cart_items');
    localStorage.removeItem('dtf_cart');

    const response = await fetch('/api/cancelar-orden-pago-fallido', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ orden_id: id })
    });

    if (!response.ok) {
      throw new Error(`Error ${response.status}: ${response.statusText}`);
    }

    cancelledOrder.value = true;
  } catch (error) {
    console.error('Failed to cancel order:', error);
    cancelError.value = error.message || 'Unable to cancel order';
  } finally {
    cancelling.value = false;
  }
};

const handleCancelOrder = () => {
  if (orderId.value) {
    cancelFailedMercadoPagoOrder(Number(orderId.value));
  }
};

onMounted(async () => {
  // Try to get order_id from query params
  if (route.query.order_id) {
    orderId.value = route.query.order_id;
  } else {
    // Fallback to sessionStorage
    const lastOrderId = sessionStorage.getItem(lastMercadoPagoOrderKey);
    if (lastOrderId) {
      orderId.value = lastOrderId;
    }
  }

  // Auto-cancel if coming from MercadoPago error
  if (orderId.value && route.query.token) {
    await cancelFailedMercadoPagoOrder(Number(orderId.value));
  }

  // Clean up sessionStorage
  sessionStorage.removeItem(lastMercadoPagoOrderKey);
});
</script>

<style scoped>
.payment-state {
  
  min-height: calc(100vh - 70px);
  background:
    radial-gradient(circle at top left, rgba(196, 148, 133, 0.22), transparent 32%),
    linear-gradient(180deg, #fbf5f0 0%, #f1e3dc 100%);
  color: #5f5244;
}

.payment-state__shell {
  padding: 3rem;
  border-radius: 32px;
  background: rgba(255, 250, 246, 0.88);
  border: 1px solid rgba(170, 108, 96, 0.18);
  box-shadow: 0 24px 60px rgba(111, 90, 70, 0.09);
  text-align: center;
}

.payment-state__eyebrow {
  display: inline-flex;
  padding: 0.5rem 1rem;
  border-radius: 999px;
  background: rgba(170, 108, 96, 0.1);
  color: #9a5d56;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.74rem;
}

.payment-state__seal {
  width: 92px;
  height: 92px;
  margin: 1.4rem auto 1.2rem;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #fff9f2;
  font-size: 1.25rem;
  letter-spacing: 0.16em;
}

.payment-state__seal--failure {
  background: radial-gradient(circle at 30% 30%, #e1b4a7, #bf7f73 70%, #8f5b53 100%);
}

.payment-state h1 {
  font-size: clamp(2.4rem, 5vw, 4.5rem);
  color: #6b5b47;
  margin-bottom: 1rem;
}

.payment-state p {
  max-width: 680px;
  margin: 0 auto;
  line-height: 1.9;
  color: #7a6856;
}

.payment-state__actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
  margin-top: 2rem;
}

.payment-state__btn {
  min-width: 210px;
  border-radius: 999px !important;
  letter-spacing: 0.08em;
}

.payment-state__btn--solid {
  background: linear-gradient(135deg, #9a5d56, #b6776d) !important;
  color: #fffdf9 !important;
}

.payment-state__btn--cancel {
  color: #bf7f73 !important;
  border-color: #bf7f73 !important;
}

.payment-state__btn--cancel:hover {
  background: rgba(191, 127, 115, 0.08) !important;
}

@media (max-width: 600px) {
  .payment-state {
    margin-top: 0px;
    min-height: calc(100vh - 70px);
  }

  .payment-state__shell {
    padding: 2rem 1.3rem;
  }
}
</style>