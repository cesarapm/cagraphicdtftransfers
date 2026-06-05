<template>
  <v-container class="detalle-producto">
    <v-btn variant="text" @click="$router.back()" class="back-button mb-6">
      <v-icon start>mdi-arrow-left</v-icon>
      Volver
    </v-btn>

    <v-row v-if="loading">
      <v-col cols="12" class="text-center py-12">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
        <p class="text-h6 mt-4">Cargando producto...</p>
      </v-col>
    </v-row>

    <v-row v-else-if="product" class="detail-grid">
      <v-col cols="12" md="7">
        <section class="gallery-panel">
          <button
            type="button"
            class="main-image-button"
            @click="openGallery(activeImageIndex)"
          >
            <img
              v-if="selectedImage"
              :src="selectedImage"
              :alt="product.name"
              class="main-product-image"
            >
            <div v-else class="main-image-fallback">
              <span class="fallback-emoji">{{ getProductEmoji(product.category) }}</span>
            </div>
            <div class="image-zoom-pill">
              <v-icon size="18">mdi-magnify-plus-outline</v-icon>
              Ver detalle
            </div>
          </button>

          <div v-if="galleryImages.length" class="thumbnail-strip">
            <button
              v-for="(image, index) in galleryImages"
              :key="`${image}-${index}`"
              type="button"
              class="thumbnail-button"
              :class="{ 'thumbnail-button--active': index === activeImageIndex }"
              @click="activeImageIndex = index"
            >
              <img :src="image" :alt="`${product.name} vista ${index + 1}`" class="thumbnail-image">
            </button>
          </div>
        </section>
      </v-col>

      <v-col cols="12" md="5">
        <section class="detail-panel">
          <div class="detail-header">
            <div class="detail-badges">
              <span class="detail-kicker">{{ formatCategory(product.category) }}</span>
              <span v-if="product.collection" class="detail-collection">{{ formatCollection(product.collection) }}</span>
              <span v-if="product.codigo" class="detail-code">{{ product.codigo }}</span>
            </div>

            <h1 class="detail-title">{{ product.name }}</h1>
            <p class="detail-price">${{ product.price }} <span>MXN</span></p>

            <div class="status-row">
              <v-chip class="stock-chip" :color="product.stock > 0 ? 'success' : 'error'" size="large">
                {{ product.stock > 0 ? `${product.stock} disponibles` : 'Agotado' }}
              </v-chip>
              <span v-if="product.destacado" class="featured-pill">Edición destacada</span>
            </div>
          </div>

          <div class="description-block">
            <h2>Descripción</h2>
            <p v-for="(paragraph, index) in descriptionParagraphs" :key="index" class="description-text">
              {{ paragraph }}
            </p>
            <p v-if="descriptionParagraphs.length === 0" class="description-text">
              Esta pieza está lista para revelar su historia en cuanto agregues una descripción más detallada.
            </p>
          </div>

          <div v-if="productSpecs.length" class="specs-card">
            <h3>Ficha técnica</h3>
            <div class="specs-grid">
              <div v-for="spec in productSpecs" :key="spec.label" class="spec-item">
                <span class="spec-label">{{ spec.label }}</span>
                <strong class="spec-value">{{ spec.value }}</strong>
              </div>
            </div>
          </div>

          <div class="purchase-card">
            <div class="purchase-quantity-row">
              <div class="purchase-quantity-copy">
                <span class="purchase-quantity-label">Cantidad</span>
                <small class="purchase-quantity-hint">Puedes agregar hasta {{ maxQuantity }} {{ maxQuantity === 1 ? 'pieza' : 'piezas' }}</small>
              </div>

              <div class="purchase-quantity-control">
                <v-btn
                  icon
                  variant="tonal"
                  size="small"
                  class="quantity-stepper"
                  :disabled="quantity <= 1"
                  @click="changeQuantity(-1)"
                >
                  <v-icon size="18">mdi-minus</v-icon>
                </v-btn>

                <v-text-field
                  v-model.number="quantity"
                  type="number"
                  variant="outlined"
                  min="1"
                  :max="maxQuantity"
                  hide-details
                  density="comfortable"
                  class="purchase-quantity-field"
                ></v-text-field>

                <v-btn
                  icon
                  variant="tonal"
                  size="small"
                  class="quantity-stepper"
                  :disabled="quantity >= maxQuantity"
                  @click="changeQuantity(1)"
                >
                  <v-icon size="18">mdi-plus</v-icon>
                </v-btn>
              </div>
            </div>

            <v-btn
              color="primary"
              size="x-large"
              block
              class="mb-3 action-button"
              @click="addToCart"
              :disabled="!product || product.stock === 0"
            >
              <v-icon start>mdi-cart</v-icon>
              {{ product.stock > 0 ? 'Agregar al carrito' : 'No disponible' }}
            </v-btn>

            <v-btn
              color="grey-darken-4"
              size="x-large"
              block
              variant="outlined"
              class="action-button action-button--ghost"
              @click="buyNow"
              :disabled="!product || product.stock === 0"
            >
              Comprar ahora
            </v-btn>
          </div>
        </section>
      </v-col>
    </v-row>

    <v-row v-else class="my-12">
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey">mdi-alert-circle</v-icon>
        <p class="text-h5 mt-4">Producto no encontrado</p>
      </v-col>
    </v-row>

    <v-snackbar v-model="snackbar" :timeout="2000" color="success">
      <v-icon start>mdi-check-circle</v-icon>
      {{ snackbarText }}
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Fancybox } from '@fancyapps/ui'
import '@fancyapps/ui/dist/fancybox/fancybox.css'
import { useCart } from '../composables/useCart'

