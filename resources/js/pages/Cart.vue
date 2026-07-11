<template>
  <v-container class="my-8 cart-page">
    <h1 class="text-h3 mb-8">Shopping Cart</h1>

    <!-- DTF Transfers Section -->
    <div v-if="dtfCartItems.length > 0" class="mb-8">
      <h2 class="text-h5 mb-4">DTF Transfers</h2>
      <v-card class="mb-4">
        <v-table>
          <thead>
            <tr>
              <th class="text-left">Image</th>
              <th class="text-left">Size</th>
              <th class="text-center">Quantity</th>
              <th class="text-right">Price</th>
              <th class="text-right">Total</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in dtfCartItems" :key="item.id">
              <td>
                <div class="cart-image-container">
                  <v-img
                    v-if="item.imagePreview"
                    :src="item.imagePreview"
                    :alt="item.product?.name || item.dtf_size?.name"
                    max-width="80"
                    max-height="80"
                    class="rounded"
                  />
                  <div v-else class="cart-image-placeholder">
                    <span v-if="item.type === 'gang_sheet'" class="placeholder-icon">📋</span>
                    <span v-else class="placeholder-icon">📦</span>
                  </div>
                </div>
              </td>
              <td>
                <div>
                  <p class="font-weight-bold">{{ item.product?.name || item.dtf_size?.name }}</p>
                  <p class="text-caption text-grey">
                    {{ item.product?.width || item.dtf_size?.width }}" x {{ item.product?.height || item.dtf_size?.height }}"
                  </p>
                </div>
              </td>
              <td class="text-center">
                <div class="d-flex align-center justify-center gap-2">
                  <v-btn
                    icon="mdi-minus"
                    size="x-small"
                    variant="outlined"
                    @click="updateDtfQuantity(item.id, item.quantity - 1)"
                  ></v-btn>
                  <span>{{ item.quantity }}</span>
                  <v-btn
                    icon="mdi-plus"
                    size="x-small"
                    variant="outlined"
                    @click="updateDtfQuantity(item.id, item.quantity + 1)"
                  ></v-btn>
                </div>
              </td>
              <td class="text-right">${{ Number(item.unitPrice).toFixed(2) }}</td>
              <td class="text-right font-weight-bold">${{ Number(item.totalPrice).toFixed(2) }}</td>
              <td class="text-center">
                <v-btn
                  icon="mdi-delete"
                  size="x-small"
                  color="error"
                  variant="text"
                  @click="removeDtfItem(item.id)"
                ></v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card>
    </div>

    <v-row>
      <v-col cols="12" md="8">
        <v-card v-if="cartItems.length === 0 && dtfCartItems.length === 0" class="pa-12 text-center">
          <v-icon size="64" color="grey">mdi-cart-outline</v-icon>
          <p class="text-h6 mt-4">Your cart is empty</p>
          <v-btn color="primary" class="mt-4" :to="{ name: 'DtfTransfersSize' }">
            Continue Shopping
          </v-btn>
        </v-card>

        <v-card v-else-if="cartItems.length > 0">
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

      <v-col cols="12" md="4" v-if="cartItems.length > 0 || dtfCartItems.length > 0">
        <v-card>
          <v-card-title>Order Summary</v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <div class="d-flex justify-space-between mb-2">
              <span>Subtotal:</span>
              <span>${{ Number(totalAmount).toFixed(2) }}</span>
            </div>
            <div class="d-flex justify-space-between mb-2">
              <span>Shipping:</span>
              <span>{{ Number(totalAmount) > 50 ? 'Free' : '$10.00' }}</span>
            </div>
            <v-divider class="my-3"></v-divider>
            <div class="d-flex justify-space-between">
              <span class="font-weight-bold">Total:</span>
              <span class="font-weight-bold text-h6">
                ${{ (Number(totalAmount) + (Number(totalAmount) > 50 ? 0 : 10)).toFixed(2) }}
              </span>
            </div>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn
              color="success"
              block
              size="large"
              @click="openCheckoutDialog"
            >
              Proceed to Checkout
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- CHECKOUT DIALOG -->
    <v-dialog v-model="checkoutDialog" max-width="800" persistent scrollable>
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center">
          <span>Checkout</span>
          <v-btn icon="mdi-close" variant="text" @click="closeCheckoutDialog"></v-btn>
        </v-card-title>
        <v-divider></v-divider>

        <v-card-text>
          <!-- User Info Section -->
          <div class="mb-6">
            <h3 class="text-h6 mb-4">📋 Shipping Information</h3>
            
            <v-alert v-if="userIsRegistered" type="success" variant="tonal" class="mb-4">
              ✅ You are registered. Using your saved information.
              <v-btn variant="text" size="small" @click="toggleEditProfile" class="ml-2">
                {{ editingProfile ? 'Cancel' : 'Edit' }}
              </v-btn>
            </v-alert>

            <v-row v-if="!userIsRegistered || editingProfile">
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="checkoutForm.firstName"
                  label="First Name *"
                  variant="outlined"
                  density="compact"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="checkoutForm.lastName"
                  label="Last Name *"
                  variant="outlined"
                  density="compact"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="checkoutForm.email"
                  label="Email *"
                  type="email"
                  variant="outlined"
                  density="compact"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="checkoutForm.phone"
                  label="Phone *"
                  variant="outlined"
                  density="compact"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="checkoutForm.address"
                  label="Address *"
                  variant="outlined"
                  density="compact"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="4">
                <v-text-field
                  v-model="checkoutForm.city"
                  label="City *"
                  variant="outlined"
                  density="compact"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="4">
                <v-text-field
                  v-model="checkoutForm.state"
                  label="State *"
                  variant="outlined"
                  density="compact"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="4">
                <v-text-field
                  v-model="checkoutForm.zipCode"
                  label="Zip Code *"
                  variant="outlined"
                  density="compact"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="checkoutForm.notes"
                  label="Additional notes (optional)"
                  variant="outlined"
                  density="compact"
                  rows="2"
                ></v-textarea>
              </v-col>
              <v-col cols="12" v-if="!userIsRegistered">
                <v-checkbox
                  v-model="checkoutForm.saveProfile"
                  label="Save my information for next time"
                ></v-checkbox>
              </v-col>
            </v-row>

            <div v-else class="bg-grey-lighten-4 pa-4 rounded">
              <p><strong>{{ checkoutForm.firstName }} {{ checkoutForm.lastName }}</strong></p>
              <p>{{ checkoutForm.email }}</p>
              <p>{{ checkoutForm.phone }}</p>
              <p>{{ checkoutForm.address }}, {{ checkoutForm.city }}, {{ checkoutForm.state }} {{ checkoutForm.zipCode }}</p>
            </div>
          </div>

          <!-- Payment Method Section -->
          <div class="mb-6">
            <h3 class="text-h6 mb-4">💳 Payment Method</h3>
            
            <v-progress-circular 
              v-if="loadingPaymentMethods" 
              indeterminate 
              color="primary"
              class="mx-auto d-block"
            ></v-progress-circular>

            <v-radio-group 
              v-else
              v-model="checkoutForm.paymentMethod"
              class="payment-methods-group"
            >
              <div 
                v-for="method in availablePaymentMethods" 
                :key="method.id"
                class="payment-option mb-3"
                :class="{ 'payment-option--active': checkoutForm.paymentMethod === method.id }"
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
                <p class="text-caption text-grey pl-8">{{ method.description }}</p>
              </div>
            </v-radio-group>
          </div>

          <!-- Discount Code Section -->
          <div class="mb-6">
            <h3 class="text-h6 mb-4">🎟️ Discount Code</h3>
            
            <div v-if="!discountApplied" class="d-flex gap-2">
              <v-text-field
                v-model="discountCode"
                label="Discount Code"
                placeholder="e.g. SUMMER2024"
                variant="outlined"
                density="compact"
                :disabled="discountLoading"
                @keyup.enter="validateDiscountCode"
              ></v-text-field>
              <v-btn
                color="primary"
                :loading="discountLoading"
                @click="validateDiscountCode"
                class="align-self-end"
              >
                Apply
              </v-btn>
            </div>

            <!-- Error message -->
            <v-alert
              v-if="discountError"
              type="error"
              variant="tonal"
              closable
              class="mt-3"
              @close="discountError = ''"
            >
              {{ discountError }}
            </v-alert>

            <!-- Discount applied -->
            <v-alert
              v-if="discountApplied && appliedDiscount"
              type="success"
              variant="tonal"
              class="mt-3"
            >
              <div class="d-flex justify-space-between align-center">
                <div>
                  <strong>{{ appliedDiscount.code }}</strong> applied!
                  <br>
                  <small>Discount: ${{ appliedDiscount.discount_amount.toFixed(2) }}</small>
                </div>
                <v-btn
                  icon="mdi-close"
                  variant="text"
                  size="small"
                  @click="removeDiscountCode"
                ></v-btn>
              </div>
            </v-alert>
          </div>

          <!-- Order Summary -->
          <div class="bg-primary-lighten-5 pa-4 rounded mb-4">
            <h3 class="text-h6 mb-3">📦 Order Summary</h3>
            <div class="d-flex justify-space-between mb-2">
              <span>Subtotal:</span>
              <span>${{ Number(totalAmount).toFixed(2) }}</span>
            </div>
            <div v-if="appliedDiscount" class="d-flex justify-space-between mb-2 text-success">
              <span>Discount ({{ appliedDiscount.code }}):</span>
              <span>-${{ appliedDiscount.discount_amount.toFixed(2) }}</span>
            </div>
            <div class="d-flex justify-space-between mb-2">
              <span>Shipping:</span>
              <span>${{ (Number(totalWithDiscount) > 50 ? 0 : 10).toFixed(2) }}</span>
            </div>
            <v-divider class="my-2"></v-divider>
            <div class="d-flex justify-space-between font-weight-bold text-h6">
              <span>Total:</span>
              <span class="text-success">
                ${{ (parseFloat(totalWithDiscount) + (Number(totalWithDiscount) > 50 ? 0 : 10)).toFixed(2) }}
              </span>
            </div>
          </div>
        </v-card-text>

        <v-divider></v-divider>
        <v-card-actions>
          <v-btn variant="outlined" @click="closeCheckoutDialog">Cancel</v-btn>
          <v-spacer></v-spacer>
          <v-btn 
            color="success" 
            size="large"
            :loading="submitting"
            @click="submitOrder"
          >
            Complete Purchase
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Success Dialog -->
    <v-dialog v-model="successDialog" max-width="600" persistent>
      <v-card>
        <v-card-title class="text-h5 text-center py-6 bg-success">
          <v-icon size="64" color="white" class="mb-2">mdi-check-circle</v-icon>
          <div class="text-white">Order Confirmed!</div>
        </v-card-title>
        <v-card-text class="pa-6 text-center">
          <p class="text-h6 mb-2">Order #{{ orderNumber }}</p>
          <p class="text-h5 font-weight-bold text-success mb-4">${{ orderTotal }}</p>
          <p class="text-body-2 text-grey" v-if="successMessage">{{ successMessage }}</p>
        </v-card-text>
        <v-card-actions class="justify-center pb-6">
          <v-btn color="success" @click="closeSuccessDialog">Continue Shopping</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Error Dialog -->
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
          <v-btn color="error" @click="errorDialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCart } from '../composables/useCart';
