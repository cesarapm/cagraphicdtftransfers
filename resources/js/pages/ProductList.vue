<template>
  <v-container class="product-list">
    <section class="catalog-hero">
      <span class="catalog-kicker">Colección completa</span>
      <h1 class="catalog-title">Piezas que conservan origen y presencia</h1>
      <p class="catalog-text">
        Explora joyería artesanal mexicana con simbología, materiales nobles y un lenguaje visual más limpio, sereno y preciso.
      </p>
    </section>

    <section class="filters-shell mb-8">
      <div class="filters-copy">
        <span class="filters-label">Filtrar catálogo</span>
        <p class="filters-summary">
          {{ totalProducts }} {{ totalProducts === 1 ? 'pieza disponible' : 'piezas disponibles' }}
        </p>
      </div>

      <v-row class="filters-grid" align="center">
        <v-col cols="12" md="3">
          <v-select
            v-model="selectedCollection"
            :items="collectionOptions"
            label="Colección"
            variant="outlined"
            density="comfortable"
            hide-details
            item-title="title"
            item-value="value"
            prepend-inner-icon="mdi-diamond-stone"
            class="filter-field"
          ></v-select>
        </v-col>
        <v-col cols="12" md="3">
          <v-select
            v-model="selectedCategory"
            :items="categories"
            label="Categoría"
            variant="outlined"
            density="comfortable"
            hide-details
            item-title="title"
            item-value="value"
            prepend-inner-icon="mdi-shape-outline"
            class="filter-field"
          ></v-select>
        </v-col>
        <v-col cols="12" md="3">
          <v-select
            v-model="sortBy"
            :items="sortOptions"
            label="Ordenar por"
            variant="outlined"
            density="comfortable"
            hide-details
            prepend-inner-icon="mdi-tune-vertical-variant"
            class="filter-field"
          ></v-select>
        </v-col>
        <v-col cols="12" md="3">
          <v-text-field
            v-model="search"
            label="Buscar por nombre o descripción"
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="comfortable"
            hide-details
            clearable
            class="filter-field"
          ></v-text-field>
        </v-col>
      </v-row>
    </section>

    <!-- Grid de productos -->
    <v-row v-if="!loading" class="products-grid-row">
      <v-col
        v-for="product in filteredProducts"
        :key="product.id"
        cols="12" sm="6" md="4" lg="3"
      >
        <v-card class="product-card" hover @click="viewProduct(product.id)">
          <div class="product-card__media">
            <div class="product-card__badges">
              <span class="product-card__category">{{ formatCategory(product.category) }}</span>
              <span v-if="product.destacado" class="product-card__featured">Destacado</span>
            </div>
            <v-img
              v-if="product.image"
              :src="product.image"
              :alt="product.name"
              class="product-card__image"
              cover
              height="280"
            />
            <div v-else class="product-card__fallback">
              <span class="product-card__emoji">{{ getProductEmoji(product.category) }}</span>
            </div>
          </div>
          <v-card-text class="product-card__content">
            <p v-if="product.collection" class="product-card__collection">{{ formatCollection(product.collection) }}</p>
            <h3 class="product-card__title">{{ product.name }}</h3>
            <p class="product-card__description">{{ truncateDescription(product.description) }}</p>
            <div class="product-card__meta">
              <span v-if="product.material" class="product-card__meta-item">{{ product.material }}</span>
              <span class="product-card__meta-item" :class="{ 'product-card__meta-item--soldout': product.stock === 0 }">
                {{ product.stock > 0 ? `${product.stock} disponibles` : 'Agotado' }}
              </span>
            </div>
          </v-card-text>
          <v-card-actions class="product-card__actions">
            <span class="product-card__price">${{ product.price }}</span>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              size="small"
              class="product-card__button"
              @click.stop="addToCart(product)"
              :disabled="product.stock === 0"
            >
              {{ product.stock === 0 ? 'Agotado' : 'Agregar' }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <v-row v-if="loading">
      <v-col cols="12" class="text-center py-12">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
        <p class="text-h6 mt-4">Cargando productos...</p>
      </v-col>
    </v-row>

    <v-row v-if="filteredProducts.length === 0">
      <v-col cols="12" class="text-center py-12">
        <v-icon size="64" color="grey">mdi-package-variant</v-icon>
        <p class="text-h6 text-grey mt-4">No se encontraron productos</p>
      </v-col>
    </v-row>

    <div v-if="!loading && totalPages > 1 && filteredProducts.length > 0" class="pagination-shell">
      <v-pagination
        v-model="currentPage"
        :length="totalPages"
        :total-visible="$vuetify.display.smAndDown ? 4 : 7"
        rounded="circle"
        active-color="primary"
      />
    </div>

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar" :timeout="2000" color="success">
      <v-icon start>mdi-check-circle</v-icon>
      {{ snackbarText }}
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCart } from '../composables/useCart';

