<!-- GUÍA: Integración de Códigos de Descuento en Cart.vue -->

<!-- 1. AGREGAR CAMPOS EN <script setup> -->

// Agregar estos estados después de los existentes:
const discountCode = ref('');
const appliedDiscount = ref(null); // { code_id, code, discount_amount, final_price }
const discountError = ref('');
const discountLoading = ref(false);
const discountApplied = ref(false);

// 2. COMPUTED PROPERTY PARA TOTAL CON DESCUENTO -->

const totalWithDiscount = computed(() => {
  const baseTotal = parseFloat(totalAmount.value) || 0;
  if (appliedDiscount.value) {
    return (baseTotal - appliedDiscount.value.discount_amount).toFixed(2);
  }
  return baseTotal;
});

// 3. FUNCIÓN PARA VALIDAR CÓDIGO DE DESCUENTO -->

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
      subtotal: totalAmount.value, // Enviar el total sin descuento
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

// 4. FUNCIÓN PARA LIMPIAR DESCUENTO -->

const removeDiscountCode = () => {
  discountCode.value = '';
  appliedDiscount.value = null;
  discountApplied.value = false;
  discountError.value = '';
};

// 5. ACTUALIZAR buildOrderPayload() PARA INCLUIR DESCUENTO -->

const buildOrderPayload = () => {
  const payload = {
    // ... campos existentes ...
    discount_code_id: appliedDiscount.value?.code_id || null,
    discount_amount: appliedDiscount.value?.discount_amount || 0,
    total: totalWithDiscount.value, // Usar el total con descuento
  };
  return payload;
};

// 6. ACTUALIZAR submitOrder() PARA REGISTRAR USO DE CÓDIGO -->

const submitOrder = async () => {
  try {
    const payload = buildOrderPayload();
    
    // ... enviar orden ...
    const response = await axios.post('/api/crear-pedido', payload);
    
    // Si el código fue aplicado y el cliente está registrado, registrar el uso
    if (appliedDiscount.value?.code_id && authStore.user?.id) {
      try {
        await axios.post(`/api/discount-codes/${appliedDiscount.value.code_id}/use`);
      } catch (error) {
        console.error('Error registrando uso del código:', error);
        // No bloquear si falla registrar, la orden ya se creó
      }
    }
    
    // ... resto del código ...
  } catch (error) {
    // ... manejo de errores ...
  }
};

<!-- 7. AGREGAR EN <template> - SECCIÓN DE DESCUENTO -->

<!-- Antes del resumen de orden, agregar: -->
<v-card class="mb-6 discount-section">
  <v-card-title>Código de Descuento</v-card-title>
  <v-card-text>
    <div v-if="!discountApplied" class="d-flex gap-2">
      <v-text-field
        v-model="discountCode"
        label="Código de descuento"
        placeholder="Ej: SUMMER2024"
        variant="outlined"
        size="small"
        :disabled="discountLoading"
        @keyup.enter="validateDiscountCode"
      ></v-text-field>
      <v-btn
        color="primary"
        :loading="discountLoading"
        @click="validateDiscountCode"
        class="align-self-end"
      >
        Aplicar
      </v-btn>
    </div>

    <!-- Error message -->
    <v-alert
      v-if="discountError"
      type="error"
      variant="tonal"
      closable
      class="mt-3"
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
          <strong>{{ appliedDiscount.code }}</strong> aplicado
          <br>
          <small>Descuento: ${{ appliedDiscount.discount_amount }}</small>
        </div>
        <v-btn
          icon="mdi-close"
          variant="text"
          size="small"
          @click="removeDiscountCode"
        ></v-btn>
      </div>
    </v-alert>
  </v-card-text>
</v-card>

<!-- 8. ACTUALIZAR RESUMEN DE ORDEN -->

<!-- En la sección de Order Summary, cambiar: -->
<div class="summary-row">
  <span>Subtotal:</span>
  <span>${{ parseFloat(totalAmount.value).toFixed(2) }}</span>
</div>

<!-- Agregar después: -->
<div v-if="appliedDiscount" class="summary-row discount-row">
  <span>Descuento ({{ appliedDiscount.code }}):</span>
  <span class="discount-amount">-${{ appliedDiscount.discount_amount }}</span>
</div>

<!-- Total final -->
<div class="summary-row total-row">
  <span>Total:</span>
  <span>${{ totalWithDiscount }}</span>
</div>