import { getImageFromIndexedDB, deleteImageFromIndexedDB } from '../services/cartStorageService';
import axios from 'axios';

const router = useRouter();
const { cartItems, removeFromCart, updateQuantity, subtotal, clearCart } = useCart();
const dtfCartItems = ref([]);

// Checkout dialog state
const checkoutDialog = ref(false);
const successDialog = ref(false);
const errorDialog = ref(false);
const submitting = ref(false);
const editingProfile = ref(false);

// User registration state
const userIsRegistered = ref(false);
const storageKey = 'cart_user_profile';

// Payment methods
const availablePaymentMethods = ref([]);
const loadingPaymentMethods = ref(false);

// Form data
const checkoutForm = ref({
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
  saveProfile: false
});

// Messages
const orderNumber = ref('');
const orderTotal = ref(0);
const successMessage = ref('');
const errorMessage = ref('');

// Discount code state
const discountCode = ref('');
const appliedDiscount = ref(null); // { code_id, code, discount_amount, final_price }
const discountError = ref('');
const discountLoading = ref(false);
const discountApplied = ref(false);

// Load DTF cart items
onMounted(() => {
  const stored = localStorage.getItem('dtf_cart_items');
  
  if (stored) {
    try {
      let items = JSON.parse(stored);
      
      // Migrate old structure to new structure if needed
      items = items.map(item => {
        // If item has old structure (dtf_size), convert it
        if (item.dtf_size && !item.product) {
          return {
            id: item.id,
            type: item.type || 'size',
            product: {
              id: item.dtf_size.id,
              type: 'size',
              name: item.dtf_size.name,
              description: item.dtf_size.description || '',
              sku: item.dtf_size.sku || '',
              category: 'dtf_size',
              width: item.dtf_size.width,
              height: item.dtf_size.height,
              unit: item.dtf_size.unit,
              price: item.dtf_size.price,
            },
            quantity: item.quantity,
            imagePreview: item.imagePreview,
            unitPrice: Number(item.unitPrice) || 0,
            totalPrice: Number(item.totalPrice) || 0
          };
        }
        // Otherwise, return as-is (already new structure)
        return {
          ...item,
          unitPrice: Number(item.unitPrice) || 0,
          totalPrice: Number(item.totalPrice) || 0
        };
      });
      
      dtfCartItems.value = items;
      
      // Cargar imágenes desde IndexedDB
      items.forEach(async (item) => {
        const image = await getImageFromIndexedDB(item.id.toString());
        if (image) {
          const idx = dtfCartItems.value.findIndex(i => i.id === item.id);
          if (idx !== -1) {
            dtfCartItems.value[idx].imagePreview = image;
          }
        }
      });
      
    } catch (error) {
      console.error('❌ Error loading DTF cart items:', error);
    }
  }
  
  // Load user profile if exists
  loadUserProfile();
  loadPaymentMethods();
});

