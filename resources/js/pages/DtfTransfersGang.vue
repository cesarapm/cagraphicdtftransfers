<script setup>
import { onMounted, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import ComponetDttf from '../components/dtf/ComponetDttf.vue';

const router = useRouter();
const sizes = ref([]);
const loading = ref(false);
const selectedSize = ref(null);
const quantity = ref(1);
const expandedAccordion = ref(null);
const cartItems = ref([]);
const uploadedImage = ref(null);
const imagePreview = ref(null);

onMounted(() => {
    document.title = 'DTF Transfers Gang | CA Graphic DTF Transfers';

    const updateMeta = (name, content, isProperty = false) => {
        const selector = isProperty ? `meta[property="${name}"]` : `meta[name="${name}"]`;
        let meta = document.querySelector(selector);
        if (meta) meta.remove();

        const newMeta = document.createElement('meta');
        if (isProperty) {
            newMeta.setAttribute('property', name);
        } else {
            newMeta.setAttribute('name', name);
        }
        newMeta.content = content;
        document.head.appendChild(newMeta);
    };

    updateMeta('description', 'Explore the gang options of DTF transfers available through CA Graphic DTF Transfers. Learn about the dimensions and options for your printing needs.');
    updateMeta('keywords', 'DTF transfers gang, transfer dimensions, printing options');
    updateMeta('og:title', 'DTF Transfers Gang | CA Graphic DTF Transfers', true);
    updateMeta('og:description', 'Explore the gang options of DTF transfers available through CA Graphic DTF Transfers. Learn about the dimensions and options for your printing needs.', true);

    // Load cart items from localStorage
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
                        unitPrice: item.unitPrice,
                        totalPrice: item.totalPrice
                    };
                }
                // Otherwise, return as-is (already new structure)
                return item;
            });
            
            cartItems.value = items;
            // console.log('Loaded cart items from localStorage:', cartItems.value); 
        } catch (error) {
            console.error('Error loading cart items:', error);
        }
    }

    fetchSizes();
});

const fetchSizes = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/dtf-gangs');
        sizes.value = response.data.filter(size => size.is_active);

        // console.log('Fetched sizes:', sizes.value);
    } catch (error) {
        console.error('Error fetching sizes:', error);
    } finally {
        loading.value = false;
    }
};

const selectSize = (size) => {
    selectedSize.value = size;
    quantity.value = 1;
};

const incrementQuantity = () => {
    quantity.value++;
};

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

const toggleAccordion = (id) => {
    expandedAccordion.value = expandedAccordion.value === id ? null : id;
};

const shareProduct = () => {
    if (!selectedSize.value) return;
    
    const shareUrl = window.location.href;
    
    // Intenta clipboard API primero
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(shareUrl)
            .then(() => alert('✅ Link copied! Share this with friends.'))
            .catch(() => fallbackCopy(shareUrl));
    } else {
        // Fallback para navegadores antiguos
        fallbackCopy(shareUrl);
    }
};

const fallbackCopy = (text) => {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        alert('✅ Link copied! Share this with friends.');
    } catch (err) {
        alert('Could not copy link');
    }
    document.body.removeChild(textarea);
};

