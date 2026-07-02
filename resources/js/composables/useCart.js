import { ref, computed, watch } from 'vue';

const CART_KEY = 'gorras_cart';
const DTF_CART_KEY = 'dtf_cart_items';

// Estado del carrito (compartido entre todos los componentes)
const cartItems = ref([]);
const dtfCartItems = ref([]);

// Cargar carritos del localStorage al iniciar
const loadCart = () => {
  // Carrito regular
  const saved = localStorage.getItem(CART_KEY);
  if (saved) {
    try {
      cartItems.value = JSON.parse(saved);
    } catch (e) {
      cartItems.value = [];
    }
  }

  // Carrito DTF
  const savedDtf = localStorage.getItem(DTF_CART_KEY);
  if (savedDtf) {
    try {
      dtfCartItems.value = JSON.parse(savedDtf);
    } catch (e) {
      dtfCartItems.value = [];
    }
  }
};

// Guardar carrito en localStorage
const saveCart = () => {
  localStorage.setItem(CART_KEY, JSON.stringify(cartItems.value));
  localStorage.setItem(DTF_CART_KEY, JSON.stringify(dtfCartItems.value));
};

// Inicializar carrito
loadCart();

// Observar cambios y guardar automáticamente
watch([cartItems, dtfCartItems], saveCart, { deep: true });

export function useCart() {
  // Agregar producto al carrito regular
  const addToCart = (product, quantity = 1) => {
    const existingItem = cartItems.value.find(item => item.id === product.id);

    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      cartItems.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image,
        category: product.category,
        quantity: quantity
      });
    }
  };

  // Agregar item DTF al carrito
  const addDtfToCart = (dtfItem) => {
    const existingItem = dtfCartItems.value.find(item => item.id === dtfItem.id);

    if (existingItem) {
      existingItem.quantity += dtfItem.quantity;
      existingItem.totalPrice = existingItem.unitPrice * existingItem.quantity;
    } else {
      dtfCartItems.value.push(dtfItem);
    }
  };

  // Remover producto del carrito regular
  const removeFromCart = (productId) => {
    const index = cartItems.value.findIndex(item => item.id === productId);
    if (index > -1) {
      cartItems.value.splice(index, 1);
    }
  };

  // Remover producto DTF del carrito
  const removeDtfFromCart = (itemId) => {
    dtfCartItems.value = dtfCartItems.value.filter(item => item.id !== itemId);
  };

  // Actualizar cantidad
  const updateQuantity = (productId, quantity) => {
    const item = cartItems.value.find(item => item.id === productId);
    if (item) {
      item.quantity = Math.max(1, quantity);
    }
  };

  // Actualizar cantidad DTF
  const updateDtfQuantity = (itemId, quantity) => {
    const item = dtfCartItems.value.find(item => item.id === itemId);
    if (item && quantity > 0) {
      item.quantity = parseInt(quantity);
      item.totalPrice = Number(item.unitPrice) * parseInt(quantity);
    }
  };

  // Limpiar carrito
  const clearCart = () => {
    cartItems.value = [];
    dtfCartItems.value = [];
  };

  // Total de items
  const itemCount = computed(() => {
    const regularCount = cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
    const dtfCount = dtfCartItems.value.reduce((sum, item) => sum + item.quantity, 0);
    return regularCount + dtfCount;
  });

  // Subtotal regular
  const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + (Number(item.price) * item.quantity), 0);
  });

  // Subtotal DTF
  const dtfSubtotal = computed(() => {
    return dtfCartItems.value.reduce((sum, item) => sum + (Number(item.totalPrice) || 0), 0);
  });

  // Subtotal total
  const totalSubtotal = computed(() => {
    return subtotal.value + dtfSubtotal.value;
  });

  // Total (puedes agregar envío o impuestos aquí)
  const total = computed(() => {
    return totalSubtotal.value;
  });

  return {
    cartItems,
    dtfCartItems,
    addToCart,
    addDtfToCart,
    removeFromCart,
    removeDtfFromCart,
    updateQuantity,
    updateDtfQuantity,
    clearCart,
    itemCount,
    subtotal,
    dtfSubtotal,
    totalSubtotal,
    total
  };
}