// Calculate totals
const totalAmount = computed(() => {
  const regularTotal = Number(subtotal.value) || 0;
  const dtfTotal = dtfCartItems.value.reduce((sum, item) => {
    return sum + (Number(item.totalPrice) || 0);
  }, 0);
  return regularTotal + dtfTotal;
});

const shippingCost = computed(() => Number(totalAmount.value) > 50 ? 0 : 10);

const totalWithDiscount = computed(() => {
  const base = parseFloat(totalAmount.value) || 0;
  if (appliedDiscount.value) {
    return (base - appliedDiscount.value.discount_amount).toFixed(2);
  }
  return base.toFixed(2);
});

// Load user profile from authenticated customer
const loadUserProfile = async () => {
  try {
    // Obtener el customer logeado desde la API
    // Sanctum usa cookies por defecto, pero también intentamos con token si existe
    const token = localStorage.getItem('auth_token') || 
                  localStorage.getItem('token') ||
                  localStorage.getItem('sanctum_token') || '';
    
    const headers = {
      'Accept': 'application/json'
    };
    
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch('/api/customer', { headers });

    if (response.ok) {
      const customer = await response.json();
      
      if (customer && customer.email) {
        // Customer está logeado - mapear datos del modelo Customer
        checkoutForm.value = {
          ...checkoutForm.value,
          firstName: customer.first_name || '',
          lastName: customer.last_name || '',
          email: customer.email || '',
          phone: customer.phone || '',
          address: customer.address || '',
          city: customer.city || '',
          state: customer.state || '',
          zipCode: customer.zip_code || ''
        };
        userIsRegistered.value = true;
        editingProfile.value = false;
          // console.log('✅ Customer logeado cargado:', {
          //   name: `${customer.first_name} ${customer.last_name}`,
          //   email: customer.email
          // });
        return;
      }
    }
  } catch (error) {
    console.warn('No hay customer logeado:', error);
  }

  // Fallback: intentar cargar desde localStorage si la API no devuelve un customer
  const saved = localStorage.getItem(storageKey);
  if (saved) {
    try {
      const profile = JSON.parse(saved);
      checkoutForm.value = { ...checkoutForm.value, ...profile };
      userIsRegistered.value = true;
      editingProfile.value = false;
      // console.log('✅ Información cargada desde localStorage');
    } catch (error) {
      console.error('Error cargando perfil desde localStorage:', error);
    }
  } else {
    // console.log('⚠️ Sin información de cliente - mostrando formulario');
    userIsRegistered.value = false;
  }
};

