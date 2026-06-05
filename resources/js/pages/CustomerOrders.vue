<template>
  <div class="orders-page">
    <section class="orders-hero">
      <v-container class="py-16">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="orders-shell">
              <div class="orders-copy">
                <span class="orders-kicker">Mis pedidos</span>
                <h1>Tus compras, en un solo lugar.</h1>
                <p>
                  Consulta tus pedidos sin contraseña. Solo escribe tu correo, recibe un código temporal y revisa el historial asociado.
                </p>
              </div>

              <div class="orders-access-card">
                <div class="orders-access-head">
                  <div>
                    <span class="orders-label">Acceso</span>
                    <strong>&nbsp;{{ isVerified ? 'Verificado' : verificationStepLabel }}</strong>
                  </div>
                  <span class="orders-pill" :class="{ 'orders-pill--ready': isVerified }">
                    {{ isVerified ? 'Historial desbloqueado' : 'Codigo temporal por correo' }}
                  </span>
                </div>

                <p class="orders-access-copy">
                  {{ isVerified
                    ? 'Tu historial ya esta listo. Si vuelves despues, solo solicita otro codigo.'
                    : 'Usamos el mismo correo de tus compras para enviarte un codigo de acceso que vence en 15 minutos.' }}
                </p>

                <v-form class="orders-form" @submit.prevent="handlePrimaryAction">
                  <v-text-field
                    v-model="email"
                    label="Correo electronico"
                    type="email"
                    variant="outlined"
                    density="comfortable"
                    :disabled="loading || codeRequested"
                    hint="Usa el correo con el que hiciste tu compra."
                    persistent-hint
                  ></v-text-field>

                  <v-text-field
                    v-if="codeRequested && !isVerified"
                    v-model="code"
                    label="Codigo de 6 digitos"
                    variant="outlined"
                    density="comfortable"
                    maxlength="6"
                    counter="6"
                    hint="Te lo enviamos por correo y vence en 15 minutos."
                    persistent-hint
                  ></v-text-field>

                  <div class="orders-actions">
                    <v-btn
                      class="orders-button"
                      type="submit"
                      elevation="0"
                      :loading="loading"
                      :disabled="loading || !canSubmitPrimary"
                    >
                      {{ primaryButtonLabel }}
                    </v-btn>

                    <v-btn
                      v-if="codeRequested && !isVerified"
                      variant="text"
                      color="secondary"
                      :disabled="loading || !email"
                      @click="requestCode"
                    >
                      Reenviar codigo
                    </v-btn>

                    <v-btn
                      v-if="codeRequested || isVerified"
                      variant="text"
                      color="secondary"
                      :disabled="loading"
                      @click="resetAccess"
                    >
                      Empezar de nuevo
                    </v-btn>
                  </div>
                </v-form>

                <v-alert
                  v-if="infoMessage"
                  type="success"
                  variant="tonal"
                  class="mt-4"
                >
                  {{ infoMessage }}
                </v-alert>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section class="orders-content">
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

            <div v-if="loading" class="orders-loading">
              <div class="orders-loading__orb"></div>
              <p>Estamos consultando tus compras...</p>
            </div>

            <div v-else-if="isVerified && customer" class="orders-grid">
              <article class="orders-card orders-card--profile">
                <div class="orders-card__topline">
                  <span class="orders-label">Cliente</span>
                  <span class="orders-badge">{{ orders.length }} pedido(s)</span>
                </div>

                <h2>{{ customer.first_name }} {{ customer.last_name }}</h2>
                <p>
                  Este panel muestra las compras que encontramos asociadas a {{ customer.email }}.
                </p>

                <div class="orders-profile-grid">
                  <div>
                    <span class="orders-label">Telefono</span>
                    <strong>&nbsp;{{ customer.phone || 'Sin telefono' }}</strong>
                  </div>
                  <div>
                    <span class="orders-label">Direccion</span>
                    <strong>&nbsp;{{ customer.address || 'Sin direccion' }}</strong>
                  </div>
                  <div>
                    <span class="orders-label">Ciudad / Estado</span>
                    <strong>&nbsp;{{ customer.city || 'Sin ciudad' }}, {{ customer.state || 'Sin estado' }}</strong>
                  </div>
                  <div>
                    <span class="orders-label">CP</span>
                    <strong>&nbsp;{{ customer.zip_code || 'Sin CP' }}</strong>
                  </div>
                </div>
              </article>

              <article
                v-for="order in orders"
                :key="order.order_number"
                class="orders-card"
              >
                <div class="orders-card__topline">
                  <div>
                    <span class="orders-label">Orden</span>
                    <h3>{{ order.order_number }}</h3>
                  </div>
                  <span class="orders-status" :class="`orders-status--${order.status_tone}`">
                    {{ order.status_label }}
                  </span>
                </div>

                <div class="orders-meta-grid">
                  <div>
                    <span class="orders-label">Fecha</span>
                    <strong>&nbsp;{{ formatDate(order.created_at) }}</strong>
                  </div>
                  <div>
                    <span class="orders-label">Pago</span>
                    <strong>&nbsp;{{ order.metodo_pago || 'No especificado' }}</strong>
                  </div>
                  <div>
                    <span class="orders-label">Total</span>
                    <strong>&nbsp;{{ formatCurrency(order.total) }}</strong>
                  </div>
                </div>

                <div class="orders-items">
                  <div v-for="item in order.items" :key="`${order.order_number}-${item.product_name}`" class="orders-item">
                    <div>
                      <strong>&nbsp;{{ item.product_name }}</strong>
                      <span>&nbsp;{{ item.quantity }} pieza(s)</span>
                    </div>
                    <span>&nbsp;{{ formatCurrency(item.total) }}</span>
                  </div>
                </div>

                <div class="orders-card__actions">
                  <a :href="order.tracking_url" class="orders-link">
                    Ver seguimiento detallado
                  </a>
                </div>
              </article>
            </div>

            <div v-else class="orders-empty">
              <div class="orders-empty__seal">IQ</div>
              <h2>Solicita tu codigo para ver tus pedidos.</h2>
              <p>
                Si ya compraste con nosotros, te enviaremos un acceso temporal al correo que usaste en tu checkout.
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

