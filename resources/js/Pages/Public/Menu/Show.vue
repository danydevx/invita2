<template>
  <div class="menu-page bg-light min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
      <div class="container">
        <a class="navbar-brand" href="#">{{ business.name }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" :href="`/${business.slug}`">Inicio</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="#">Menú</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container py-4">
      <div v-if="search" class="mb-4">
        <h5>Resultados para "{{ search }}"</h5>
        <p class="text-muted">{{ searchResults?.data?.length || 0 }} productos encontrados</p>
      </div>

      <div v-if="!search" class="mb-4">
        <form @submit.prevent="searchMenu">
          <div class="input-group">
            <input v-model="searchQuery" type="text" class="form-control" placeholder="Buscar en el menú...">
            <button class="btn btn-outline-primary" type="submit">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>
      </div>

      <div v-if="featuredProducts && featuredProducts.length > 0 && !search" class="mb-5">
        <h4 class="mb-3"><i class="bi bi-star-fill text-warning"></i> Destacados</h4>
        <div class="row">
          <div v-for="product in featuredProducts" :key="product.id" class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card h-100 shadow-sm">
              <img v-if="product.image" :src="product.image" class="card-img-top" :alt="product.title" style="height: 120px; object-fit: cover;">
              <div class="card-body p-2">
                <h6 class="card-title mb-1">{{ product.title }}</h6>
                <p v-if="product.category" class="small text-muted mb-1">{{ product.category.title }}</p>
                <p v-if="product.show_price && product.display_price" class="fw-bold mb-0">${{ product.display_price }}</p>
                <p v-else-if="product.show_price && product.variants?.length" class="fw-bold mb-0 small">Desde ${{ product.variants[0].price }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="search && searchResults?.data?.length > 0">
        <div class="row">
          <div v-for="product in searchResults.data" :key="product.id" class="col-12 mb-3">
            <div class="card">
              <div class="row g-0">
                <div v-if="product.image" class="col-4">
                  <img :src="product.image" class="img-fluid rounded-start h-100" :alt="product.title" style="object-fit: cover;">
                </div>
                <div class="col-8">
                  <div class="card-body">
                    <h5 class="card-title">{{ product.title }}</h5>
                    <p class="card-text"><small class="text-muted">{{ product.category?.title }}</small></p>
                    <p v-if="product.description" class="card-text small">{{ product.description }}</p>
                    <div v-if="product.variants?.length" class="small">
                      <div v-for="variant in product.variants" :key="variant.id" class="d-flex justify-content-between">
                        <span>{{ variant.title }}</span>
                        <span class="fw-bold">${{ variant.price }}</span>
                      </div>
                    </div>
                    <p v-else-if="product.show_price && product.display_price" class="fw-bold mb-0">${{ product.display_price }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="search" class="alert alert-info">
        No se encontraron productos para "{{ search }}"
      </div>

      <div v-if="!search">
        <ul class="nav nav-pills mb-4" role="tablist">
          <li class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'all' }" @click="activeTab = 'all'" type="button">
              Todos <span class="badge bg-secondary">{{ allProducts?.length || 0 }}</span>
            </button>
          </li>
          <li v-for="category in categories" :key="category.id" class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'cat-' + category.id }" @click="activeTab = 'cat-' + category.id" type="button">
              {{ category.title }} <span class="badge bg-secondary">{{ getCategoryProductCount(category) }}</span>
            </button>
          </li>
          <li v-if="uncategorizedProducts?.length > 0" class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'uncategorized' }" @click="activeTab = 'uncategorized'" type="button">
              Sin categoría <span class="badge bg-secondary">{{ uncategorizedProducts?.length || 0 }}</span>
            </button>
          </li>
        </ul>

        <div v-if="activeTab === 'all'" class="row">
          <div v-for="product in allProducts" :key="product.id" class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card h-100 shadow-sm">
              <img v-if="product.image" :src="product.image" class="card-img-top" :alt="product.title" style="height: 120px; object-fit: cover;">
              <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-start mb-1">
                  <h6 class="mb-0" style="font-size: 0.9rem;">{{ product.title }}</h6>
                  <span v-if="product.featured" class="badge bg-warning small">Destacado</span>
                </div>
                <p v-if="product.category" class="small text-muted mb-1">{{ product.category.title }}</p>
                <p v-if="product.description" class="card-text small text-muted mb-1">{{ product.description.substring(0, 50) }}...</p>
                <div v-if="product.variants?.length" class="small">
                  <span class="fw-bold">Desde ${{ product.variants[0].price }}</span>
                </div>
                <p v-else-if="product.show_price && product.display_price" class="fw-bold mb-0 small">${{ product.display_price }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="activeTab === 'uncategorized'" class="row">
          <div v-for="product in uncategorizedProducts" :key="product.id" class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card h-100 shadow-sm">
              <img v-if="product.image" :src="product.image" class="card-img-top" :alt="product.title" style="height: 120px; object-fit: cover;">
              <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-start mb-1">
                  <h6 class="mb-0" style="font-size: 0.9rem;">{{ product.title }}</h6>
                  <span v-if="product.featured" class="badge bg-warning small">Destacado</span>
                </div>
                <p v-if="product.description" class="card-text small text-muted mb-1">{{ product.description.substring(0, 50) }}...</p>
                <div v-if="product.variants?.length" class="small">
                  <span class="fw-bold">Desde ${{ product.variants[0].price }}</span>
                </div>
                <p v-else-if="product.show_price && product.display_price" class="fw-bold mb-0 small">${{ product.display_price }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="activeTab.startsWith('cat-')">
          <div v-for="category in categories" :key="category.id">
            <div v-if="'cat-' + category.id === activeTab">
              <div class="d-flex align-items-center mb-3">
                <img v-if="category.image" :src="category.image" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                <h4 class="mb-0">{{ category.title }}</h4>
              </div>

              <div v-if="category.products?.length > 0" class="mb-4">
                <h6 class="text-muted">Productos de esta categoría</h6>
                <div class="row">
                  <div v-for="product in category.products" :key="product.id" class="col-6 col-md-4 col-lg-3 mb-3">
                    <div class="card h-100 shadow-sm">
                      <img v-if="product.image" :src="product.image" class="card-img-top" :alt="product.title" style="height: 120px; object-fit: cover;">
                      <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                          <h6 class="mb-0" style="font-size: 0.9rem;">{{ product.title }}</h6>
                          <span v-if="product.featured" class="badge bg-warning small">Destacado</span>
                        </div>
                        <p v-if="product.description" class="card-text small text-muted mb-1">{{ product.description.substring(0, 50) }}...</p>
                        <div v-if="product.variants?.length" class="small">
                          <span class="fw-bold">Desde ${{ product.variants[0].price }}</span>
                        </div>
                        <p v-else-if="product.show_price && product.display_price" class="fw-bold mb-0 small">${{ product.display_price }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="category.children && category.children.length > 0">
                <div v-for="child in category.children" :key="child.id" class="mb-4">
                  <div class="d-flex align-items-center">
                    <img v-if="child.image" :src="child.image" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                    <h6 class="text-muted mb-0">{{ child.title }}</h6>
                  </div>
                  <div class="row mt-2">
                    <div v-for="product in child.products" :key="product.id" class="col-6 col-md-4 col-lg-3 mb-2">
                      <div class="card h-100 shadow-sm">
                        <img v-if="product.image" :src="product.image" class="card-img-top" :alt="product.title" style="height: 120px; object-fit: cover;">
                        <div class="card-body p-2">
                          <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0" style="font-size: 0.85rem;">{{ product.title }}</h6>
                            <span v-if="product.featured" class="badge bg-warning small">Destacado</span>
                          </div>
                          <p v-if="product.description" class="card-text small text-muted mb-1">{{ product.description.substring(0, 40) }}...</p>
                          <div v-if="product.variants?.length" class="small">
                            <span class="fw-bold">Desde ${{ product.variants[0].price }}</span>
                          </div>
                          <p v-else-if="product.show_price && product.display_price" class="fw-bold mb-0 small">${{ product.display_price }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <footer class="bg-dark text-light py-4 mt-5">
      <div class="container text-center">
        <p class="mb-0">{{ business.name }}</p>
        <small class="text-muted">{{ business.address }}</small>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  business: Object,
  categories: Array,
  uncategorizedProducts: Array,
  allProducts: Array,
  featuredProducts: Array,
  searchResults: Object,
  search: String,
})

const searchQuery = ref('')
const activeTab = ref('all')

const searchMenu = () => {
  if (searchQuery.value.trim()) {
    window.location.href = `/${props.business.slug}/menu?search=${encodeURIComponent(searchQuery.value)}`
  }
}

const getCategoryProductCount = (category) => {
  let count = category.products?.length || 0
  if (category.children) {
    category.children.forEach(child => {
      count += child.products?.length || 0
    })
  }
  return count
}
</script>

<style scoped>
.menu-page {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.card {
  border-radius: 8px;
  overflow: hidden;
}

.card-img-top {
  transition: transform 0.2s;
}

.card:hover .card-img-top {
  transform: scale(1.05);
}

.nav-pills .nav-link {
  color: #495057;
  border-radius: 20px;
  padding: 8px 16px;
  margin-right: 8px;
}

.nav-pills .nav-link.active {
  background-color: #0d6efd;
  color: white;
}

.nav-pills .nav-link:hover:not(.active) {
  background-color: #e9ecef;
}

.nav-pills .badge {
  font-size: 0.75rem;
}

@media (max-width: 768px) {
  .card-img-top {
    height: 100px;
  }
}
</style>