// Load payment methods
const loadPaymentMethods = async () => {
  try {
    loadingPaymentMethods.value = true;
    const response = await fetch('/api/payment-methods');
    const result = await response.json();
    
    if (result.success && result.payment_methods) {
      availablePaymentMethods.value = result.payment_methods;
      if (result.default_method) {
        checkoutForm.value.paymentMethod = result.default_method;
      }
    }
  } catch (error) {
    console.error('Error loading payment methods:', error);
    // Fallback
    availablePaymentMethods.value = [{
      id: 'mercado_pago',
      name: 'Mercado Pago',
      description: 'Pay with card, balance or available methods',
      icon: 'mdi-credit-card-outline',
      enabled: true
    }];
  } finally {
    loadingPaymentMethods.value = false;
  }
};

// Discount Code Methods
const validateDiscountCode = async () => {
  if (!discountCode.value.trim()) {
    discountError.value = 'Ingresa un código de descuento';
    return;
  }

  discountLoading.value = true;
  discountError.value = '';

  try {
    const response = await axios.post('/api/discount-codes/validate', {
      code: discountCode.value.trim().toUpperCase(),
      subtotal: totalAmount.value, // Total sin descuento
    });

    appliedDiscount.value = {
      code_id: response.data.code_id,
      code: response.data.code,
      discount_type: response.data.discount_type,
      discount_value: response.data.discount_value,
      discount_amount: response.data.discount_amount,
      final_price: response.data.final_price,
    };

    discountApplied.value = true;
    discountError.value = '';
  } catch (error) {
    appliedDiscount.value = null;
    discountApplied.value = false;

    if (error.response?.status === 404) {
      discountError.value = 'Código de descuento no encontrado.';
    } else if (error.response?.status === 422) {
      discountError.value = error.response.data.errors?.[0] || 'Código inválido.';
    } else {
      discountError.value = 'Error al validar el código.';
    }
  } finally {
    discountLoading.value = false;
  }
};

