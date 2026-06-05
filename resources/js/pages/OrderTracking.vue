<template>
  <div class="tracking-page">
    <section class="tracking-hero">
      <v-container class="py-16">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="tracking-shell">
              <div class="tracking-hero-copy">
                <span class="tracking-kicker">Seguimiento</span>
                <h1>Tu pedido tiene un hilo propio.</h1>
                <p>
                  Consulta el estado de tu orden con tu número de pedido.
                  Si entraste desde el correo, el acceso ya viene listo.
                </p>
              </div>

              <div class="tracking-access-card">
                <div class="tracking-access-head">
                  <div>
                    <span class="tracking-label">Orden</span>
                    <strong>&nbsp;{{ normalizedOrderNumber }}</strong>
                  </div>
                  <span class="tracking-token-pill" :class="{ 'tracking-token-pill--ready': hasToken }">
                    {{ hasToken ? 'Acceso seguro listo' : 'Se requiere verificacion' }}
                  </span>
                </div>
              

                <p class="tracking-access-copy">
                  {{ hasToken
                    ? 'Abriste un enlace privado con token de seguimiento. Vamos a consultar tu pedido en cuanto cargue la pagina.'
                    : 'Si abriste la liga sin token, confirma tu correo para consultar el estatus.' }}
                </p>

                <v-form class="tracking-form" @submit.prevent="submitLookup">
                  <v-text-field
                    v-model="email"
                    label="Correo electronico"
                    type="email"
                    variant="outlined"
                    density="comfortable"
                    :disabled="loading || hasToken"
                    :hint="hasToken ? 'Este enlace ya incluye la verificacion necesaria.' : 'Usa el mismo correo con el que hiciste tu compra.'"
                    persistent-hint
                  ></v-text-field>

                  <v-btn
                    class="tracking-button"
                    type="submit"
                    elevation="0"
                    :loading="loading"
                    :disabled="loading || (!hasToken && !email)"
                  >
                    {{ hasToken ? 'Actualizar estatus' : 'Consultar pedido' }}
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
              <p>Consultando el recorrido de tu orden...</p>
            </div>

            <div v-else-if="order" class="tracking-grid">
              <article class="tracking-card tracking-card--hero">
                <div class="tracking-card__topline">
                  <span class="tracking-label">Estado actual</span>
                  <span class="tracking-status" :class="statusThemeClass">
                    {{ order.status_label }}
                  </span>
                </div>

                <h2>{{ order.order_number }}</h2>
                <p>
                  {{ statusNarrative }}
                </p>

                <div class="tracking-state-panel" :class="statusThemeClass">
                  <div class="tracking-state-panel__lead">
                    <span class="tracking-label">Lo que esta pasando</span>
                    <strong>{{ statusDetails.headline }}</strong>
                    <p>{{ statusDetails.detail }}</p>
                  </div>
                  <div class="tracking-state-panel__grid">
                    <div>
                      <span class="tracking-label">Siguiente paso</span>
                      <strong>{{ statusDetails.nextStep }}</strong>
                    </div>
                    <div>
                      <span class="tracking-label">Tiempo estimado</span>
                      <strong>{{ statusDetails.eta }}</strong>
                    </div>
                    <div>
                      <span class="tracking-label">Referencia</span>
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
                    <span class="tracking-label">Cliente</span>
                    <strong>&nbsp;{{ order.customer_full_name }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Estado de la orden</span>
                    <strong>&nbsp;{{ order.status_label }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Pago</span>
                    <strong>&nbsp;{{ order.metodo_pago || 'No especificado' }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Creado</span>
                    <strong>&nbsp;{{ formattedCreatedAt }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Total</span>
                    <strong>&nbsp;{{ formattedCurrency(order.total) }}</strong>
                  </div>
                </div>
              </article>

              <article class="tracking-card">
                <h3>Entrega</h3>
                <div class="tracking-detail-list">
                  <div>
                    <span class="tracking-label">Direccion</span>
                    <strong>&nbsp;{{ order.shipping_address }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Ciudad / Estado</span>
                    <strong>&nbsp;{{ order.shipping_city }}, {{ order.shipping_state }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Codigo postal</span>
                    <strong>&nbsp;{{ order.shipping_zip_code }}</strong>
                  </div>
                </div>
              </article>

              <article class="tracking-card">
                <h3>Movimiento de pago</h3>
                <div v-if="order.payment" class="tracking-detail-list">
                  <div>
                    <span class="tracking-label">ID de pago</span>
                    <strong>&nbsp;{{ order.payment.id_pago }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Estado</span>
                    <strong>&nbsp;{{ order.payment.estado || 'Sin actualizar' }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Metodo</span>
                    <strong>&nbsp;{{ order.payment.metodo_pago || order.metodo_pago }}</strong>
                  </div>
                  <div>
                    <span class="tracking-label">Autorizacion</span>
                    <strong>&nbsp;{{ order.payment.codigo_autorizacion || 'Sin codigo' }}</strong>
                  </div>
                </div>
                <p v-else class="tracking-empty-copy">
                  Todavia no hay un movimiento de pago sincronizado para esta orden.
                </p>
              </article>

              <article class="tracking-card tracking-card--wide">
                <div class="tracking-card__topline">
                  <h3>Tu pedido</h3>
                  <span class="tracking-total">{{ formattedCurrency(order.total) }}</span>
                </div>

                <div class="tracking-items">
                  <div v-for="item in order.items" :key="`${item.product_name}-${item.quantity}`" class="tracking-item">
                    <div>
                      <strong>&nbsp;{{ item.product_name }}</strong>
                      <span>&nbsp;{{ item.quantity }} pieza(s)</span>
                    </div>
                    <div class="tracking-item__prices">
                      <strong>&nbsp;{{ formattedCurrency(item.total) }}</strong>
                      <span>&nbsp;{{ formattedCurrency(item.unit_price) }} c/u</span>
                    </div>
                  </div>
                </div>
              </article>

              <article v-if="order.notes" class="tracking-card tracking-card--wide">
                <h3>Notas de tu compra</h3>
                <p class="tracking-notes">&nbsp;{{ order.notes }}</p>
              </article>
            </div>

            <div v-else class="tracking-empty">
              <div class="tracking-empty__seal">IQ</div>
              <h2>Abre tu seguimiento para ver el estado.</h2>
              <p>
                Usa el enlace del correo o escribe el correo con el que realizaste la compra para consultar tu orden.
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
    return 'Sin fecha';
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
        headline: 'Tu pago fue confirmado y la orden quedo apartada.',
        detail: 'La compra ya es valida y puede pasar a preparacion interna antes de enviarse.',
        nextStep: 'Preparar empaque y salida',
        eta: 'Actualizacion en breve',
      };
    case 'processing':
      return {
        headline: 'Estamos preparando tu pedido.',
        detail: 'El equipo ya tomo tu orden y esta revisando piezas, empaque y salida para envio.',
        nextStep: 'Confirmar preparacion y guia',
        eta: 'Normalmente dentro de 24 a 48 horas',
      };
    case 'pendiente':
      return {
        headline: 'El pago sigue en revision.',
        detail: 'Todavia estamos esperando confirmacion del procesador o de la validacion bancaria.',
        nextStep: 'Validar pago',
        eta: 'Puede tardar unos minutos',
      };
    case 'pendiente_transferencia':
      return {
        headline: 'Esperamos la confirmacion de tu transferencia.',
        detail: 'En cuanto se valide el deposito, la orden cambiara a preparacion.',
        nextStep: 'Revisar comprobante',
        eta: 'Depende de la validacion manual',
      };
    case 'shipped':
      return {
        headline: 'Tu pedido ya va en camino.',
        detail: 'La orden salio de preparacion y ya fue enviada a la direccion registrada.',
        nextStep: 'Esperar entrega',
        eta: 'Segun la paqueteria',
      };
    case 'delivered':
      return {
        headline: 'La entrega se completo.',
        detail: 'Tu pedido ya aparece como entregado.',
        nextStep: 'Disfrutar tu compra',
        eta: 'Completado',
      };
    case 'rechazado':
    case 'cancelado':
    case 'cancelled':
      return {
        headline: 'La orden necesita atencion.',
        detail: 'El pedido no pudo continuar por rechazo o cancelacion del proceso.',
        nextStep: 'Contactar soporte o reintentar pago',
        eta: 'Pendiente de accion',
      };
    default:
      return {
        headline: 'Estamos siguiendo tu pedido.',
        detail: 'Este panel se actualiza conforme cambie el estado de la orden.',
        nextStep: 'Esperar siguiente actualizacion',
        eta: 'Sin estimado',
      };
  }
});

const statusNarrative = computed(() => {
  const currentStatus = order.value?.status;

  switch (currentStatus) {
    case 'aprobado':
      return 'Tu pago fue confirmado y la orden ya puede avanzar hacia preparacion y envio.';
    case 'processing':
      return 'Ya estamos trabajando tu pedido internamente antes de asignarlo a envio.';
    case 'pendiente':
      return 'Tu orden existe y el pago aun esta en validacion o en espera de confirmacion.';
    case 'pendiente_transferencia':
      return 'Estamos esperando la confirmacion manual de tu transferencia para continuar.';
    case 'shipped':
      return 'La orden ya fue enviada y esta en camino hacia la direccion registrada.';
    case 'delivered':
      return 'La entrega se marco como completada.';
    case 'rechazado':
    case 'cancelado':
    case 'cancelled':
      return 'La orden necesita atencion porque el pago no continuo o fue cancelado.';
    default:
      return 'Este panel se actualiza conforme cambia el estado de tu compra.';
  }
});

const trackingSteps = computed(() => {
  const currentStatus = order.value?.status;
  const isRejected = ['rechazado', 'cancelado', 'cancelled'].includes(currentStatus);

  return [
    {
      key: 'created',
      title: 'Orden registrada',
      copy: 'Tu compra ya existe en el sistema.',
      done: Boolean(order.value?.order_number),
      active: currentStatus === 'pendiente' || currentStatus === 'pendiente_transferencia',
    },
    {
      key: 'validated',
      title: 'Pago validado',
      copy: 'Confirmamos el pago o la autorizacion de compra.',
      done: ['aprobado', 'processing', 'shipped', 'delivered'].includes(currentStatus),
      active: currentStatus === 'aprobado',
    },
    {
      key: 'processing',
      title: 'Preparacion',
      copy: 'Empaque, revision y salida de la pieza.',
      done: ['shipped', 'delivered'].includes(currentStatus),
      active: currentStatus === 'processing',
    },
    {
      key: 'delivery',
      title: isRejected ? 'Incidencia' : 'Entrega',
      copy: isRejected ? 'La orden requiere accion para continuar.' : 'El pedido va en camino o ya fue entregado.',
      done: currentStatus === 'delivered',
      active: ['shipped', 'rechazado', 'cancelado', 'cancelled'].includes(currentStatus),
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
    errorMessage.value = 'No encontramos un numero de orden valido en el enlace.';
    return;
  }

  if (!hasToken.value && !email.value.trim()) {
    errorMessage.value = 'Escribe el correo con el que hiciste tu compra para consultar el pedido.';
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  const searchParams = new URLSearchParams({
    order_number: normalizedOrderNumber.value,
  });

  if (hasToken.value) {
    searchParams.set('token', trackingToken.value);
  } else {
    searchParams.set('email', email.value.trim());
  }

  try {
    const response = await fetch(`/api/pedidos/seguimiento?${searchParams.toString()}`, {
      headers: {
        Accept: 'application/json'
      }
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result?.message || 'No se pudo obtener el seguimiento del pedido.');
    }

    order.value = result.order;
  } catch (error) {
    order.value = null;
    errorMessage.value = error.message || 'No se pudo consultar el seguimiento del pedido.';
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
  margin-top: 70px;
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