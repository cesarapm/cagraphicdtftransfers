<template>
  <v-container class="checked-page">
    <h1 class="text-h3 mb-8">Finalizar Compra</h1>

    <v-alert v-if="cartItems.length === 0" type="warning" class="mb-6">
      Tu carrito está vacío. <router-link :to="{ name: 'ProductList' }">Ver productos</router-link>
    </v-alert>

    <v-form v-else @submit.prevent="submitOrder">
      <v-row>
        <v-col cols="12" md="8">
          <!-- Información Personal -->
          <v-card class="mb-6">
            <v-card-title>📋 Información Personal</v-card-title>
            <v-card-text>
              <v-alert
                v-if="savedProfileExists"
                type="info"
                variant="tonal"
                class="mb-4"
              >
                Encontramos datos guardados en este dispositivo y ya precargamos tu formulario.
                <template #append>
                  <v-btn variant="text" size="small" @click="clearSavedProfile">
                    Olvidar datos
                  </v-btn>
                </template>
              </v-alert>

              <v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.firstName"
                    label="Nombre *"
                    variant="outlined"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.lastName"
                    label="Apellido *"
                    variant="outlined"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.email"
                    label="Email *"
                    type="email"
                    variant="outlined"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.phone"
                    label="Teléfono *"
                    variant="outlined"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-checkbox
                    v-model="form.saveCustomerProfile"
                    color="primary"
                    density="comfortable"
                    hide-details
                    label="Guardar mis datos y compras en este dispositivo para la próxima vez"
                  ></v-checkbox>
                  <p class="text-body-2 text-medium-emphasis mt-2">
                    No te pediremos contraseña. Solo usaremos esta información para autocompletar futuras compras en este navegador.
                  </p>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Dirección de Envío -->
          <v-card class="mb-6">
            <v-card-title>📍 Dirección de Envío</v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="form.address"
                    label="Dirección *"
                    variant="outlined"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model="form.city"
                    label="Ciudad *"
                    variant="outlined"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model="form.state"
                    label="Estado *"
                    variant="outlined"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model="form.zipCode"
                    label="Código Postal *"
                    variant="outlined"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.notes"
                    label="Notas adicionales (opcional)"
                    variant="outlined"
                    rows="2"
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Método de Pago -->
          <v-card>
            <v-card-title>💳 Método de Pago</v-card-title>
            <v-card-text>
              <v-progress-circular 
                v-if="loadingPaymentMethods" 
                indeterminate 
                color="primary"
                class="mx-auto d-block"
              ></v-progress-circular>
              
              <v-alert 
                v-else-if="availablePaymentMethods.length === 0" 
                type="warning"
                variant="tonal"
              >
                No hay métodos de pago disponibles. Contacta al administrador.
              </v-alert>

              <v-radio-group 
                v-else 
                v-model="form.paymentMethod" 
                class="payment-methods-group"
              >
                <div 
                  v-for="method in availablePaymentMethods" 
                  :key="method.id"
                  class="payment-option" 
                  :class="{ 'payment-option--active': form.paymentMethod === method.id }"
                >
                  <v-radio 
                    :label="method.name" 
                    :value="method.id"
                  >
                    <template #label>
                      <span class="d-flex align-center">
                        <v-icon :icon="method.icon" size="small" class="mr-2"></v-icon>
                        {{ method.name }}
                      </span>
                    </template>
                  </v-radio>
                  <p class="payment-option__description">
                    {{ method.description }}
                  </p>
                </div>
              </v-radio-group>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Resumen -->
        <v-col cols="12" md="4">
          <v-card v-if="recentOrders.length" class="mb-4">
            <v-card-title class="d-flex justify-space-between align-center">
              <span>🕘 Compras recientes</span>
              <v-btn 
                icon="mdi-refresh" 
                size="x-small" 
                variant="text" 
                @click="loadRecentOrders"
                title="Actualizar estados"
              ></v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-list density="compact">
              <v-list-item v-for="order in recentOrders" :key="order.order_number">
                <v-list-item-title>
                  {{ order.order_number }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ order.status_label }} • {{ order.created_at }}
                </v-list-item-subtitle>
                <template v-slot:append>
                  <span>${{ Number(order.total).toFixed(2) }}</span>
                </template>
              </v-list-item>
            </v-list>
          </v-card>

          <v-card class="mb-4">
            <v-card-title>🛒 Tu Pedido</v-card-title>
            <v-divider></v-divider>
            <v-list density="compact">
              <v-list-item v-for="item in cartItems" :key="item.id">
                <v-list-item-title>
                  {{ item.name }} x{{ item.quantity }}
                </v-list-item-title>
                <template v-slot:append>
                  <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
                </template>
              </v-list-item>
            </v-list>
          </v-card>

          <v-card>
            <v-card-title>💰 Resumen</v-card-title>
            <v-divider></v-divider>
            <v-card-text>
              <div class="d-flex justify-space-between mb-2">
                <span>Subtotal:</span>
                <span>${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="d-flex justify-space-between mb-2">
                <span>Envío:</span>
                <span>${{ shippingCost.toFixed(2) }}</span>
              </div>
              <v-divider class="my-3"></v-divider>
              <div class="d-flex justify-space-between">
                <span class="font-weight-bold text-h6">Total:</span>
                <span class="font-weight-bold text-h5 text-success">
                  ${{ total.toFixed(2) }}
                </span>
              </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions class="flex-column pa-4">
              <v-btn
                color="primary"
                block
                size="large"
                type="submit"
                :loading="submitting"
                :prepend-icon="selectedPaymentMethod.icon"
                class="mb-2"
              >
                {{ submitButtonText }}
              </v-btn>
              <v-btn
                variant="outlined"
                block
                @click="$router.back()"
              >
                Volver
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-form>

    <!-- Modal de Éxito -->
    <v-dialog v-model="successDialog" max-width="600" persistent>
      <v-card>
        <v-card-title class="text-h5 text-center py-6 bg-success">
          <v-icon size="64" color="white" class="mb-2">mdi-check-circle</v-icon>
          <div class="text-white">¡Pedido registrado!</div>
        </v-card-title>
        <v-card-text class="pa-6">
          <div class="text-center mb-4">
            <p class="text-h6 mb-2">Orden #{{ orderNumber }}</p>
            <p class="text-h5 font-weight-bold text-success mb-2">${{ orderTotal }}</p>
          </div>
          
          <!-- Información de transferencia bancaria -->
          <div v-if="form.paymentMethod === 'transferencia' && bankInfo" class="mt-4">
            <v-divider class="mb-4"></v-divider>
            <h3 class="text-h6 mb-3">📱 Datos para transferencia:</h3>
            
            <v-list density="compact" class="bg-grey-lighten-4 rounded">
              <v-list-item v-if="bankInfo.banco">
                <template #prepend>
                  <v-icon icon="mdi-bank" size="small"></v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Banco</v-list-item-title>
                <v-list-item-subtitle>{{ bankInfo.banco }}</v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item v-if="bankInfo.beneficiario">
                <template #prepend>
                  <v-icon icon="mdi-account" size="small"></v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Beneficiario</v-list-item-title>
                <v-list-item-subtitle>{{ bankInfo.beneficiario }}</v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item v-if="bankInfo.cuenta">
                <template #prepend>
                  <v-icon icon="mdi-credit-card-outline" size="small"></v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">Cuenta</v-list-item-title>
                <v-list-item-subtitle>{{ bankInfo.cuenta }}</v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item v-if="bankInfo.clabe">
                <template #prepend>
                  <v-icon icon="mdi-numeric" size="small"></v-icon>
                </template>
                <v-list-item-title class="font-weight-bold">CLABE</v-list-item-title>
                <v-list-item-subtitle>{{ bankInfo.clabe }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>
            
            <v-alert 
              v-if="bankInfo.instrucciones" 
              type="info" 
              variant="tonal"
              class="mt-4"
            >
              {{ bankInfo.instrucciones }}
            </v-alert>
          </div>
          
          <p class="text-body-2 text-grey mt-4 text-center">
            {{ successMessage }}
          </p>
        </v-card-text>
        <v-card-actions class="justify-center pb-6 flex-column px-6">
          <v-btn
            v-if="form.paymentMethod === 'transferencia' && bankInfo?.whatsapp"
            color="success"
            prepend-icon="mdi-whatsapp"
            @click="openWhatsApp"
            block
            class="mb-2"
          >
            Enviar comprobante por WhatsApp
          </v-btn>
          <v-btn
            color="primary"
            variant="outlined"
            @click="closeSuccessDialog"
            block
          >
            Entendido
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Modal de Error -->
    <v-dialog v-model="errorDialog" max-width="500">
      <v-card>
        <v-card-title class="text-h5 text-center py-6 bg-error">
          <v-icon size="64" color="white" class="mb-2">mdi-alert-circle</v-icon>
          <div class="text-white">Error</div>
        </v-card-title>
        <v-card-text class="text-center pa-6">
          <p class="text-body-1">{{ errorMessage }}</p>
        </v-card-text>
        <v-card-actions class="justify-center pb-6">
          <v-btn color="error" @click="errorDialog = false">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Snackbar de validación -->
    <v-snackbar v-model="validationSnackbar" color="warning" :timeout="3000">
      Por favor completa todos los campos requeridos
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useCart } from '../composables/useCart';

const router = useRouter();
const { cartItems, subtotal, clearCart } = useCart();

const checkoutProfileStorageKey = 'ecommerce_checkout_profile';
const checkoutOrdersStorageKey = 'ecommerce_checkout_orders';

const createEmptyForm = () => ({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  zipCode: '',
  notes: '',
  paymentMethod: 'mercado_pago',
  saveCustomerProfile: false
});

const form = ref(createEmptyForm());

const shippingCost = computed(() => subtotal.value > 50 ? 0 : 10);
const total = computed(() => subtotal.value + shippingCost.value);

// Método de pago seleccionado con su configuración
const selectedPaymentMethod = computed(() => {
  return availablePaymentMethods.value.find(m => m.id === form.value.paymentMethod) || {
    id: 'mercado_pago',
    name: 'Mercado Pago',
    icon: 'mdi-credit-card-outline'
  };
});

const submitButtonText = computed(() => {
  if (form.value.paymentMethod === 'mercado_pago') {
    return 'Continuar a Mercado Pago';
  } else if (form.value.paymentMethod === 'paypal') {
    return 'Continuar a PayPal';
  } else if (form.value.paymentMethod === 'transferencia') {
    return 'Crear pedido por transferencia';
  }
  return 'Realizar pedido';
});

const submitting = ref(false);
const savedProfileExists = ref(false);
const recentOrders = ref([]);

// Métodos de pago disponibles
const availablePaymentMethods = ref([]);
const loadingPaymentMethods = ref(true);
const bankInfo = ref(null);

// Estados para modales
const successDialog = ref(false);
const errorDialog = ref(false);
const validationSnackbar = ref(false);
const errorMessage = ref('');
const orderNumber = ref('');
const orderTotal = ref(0);
const successMessage = ref('');
const currentOrder = ref(null); // Para guardar la orden actual

// Cargar métodos de pago desde la API
const loadPaymentMethods = async () => {
  try {
    loadingPaymentMethods.value = true;
    const response = await fetch('/api/payment-methods');
    const result = await response.json();
    
    if (result.success && result.payment_methods) {
      availablePaymentMethods.value = result.payment_methods;
      
      // Establecer el método por defecto
      if (result.default_method && !form.value.paymentMethod) {
        form.value.paymentMethod = result.default_method;
      }
      
      // Si el método actual no está disponible, seleccionar el primero
      const currentMethodAvailable = availablePaymentMethods.value.some(
        m => m.id === form.value.paymentMethod
      );
      if (!currentMethodAvailable && availablePaymentMethods.value.length > 0) {
        form.value.paymentMethod = availablePaymentMethods.value[0].id;
      }
      
      // Guardar info bancaria si existe
      const transferenciaMethod = availablePaymentMethods.value.find(m => m.id === 'transferencia');
      if (transferenciaMethod && transferenciaMethod.bank_info) {
        bankInfo.value = transferenciaMethod.bank_info;
      }
    }
  } catch (error) {
    console.error('Error al cargar métodos de pago:', error);
    // Fallback a MercadoPago si hay error
    availablePaymentMethods.value = [{
      id: 'mercado_pago',
      name: 'Mercado Pago',
      description: 'Paga con tarjeta, saldo o métodos disponibles en Mercado Pago.',
      icon: 'mdi-credit-card-outline',
      enabled: true,
    }];
    form.value.paymentMethod = 'mercado_pago';
  } finally {
    loadingPaymentMethods.value = false;
  }
};

const loadSavedProfile = () => {
  const rawProfile = window.localStorage.getItem(checkoutProfileStorageKey);

  if (!rawProfile) {
    savedProfileExists.value = false;
    return;
  }

  try {
    const profile = JSON.parse(rawProfile);
    form.value = {
      ...form.value,
      ...profile,
      notes: form.value.notes,
      paymentMethod: form.value.paymentMethod,
      saveCustomerProfile: true
    };
    savedProfileExists.value = true;
  } catch (error) {
    console.warn('No se pudieron cargar los datos guardados del checkout.', error);
    window.localStorage.removeItem(checkoutProfileStorageKey);
    savedProfileExists.value = false;
  }
};

const loadRecentOrders = async () => {
  // Intentar cargar desde el servidor si hay un email
  const email = form.value.email || window.localStorage.getItem('checkout_user_email');
  
  if (email) {
    try {
      const response = await fetch(`/api/ordenes-recientes?email=${encodeURIComponent(email)}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
        },
      });

      if (response.ok) {
        const result = await response.json();
        if (result.success && result.orders) {
          recentOrders.value = result.orders;
          // Actualizar localStorage con datos frescos del servidor
          window.localStorage.setItem(checkoutOrdersStorageKey, JSON.stringify(result.orders));
          return;
        }
      }
    } catch (error) {
      console.warn('No se pudieron cargar órdenes desde el servidor, usando localStorage', error);
    }
  }

  // Fallback: cargar desde localStorage
  const rawOrders = window.localStorage.getItem(checkoutOrdersStorageKey);

  if (!rawOrders) {
    recentOrders.value = [];
    return;
  }

  try {
    const orders = JSON.parse(rawOrders);
    
    // Mapear órdenes antiguas al nuevo formato
    recentOrders.value = orders.map(order => {
      // Si tiene payment_status, usar ese, si no, mapear desde status viejo
      const payment_status = order.payment_status || 
        (order.status === 'aprobado' ? 'pagado' : 
         order.status === 'rechazado' ? 'rechazado' : 'pendiente_pago');
      
      const order_status = order.order_status || 
        (order.status === 'aprobado' ? 'procesando' : 'pendiente');
      
      // Regenerar status_label correcto
      let status_label = '⏳ Pendiente de pago';
      if (payment_status === 'pagado') {
        status_label = '✅ Pagado';
      } else if (payment_status === 'rechazado') {
        status_label = '❌ Rechazado';
      } else if (payment_status === 'pendiente_pago') {
        if (order.metodo_pago === 'transferencia') {
          status_label = '🏦 Pendiente de transferencia';
        } else {
          status_label = '⏳ Pendiente de pago';
        }
      }
      
      return {
        ...order,
        payment_status,
        order_status,
        status_label
      };
    });
  } catch (error) {
    console.warn('No se pudieron cargar las compras recientes.', error);
    window.localStorage.removeItem(checkoutOrdersStorageKey);
    recentOrders.value = [];
  }
};

const persistSavedProfile = () => {
  const profile = {
    firstName: form.value.firstName,
    lastName: form.value.lastName,
    email: form.value.email,
    phone: form.value.phone,
    address: form.value.address,
    city: form.value.city,
    state: form.value.state,
    zipCode: form.value.zipCode
  };

  window.localStorage.setItem(checkoutProfileStorageKey, JSON.stringify(profile));
  window.localStorage.setItem('checkout_user_email', form.value.email);
  savedProfileExists.value = true;
};

const persistRecentOrder = (order) => {
  // Generar etiqueta de estado según payment_status
  let status_label = 'Pendiente de pago';
  
  if (order.payment_status === 'pagado') {
    status_label = '✅ Pagado';
  } else if (order.payment_status === 'rechazado') {
    status_label = '❌ Rechazado';
  } else if (order.payment_status === 'pendiente_pago') {
    if (order.metodo_pago === 'transferencia') {
      status_label = '🏦 Pendiente de transferencia';
    } else {
      status_label = '⏳ Pendiente de pago';
    }
  }

  const orderHistory = [
    {
      order_number: order.order_number,
      total: Number(order.total),
      payment_status: order.payment_status || order.status || 'pendiente_pago',
      order_status: order.order_status || 'pendiente',
      metodo_pago: order.metodo_pago || '',
      status_label: status_label,
      created_at: new Date().toLocaleDateString('es-MX'),
    },
    ...recentOrders.value.filter(item => item.order_number !== order.order_number)
  ].slice(0, 5);

  recentOrders.value = orderHistory;
  window.localStorage.setItem(checkoutOrdersStorageKey, JSON.stringify(orderHistory));
};

const clearSavedProfile = () => {
  window.localStorage.removeItem(checkoutProfileStorageKey);
  window.localStorage.removeItem(checkoutOrdersStorageKey);
  savedProfileExists.value = false;
  recentOrders.value = [];
  form.value.saveCustomerProfile = false;
};

onMounted(() => {
  loadPaymentMethods();
  loadSavedProfile();
  loadRecentOrders();
});

// Watch para recargar órdenes cuando el email cambie
let emailTimeout = null;
watch(() => form.value.email, (newEmail, oldEmail) => {
  if (newEmail && newEmail !== oldEmail && newEmail.includes('@')) {
    // Debounce manual: esperar 1 segundo después del último cambio
    if (emailTimeout) clearTimeout(emailTimeout);
    emailTimeout = setTimeout(() => {
      // Guardar email para uso futuro
      window.localStorage.setItem('checkout_user_email', newEmail);
      // Recargar órdenes desde servidor
      loadRecentOrders();
    }, 1000);
  }
});

const extractApiErrorMessage = async (response, result) => {
  if (result?.errors) {
    const firstErrorGroup = Object.values(result.errors)[0];
    if (Array.isArray(firstErrorGroup) && firstErrorGroup[0]) {
      return firstErrorGroup[0];
    }
  }

  if (result?.message) {
    return result.message;
  }

  const fallbackText = await response.text();
  return fallbackText || 'No se pudo crear la orden';
};

const closeSuccessDialog = () => {
  successDialog.value = false;
  clearCart();
  router.push({ name: 'Home' });
};

const openWhatsApp = () => {
  if (currentOrder.value) {
    const whatsappUrl = buildTransferWhatsAppUrl(currentOrder.value);
    window.open(whatsappUrl, '_blank');
  }
};

const buildTransferWhatsAppUrl = (order) => {
  const whatsappNumber = bankInfo.value?.whatsapp || '5580091558';
  
  const itemsSummary = cartItems.value
    .map(item => `- ${item.name} x${item.quantity}`)
    .join('\n');

  const message = [
    '¡Hola! Acabo de generar un pedido por transferencia.',
    '',
    `Orden: #${order.order_number}`,
    `Nombre: ${form.value.firstName} ${form.value.lastName}`,
    `Teléfono: ${form.value.phone}`,
    `Email: ${form.value.email}`,
    `Dirección: ${form.value.address}, ${form.value.city}, ${form.value.state}, CP ${form.value.zipCode}`,
    '',
    'Productos:',
    itemsSummary,
    '',
    `Total: $${Number(order.total).toFixed(2)} MXN`,
    '',
    'Compartiré mi comprobante de pago para confirmar la orden.'
  ].join('\n');

  return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
};

const buildOrderPayload = () => ({
  customer_first_name: form.value.firstName,
  customer_last_name: form.value.lastName,
  customer_email: form.value.email,
  customer_phone: form.value.phone,
  shipping_address: form.value.address,
  shipping_city: form.value.city,
  shipping_state: form.value.state,
  shipping_zip_code: form.value.zipCode,
  subtotal: subtotal.value,
  shipping_cost: shippingCost.value,
  total: total.value,
  notes: form.value.notes,
  save_customer_profile: form.value.saveCustomerProfile,
  items: cartItems.value.map(item => ({
    product_id: item.id,
    product_name: item.name,
    quantity: item.quantity,
    unit_price: item.price
  }))
});

const submitOrder = async () => {
  // Validar formulario
  if (!form.value.firstName || !form.value.lastName || !form.value.email || !form.value.phone || !form.value.address || !form.value.city || !form.value.state || !form.value.zipCode) {
    validationSnackbar.value = true;
    return;
  }

  const transferWindow = form.value.paymentMethod === 'transferencia'
    ? window.open('', '_blank')
    : null;

  try {
    submitting.value = true;
    
    // Determinar el endpoint según el método de pago
    let endpoint = '/api/crear-pedido';
    if (form.value.paymentMethod === 'transferencia') {
      endpoint = '/api/crear-pedido/transferencia';
    } else if (form.value.paymentMethod === 'paypal') {
      endpoint = '/api/crear-pedido/paypal';
    }

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(buildOrderPayload())
    });

    const result = await response.json();

    if (!response.ok || result.success === false) {
      throw new Error(await extractApiErrorMessage(response, result));
    }

    if (form.value.saveCustomerProfile) {
      persistSavedProfile();
      persistRecentOrder(result.order);
    }

    // Manejar Mercado Pago
    if (form.value.paymentMethod === 'mercado_pago') {
      sessionStorage.setItem('last_mercado_pago_order_id', String(result.order.id));

      if (!result.checkout_url) {
        throw new Error('La orden se creó, pero Mercado Pago no devolvió una URL de pago.');
      }

      window.location.href = result.checkout_url;
      return;
    }

    // Manejar PayPal
    if (form.value.paymentMethod === 'paypal') {
      sessionStorage.setItem('last_paypal_order_id', String(result.order.id));
      sessionStorage.setItem('paypal_order_id', String(result.paypal_order_id || ''));

      if (!result.checkout_url) {
        throw new Error('La orden se creó, pero PayPal no devolvió una URL de pago.');
      }

      window.location.href = result.checkout_url;
      return;
    }

    // Manejar Transferencia
    const transferWhatsAppUrl = buildTransferWhatsAppUrl(result.order);

    currentOrder.value = result.order; // Guardar orden actual
    orderNumber.value = result.order.order_number;
    orderTotal.value = Number(result.order.total).toFixed(2);
    successMessage.value = 'Tu pedido quedó registrado con pago por transferencia. Usa los datos bancarios mostrados para realizar tu transferencia.';
    
    if (transferWindow) {
      transferWindow.location.replace(transferWhatsAppUrl);
    } else {
      window.open(transferWhatsAppUrl, '_blank');
    }

    successDialog.value = true;
    return;

  } catch (error) {
    if (transferWindow && !transferWindow.closed) {
      transferWindow.close();
    }

    console.error('Error al procesar la orden:', error);
    errorMessage.value = 'Error al procesar tu pedido: ' + error.message;
    errorDialog.value = true;
  } finally {
    submitting.value = false;
  }
};
</script>
<style>
    header{
    font-family: var(--font-brand), serif;
    margin: 0;
    padding: 0;
    font-size: 16px;
    scroll-behavior: smooth;
}
* {
      font-family: var(--font-brand), serif;
    margin: 0;
    padding: 0;
    font-size: 16px;
    scroll-behavior: smooth;
  }
.checked-page{
margin-top: 150px !important;
}

.payment-option {
  padding: 1rem 1rem 0.85rem;
  border: 1px solid rgba(184, 151, 120, 0.16);
  border-radius: 18px;
  background: rgba(249, 245, 241, 0.86);
  margin-bottom: 0.85rem;
}

.payment-option--active {
  border-color: rgba(140, 116, 95, 0.34);
  box-shadow: 0 14px 28px rgba(107, 91, 71, 0.08);
}

.payment-option__description {
  color: #7f6d5a;
  line-height: 1.65;
  padding-left: 2.2rem;
  padding-top: 0.2rem;
  font-size: 0.95rem;
}


</style>