const removeDiscountCode = () => {
  discountCode.value = '';
  appliedDiscount.value = null;
  discountApplied.value = false;
  discountError.value = '';
};

// Dialog controls
const openCheckoutDialog = () => {
  if (cartItems.value.length === 0 && dtfCartItems.value.length === 0) {
    alert('Your cart is empty');
    return;
  }
  checkoutDialog.value = true;
};

const closeCheckoutDialog = () => {
  checkoutDialog.value = false;
  editingProfile.value = false;
};

const toggleEditProfile = () => {
  editingProfile.value = !editingProfile.value;
};

// Cart management
const updateDtfQuantity = (id, newQuantity) => {
  const item = dtfCartItems.value.find(item => item.id === id);
  if (item && newQuantity > 0) {
    item.quantity = parseInt(newQuantity);
    item.totalPrice = Number(item.unitPrice) * parseInt(newQuantity);
    
    // Save to localStorage with proper serialization (SIN imagePreview)
    const serializableCart = dtfCartItems.value.map(item => ({
      id: item.id,
      type: item.type,
      product: item.product,
      quantity: item.quantity,
      unitPrice: item.unitPrice,
      totalPrice: item.totalPrice
      // ✅ imagePreview NO se guarda
    }));
    localStorage.setItem('dtf_cart_items', JSON.stringify(serializableCart));
  }
};