const savedProfileKey = 'ecommerce_checkout_profile';
const customerOrdersSessionKey = 'ecommerce_customer_orders_session';

const email = ref('');
const code = ref('');
const codeRequested = ref(false);
const loading = ref(false);
const infoMessage = ref('');
const errorMessage = ref('');
const customer = ref(null);
const orders = ref([]);

const isVerified = computed(() => customer.value !== null && orders.value.length > 0);
const verificationStepLabel = computed(() => codeRequested.value ? 'Confirma tu codigo' : 'Solicita tu codigo');
const primaryButtonLabel = computed(() => codeRequested.value && !isVerified.value ? 'Ver mis pedidos' : 'Enviar codigo');
const canSubmitPrimary = computed(() => {
  if (!email.value.trim()) {
    return false;
  }

  if (codeRequested.value && !isVerified.value) {
    return code.value.trim().length === 6;
  }

  return true;
});

const persistSession = () => {
  window.sessionStorage.setItem(customerOrdersSessionKey, JSON.stringify({
    email: email.value,
    customer: customer.value,
    orders: orders.value,
    codeRequested: codeRequested.value,
  }));
};

const restoreSession = () => {
  const raw = window.sessionStorage.getItem(customerOrdersSessionKey);

  if (!raw) {
    return;
  }

  try {
    const parsed = JSON.parse(raw);
    email.value = parsed.email || '';
    customer.value = parsed.customer || null;
    orders.value = parsed.orders || [];
    codeRequested.value = Boolean(parsed.codeRequested || parsed.orders?.length);
  } catch (error) {
    console.warn('No se pudo restaurar la sesion de pedidos.', error);
    window.sessionStorage.removeItem(customerOrdersSessionKey);
  }
};

const preloadEmail = () => {
  const rawProfile = window.localStorage.getItem(savedProfileKey);

  if (!rawProfile) {
    return;
  }

  try {
    const profile = JSON.parse(rawProfile);
    if (!email.value && profile.email) {
      email.value = profile.email;
    }
  } catch (error) {
    console.warn('No se pudo precargar el correo guardado.', error);
  }
};

const resetAccess = () => {
  code.value = '';
  codeRequested.value = false;
  customer.value = null;
  orders.value = [];
  infoMessage.value = '';
  errorMessage.value = '';
  window.sessionStorage.removeItem(customerOrdersSessionKey);
};

const requestCode = async () => {
  if (!email.value.trim()) {
    errorMessage.value = 'Escribe tu correo para continuar.';
    return;
  }

  loading.value = true;
  errorMessage.value = '';
  infoMessage.value = '';

  try {
    const response = await fetch('/api/clientes/pedidos/acceso', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({ email: email.value.trim() })
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result?.message || 'No se pudo enviar el codigo.');
    }

    codeRequested.value = true;
    infoMessage.value = result.message;
    persistSession();
  } catch (error) {
    errorMessage.value = error.message || 'No se pudo enviar el codigo.';
  } finally {
    loading.value = false;
  }
};

const verifyCode = async () => {
  loading.value = true;
  errorMessage.value = '';
  infoMessage.value = '';

  try {
    const response = await fetch('/api/clientes/pedidos/verificar', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({
        email: email.value.trim(),
        code: code.value.trim()
      })
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result?.message || 'No se pudo verificar el codigo.');
    }

    customer.value = result.customer;
    orders.value = result.orders;
    infoMessage.value = 'Acceso verificado. Ya puedes revisar tus pedidos.';
    persistSession();
  } catch (error) {
    customer.value = null;
    orders.value = [];
    errorMessage.value = error.message || 'No se pudo verificar el codigo.';
  } finally {
    loading.value = false;
  }
};