const route = useRoute()
const router = useRouter()
const quantity = ref(1)
const product = ref(null)
const loading = ref(false)
const activeImageIndex = ref(0)

const categoryEmojis = {
  anillo: '◌',
  collar: '✦',
  aretes: '✧',
  pulsera: '⟡'
}

const collectionValueMap = {
  'Coleccion Cosmologia Maya': 'Cosmología Maya',
  'Coleccion Maya Contemporanea': 'Maya Contemporánea'
}

const formatCategory = (category) => {
  if (!category) {
    return 'Pieza artesanal'
  }

  return category
    .replace(/[-_]/g, ' ')
    .replace(/\b\w/g, (letter) => letter.toUpperCase())
}

const formatCollection = (collection) => {
  return collectionValueMap[collection] || collection || 'Colección artesanal'
}

const getProductEmoji = (category) => {
  return categoryEmojis[category] || '✺'
}

const galleryImages = computed(() => {
  const images = [
    product.value?.image,
    ...(Array.isArray(product.value?.gallery) ? product.value.gallery : [])
  ]

  return [...new Set(images.filter(Boolean))]
})

const selectedImage = computed(() => {
  return galleryImages.value[activeImageIndex.value] ?? galleryImages.value[0] ?? null
})

const descriptionParagraphs = computed(() => {
  return (product.value?.description ?? '')
    .split(/\n+/)
    .map((paragraph) => paragraph.trim())
    .filter(Boolean)
})

const productSpecs = computed(() => {
  return [
    { label: 'Código', value: product.value?.codigo },
    { label: 'Material', value: product.value?.material },
    { label: 'Peso', value: product.value?.peso },
    { label: 'Dimensiones', value: product.value?.dimensiones }
  ].filter((item) => item.value)
})

const maxQuantity = computed(() => {
  return Math.max(1, Number(product.value?.stock ?? 1))
})

const normalizeQuantity = () => {
  const parsedQuantity = Number(quantity.value) || 1
  quantity.value = Math.min(Math.max(1, parsedQuantity), maxQuantity.value)
}

const changeQuantity = (delta) => {
  quantity.value = Number(quantity.value || 1) + delta
  normalizeQuantity()
}

const openGallery = (startIndex = 0) => {
  if (!galleryImages.value.length) {
    return
  }

  Fancybox.show(
    galleryImages.value.map((image) => ({ src: image, type: 'image' })),
    {
      startIndex,
      compact: false,
      dragToClose: true,
      Images: {
        zoom: true
      },
      Toolbar: {
        display: {
          left: ['infobar'],
          middle: [],
          right: ['zoomIn', 'zoomOut', 'toggle1to1', 'fullscreen', 'close']
        }
      },
      Thumbs: {
        type: 'classic'
      }
    }
  )
}

