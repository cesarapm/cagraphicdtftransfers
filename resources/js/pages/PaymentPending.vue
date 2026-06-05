<template>
  <div class="payment-state payment-state--pending">
    <section class="payment-state__hero">
      <v-container class="py-16">
        <v-row justify="center">
          <v-col cols="12" lg="9">
            <div class="payment-state__shell">
              <span class="payment-state__eyebrow">Pago pendiente</span>
              <div class="payment-state__seal payment-state__seal--pending">...</div>
              <h1>Tu pago esta en revision.</h1>
              <p>
                Mercado Pago recibió el intento y está terminando de confirmarlo.
                En cuanto se procese, la orden seguirá su curso normal.
              </p>

              <div class="payment-state__note">
                Si usaste transferencia o un método con validación adicional, este estado puede tardar unos minutos.
              </div>

              <div class="payment-state__actions">
                <v-btn class="payment-state__btn payment-state__btn--solid" :to="{ name: 'Home' }" elevation="0">
                  Volver al inicio
                </v-btn>
                <v-btn class="payment-state__btn" variant="outlined" :to="{ name: 'Contact' }">
                  Necesito ayuda
                </v-btn>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>
  </div>
</template>

<style scoped>
.payment-state {
  margin-top: 70px;
  min-height: calc(100vh - 70px);
  background:
    radial-gradient(circle at top right, rgba(216, 196, 173, 0.44), transparent 34%),
    linear-gradient(180deg, #f8f4ee 0%, #eee5d8 100%);
  color: #5f5244;
}

.payment-state__shell {
  padding: 3rem;
  border-radius: 32px;
  background: rgba(255, 251, 246, 0.86);
  border: 1px solid rgba(184, 151, 120, 0.2);
  box-shadow: 0 24px 60px rgba(111, 90, 70, 0.09);
  text-align: center;
}

.payment-state__eyebrow {
  display: inline-flex;
  padding: 0.5rem 1rem;
  border-radius: 999px;
  background: rgba(184, 151, 120, 0.1);
  color: #8c745f;
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
  font-size: 1.4rem;
  letter-spacing: 0.2em;
}

.payment-state__seal--pending {
  background: radial-gradient(circle at 30% 30%, #ecd8b8, #c3a177 68%, #907055 100%);
}

.payment-state h1 {
  font-size: clamp(2.4rem, 5vw, 4.5rem);
  color: #6b5b47;
  margin-bottom: 1rem;
}

.payment-state p,
.payment-state__note {
  max-width: 680px;
  margin: 0 auto;
  line-height: 1.9;
  color: #7a6856;
}

.payment-state__note {
  margin-top: 1.3rem;
  padding: 1rem 1.2rem;
  border-radius: 18px;
  background: rgba(245, 237, 228, 0.88);
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
  background: linear-gradient(135deg, #8c745f, #a88d74) !important;
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