const handlePrimaryAction = async () => {
  if (codeRequested.value && !isVerified.value) {
    await verifyCode();
    return;
  }

  await requestCode();
};

const formatCurrency = (amount) => new Intl.NumberFormat('es-MX', {
  style: 'currency',
  currency: 'MXN'
}).format(Number(amount || 0));

const formatDate = (dateString) => {
  if (!dateString) {
    return 'Sin fecha';
  }

  return new Intl.DateTimeFormat('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short'
  }).format(new Date(dateString));
};

onMounted(() => {
  preloadEmail();
  restoreSession();
});
</script>

<style scoped>
.orders-page {
  margin-top: 70px;
  background:
    radial-gradient(circle at top left, rgba(216, 196, 173, 0.28), transparent 28%),
    linear-gradient(180deg, #fbf8f4 0%, #f0e9e0 100%);
  color: #5f5244;
  min-height: calc(100vh - 70px);
}

.orders-shell {
  display: grid;
  grid-template-columns: 1.15fr 0.85fr;
  gap: 1.5rem;
  align-items: stretch;
}

.orders-copy,
.orders-access-card,
.orders-card,
.orders-empty,
.orders-loading {
  border-radius: 30px;
  border: 1px solid rgba(184, 151, 120, 0.16);
  background: rgba(255, 251, 246, 0.88);
  box-shadow: 0 22px 52px rgba(111, 90, 70, 0.08);
}

.orders-copy {
  padding: 3rem;
  background:
    radial-gradient(circle at top right, rgba(217, 200, 181, 0.35), transparent 30%),
    linear-gradient(180deg, rgba(255, 251, 246, 0.92), rgba(244, 237, 228, 0.9));
}

.orders-kicker,
.orders-label {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  color: #8c745f;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.74rem;
}

.orders-copy h1 {
  font-size: clamp(2.8rem, 5vw, 4.6rem);
  margin: 1rem 0 1.1rem;
  color: #6b5b47;
  line-height: 0.94;
}

.orders-copy p,
.orders-access-copy,
.orders-empty p,
.orders-card p {
  line-height: 1.9;
  color: #7c6b58;
}

.orders-access-card,
.orders-card {
  padding: 2rem;
}

.orders-access-head,
.orders-card__topline,
.orders-item,
.orders-card__actions {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}

.orders-pill,
.orders-status,
.orders-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.45rem 0.9rem;
  border-radius: 999px;
  font-size: 0.78rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.orders-pill,
.orders-badge,
.orders-status--info {
  background: rgba(166, 139, 115, 0.14);
  color: #8c745f;
}

.orders-pill--ready,
.orders-status--success {
  background: rgba(79, 145, 101, 0.14);
  color: #2f7550;
}

.orders-status--processing {
  background: rgba(95, 111, 166, 0.16);
  color: #445b9a;
}

.orders-status--warning {
  background: rgba(201, 147, 68, 0.15);
  color: #9a6700;
}

.orders-status--error {
  background: rgba(176, 92, 78, 0.14);
  color: #9a5d56;
}

.orders-form {
  margin-top: 1.3rem;
}

.orders-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.orders-button {
  border-radius: 999px !important;
  background: linear-gradient(135deg, #8c745f, #a88d74) !important;
  color: #fffdf9 !important;
  letter-spacing: 0.08em;
}

.orders-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.4rem;
}

.orders-card--profile {
  grid-column: 1 / -1;
}

.orders-profile-grid,
.orders-meta-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
  margin-top: 1.5rem;
}

.orders-items {
  margin-top: 1.5rem;
  display: grid;
  gap: 0.8rem;
}

.orders-item {
  padding: 1rem 0;
  border-bottom: 1px solid rgba(184, 151, 120, 0.16);
}

.orders-item:last-child {
  border-bottom: 0;
}

.orders-link {
  color: #7b5f3f;
  text-decoration: none;
  font-weight: 700;
}

.orders-empty,
.orders-loading {
  padding: 3rem;
  text-align: center;
}

.orders-empty__seal,
.orders-loading__orb {
  width: 72px;
  height: 72px;
  margin: 0 auto 1.4rem;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, rgba(140, 116, 95, 0.92), rgba(168, 141, 116, 0.85));
  color: #fffaf4;
  letter-spacing: 0.18em;
}

.orders-loading__orb {
  position: relative;
}

.orders-loading__orb::after {
  content: '';
  position: absolute;
  inset: 10px;
  border-radius: 50%;
  border: 2px solid rgba(255, 250, 244, 0.7);
  border-top-color: transparent;
  animation: spin 1.1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 960px) {
  .orders-shell,
  .orders-grid {
    grid-template-columns: 1fr;
  }

  .orders-copy,
  .orders-access-card,
  .orders-card,
  .orders-empty,
  .orders-loading {
    padding: 1.5rem;
  }

  .orders-profile-grid,
  .orders-meta-grid {
    grid-template-columns: 1fr;
  }
}
</style>