// Cart Functions
const addToCart = () => {
    // console.log('🔥 addToCart() called');
    
    if (!selectedSize.value || !uploadedImage.value) {
        alert('Please select a size and upload an image');
        return;
    }

    if (!imagePreview.value) {
        alert('Error processing image');
        return;
    }

    // console.log('📌 selectedSize.value:', selectedSize.value);
    // console.log('📌 selectedSize.value.id:', selectedSize.value.id);
    // console.log('📌 selectedSize.value.name:', selectedSize.value.name);
    // console.log('📌 selectedSize.value.price:', selectedSize.value.price);

    const cartItem = {
        id: Date.now(),
        type: 'gang',
        product: {
            id: selectedSize.value.id,  // ← CRITICAL: This is the gang sheet ID
            type: 'gang',
            name: selectedSize.value.name,
            description: selectedSize.value.description || '',
            sku: selectedSize.value.sku || '',
            category: 'dtf_gang',
            width: selectedSize.value.width,
            height: selectedSize.value.height,
            unit: selectedSize.value.unit,
            price: selectedSize.value.price,
        },
        quantity: parseInt(quantity.value),
        imagePreview: imagePreview.value,
        imageFile: null,
        unitPrice: hasPromotion.value ? Number(finalPrice.value) : Number(selectedSize.value.price) || 0,
        totalPrice: (hasPromotion.value ? Number(finalPrice.value) : Number(selectedSize.value.price) || 0) * parseInt(quantity.value)
    };

    // console.log('✅ GANG CART ITEM CREATED:', {
    //     id: cartItem.id,
    //     type: cartItem.type,
    //     'product.id': cartItem.product.id,
    //     'product.name': cartItem.product.name,
    //     'product.price': cartItem.product.price,
    // });

    // Store the File object in a Map (not serializable)
    if (!window.dtfCartImageMap) {
        window.dtfCartImageMap = new Map();
    }
    window.dtfCartImageMap.set(cartItem.id.toString(), uploadedImage.value);

    cartItems.value.push(cartItem);
    
    // Store serializable data in localStorage
    const serializableCart = cartItems.value.map(item => ({
        id: item.id,
        type: item.type,
        product: item.product,  // ← Must include full product object
        quantity: item.quantity,
        imagePreview: item.imagePreview,
        unitPrice: Number(item.unitPrice) || 0,
        totalPrice: Number(item.totalPrice) || 0
    }));
    
    // console.log('💾 ABOUT TO SAVE TO LOCALSTORAGE:');
    // console.log('   Items count:', serializableCart.length);
    serializableCart.forEach((item, idx) => {
        // console.log(`   Item ${idx}:`, {
        //     id: item.id,
        //     type: item.type,
        //     'product.id': item.product?.id,
        //     'product.name': item.product?.name,
        // });
    });
    
    const jsonString = JSON.stringify(serializableCart);
    localStorage.setItem('dtf_cart_items', jsonString);
    
    // console.log('📝 RAW JSON SAVED TO LOCALSTORAGE:');
    // console.log(jsonString);
    
    // console.log('✅ VERIFICATION - Reading back from localStorage:');
    const readBack = JSON.parse(localStorage.getItem('dtf_cart_items'));
    readBack.forEach((item, idx) => {
        // console.log(`   Item ${idx}:`, {
        //     id: item.id,
        //     type: item.type,
        //     'product.id': item.product?.id,
        //     'product.name': item.product?.name,
        // });
    });
    
    resetForm();
    
    // Redirect to cart page after adding item
    router.push({ name: 'Cart' });
};

const resetForm = () => {
    uploadedImage.value = null;
    imagePreview.value = null;
    quantity.value = 1;
};

const removeFromCart = (id) => {
    cartItems.value = cartItems.value.filter(item => item.id !== id);
    
    // Remove from image map
    if (window.dtfCartImageMap) {
        window.dtfCartImageMap.delete(id.toString());
    }
    
    // Update localStorage
    const serializableCart = cartItems.value.map(item => ({
        id: item.id,
        type: item.type,
        product: item.product,
        quantity: item.quantity,
        imagePreview: item.imagePreview,
        unitPrice: Number(item.unitPrice) || 0,
        totalPrice: Number(item.totalPrice) || 0
    }));
    
    localStorage.setItem('dtf_cart_items', JSON.stringify(serializableCart));
};

const updateCartQuantity = (id, newQuantity) => {
    const item = cartItems.value.find(item => item.id === id);
    if (item && newQuantity > 0) {
        item.quantity = parseInt(newQuantity);
        item.totalPrice = Number(item.unitPrice) * parseInt(newQuantity);
        
        // Update localStorage
        const serializableCart = cartItems.value.map(item => ({
            id: item.id,
            type: item.type,
            product: item.product,
            quantity: item.quantity,
            imagePreview: item.imagePreview,
            unitPrice: Number(item.unitPrice) || 0,
            totalPrice: Number(item.totalPrice) || 0
        }));
        
        localStorage.setItem('dtf_cart_items', JSON.stringify(serializableCart));
    }
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        uploadedImage.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const cartTotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + Number(item.totalPrice || 0), 0).toFixed(2);
});

