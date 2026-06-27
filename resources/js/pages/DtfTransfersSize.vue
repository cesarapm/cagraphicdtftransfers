<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import ComponetDttf from '../components/dtf/ComponetDttf.vue';

const sizes = ref([]);
const loading = ref(false);
const selectedSize = ref(null);
const quantity = ref(1);
const expandedAccordion = ref(null);

onMounted(() => {
    document.title = 'DTF Transfers Size | CA Graphic DTF Transfers';

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

    updateMeta('description', 'Explore the size of DTF transfers available through CA Graphic DTF Transfers. Learn about the dimensions and options for your printing needs.');
    updateMeta('keywords', 'DTF transfers size, transfer dimensions, printing options');
    updateMeta('og:title', 'DTF Transfers Size | CA Graphic DTF Transfers', true);
    updateMeta('og:description', 'Explore the size of DTF transfers available through CA Graphic DTF Transfers. Learn about the dimensions and options for your printing needs.', true);

    fetchSizes();
});

const fetchSizes = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/dtf-sizes');
        sizes.value = response.data.filter(size => size.is_active);
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
                      <p class="price-value">${{ selectedSize.price }}</p>
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

                    <button class="upload-btn">
                      Upload Gang Sheet
                    </button>
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
                  {{ size.width }}" x {{ size.height }}"
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
  background-image: url('/images/portadas/DTF_Transfers_SIZE.webp');
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
  grid-template-columns: 1fr 1fr;
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
