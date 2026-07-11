<template>
  <div class="tracking-page">
    <section class="tracking-hero">
      <v-container class="py-16">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="tracking-shell">
              <div class="tracking-hero-copy">
                <span class="tracking-kicker">Order Tracking</span>
                <h1>Your order has its own thread.</h1>
                <p>
                  Check your order status with your order number.
                  If you came from the email, access is already ready.
                </p>
              </div>

              <div class="tracking-access-card">
                <div class="tracking-access-head">
                  <div>
                    <span class="tracking-label">Order</span>
                    <strong>&nbsp;{{ normalizedOrderNumber }}</strong>
                  </div>
                  <span class="tracking-token-pill" :class="{ 'tracking-token-pill--ready': hasToken }">
                    {{ hasToken ? 'Secure access ready' : 'Verification required' }}
                  </span>
                </div>
              

                <p class="tracking-access-copy">
                  {{ hasToken
                    ? 'You opened a private link with tracking token. We will check your order as soon as the page loads.'
                    : 'If you opened the link without a token, confirm your email to check the status.' }}
                </p>

                <v-form class="tracking-form" @submit.prevent="submitLookup">
                  <v-text-field
                    v-model="email"
                    label="Email address"
                    type="email"
                    variant="outlined"
                    density="comfortable"
                    :disabled="loading || hasToken"
                    :hint="hasToken ? 'This link already includes the necessary verification.' : 'Use the same email you used to make your purchase.'"
                    persistent-hint
                  ></v-text-field>

                  <v-btn
                    class="tracking-button"
                    type="submit"
                    elevation="0"
                    :loading="loading"
                    :disabled="loading || (!hasToken && !email)"
                  >
                    {{ hasToken ? 'Update status' : 'Check order' }}
                  </v-btn>
                </v-form>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section class="tracking-content">
      <v-container class="pb-16">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <v-alert
              v-if="errorMessage"
              type="error"
              variant="tonal"
              class="mb-6"
            >
              {{ errorMessage }}
            </v-alert>

            <div v-if="loading" class="tracking-loading">
              <div class="tracking-loading__orb"></div>
              <p>Checking your order journey...</p>
            </div>

            <div v-else-if="order" class="tracking-grid">
              <article class="tracking-card tracking-card--hero">
                <div class="tracking-card__topline">
                  <span class="tracking-label">Current status</span>
                  <span class="tracking-status" :class="statusThemeClass">
                    {{ statusNarrative }}
                  </span>
                </div>

                <h2>{{ order.order_number }}</h2>
                <p>
                  {{ statusNarrative }}
                </p>

                <div class="tracking-state-panel" :class="statusThemeClass">
                  <div class="tracking-state-panel__lead">
                    <span class="tracking-label">What's happening</span>
                    <strong>{{ statusDetails.headline }}</strong>
                    <p>{{ statusDetails.detail }}</p>
                  </div>
                  <div class="tracking-state-panel__grid">
                    <div>
                      <span class="tracking-label">Next step</span>
                      <strong>{{ statusDetails.nextStep }}</strong>
                    </div>
                    <div>
                      <span class="tracking-label">Estimated time</span>
                      <strong>{{ statusDetails.eta }}</strong>
                    </div>
                    <div>
                      <span class="tracking-label">Reference</span>
                      <strong>{{ order.order_number }}</strong>
                    </div>
                  </div>
                </div>

                <div class="tracking-progress">
                  <div
                    v-for="step in trackingSteps"
                    :key="step.key"
                    class="tracking-progress__step"
                    :class="{
                      'tracking-progress__step--done': step.done,
                      'tracking-progress__step--active': step.active,
                    }"
                  >
                    <div class="tracking-progress__dot"></div>
                    <div>
                      <strong>{{ step.title }}</strong>
                      <span>{{ step.copy }}</span>
                    </div>
                  </div>
                </div>

                <div class="tracking-meta-grid">
                  <div>
                    <span class="tracking-label">Customer</span>
                    <strong>&nbsp;{{ order.customer_full_name }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Order status</span>
                    <strong>&nbsp;{{ statusNarrative }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Payment</span>
                    <strong>&nbsp;{{ order.metodo_pago || 'Not specified' }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Created</span>
                    <strong>&nbsp;{{ formattedCreatedAt }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Total</span>
                    <strong>&nbsp;{{ formattedCurrency(order.total) }}</strong>
                  </div>
                </div>
              </article>

              <article class="tracking-card">
                <h3>Delivery</h3>
                <div class="tracking-detail-list">
                  <div>
                    <span class="tracking-label">Address</span>
                    <strong>&nbsp;{{ order.shipping_address }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">City / State</span>
                    <strong>&nbsp;{{ order.shipping_city }}, {{ order.shipping_state }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Postal code</span>
                    <strong>&nbsp;{{ order.shipping_zip_code }}</strong>
                  </div>
                </div>
              </article>

              <article class="tracking-card">
                <h3>Payment Movement</h3>
                <div v-if="order.payment" class="tracking-detail-list">
                  <div>
                    <span class="tracking-label">Payment ID</span>
                    <strong>&nbsp;{{ order.payment.id_pago }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Status</span>
                    <strong>&nbsp;{{ order.payment.estado || 'Not updated' }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Method</span>
                    <strong>&nbsp;{{ order.payment.metodo_pago || order.metodo_pago }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Authorization</span>
                    <strong>&nbsp;{{ order.payment.codigo_autorizacion || 'No code' }}</strong>
                  </div>
                </div>
                <p v-else class="tracking-empty-copy">
                  There is no synchronized payment movement for this order yet.
                </p>
              </article>

              <article class="tracking-card tracking-card--wide">
                <div class="tracking-card__topline">
                  <h3>Your Order</h3>
                  <span class="tracking-total">{{ formattedCurrency(order.total) }}</span>
                </div>

                <div class="tracking-items">
                  <div v-for="item in order.items" :key="`${item.product_name}-${item.quantity}`" class="tracking-item">
                    <div>
                      <strong>&nbsp;{{ item.product_name }}</strong>
                      <span>&nbsp;{{ item.quantity }} piece(s)</span>
                    </div>
                    <div class="tracking-item__prices">
                      <strong>&nbsp;{{ formattedCurrency(item.total) }}</strong>
                      <span>&nbsp;{{ formattedCurrency(item.unit_price) }} each</span>
                    </div>
                  </div>
                </div>

                <div class="tracking-price-breakdown">
                  <div class="tracking-price-row">
                    <span class="tracking-label">Subtotal</span>
                    <strong>{{ formattedCurrency((Number(order.total) - (order.shipping_cost || 0) + (order.discount_amount || 0))) }}</strong>
                  </div>
                  <div v-if="order.discount_amount && order.discount_amount > 0" class="tracking-price-row tracking-price-row--discount">
                    <span class="tracking-label">
                      Discount
                      <span v-if="order.discount_code">({{ order.discount_code }})</span>
                    </span>
                    <strong>-{{ formattedCurrency(order.discount_amount) }}</strong>
                  </div>
                  <div v-if="order.shipping_cost" class="tracking-price-row">
                    <span class="tracking-label">Shipping</span>
                    <strong>{{ formattedCurrency(order.shipping_cost) }}</strong>
                  </div>
                  <div class="tracking-price-row tracking-price-row--total">
                    <span class="tracking-label">Total</span>
                    <strong>{{ formattedCurrency(order.total) }}</strong>
                  </div>
                </div>
              </article>

              <article v-if="order.notes" class="tracking-card tracking-card--wide">
                <h3>Purchase Notes</h3>
                <p class="tracking-notes">&nbsp;{{ order.notes }}</p>
              </article>
            </div>

            <div v-else class="tracking-empty">
              <div class="tracking-empty__seal">IQ</div>
              <h2>Open your tracking to see the status.</h2>
              <p>
                Use the link from the email or enter the email you used to make your purchase to check your order.
              </p>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const order = ref(null);
const email = ref('');
const loading = ref(false);
const errorMessage = ref('');

const normalizedOrderNumber = computed(() => String(route.params.orderNumber || '').trim());
const trackingToken = computed(() => {
  const token = route.query.token;
  return typeof token === 'string' ? token.trim() : '';
});
const hasToken = computed(() => trackingToken.value.length > 0);

const formattedCreatedAt = computed(() => {
  if (!order.value?.created_at) {
    return 'No date';
  }

  return new Intl.DateTimeFormat('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short'
  }).format(new Date(order.value.created_at));
});

const statusThemeClass = computed(() => `tracking-status--${order.value?.status_tone || 'info'}`);

const statusDetails = computed(() => {
  switch (order.value?.status) {
    case 'aprobado':
      return {
        headline: 'Your payment was confirmed and the order is set aside.',
        detail: 'The purchase is now valid and can proceed to internal preparation before shipping.',
        nextStep: 'Prepare packaging and shipment',
        eta: 'Update coming soon',
      };
    case 'procesando':
      return {
        headline: 'We are preparing your order.',
        detail: 'The team has taken your order and is reviewing pieces, packaging and shipment.',
        nextStep: 'Confirm preparation and tracking',
        eta: 'Usually within 24 to 48 hours',
      };
    case 'pendiente':
      return {
        headline: 'Payment is still under review.',
        detail: 'We are still waiting for confirmation from the processor or bank validation.',
        nextStep: 'Validate payment',
        eta: 'May take a few minutes',
      };
    case 'pendiente_transferencia':
      return {
        headline: 'We are waiting for your transfer confirmation.',
        detail: 'Once the deposit is validated, the order will change to preparation.',
        nextStep: 'Check receipt',
        eta: 'Depends on manual validation',
      };
    case 'enviado':
      return {
        headline: 'Your order is on its way.',
        detail: 'The order has left preparation and has been sent to the registered address.',
        nextStep: 'Wait for delivery',
        eta: 'According to the courier',
      };
    case 'entregado':
      return {
        headline: 'Delivery is complete.',
        detail: 'Your order now shows as delivered.',
        nextStep: 'Enjoy your purchase',
        eta: 'Completed',
      };
    case 'rechazado':
    case 'cancelado':
    case 'cancelled':
      return {
        headline: 'The order needs attention.',
        detail: 'The order could not continue due to rejection or cancellation of the process.',
        nextStep: 'Contact support or retry payment',
        eta: 'Pending action',
      };
    default:
      return {
        headline: 'We are tracking your order.',
        detail: 'This panel updates as your order status changes.',
        nextStep: 'Wait for next update',
        eta: 'No estimate',
      };
  }
});

const statusNarrative = computed(() => {
  const currentStatus = order.value?.status;

  switch (currentStatus) {
    case 'aprobado':
      return '✅ Approved';
    case 'procesando':
      return '🔄 Processing';
    case 'pendiente':
      return '⏳ Pending';
    case 'pendiente_transferencia':
      return '⏳ Pending Transfer';
    case 'enviado':
      return '📦 Shipped';
    case 'entregado':
      return '✅ Delivered';
    case 'rechazado':
      return '❌ Rejected';
    case 'cancelado':
    case 'cancelled':
      return '❌ Cancelled';
    default:
      return '❓ Unknown Status';
  }
});

const trackingSteps = computed(() => {
  const currentStatus = order.value?.status;
  const isRejected = ['rechazado', 'cancelado', 'cancelled'].includes(currentStatus);

  return [
    {
      key: 'created',
      title: '📋 Order Registered',
      copy: 'Your purchase already exists in the system.',
      done: Boolean(order.value?.order_number),
      active: currentStatus === 'pendiente' || currentStatus === 'pendiente_transferencia',
    },
    {
      key: 'validated',
      title: '✅ Payment Validated',
      copy: 'We confirmed payment or purchase authorization.',
      done: ['aprobado', 'procesando', 'enviado', 'entregado'].includes(currentStatus),
      active: currentStatus === 'aprobado',
    },
    {
      key: 'processing',
      title: '🔄 Processing',
      copy: 'Packaging, review and shipment of the item.',
      done: ['enviado', 'entregado'].includes(currentStatus),
      active: currentStatus === 'procesando',
    },
    {
      key: 'delivery',
      title: isRejected ? '⚠️ Incident' : '📦 Delivery',
      copy: isRejected ? 'The order requires action to continue.' : 'The order is on its way or has already been delivered.',
      done: currentStatus === 'entregado',
      active: ['enviado', 'rechazado', 'cancelado', 'cancelled'].includes(currentStatus),
    },
  ];
});

const formattedCurrency = (amount) => {
  const numericAmount = Number(amount || 0);

  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
  }).format(numericAmount);
};

const fetchOrderTracking = async () => {
  if (!normalizedOrderNumber.value) {
    errorMessage.value = 'We could not find a valid order number in the link.';
    return;
  }

  if (!hasToken.value && !email.value.trim()) {
    errorMessage.value = 'Enter the email you used to make your purchase to check your order.';
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  const searchParams = new URLSearchParams({
    order_number: normalizedOrderNumber.value,
  });

  if (!hasToken.value) {
    searchParams.set('email', email.value.trim());
  }

  const headers = {
    Accept: 'application/json',
  };

  if (hasToken.value) {
    headers.Authorization = `Bearer ${trackingToken.value}`;
  }

  try {
    const response = await fetch(`/api/pedidos/seguimiento?${searchParams.toString()}`, {
      headers,
    });

    const result = await response.json();

    // console.log('Order tracking response:', result);

    if (!response.ok) {
      throw new Error(result?.message || 'No se pudo obtener el seguimiento del pedido.');
    }

    order.value = result.order;
  } catch (error) {
    order.value = null;
    errorMessage.value = error.message || 'Could not check order tracking.';
  } finally {
    loading.value = false;
  }
};

const submitLookup = async () => {
  await fetchOrderTracking();
};

onMounted(async () => {
  if (hasToken.value) {
    await fetchOrderTracking();
  }
});
</script>

<style scoped>
.tracking-page {
  margin-top: 0px;
  background:
    radial-gradient(circle at top left, rgba(216, 196, 173, 0.28), transparent 28%),
    linear-gradient(180deg, #fbf8f4 0%, #f0e9e0 100%);
  color: #5f5244;
  min-height: calc(100vh - 70px);
}

.tracking-hero {
  position: relative;
  overflow: hidden;
}

.tracking-shell {
  display: grid;
  grid-template-columns: 1.15fr 0.85fr;
  gap: 1.5rem;
  align-items: stretch;
}

.tracking-hero-copy,
.tracking-access-card,
.tracking-card,
.tracking-empty,
.tracking-loading {
  border-radius: 30px;
  border: 1px solid rgba(184, 151, 120, 0.16);
  background: rgba(255, 251, 246, 0.88);
  box-shadow: 0 22px 52px rgba(111, 90, 70, 0.08);
}

.tracking-hero-copy {
  padding: 3rem;
  background:
    radial-gradient(circle at top right, rgba(217, 200, 181, 0.35), transparent 30%),
    linear-gradient(180deg, rgba(255, 251, 246, 0.92), rgba(244, 237, 228, 0.9));
}

.tracking-kicker,
.tracking-label {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  color: #8c745f;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.74rem;
}

.tracking-hero-copy h1 {
  font-size: clamp(2.8rem, 5vw, 4.9rem);
  margin: 1rem 0 1.1rem;
  color: #6b5b47;
  line-height: 0.94;
}

.tracking-hero-copy p,
.tracking-access-copy,
.tracking-empty p,
.tracking-notes,
.tracking-empty-copy {
  line-height: 1.9;
  color: #7c6b58;
}

.tracking-access-card {
  padding: 2rem;
}

.tracking-access-head,
.tracking-card__topline,
.tracking-item {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}

.tracking-access-head strong,
.tracking-meta-grid strong,
.tracking-detail-list strong,
.tracking-item strong,
.tracking-total {
  color: #5d4d3c;
}

.tracking-token-pill,
.tracking-status {
  display: inline-flex;
  align-items: center;
  padding: 0.45rem 0.9rem;
  border-radius: 999px;
  font-size: 0.78rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.tracking-token-pill {
  background: rgba(166, 139, 115, 0.14);
  color: #8c745f;
}

.tracking-token-pill--ready,
.tracking-status--success {
  background: rgba(79, 145, 101, 0.14);
  color: #2f7550;
}

.tracking-status--processing {
  background: rgba(95, 111, 166, 0.16);
  color: #445b9a;
}

.tracking-status--warning {
  background: rgba(201, 147, 68, 0.15);
  color: #9a6700;
}

.tracking-status--error {
  background: rgba(176, 92, 78, 0.14);
  color: #9a5d56;
}

.tracking-status--info {
  background: rgba(140, 116, 95, 0.12);
  color: #7c6755;
}

.tracking-form {
  margin-top: 1.3rem;
}

.tracking-button {
  width: 100%;
  margin-top: 0.4rem;
  border-radius: 999px !important;
  background: linear-gradient(135deg, #8c745f, #a88d74) !important;
  color: #fffdf9 !important;
  letter-spacing: 0.08em;
}

.tracking-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.4rem;
}

.tracking-state-panel {
  margin-top: 1.5rem;
  padding: 1.2rem;
  border-radius: 24px;
  border: 1px solid rgba(184, 151, 120, 0.16);
  background: rgba(247, 241, 234, 0.9);
}

.tracking-state-panel__lead strong,
.tracking-state-panel__grid strong,
.tracking-progress__step strong {
  display: block;
  color: #5d4d3c;
}

.tracking-state-panel__lead p {
  margin: 0.55rem 0 0;
  color: #7c6b58;
  line-height: 1.75;
}

.tracking-state-panel__grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.9rem;
  margin-top: 1rem;
}

.tracking-state-panel__grid div {
  padding: 0.95rem 1rem;
  border-radius: 18px;
  background: rgba(255, 251, 246, 0.86);
}

.tracking-state-panel.tracking-status--processing {
  background: linear-gradient(135deg, rgba(235, 239, 252, 0.95), rgba(243, 238, 251, 0.92));
  border-color: rgba(95, 111, 166, 0.24);
}

.tracking-state-panel.tracking-status--success {
  background: linear-gradient(135deg, rgba(234, 246, 238, 0.96), rgba(245, 250, 246, 0.92));
  border-color: rgba(79, 145, 101, 0.2);
}

.tracking-state-panel.tracking-status--warning {
  background: linear-gradient(135deg, rgba(250, 243, 226, 0.96), rgba(251, 247, 240, 0.92));
  border-color: rgba(201, 147, 68, 0.2);
}

.tracking-state-panel.tracking-status--error {
  background: linear-gradient(135deg, rgba(250, 236, 233, 0.96), rgba(251, 244, 241, 0.92));
  border-color: rgba(176, 92, 78, 0.2);
}

.tracking-progress {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 0.9rem;
  margin-top: 1.2rem;
}

.tracking-progress__step {
  display: flex;
  gap: 0.8rem;
  align-items: flex-start;
  padding: 1rem;
  border-radius: 20px;
  background: rgba(245, 239, 233, 0.68);
  border: 1px solid transparent;
}

.tracking-progress__dot {
  width: 12px;
  height: 12px;
  margin-top: 0.35rem;
  border-radius: 50%;
  background: rgba(140, 116, 95, 0.22);
  flex: 0 0 12px;
}

.tracking-progress__step span {
  display: block;
  margin-top: 0.25rem;
  color: #836f5d;
  line-height: 1.6;
}

.tracking-progress__step--done {
  background: rgba(233, 245, 237, 0.86);
  border-color: rgba(79, 145, 101, 0.18);
}

.tracking-progress__step--done .tracking-progress__dot {
  background: #2f7550;
}

.tracking-progress__step--active {
  background: rgba(240, 235, 250, 0.92);
  border-color: rgba(95, 111, 166, 0.24);
}

.tracking-progress__step--active .tracking-progress__dot {
  background: #445b9a;
  box-shadow: 0 0 0 6px rgba(95, 111, 166, 0.12);
}

.tracking-card {
  padding: 1.8rem;
}

.tracking-card--hero,
.tracking-card--wide {
  grid-column: 1 / -1;
}

.tracking-meta-grid,
.tracking-detail-list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
  margin-top: 1.3rem;
}

.tracking-detail-list div,
.tracking-meta-grid div {
  padding: 1rem;
  border-radius: 20px;
  background: rgba(245, 239, 233, 0.82);
}

.tracking-items {
  display: grid;
  gap: 0.9rem;
  margin-top: 1.2rem;
}

.tracking-item {
  align-items: center;
  padding: 1rem 1.1rem;
  border-radius: 20px;
  background: rgba(245, 239, 233, 0.8);
}

.tracking-item span,
.tracking-item__prices span {
  display: block;
  color: #836f5d;
  margin-top: 0.25rem;
}

.tracking-item__prices {
  text-align: right;
}

.tracking-price-breakdown {
  margin-top: 1.5rem;
  padding: 1rem;
  border-radius: 20px;
  background: rgba(245, 239, 233, 0.8);
  border-top: 1px solid rgba(184, 151, 120, 0.16);
}

.tracking-price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.7rem 0;
  border-bottom: 1px solid rgba(184, 151, 120, 0.1);
  font-size: 0.95rem;
}

.tracking-price-row:last-child {
  border-bottom: none;
}

.tracking-price-row strong {
  color: #5d4d3c;
  font-weight: 700;
}

.tracking-price-row--discount strong {
  color: #2f7550;
}

.tracking-price-row--total {
  padding-top: 1rem;
  font-weight: 700;
  border-top: 1px solid rgba(184, 151, 120, 0.2);
}

.tracking-price-row--total strong {
  font-size: 1.1rem;
  color: #5d4d3c;
}

.tracking-total {
  font-size: 1.2rem;
}

.tracking-empty,
.tracking-loading {
  padding: 3rem;
  text-align: center;
}

.tracking-empty__seal,
.tracking-loading__orb {
  width: 82px;
  height: 82px;
  margin: 0 auto 1.2rem;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: radial-gradient(circle at 30% 30%, #f0dfc8, #b89778 68%, #8b6e56 100%);
  color: #fffaf4;
  letter-spacing: 0.16em;
}

.tracking-loading__orb {
  position: relative;
  animation: pulse 1.6s ease-in-out infinite;
}

.tracking-loading__orb::after {
  content: '';
  width: 24px;
  height: 24px;
  border-radius: 50%;
  border: 2px solid rgba(255, 250, 244, 0.7);
  border-top-color: transparent;
  animation: spin 1.1s linear infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(184, 151, 120, 0.18);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 0 14px rgba(184, 151, 120, 0);
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 960px) {
  .tracking-shell,
  .tracking-grid,
  .tracking-progress,
  .tracking-state-panel__grid,
  .tracking-meta-grid,
  .tracking-detail-list {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .tracking-page {
    margin-top: 84px;
    min-height: calc(100vh - 84px);
  }

  .tracking-hero-copy,
  .tracking-access-card,
  .tracking-card,
  .tracking-empty,
  .tracking-loading {
    padding: 1.5rem;
  }

  .tracking-access-head,
  .tracking-card__topline,
  .tracking-item {
    flex-direction: column;
  }

  .tracking-item__prices {
    text-align: left;
  }
}
</style>