const removeDtfItem = async (id) => {
  // Si es un gang sheet, eliminar primero de la BD
  const item = dtfCartItems.value.find(item => item.id === id);
  
  if (item && item.type === 'gang_sheet' && item.gangSheetid) {
    try {
      const token = localStorage.getItem('auth_token') || 
                    localStorage.getItem('token') ||
                    localStorage.getItem('sanctum_token') || '';

      const headers = {
        'Accept': 'application/json'
      };

      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      const response = await fetch(`/api/gang-sheets/${item.gangSheetid}`, {
        method: 'DELETE',
        headers
      });

      if (!response.ok) {
        console.warn('⚠️ Failed to delete gang sheet from DB:', response.statusText);
      } else {
        const result = await response.json();
        // console.log('✅ Gang sheet deleted from DB:', result.message);
      }
    } catch (error) {
      console.error('❌ Error deleting gang sheet:', error);
    }
  }

  // Eliminar imagen de IndexedDB
  try {
    await deleteImageFromIndexedDB(id.toString());
    // console.log('✅ Image deleted from IndexedDB');
  } catch (error) {
    console.error('⚠️ Error deleting image from IndexedDB:', error);
  }

  // Eliminar del carrito local
  dtfCartItems.value = dtfCartItems.value.filter(item => item.id !== id);

  // Save to localStorage with proper serialization (SIN imagePreview)
  const serializableCart = dtfCartItems.value.map(item => ({
    id: item.id,
    type: item.type,
    product: item.product,
    quantity: item.quantity,
    unitPrice: item.unitPrice,
    totalPrice: item.totalPrice
    // ✅ imagePreview NO se guarda
  }));
  localStorage.setItem('dtf_cart_items', JSON.stringify(serializableCart));
};