const fetchProduct = async () => {
  loading.value = true

  try {
    const response = await fetch(`/api/products/${route.params.id}`)

    if (!response.ok) {
      product.value = null
      return
    }

    product.value = await response.json()
    activeImageIndex.value = 0
    quantity.value = 1
  } catch (error) {
    console.error('Error al cargar el producto:', error)
    product.value = null
  } finally {
    loading.value = false
  }
}

watch(
  () => route.params.id,
  () => {
    fetchProduct()
  }
)

watch(quantity, () => {
  normalizeQuantity()
})

watch(maxQuantity, () => {
  normalizeQuantity()
})

onMounted(() => {
  fetchProduct()
})

const { addToCart: addProductToCart } = useCart()

const snackbar = ref(false)
const snackbarText = ref('')

const addToCart = () => {
  if (!product.value || product.value.stock === 0) {
    return
  }

  normalizeQuantity()
  addProductToCart(product.value, quantity.value)
  snackbarText.value = `${quantity.value} x ${product.value.name} agregado al carrito`
  snackbar.value = true
}

const buyNow = () => {
  if (!product.value || product.value.stock === 0) {
    return
  }

  addToCart()
  router.push({ name: 'Checkout' })
}
</script>

<style scoped>
.detalle-producto {
  margin-top: 130px !important;
  padding-bottom: 4rem;
}

.back-button {
  color: #7f6d5a;
}

.detail-grid {
  align-items: start;
}

.gallery-panel,
.detail-panel {
  background: rgba(255, 252, 248, 0.94);
  border: 1px solid rgba(184, 151, 120, 0.16);
  border-radius: 30px;
  box-shadow: 0 22px 48px rgba(107, 91, 71, 0.1);
}

.gallery-panel {
  padding: 1.25rem;
}

.main-image-button {
  position: relative;
  width: 100%;
  padding: 0;
  border: 0;
  border-radius: 24px;
  overflow: hidden;
  background: linear-gradient(180deg, #f7f0e9 0%, #ecdfd2 100%);
  min-height: 560px;
  cursor: zoom-in;
}

.main-product-image,
.main-image-fallback {
  display: block;
  width: 100%;
  min-height: 560px;
  height: 100%;
}

.main-product-image {
  object-fit: cover;
}

.main-image-fallback {
  display: flex;
  align-items: center;
  justify-content: center;
}

.fallback-emoji {
  font-size: clamp(5rem, 14vw, 8rem);
  color: #8c745f;
}

.image-zoom-pill {
  position: absolute;
  right: 1rem;
  bottom: 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 0.9rem;
  border-radius: 999px;
  background: rgba(255, 250, 244, 0.88);
  color: #6b5b47;
  font-size: 0.92rem;
  font-weight: 600;
  backdrop-filter: blur(8px);
}

.thumbnail-strip {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.85rem;
  margin-top: 0.95rem;
}

.thumbnail-button {
  padding: 0;
  border: 1px solid rgba(184, 151, 120, 0.2);
  border-radius: 18px;
  overflow: hidden;
  background: #f8f2ec;
  transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
}

.thumbnail-button:hover,
.thumbnail-button--active {
  transform: translateY(-2px);
  border-color: #8c745f;
  box-shadow: 0 14px 26px rgba(140, 116, 95, 0.18);
}

.thumbnail-image {
  display: block;
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
}

.detail-panel {
  padding: 2rem;
}

.detail-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.7rem;
  margin-bottom: 1rem;
}