const route = useRoute();
const router = useRouter();

const collectionValueMap = {
  'Coleccion Cosmologia Maya': 'Cosmología Maya',
  'Coleccion Maya Contemporanea': 'Maya Contemporánea',
};

const collectionAliasMap = {
  'cosmologia-maya': 'Coleccion Cosmologia Maya',
  'maya-contemporanea': 'Coleccion Maya Contemporanea',
  'ancestral': 'Coleccion Cosmologia Maya',
  'contemporaneo': 'Coleccion Maya Contemporanea',
};

const normalizeCollection = (value) => {
  if (!value) {
    return 'Todas';
  }

  return collectionAliasMap[value] ?? (collectionValueMap[value] ? value : 'Todas');
};

const selectedCollection = ref(normalizeCollection(route.query.collection));
const selectedCategory = ref('Todas');
const sortBy = ref('name');
const search = ref('');
const products = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const totalProducts = ref(0);

const collectionOptions = [
  { title: 'Todas las colecciones', value: 'Todas' },
  { title: 'Cosmología Maya', value: 'Coleccion Cosmologia Maya' },
  { title: 'Maya Contemporánea', value: 'Coleccion Maya Contemporanea' },
];

const categories = [
  { title: 'Todas las piezas', value: 'Todas' },
  { title: 'Charm', value: 'Charm' },
  { title: 'Collar', value: 'Collar' },
  { title: 'Pulsera', value: 'Pulsera' },
  { title: 'Arete', value: 'Arete' },
  { title: 'Arracada', value: 'Arracada' },
  { title: 'Dije', value: 'Dije' },
  { title: 'Glifo', value: 'Glifo' },
  { title: 'Mandala', value: 'Mandala' }
];

const sortOptions = [
  { title: 'Nombre', value: 'name' },
  { title: 'Precio: Menor a Mayor', value: 'price-asc' },
  { title: 'Precio: Mayor a Menor', value: 'price-desc' }
];

const categoryEmojis = {
  'Charm': '✦',
  'Collar': '◌',
  'Pulsera': '⟡',
  'Arete': '✧',
  'Arracada': '✦',
  'Dije': '✪',
  'Glifo': '✺',
  'Mandala': '✿',
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams();

    if (selectedCategory.value && selectedCategory.value !== 'Todas') {
      params.append('category', selectedCategory.value);
    }

    if (selectedCollection.value && selectedCollection.value !== 'Todas') {
      params.append('collection', selectedCollection.value);
    }

    if (search.value) {
      params.append('search', search.value);
    }

    if (sortBy.value) {
      params.append('sort', sortBy.value);
    }

    params.append('page', currentPage.value);


    const response = await fetch(`/api/products?${params}`);

    const payload = await response.json();

    products.value = payload.data ?? [];
    currentPage.value = payload.current_page ?? 1;
    totalPages.value = payload.last_page ?? 1;
    totalProducts.value = payload.total ?? products.value.length;


  } catch (error) {
    console.error('Error al cargar productos:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchProducts();
});

watch(() => route.query.collection, (value) => {
  const normalizedCollection = normalizeCollection(value);

  if (selectedCollection.value !== normalizedCollection) {
    selectedCollection.value = normalizedCollection;
  }
});

watch([selectedCollection, selectedCategory, sortBy, search], () => {
  if (currentPage.value !== 1) {
    currentPage.value = 1;
    return;
  }

  fetchProducts();
});

watch(currentPage, () => {
  fetchProducts();
});

const filteredProducts = computed(() => products.value);

const { addToCart: addProductToCart } = useCart();

const snackbar = ref(false);
const snackbarText = ref('');

const addToCart = (product) => {
  addProductToCart(product, 1);
  snackbarText.value = `${product.name} agregado al carrito`;
  snackbar.value = true;
};

const viewProduct = (id) => {
  router.push({ name: 'ProductDetail', params: { id } });
};

const getProductEmoji = (category) => {
  return categoryEmojis[category] || '✺';
};

const formatCategory = (category) => {
  return category || 'Pieza artesanal';
};

const formatCollection = (collection) => {
  return collectionValueMap[collection] || collection || 'Colección artesanal';
};

const truncateDescription = (description) => {
  if (!description) {
    return 'Una pieza diseñada para destacar el detalle, la textura y la memoria de su origen.';
  }

  return description.length > 110 ? `${description.slice(0, 110)}...` : description;
};
</script>