// Build order payload
const buildOrderPayload = () => {
  const items = [];

  // console.log('🔍 Building order payload...');
  // console.log('Regular cart items count:', cartItems.value.length);
  // console.log('DTF cart items count:', dtfCartItems.value.length);

  cartItems.value.forEach((item, index) => {
    const productItem = {
      product_id: item.id,
      product_name: item.name,
      quantity: item.quantity,
      unit_price: Number(item.price),
      total: Number(item.price) * item.quantity
    };
    // console.log(`📦 Regular Product ${index}:`, productItem);
    items.push(productItem);
  });

  dtfCartItems.value.forEach((item, index) => {
    // console.log(`🎨 DTF Item ${index} (raw):`, item);
    // console.log(`  - item.type: ${item.type}`);
    // console.log(`  - item.product: ${item.product ? 'EXISTS' : 'MISSING'}`);
    // console.log(`  - item.product?.id: ${item.product?.id}`);
    // console.log(`  - item.dtf_size_id: ${item.dtf_size_id}`);
    // console.log(`  - unitPrice: ${item.unitPrice}`);
    // console.log(`  - totalPrice: ${item.totalPrice}`);
    // console.log(`  - image: ${item.imagePreview ? 'Present' : 'Missing'}`);
    
    // Determinar product_id basado en el tipo de item
    let productId = null;
    const itemType = item.type || 'size';
    
    // console.log(`  🔍 Processing item with type: "${itemType}"`);
    
    if (itemType === 'size') {
      productId = item.dtf_size_id || item.product?.id;
      // console.log(`  💾 SIZE: dtf_size_id=${item.dtf_size_id}, product.id=${item.product?.id}, final productId=${productId}`);
    } else if (itemType === 'gang') {
      productId = item.product?.id;
      // console.log(`  💾 GANG: product.id=${item.product?.id}, final productId=${productId}`);
      if (!productId) {
        console.error(`  🔴 CRITICAL ERROR: Gang sheet without product.id! Item:`, item);
        console.error(`      Full item.product object:`, item.product);
      }
    } else if (itemType === 'gang_sheet') {
      // Custom Gang Sheet Designs from GangSheetEditorInches
      productId = item.product_id || item.product?.id;
      // console.log(`  💾 GANG_SHEET: product_id=${item.product_id}, product.id=${item.product?.id}, final productId=${productId}`);
      if (!productId) {
        console.error(`  🔴 CRITICAL ERROR: Gang sheet design without product_id! Item:`, item);
        console.error(`      Full item object:`, JSON.stringify(item, null, 2));
      }
    } else {
      productId = item.product?.id || item.dtf_size_id;
      // console.log(`  💾 OTHER (${itemType}): product.id=${item.product?.id}, dtf_size_id=${item.dtf_size_id}, final productId=${productId}`);
    }
    
    const dtfItem = {
      type: itemType,
      product_id: productId,  // Will be null if not found, which is correct for backend to handle
      product_name: item.product?.name || item.product_name || item.dtf_size?.name || 'DTF Transfer',
      quantity: item.quantity,
      unit_price: Number(item.unitPrice),
      total: Number(item.totalPrice),
      image: item.imagePreview || null,
      gangSheetid: item.gangSheetid || null
    };
    // console.log(`✅ DTF Item ${index} (formatted):`, dtfItem);
    items.push(dtfItem);
  });

  const payload = {
    customer_first_name: checkoutForm.value.firstName,
    customer_last_name: checkoutForm.value.lastName,
    customer_email: checkoutForm.value.email,
    customer_phone: checkoutForm.value.phone,
    shipping_address: checkoutForm.value.address,
    shipping_city: checkoutForm.value.city,
    shipping_state: checkoutForm.value.state,
    shipping_zip_code: checkoutForm.value.zipCode,
    subtotal: totalAmount.value,
    discount_code_id: appliedDiscount.value?.code_id || null,
    discount_amount: appliedDiscount.value?.discount_amount || 0,
    shipping_cost: shippingCost.value,
    total: parseFloat(totalWithDiscount.value) + shippingCost.value,
    notes: checkoutForm.value.notes,
    save_customer_profile: checkoutForm.value.saveProfile,
    payment_method: checkoutForm.value.paymentMethod,
    items: items,
    discount_code: appliedDiscount.value?.code || null
  };
  
  // console.log('📤 FINAL PAYLOAD TO SEND:', JSON.stringify(payload, null, 2));
  return payload;
};