// Computed for promotion handling
const hasPromotion = computed(() => {
    return selectedSize.value?.active_promotion != null;
});

const promotionDiscount = computed(() => {
    if (!hasPromotion.value) return 0;
    const promo = selectedSize.value.active_promotion;
    const price = Number(selectedSize.value.price) || 0;
    const discountValue = Number(promo.discount_value) || 0;
    if (promo.discount_type === 'percentage') {
        return (price * discountValue) / 100;
    }
    return discountValue;
});

const finalPrice = computed(() => {
    if (!hasPromotion.value) return Number(selectedSize.value.price) || 0;
    const price = Number(selectedSize.value.price) || 0;
    return Math.max(0, price - promotionDiscount.value);
});
</script>

<template>
  <div class="dtf-transfers-size-page">
    <!-- Hero Section -->
    <section class="dtf-transfers-size-hero">
    </section>

    <!-- Main Content -->
    <section class="sizes-section">
      <div class="container">
        <div class="content-grid">
          <!-- Right Column: Details & Image -->
          <div class="details-column">
            <div v-if="selectedSize" class="details-card">
              <div class="details-grid">
                <!-- Image -->
                <div class="image-container">
                  <div v-if="selectedSize.image_path" class="image-wrapper">
                    <img 
                      :src="`/storage/${selectedSize.image_path}`" 
                      :alt="selectedSize.name"
                      class="product-image"
                    />
                  </div>
                  <div v-else class="no-image">
                    <p class="no-image-icon">📦</p>
                    <p>No image available</p>
                  </div>
                </div>

                <!-- Details -->
                <div class="details-content">
                  <div class="details-inner">
                    <!-- Header -->
                    <h2 class="product-name">{{ selectedSize.name }}</h2>
                    <p class="product-subtitle">DTF Transfer Size</p>

                    <!-- Price -->
                    <div class="price-section">
                      <p class="price-label">Price</p>
                      <div class="price-display">
                        <div v-if="hasPromotion" class="promotion-price-wrapper">
                          <div class="discount-badge">
                            <span class="discount-percent">-{{ selectedSize.active_promotion.discount_value }}%</span>
                          </div>
                          <p class="price-original">${{ Number(selectedSize.price).toFixed(2) }}</p>
                          <p class="price-value-discount">${{ Number(finalPrice).toFixed(2) }}</p>
                        </div>
                        <div v-else>
                          <p class="price-value">${{ Number(selectedSize.price).toFixed(2) }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Specifications -->
                    <div class="specs-box">
                      <div class="specs-grid">
                        <div class="spec-item">
                          <p class="spec-label">Width</p>
                          <p class="spec-value">{{ selectedSize.width }}</p>
                        </div>
                        <div class="spec-item">
                          <p class="spec-label">Height</p>
                          <p class="spec-value">{{ selectedSize.height }}</p>
                        </div>
                        <div class="spec-item full-width">
                          <p class="spec-label">Unit</p>
                          <p class="spec-value">{{ selectedSize.unit }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Description -->
                    <div v-if="selectedSize.description" class="description-section">
                      <p class="description-label">Description</p>
                      <div class="description-content" v-html="selectedSize.description"></div>
                    </div>

                    <!-- Warning Badge -->
                    <div class="warning-badge">
                      <span class="badge-icon">⚠️</span>
                      <span class="badge-text">DO NOT MIRROR YOUR IMAGES - THEY PRINT EXACTLY AS DISPLAYED.</span>
                    </div>

                    


                  </div>

                  <!-- Quantity & Action -->
                  <div class="action-section">
                    <div class="quantity-group">
                      <span class="quantity-label">Quantity:</span>
                      <div class="quantity-control">
                        <button 
                          @click="decrementQuantity"
                          class="qty-btn"
                        >
                          −
                        </button>
                        <input 
                          v-model.number="quantity" 
                          type="number" 
                          min="1"
                          class="qty-input"
                        />
                        <button 
                          @click="incrementQuantity"
                          class="qty-btn"
                        >
                          +
                        </button>
                      </div>
                    </div>

              

                    <!-- Image Upload Section -->
                    <div class="image-upload-section" v-if="selectedSize">
                      <label class="upload-label">Upload Your Image</label>
                      <div class="upload-input-wrapper">
                        <input 
                          type="file" 
                          accept="image/*" 
                          @change="handleImageUpload"
                          class="file-input"
                          id="imageUpload"
                        />
                        <label for="imageUpload" class="file-label">
                          <span v-if="!uploadedImage" class="upload-icon">📷</span>
                          <span v-else class="success-icon">✓</span>
                          {{ uploadedImage ? uploadedImage.name : 'Choose Image' }}
                        </label>
                      </div>
                      
                      <!-- Image Preview -->
                      <div v-if="imagePreview" class="image-preview">
                        <img :src="imagePreview" alt="Preview" />
                      </div>

                      <!-- Add to Cart Button -->
                      <button @click="addToCart" class="add-to-cart-btn">
                        Add to Cart
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="empty-state">
              <p class="empty-title">Select a size to see details</p>
              <p class="empty-subtitle">Choose from the available sizes on the left</p>
            </div>
          </div>

          <!-- Left Column: Size Selector -->
          <div class="sizes-column">
            <div class="sizes-card">
              <h3 class="sizes-title">Available Sizes</h3>
              
              <div v-if="loading" class="loading-state">
                <p>Loading sizes...</p>
              </div>

              <div v-else-if="sizes.length > 0" class="sizes-buttons">
                <button
                  v-for="size in sizes"
                  :key="size.id"
                  @click="selectSize(size)"
                  :class="{ active: selectedSize?.id === size.id }"
                  class="size-btn"
                >
                  <!-- {{ size.width }}" x {{ size.height }}" -->

                  <!-- {{ Math.round(size.width) }}" x {{ Math.round(size.height) }}" -->
                  {{ size.name }}
                </button>
              </div>

              <div v-else class="empty-sizes">
                <p>No sizes available.</p>
              </div>

              <!-- Accordion Section -->
              <div class="accordion-section">
                <!-- Share -->
                <div class="accordion-item">
                  <button 
                    @click="toggleAccordion('share')"
                    class="accordion-header"
                  >
                    <span class="accordion-icon">🔗</span>
                    <span class="accordion-title">Share</span>
                    <span class="accordion-toggle" :class="{ open: expandedAccordion === 'share' }">▼</span>
                  </button>
                  <div v-show="expandedAccordion === 'share'" class="accordion-content">
                    <p>Share this product with friends and family.</p>
                    <button @click="shareProduct" class="share-btn" v-if="selectedSize">
                      Share Link
                    </button>
                    <p v-else class="no-size-warning">Select a size first to share</p>
                  </div>
                </div>

                <!-- Shipping & Returns -->
                <div class="accordion-item">
                  <button 
                    @click="toggleAccordion('shipping')"
                    class="accordion-header"
                  >
                    <span class="accordion-icon">📦</span>
                    <span class="accordion-title">Shipping & Returns</span>
                    <span class="accordion-toggle" :class="{ open: expandedAccordion === 'shipping' }">▼</span>
                  </button>
                  <div v-show="expandedAccordion === 'shipping'" class="accordion-content">
                    <p>Place your order before 2:00 PM CST, and we'll ship it the same business day. Orders received after 2:00 PM CST will be shipped on the next business day.</p>
                  </div>
                </div>

                <!-- 24/7 Support -->
                <div class="accordion-item">
                  <button 
                    @click="toggleAccordion('support')"
                    class="accordion-header"
                  >
                    <span class="accordion-icon">☎️</span>
                    <span class="accordion-title">24/7 Support</span>
                    <span class="accordion-toggle" :class="{ open: expandedAccordion === 'support' }">▼</span>
                  </button>
                  <div v-show="expandedAccordion === 'support'" class="accordion-content">
                    <p><strong>We're Here for You — Anytime, Day or Night.</strong></p>
                    <p>Whether you have questions, need assistance with an order, or encounter an issue, our dedicated team is always ready to help. We're committed to providing fast, reliable support whenever you need it.</p>
                    <p><strong>For urgent matters, call us at</strong><br><a href="tel:+13128434099">+1 (312) 843 - 4099</a></p>
                    <p><strong>Or email us anytime at</strong><br><a href="mailto:contact@cagraphicdtftransfers.com">contact@cagraphicdtftransfers.com</a></p>
                    <p>Need assistance? Our live chat and contact form options make it easy to get the help you need, quickly and conveniently.</p>
                    <p>Your satisfaction is our priority, and we're committed to providing exceptional service every step of the way. Se habla español — we're happy to assist you in both English and Spanish.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <ComponetDttf />

    <!-- Shopping Cart Section -->
    <section v-if="cartItems.length > 0" class="cart-section">
      <div class="container">
        <div class="cart-header">
          <h2 class="cart-title">Shopping Cart ({{ cartItems.length }} items)</h2>
          <router-link to="/cart" class="view-cart-link">
            → View Full Cart
          </router-link>
        </div>
        
        <div class="cart-table-wrapper">
          <table class="cart-table">
            <thead>
              <tr>
                <th class="col-image">Image</th>
                <th class="col-size">Size</th>
                <th class="col-quantity">Quantity</th>
                <th class="col-price">Unit Price</th>
                <th class="col-total">Total</th>
                <th class="col-actions">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in cartItems" :key="item.id" class="cart-row">
                <!-- Image -->
                <td class="col-image">
                  <div class="cart-image">
                    <img :src="item.imagePreview" :alt="item.product.name" />
                  </div>
                </td>

                <!-- Size -->
                <td class="col-size">
                  <div class="size-info">
                    <p class="size-name">{{ item.product.name }}</p>
                    <p class="size-dimensions">{{ item.product.width }}" x {{ item.product.height }}"</p>
                  </div>
                </td>

                <!-- Quantity -->
                <td class="col-quantity">
                  <div class="quantity-edit">
                    <button 
                      @click="updateCartQuantity(item.id, item.quantity - 1)"
                      class="qty-edit-btn"
                    >
                      −
                    </button>
                    <input 
                      :value="item.quantity" 
                      type="number" 
                      min="1"
                      @change="updateCartQuantity(item.id, $event.target.value)"
                      class="qty-edit-input"
                    />
                    <button 
                      @click="updateCartQuantity(item.id, item.quantity + 1)"
                      class="qty-edit-btn"
                    >
                      +
                    </button>
                  </div>
                </td>

                <!-- Unit Price -->
                <td class="col-price">
                  <p class="price">${{ Number(item.unitPrice).toFixed(2) }}</p>
                </td>

                <!-- Total Price -->
                <td class="col-total">
                  <p class="total-price">${{ Number(item.totalPrice).toFixed(2) }}</p>
                </td>

                <!-- Actions -->
                <td class="col-actions">
                  <button 
                    @click="removeFromCart(item.id)"
                    class="remove-btn"
                    title="Remove from cart"
                  >
                    🗑️
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Cart Summary -->
        <div class="cart-summary">
          <div class="summary-content">
            <div class="summary-row">
              <span class="summary-label">Subtotal:</span>
              <span class="summary-value">${{ cartTotal }}</span>
            </div>
            <div class="summary-row">
              <span class="summary-label">Items:</span>
              <span class="summary-value">{{ cartItems.length }}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row total">
              <span class="summary-label">Total:</span>
              <span class="summary-value">${{ cartTotal }}</span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="cart-actions">
            <router-link to="/cart" class="checkout-btn">
              View Cart & Checkout 💳
            </router-link>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dtf-transfers-size-page {
  background: #ffffff;
  min-height: 100vh;
}

/* Hero Section */
.dtf-transfers-size-hero {
  position: relative;
  height: 622px;
  background-image: url('/images/portadas/DTF_Transfers_GANG.webp');
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 1024px) {
  .dtf-transfers-size-hero {
    height: 400px;
    background-attachment: scroll;
  }
}

@media (max-width: 768px) {
  .dtf-transfers-size-hero {
    height: 250px;
    background-position: center right;
  }
}

@media (max-width: 480px) {
  .dtf-transfers-size-hero {
    height: 160px;
    background-position: center center;
  }
}

/* Sizes Section */
.sizes-section {
  background: #ffffff;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.content-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

/* Details Column */
.details-column {
  grid-column: 1;
}

.details-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  padding: 1.5rem;
}

.image-container {
  background: #f9fafb;
  border-radius: 8px;
  padding: 1rem;
  display: flex;
  align-items: flex-start;
}

.image-wrapper {
  width: 100%;
}

.product-image {
  width: 100%;
  height: auto;
  border-radius: 8px;
  display: block;
}

.no-image {
  text-align: center;
  color: #d1d5db;
  padding: 2rem 0;
  width: 100%;
}

.no-image-icon {
  font-size: 2.25rem;
  margin-bottom: 0.5rem;
}

/* Details Content */
.details-content {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.details-inner {
  flex: 1;
}

.product-name {
  font-size: 1.875rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.product-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 1rem;
}

.price-section {
  margin-bottom: 1.5rem;
}

.price-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.price-value {
  font-size: 2.25rem;
  font-weight: bold;
  color: #16a34a;
}

.price-display {
  position: relative;
}

.promotion-price-wrapper {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  position: relative;
}

.discount-badge {
  position: absolute;
  top: -0.5rem;
  right: 0;
  background: #ef4444;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-weight: bold;
  font-size: 0.875rem;
  z-index: 10;
}

.discount-percent {
  display: inline-block;
}

.price-original {
  font-size: 1.125rem;
  color: #9ca3af;
  text-decoration: line-through;
  margin: 0;
}

.price-value-discount {
  font-size: 2.25rem;
  font-weight: bold;
  color: #16a34a;
  margin: 0;
}

.specs-box {
  background: #f9fafb;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.specs-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.spec-item {
  display: flex;
  flex-direction: column;
}

.spec-item.full-width {
  grid-column: 1 / -1;
}

.spec-label {
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.spec-value {
  font-size: 1.25rem;
  font-weight: bold;
  color: #1f2937;
}

.description-section {
  margin-bottom: 1.5rem;
  border-top: 1px solid #e5e7eb;
  padding-top: 1rem;
}

.description-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.description-content {
  color: #374151;
  font-size: 0.95rem;
  line-height: 1.6;
}

.warning-badge {
  background: #000000;
  color: #ffffff;
  padding: 1rem 1.25rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.badge-icon {
  font-size: 1.25rem;
  flex-shrink: 0;
}

.badge-text {
  flex: 1;
}

/* Action Section */
.action-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.quantity-group {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.quantity-label {
  color: #374151;
  font-weight: 600;
}

.quantity-control {
  display: flex;
  align-items: center;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  overflow: hidden;
}

.qty-btn {
  width: 40px;
  height: 40px;
  border: none;
  background: transparent;
  color: #6b7280;
  cursor: pointer;
  font-size: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.qty-btn:hover {
  background: #f3f4f6;
}

.qty-input {
  width: 48px;
  height: 40px;
  border: none;
  border-left: 1px solid #d1d5db;
  border-right: 1px solid #d1d5db;
  text-align: center;
  font-size: 1rem;
  outline: none;
}

.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.qty-input[type=number] {
  appearance: textfield;
  -moz-appearance: textfield;
}

.upload-btn {
  width: 100%;
  background: #3b82f6;
  color: white;
  font-weight: bold;
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.2s;
}

.upload-btn:hover {
  background: #2563eb;
}

/* Empty State */
.empty-state {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 3rem;
  text-align: center;
}

.empty-title {
  font-size: 1.25rem;
  color: #6b7280;
  margin-bottom: 1rem;
}

.empty-subtitle {
  color: #9ca3af;
}

/* Sizes Column */
.sizes-column {
  grid-column: 2;
}

.sizes-card {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 2rem;
}

.sizes-title {
  font-size: 1.25rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.sizes-buttons {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.75rem;
}

.size-btn {
  padding: 1rem 0.75rem;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.9rem;
  background: #f3f4f6;
  color: #1f2937;
  transition: all 0.2s;
  transform-origin: center;
}

.size-btn:hover {
  background: #e5e7eb;
  transform: scale(1.05);
}

.size-btn.active {
  background: #3b82f6;
  color: white;
}

.loading-state,
.empty-sizes {
  text-align: center;
  padding: 2rem 0;
  color: #6b7280;
}

/* Accordion Section */
.accordion-section {
  margin-top: 2rem;
  border-top: 1px solid #e5e7eb;
  padding-top: 1.5rem;
}

.accordion-item {
  margin-bottom: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  overflow: hidden;
}

.accordion-header {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  background: #f9fafb;
  border: none;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 600;
  color: #1f2937;
  transition: background 0.2s;
}

.accordion-header:hover {
  background: #f3f4f6;
}

.accordion-icon {
  font-size: 1.25rem;
  flex-shrink: 0;
}

.accordion-title {
  flex: 1;
  text-align: left;
}

.accordion-toggle {
  font-size: 0.75rem;
  color: #6b7280;
  transition: transform 0.3s ease;
  flex-shrink: 0;
}

.accordion-toggle.open {
  transform: rotate(180deg);
}

.accordion-content {
  padding: 1rem;
  background: white;
  border-top: 1px solid #e5e7eb;
  color: #374151;
  font-size: 0.9rem;
  line-height: 1.6;
}

.accordion-content p {
  margin-bottom: 0.5rem;
}

.accordion-content p:last-child {
  margin-bottom: 0;
}

.accordion-content a {
  color: #3b82f6;
  text-decoration: underline;
}

.accordion-content a:hover {
  color: #2563eb;
}

.share-btn {
  margin-top: 0.75rem;
  padding: 0.5rem 1rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  transition: background 0.2s;
  width: 100%;
}

.share-btn:hover {
  background: #2563eb;
}

.no-size-warning {
  color: #ef4444;
  font-size: 0.85rem;
  margin-top: 0.5rem;
}

/* Image Upload Section */
.image-upload-section {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.upload-label {
  display: block;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.75rem;
}

.upload-input-wrapper {
  margin-bottom: 1rem;
}

.file-input {
  display: none;
}

.file-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  border: 2px dashed #3b82f6;
  border-radius: 8px;
  background: #eff6ff;
  color: #1e40af;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.file-label:hover {
  border-color: #2563eb;
  background: #e0e7ff;
}

.upload-icon,
.success-icon {
  font-size: 1.25rem;
}

.success-icon {
  color: #16a34a;
}

.image-preview {
  margin: 1rem 0;
  border-radius: 8px;
  overflow: hidden;
  max-height: 200px;
  background: #f9fafb;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Add to Cart Button */
.add-to-cart-btn {
  width: 100%;
  background: #16a34a;
  color: white;
  font-weight: bold;
  padding: 0.875rem 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.2s;
  margin-top: 1rem;
}

.add-to-cart-btn:hover {
  background: #15803d;
}

/* Shopping Cart Section */
.cart-section {
  background: #f9fafb;
  padding: 3rem 0;
  margin-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.cart-title {
  font-size: 1.875rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 2rem;
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.view-cart-link {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}

.view-cart-link:hover {
  color: #2563eb;
  text-decoration: underline;
}

.cart-table-wrapper {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: auto;
  margin-bottom: 2rem;
}

.cart-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 900px;
}

.cart-table thead {
  background: #f3f4f6;
  border-bottom: 2px solid #e5e7eb;
}

.cart-table th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.cart-table tbody tr {
  border-bottom: 1px solid #e5e7eb;
  transition: background 0.2s;
}

.cart-table tbody tr:hover {
  background: #f9fafb;
}

.cart-table td {
  padding: 1rem;
  color: #374151;
}

/* Table Columns */
.col-image {
  width: 100px;
}

.col-size {
  width: 140px;
}

.col-quantity {
  width: 140px;
}

.col-price {
  width: 100px;
}

.col-total {
  width: 100px;
}

.col-actions {
  width: 80px;
}

/* Cart Image */
.cart-image {
  width: 80px;
  height: 80px;
  border-radius: 6px;
  overflow: hidden;
  background: #f3f4f6;
}

.cart-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Size Info */
.size-info p {
  margin: 0.25rem 0;
}

.size-name {
  font-weight: 600;
  color: #1f2937;
}

.size-dimensions {
  font-size: 0.875rem;
  color: #6b7280;
}

/* Quantity Edit */
.quantity-edit {
  display: flex;
  align-items: center;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  overflow: hidden;
  width: 100px !important;

}

.qty-edit-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  color: #6b7280;
  cursor: pointer;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.qty-edit-btn:hover {
  background: #f3f4f6;
}

.qty-edit-input {
  width: 40px;
  height: 32px;
  border: none;
  border-left: 1px solid #d1d5db;
  border-right: 1px solid #d1d5db;
  text-align: center;
  font-size: 0.875rem;
  outline: none;
}

.qty-edit-input::-webkit-outer-spin-button,
.qty-edit-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.qty-edit-input[type=number] {
  appearance: textfield;
  -moz-appearance: textfield;
}

/* Price */
.price {
  font-weight: 600;
  color: #16a34a;
}

.total-price {
  font-weight: bold;
  color: #1f2937;
}

/* Remove Button */
.remove-btn {
  width: 32px;
  height: 32px;
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 1.25rem;
  transition: transform 0.2s;
}

.remove-btn:hover {
  transform: scale(1.2);
}

/* Cart Summary */
.cart-summary {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  margin-left: auto;
}

.summary-content {
  margin-bottom: 1.5rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  font-size: 0.95rem;
}

.summary-row.total {
  font-size: 1.25rem;
  font-weight: bold;
  color: #1f2937;
}

.summary-label {
  color: #6b7280;
}

.summary-value {
  color: #1f2937;
  font-weight: 600;
}

.summary-row.total .summary-value {
  color: #16a34a;
}

.summary-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 0.75rem 0;
}

/* Cart Actions */
.cart-actions {
  display: flex;
  gap: 0.75rem;
}

.checkout-btn {
  flex: 1;
  background: #10b981;
  color: white;
  font-weight: bold;
  padding: 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.2s;
}

.checkout-btn:hover {
  background: #059669;
}

/* Responsive Cart */
@media (max-width: 768px) {
  .cart-table-wrapper {
    overflow-x: auto;
  }

  .cart-table th,
  .cart-table td {
    padding: 0.75rem;
    font-size: 0.8rem;
  }

  .col-image {
    width: 80px;
  }

  .col-size {
    width: 100px;
  }

  .col-params {
    width: 120px;
  }

  .cart-image {
    width: 60px;
    height: 60px;
  }

  .cart-summary {
    max-width: 100%;
    margin-left: 0;
    margin-top: 1rem;
  }

  .param-inputs {
    grid-template-columns: 1fr;
  }
}


/* Responsive */
@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
  }

  .details-column,
  .sizes-column {
    grid-column: 1;
  }

  .sizes-card {
    position: static;
  }

  .details-grid {
    grid-template-columns: 1fr;
  }

  .sizes-buttons {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }

  .sizes-section {
    padding: 1rem 0;
  }


  .details-grid {
    padding: 1rem;
    gap: 1rem;
  }

  .product-name {
    font-size: 1.5rem;
  }

  .price-value {
    font-size: 1.875rem;
  }

  .sizes-buttons {
    grid-template-columns: repeat(2, 1fr);
  }

  .content-grid {
    gap: 1rem;
  }

  .quantity-group {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