.detail-kicker,
.detail-collection,
.detail-code,
.featured-pill {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 0.45rem 0.85rem;
  font-size: 0.8rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.detail-kicker {
  background: rgba(184, 151, 120, 0.12);
  color: #8c745f;
}

.detail-collection {
  background: rgba(140, 116, 95, 0.1);
  color: #6b5b47;
}

.detail-code {
  background: rgba(245, 239, 233, 0.95);
  color: #7f6d5a;
}

.detail-title {
  font-family: var(--font-brand), serif;
  font-size: clamp(2.1rem, 5vw, 3.4rem);
  line-height: 1.08;
  color: #5d4a38;
  margin-bottom: 0.9rem;
}

.detail-price {
  font-family: var(--font-brand), serif;
  font-size: 2rem;
  font-weight: 700;
  color: #8c745f;
  margin-bottom: 1rem;
}

.detail-price span {
  font-size: 1rem;
  font-weight: 500;
  color: #9b8874;
}

.status-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.8rem;
}

.stock-chip {
  font-weight: 600;
}

.featured-pill {
  background: rgba(140, 116, 95, 0.1);
  color: #6b5b47;
}

.description-block,
.specs-card,
.purchase-card {
  border-top: 1px solid rgba(184, 151, 120, 0.14);
  padding-top: 1.5rem;
  margin-top: 1.5rem;
}

.description-block h2,
.specs-card h3 {
  font-family: var(--font-brand), serif;
  color: #5d4a38;
  margin-bottom: 1rem;
}

.description-text {
  color: #786655;
  line-height: 1.9;
  margin-bottom: 0.95rem;
}

.specs-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}

.spec-item {
  padding: 1rem;
  border-radius: 18px;
  background: rgba(247, 241, 234, 0.9);
}

.spec-label {
  display: block;
  font-size: 0.82rem;
  color: #9b8874;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 0.45rem;
}

.spec-value {
  color: #5d4a38;
  font-size: 1rem;
}

.action-button {
  min-height: 52px;
  border-radius: 18px !important;
  font-weight: 600;
  letter-spacing: 0.04em;
}

.action-button--ghost {
  border-color: rgba(107, 91, 71, 0.25) !important;
}

.purchase-quantity-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.35rem;
  padding: 1rem 1.1rem;
  border-radius: 20px;
  background: rgba(247, 241, 234, 0.9);
}

.purchase-quantity-copy {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.purchase-quantity-label {
  font-size: 0.95rem;
  font-weight: 600;
  color: #5d4a38;
}

.purchase-quantity-hint {
  color: #8f7a65;
  font-size: 0.82rem;
  line-height: 1.4;
}

.purchase-quantity-control {
  display: flex;
  align-items: center;
  gap: 0.7rem;
}

.quantity-stepper {
  background: rgba(140, 116, 95, 0.12) !important;
  color: #6b5b47 !important;
}

.purchase-quantity-field {
  width: 110px;
}

.purchase-quantity-field :deep(.v-field) {
  border-radius: 18px;
  background: rgba(255, 252, 248, 0.96);
}

.purchase-quantity-field :deep(.v-field__input) {
  text-align: center;
  font-size: 1.1rem;
  font-weight: 600;
  color: #5d4a38;
  padding-inline: 0;
}

.purchase-quantity-field :deep(input[type='number']) {
  appearance: textfield;
  -moz-appearance: textfield;
}

.purchase-quantity-field :deep(input[type='number']::-webkit-outer-spin-button),
.purchase-quantity-field :deep(input[type='number']::-webkit-inner-spin-button) {
  -webkit-appearance: none;
  margin: 0;
}

@media (max-width: 960px) {
  .main-image-button,
  .main-product-image,
  .main-image-fallback {
    min-height: 460px;
  }

  .detail-panel {
    margin-top: 1.25rem;
  }
}

@media (max-width: 600px) {
  .detalle-producto {
    margin-top: 80px !important;
  }

  .purchase-quantity-row {
    flex-direction: column;
    align-items: stretch;
  }

  .purchase-quantity-control {
    justify-content: space-between;
  }

  .purchase-quantity-field {
    flex: 1;
    width: auto;
  }

  .gallery-panel,
  .detail-panel {
    border-radius: 22px;
  }

  .gallery-panel,
  .detail-panel {
    padding: 1rem;
  }

  .main-image-button,
  .main-product-image,
  .main-image-fallback {
    min-height: 340px;
  }

  .thumbnail-strip,
  .specs-grid {
    grid-template-columns: 1fr;
  }
}
</style>