<style scoped>
.product-list {
  margin-top: 150px !important;
  padding-bottom: 4rem;
}

.catalog-hero {
  max-width: 860px;
  margin-bottom: 2rem;
}

.pagination-shell {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
}

.catalog-kicker,
.filters-label,
.product-card__category,
.product-card__featured {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.catalog-kicker {
  padding: 0.5rem 0.95rem;
  background: rgba(184, 151, 120, 0.12);
  color: #8c745f;
  font-size: 0.78rem;
}

.catalog-title {
  margin: 1rem 0 0.85rem;
  font-family: var(--font-brand), serif;
  font-size: clamp(2.4rem, 5vw, 4rem);
  line-height: 1.05;
  color: #5d4a38;
}

.catalog-text {
  max-width: 720px;
  color: #7f6d5a;
  line-height: 1.85;
  font-size: 1.05rem;
}

.filters-shell {
  padding: 1.35rem;
  border-radius: 28px;
  background: rgba(255, 252, 248, 0.92);
  border: 1px solid rgba(184, 151, 120, 0.16);
  box-shadow: 0 18px 42px rgba(107, 91, 71, 0.08);
}

.filters-copy {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.filters-label {
  padding: 0.45rem 0.85rem;
  background: rgba(140, 116, 95, 0.08);
  color: #8c745f;
  font-size: 0.76rem;
}

.filters-summary {
  color: #7f6d5a;
  font-size: 0.98rem;
  margin: 0;
}

.filter-field :deep(.v-field) {
  border-radius: 18px;
  background: rgba(249, 245, 241, 0.88);
}

.filter-field :deep(.v-field__prepend-inner i),
.filter-field :deep(.v-label.v-field-label) {
  color: #8c745f;
}

.product-card {
  cursor: pointer;
  border-radius: 24px !important;
  border: 1px solid rgba(184, 151, 120, 0.16);
  background: rgba(255, 252, 248, 0.95) !important;
  box-shadow: 0 16px 36px rgba(107, 91, 71, 0.08) !important;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 24px 48px rgba(107, 91, 71, 0.14) !important;
  border-color: rgba(184, 151, 120, 0.28);
}

.product-card__media {
  position: relative;
  background: linear-gradient(180deg, #f7f1ea 0%, #ebdfd2 100%);
  padding: 1rem;
}

.product-card__badges {
  position: absolute;
  top: 1rem;
  left: 1rem;
  right: 1rem;
  z-index: 1;
  display: flex;
  justify-content: space-between;
  gap: 0.5rem;
}

.product-card__category,
.product-card__featured {
  padding: 0.38rem 0.75rem;
  font-size: 0.68rem;
  backdrop-filter: blur(8px);
}

.product-card__category {
  background: rgba(255, 250, 244, 0.86);
  color: #6b5b47;
}

.product-card__featured {
  background: rgba(140, 116, 95, 0.82);
  color: #fffaf4;
}

.product-card__image,
.product-card__fallback {
  border-radius: 18px;
}

.product-card__fallback {
  min-height: 280px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-card__emoji {
  font-size: 4.2rem;
  color: #8c745f;
}

.product-card__content {
  padding: 1.25rem 1.25rem 0.8rem !important;
}

.product-card__title {
  font-family: var(--font-brand), serif;
  font-size: 1.2rem;
  color: #5d4a38;
  margin-bottom: 0.65rem;
}

.product-card__collection {
  margin-bottom: 0.45rem;
  color: #8c745f;
  font-size: 0.78rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.product-card__description {
  color: #7f6d5a;
  line-height: 1.65;
  min-height: 4.9rem;
}

.product-card__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.95rem;
}

.product-card__meta-item {
  display: inline-flex;
  align-items: center;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  background: rgba(245, 239, 233, 0.92);
  color: #7f6d5a;
  font-size: 0.78rem;
}

.product-card__meta-item--soldout {
  background: rgba(169, 68, 66, 0.1);
  color: #a94442;
}

.product-card__actions {
  padding: 0 1.25rem 1.25rem !important;
  align-items: center;
}

.product-card__price {
  font-family: var(--font-brand), serif;
  font-size: 1.35rem;
  font-weight: 700;
  color: #8c745f;
}

.product-card__button {
  border-radius: 999px !important;
  font-weight: 600;
}

@media (max-width: 600px) {
  .product-list {
    margin-top: 100px !important;
  }

  .filters-shell {
    padding: 1rem;
    border-radius: 22px;
  }

  .catalog-title {
    font-size: 2.4rem;
  }
}
</style>