// Submit order
const submitOrder = async () => {
  // Validate
  if (!checkoutForm.value.firstName || !checkoutForm.value.lastName || !checkoutForm.value.email || 
      !checkoutForm.value.phone || !checkoutForm.value.address || !checkoutForm.value.city || 
      !checkoutForm.value.state || !checkoutForm.value.zipCode) {
    alert('Please fill all required fields');
    return;
  }

  try {
    submitting.value = true;

    let endpoint = '/api/crear-pedido';
    if (checkoutForm.value.paymentMethod === 'transferencia') {
      endpoint = '/api/crear-pedido/transferencia';
    } else if (checkoutForm.value.paymentMethod === 'paypal') {
      endpoint = '/api/crear-pedido/paypal';
    }

    // Obtener token de autenticación
    const token = localStorage.getItem('auth_token') || 
                  localStorage.getItem('token') ||
                  localStorage.getItem('sanctum_token') || '';

    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    };

    // Agregar token si existe
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
      // console.log('✅ Token de autenticación incluido en request');
    } else {
      console.warn('⚠️ No hay token de autenticación');
    }

    const payload = buildOrderPayload();
    // console.log('📨 Sending order to backend...');
    // console.log('📨 Endpoint:', endpoint);
    // console.log('📨 Payload items:', payload);
    // console.log('💰 PAYMENT DEBUG:');
    // console.log('  Subtotal:', payload.subtotal);
    // console.log('  Discount Amount:', payload.discount_amount);
    // console.log('  Discount Code:', payload.discount_code);
    // console.log('  Shipping Cost:', payload.shipping_cost);
    // console.log('  TOTAL ENVIADO A PAYPAL:', payload.total);

    const response = await fetch(endpoint, {
      method: 'POST',
      headers,
      body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (!response.ok || result.success === false) {
      throw new Error(result.message || 'Order creation failed');
    }

    // Save profile if requested
    if (checkoutForm.value.saveProfile) {
      const profile = {
        firstName: checkoutForm.value.firstName,
        lastName: checkoutForm.value.lastName,
        email: checkoutForm.value.email,
        phone: checkoutForm.value.phone,
        address: checkoutForm.value.address,
        city: checkoutForm.value.city,
        state: checkoutForm.value.state,
        zipCode: checkoutForm.value.zipCode
      };
      localStorage.setItem(storageKey, JSON.stringify(profile));
      userIsRegistered.value = true;
    }

    orderNumber.value = result.order.order_number;
    orderTotal.value = Number(result.order.total).toFixed(2);

    // Register discount code usage for authenticated customers
    if (appliedDiscount.value && token) {
      try {
        await axios.post(
          `/api/discount-codes/${appliedDiscount.value.code_id}/use`,
          {},
          { headers: { 'Authorization': `Bearer ${token}` } }
        );
        // console.log('✅ Discount code usage registered');
      } catch (error) {
        console.warn('⚠️ Could not register discount usage:', error.response?.data?.message);
        // Continue anyway - order was already created
      }
    }

    // Handle Mercado Pago
    if (checkoutForm.value.paymentMethod === 'mercado_pago') {
      if (result.checkout_url) {
        window.location.href = result.checkout_url;
        return;
      }
    }

    // Handle PayPal
    if (checkoutForm.value.paymentMethod === 'paypal') {
      if (result.checkout_url) {
        window.location.href = result.checkout_url;
        return;
      }
    }

    // Handle Transfer
    successMessage.value = 'Your order has been confirmed. Bank transfer information will be sent to your email.';
    successDialog.value = true;
    checkoutDialog.value = false;

  } catch (error) {
    console.error('Order error:', error);
    errorMessage.value = error.message;
    errorDialog.value = true;
  } finally {
    submitting.value = false;
  }
};

const closeSuccessDialog = () => {
  successDialog.value = false;
  clearCart();
  dtfCartItems.value = [];
  localStorage.removeItem('dtf_cart_items');
  // Reset discount
  removeDiscountCode();
  router.push({ name: 'Home' });
};
</script>

<style scoped>
.product-thumb {
  width: 80px;
  height: 80px;
  border-radius: 8px;
}

.cart-page {
  margin-top: 30px !important;
}

@media (max-width: 600px) {
  .cart-page {
    margin-top: 120px !important;
  }
}

.gap-2 {
  gap: 0.5rem;
}

.cart-image-container {
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  overflow: hidden;
  background: #f5f5f5;
}

.cart-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%);
  border: 1px dashed #bdbdbd;
}

.placeholder-icon {
  font-size: 2rem;
  opacity: 0.7;
}

.payment-option {
  padding: 1rem;
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 8px;
  background: rgba(0, 0, 0, 0.02);
  transition: all 0.2s ease;
}

.payment-option--active {
  border-color: #1976d2;
  background: rgba(25, 118, 210, 0.08);
}

.payment-option__description {
  color: #666;
  font-size: 0.85rem;
}
</style>
