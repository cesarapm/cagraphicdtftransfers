<template>
  <div class="payment-state payment-state--failure">
    <section class="payment-state__hero">
      <v-container class="py-16">
        <v-row justify="center">
          <v-col cols="12" lg="9">
            <div class="payment-state__shell">
              <span class="payment-state__eyebrow">Pago no completado</span>
              <div class="payment-state__seal payment-state__seal--failure">X</div>
              <h1>La orden sigue esperando tu siguiente intento.</h1>
              <p>
                El pago no se completó. Puedes revisar tus datos y volver al checkout para intentarlo otra vez,
                o escribirnos si necesitas apoyo con tu compra.
              </p>

              <div class="payment-state__actions">
                <v-btn class="payment-state__btn payment-state__btn--solid" :to="{ name: 'Checkout' }" elevation="0">
                  Volver al checkout
                </v-btn>
                <v-btn class="payment-state__btn" variant="outlined" :to="{ name: 'Contact' }">
                  Contactar soporte
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
import { onMounted } from 'vue';

const lastMercadoPagoOrderKey = 'last_mercado_pago_order_id';

const cancelFailedMercadoPagoOrder = async (orderId) => {
  try {
    await fetch('/api/cancelar-orden-pago-fallido', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ orden_id: orderId })
    });
  } catch (error) {
    console.error('No se pudo cancelar la orden fallida:', error);
  }
};

onMounted(async () => {
  const lastOrderId = sessionStorage.getItem(lastMercadoPagoOrderKey);
  if (!lastOrderId) {
    return;
  }

  await cancelFailedMercadoPagoOrder(Number(lastOrderId));
  sessionStorage.removeItem(lastMercadoPagoOrderKey);
});
</script>

<style scoped>
.payment-state {
  margin-top: 70px;
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

@media (max-width: 600px) {
  .payment-state {
    margin-top: 84px;
    min-height: calc(100vh - 84px);
  }

  .payment-state__shell {
    padding: 2rem 1.3rem;
  }
}
</style>