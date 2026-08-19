<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <div class="menu-page">
      <div class="menu-header">
        <BreadcrumbNav
          :baseSlug="business.slug"
          :items="[{ label: pageTitle || 'Menú' }]"
        />
      </div>

      <div class="menu-filters" v-if="searchQuery || filteredProducts.length > 0 || Object.keys(categories).length > 1">
        <div class="menu-filters__search">
          <div class="search-input">
            <i class="bi bi-search"></i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Buscar en el menú..."
              class="form-control"
            />
            <button v-if="searchQuery" class="btn-clear" @click="searchQuery = ''">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

        <div class="menu-filters__categories" v-if="!searchQuery && Object.keys(categories).length > 1">
          <button
            v-for="(cat, key) in categories"
            :key="key"
            class="category-tab"
            :class="{ active: activeCategory === key }"
            @click="scrollToCategory(key)"
          >
            {{ cat.name }}
          </button>
        </div>
      </div>

      <div class="menu-content">
        <div v-if="searchQuery" class="menu-search-results">
          <h3 class="results-title" v-if="filteredProducts.length > 0">
            {{ filteredProducts.length }} resultado(s) para "{{ searchQuery }}"
          </h3>

          <div v-if="filteredProducts.length === 0" class="no-results">
            <i class="bi bi-emoji-frown"></i>
            <p>No encontramos productos con "{{ searchQuery }}"</p>
          </div>

          <div class="product-grid">
            <div
              v-for="product in filteredProducts"
              :key="product.id"
              class="product-card"
              @click="openProductModal(product)"
            >
              <div class="product-card__info">
                <h4 class="product-card__name">{{ product.title }}</h4>
                <p class="product-card__desc">{{ truncateText(product.description, 60) }}</p>
                <div class="product-card__price">
                  <span v-if="product.price">{{ formatCurrency(product.price) }}</span>
                  <span v-if="product.has_variants" class="product-card__variants-badge">
                    <i class="bi bi-list-ul"></i> Con variantes
                  </span>
                </div>
              </div>
              <div v-if="product.image" class="product-card__image">
                <img :src="product.image" :alt="product.title" />
              </div>
              <button class="product-card__add-btn" v-if="product.has_variants">
                <i class="bi bi-plus-lg"></i>
              </button>
            </div>
          </div>
        </div>

        <div v-else class="menu-categories">
          <div
            v-for="(cat, key) in categories"
            :key="key"
            :id="'category-' + key"
            class="category-section"
          >
            <h2 class="category-section__title">
              {{ cat.name }}
              <span class="category-section__count">{{ cat.products.length }}</span>
            </h2>
            <p v-if="cat.description" class="category-section__desc">{{ cat.description }}</p>

            <div class="product-list">
              <div
                v-for="product in cat.products"
                :key="product.id"
                class="product-item"
                @click="openProductModal(product)"
              >
                <div class="product-item__info">
                  <h4 class="product-item__name">{{ product.title }}</h4>
                  <p class="product-item__desc">{{ truncateText(product.description, 70) }}</p>
                  <div class="product-item__price">
                    <span v-if="product.price" class="price">{{ formatCurrency(product.price) }}</span>
                    <span v-if="product.has_variants" class="variants-hint">Desde {{ formatCurrency(product.minPrice) }}</span>
                  </div>
                </div>
                <div v-if="product.image" class="product-item__image">
                  <img :src="product.image" :alt="product.title" />
                </div>
                <button class="product-item__add-btn" v-if="product.has_variants">
                  <i class="bi bi-plus-lg"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="selectedProduct" class="product-modal" @click="closeProductModal">
      <div class="product-modal__content" @click.stop>
        <button class="product-modal__close" @click="closeProductModal">
          <i class="bi bi-x-lg"></i>
        </button>
        <div class="product-modal__gallery">
          <div v-if="activeProductImage" class="product-modal__main-image">
            <a :href="activeProductImage" class="glightbox" data-gallery="menu-gallery">
              <img :src="activeProductImage" :alt="selectedProduct.title" />
            </a>
          </div>
          <div v-else-if="selectedProduct.image" class="product-modal__main-image">
            <img :src="selectedProduct.image" :alt="selectedProduct.title" />
          </div>
          <div v-else class="product-modal__placeholder">
            <i class="bi bi-cup-hot"></i>
          </div>
          <div v-if="productGallery.length > 1" class="product-modal__thumbs">
            <button
              v-for="(img, index) in productGallery"
              :key="img.id"
              class="product-modal__thumb"
              :class="{ active: activeProductImage === img.path }"
              @click="activeProductImage = img.path"
            >
              <img :src="img.path" :alt="img.title || selectedProduct.title" />
            </button>
          </div>
        </div>
        <div class="product-modal__info">
          <h2 class="product-modal__name">{{ selectedProduct.title }}</h2>

          <div class="product-modal__price">
            <span v-if="!selectedProduct.has_variants && selectedProduct.price" class="product-modal__price-value">
              {{ formatCurrency(selectedProduct.price) }}
            </span>
            <span v-else-if="selectedProduct.has_variants && selectedProduct.variants?.length" class="product-modal__price-value">
              Desde {{ formatCurrency(Math.min(...selectedProduct.variants.map(v => parseFloat(v.price)))) }}
            </span>
            <span v-if="selectedProduct.categoryName" class="product-modal__category-badge">
              {{ selectedProduct.categoryName }}
            </span>
          </div>

          <div v-if="selectedProduct.description" class="product-modal__description">
            <p>{{ selectedProduct.description }}</p>
          </div>

          <div v-if="selectedProduct.has_variants && selectedProduct.variants" class="product-modal__variants">
            <h4 class="product-modal__variants-title">Opciones disponibles:</h4>
            <div
              v-for="variant in selectedProduct.variants"
              :key="variant.id"
              class="product-modal__variant"
              :class="{ active: selectedVariant?.id === variant.id }"
              @click="selectedVariant = variant"
            >
              <div class="product-modal__variant-info">
                <span class="product-modal__variant-name">{{ variant.title }}</span>
                <span v-if="variant.description" class="product-modal__variant-desc">
                  {{ variant.description }}
                </span>
              </div>
              <span class="product-modal__variant-price">{{ formatCurrency(variant.price) }}</span>
            </div>
          </div>

          <div v-if="!selectedProduct.has_variants" class="product-modal__quantity">
            <label class="form-label">Cantidad:</label>
            <div class="input-group" style="max-width: 150px;">
              <button class="btn btn-outline-secondary" @click="decreaseQuantity">-</button>
              <input type="number" class="form-control text-center" v-model.number="addQuantity" min="1" />
              <button class="btn btn-outline-secondary" @click="addQuantity++">+</button>
            </div>
          </div>

          <div class="product-modal__actions">
            <button
              v-if="orderSettings?.is_active && hasValidPrice"
              class="btn btn-primary btn-lg w-100 mb-2"
              @click="addToCart"
            >
              <i class="bi bi-cart-plus me-2"></i>Agregar al carrito
            </button>
            <a
              :href="`https://wa.me/${business.whatsapp || ''}?text=Hola, me interesa: ${selectedProduct.title}`"
              target="_blank"
              class="btn btn-success btn-lg w-100"
              v-if="business.whatsapp"
            >
              <i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp
            </a>
          </div>
        </div>
      </div>
    </div>

    <Footer
      :business="business"
      :text="setting.footer_text"
      :showSocial="setting.footer_show_social"
      :socialNetworks="socialNetworks"
    />

    <AiChatWidget
      v-if="aiChatbot && aiChatbot.is_enabled"
      :businessSlug="business.slug"
      :businessName="business.name"
      :widgetColor="aiChatbot.widget_color || '#3B82F6'"
      :widgetTheme="aiChatbot.widget_theme || 'light'"
      :allowReset="aiChatbot.allow_reset_chat"
    />

    <CartDrawer
      v-if="orderSettings?.is_active"
      :isOpen="cart.isCartOpen.value"
      @close="cart.closeCart"
      @checkout="openCheckout"
    />

    <div v-if="showCheckout && orderSettings?.is_active" class="checkout-modal">
      <div class="checkout-modal__content">
        <button class="checkout-modal__close" @click="showCheckout = false">
          <i class="bi bi-x-lg"></i>
        </button>
        <CheckoutForm
          v-if="showCheckout"
          :businessId="business.id"
          :businessLocations="businessLocations || []"
          :orderSettings="orderSettings || {}"
          :whatsappNumber="orderSettings?.whatsapp_number || ''"
          @success="onCheckoutSuccess"
        />
      </div>
    </div>

    <button
      v-if="orderSettings?.is_active && cart.itemCount.value > 0"
      class="floating-cart-btn"
      @click="cart.openCart"
    >
      <i class="bi bi-cart3"></i>
      <span class="floating-cart-btn__badge">{{ cart.itemCount.value }}</span>
    </button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import NavigationMenu from '../../components/NavigationMenu.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'
import HeroSimple from '../../components/HeroSimple.vue'
import Footer from '../../components/Footer.vue'
import BreadcrumbNav from '@/Components/Minisite/BreadcrumbNav.vue'
import CartDrawer from '@/Components/Cart/CartDrawer.vue'
import CheckoutForm from '@/Components/Cart/CheckoutForm.vue'
import { useCart } from '@/composables/useCart'
import GLightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.min.css'

const props = defineProps({
  business: Object,
  setting: Object,
  pageTitle: String,
  menuData: Array,
  socialNetworks: Array,
  existingSections: Array,
  aiChatbot: Object,
  businessLocations: Array,
  orderSettings: Object,
})

const cart = useCart()
const searchQuery = ref('')
const activeCategory = ref(null)
const selectedProduct = ref(null)
const activeProductImage = ref(null)
const selectedVariant = ref(null)
const showCheckout = ref(false)
const showCartDrawer = ref(false)
const addQuantity = ref(1)

let lightbox = null

const productGallery = computed(() => {
  if (!selectedProduct.value) return []
  if (selectedProduct.value.gallery && selectedProduct.value.gallery.length > 0) {
    return selectedProduct.value.gallery
  }
  if (selectedProduct.value.image) {
    return [{ id: 'main', path: selectedProduct.value.image, title: selectedProduct.value.title }]
  }
  return []
})

const hasValidPrice = computed(() => {
  if (!selectedProduct.value) return false
  const price = selectedVariant.value
    ? parseFloat(selectedVariant.value.price)
    : parseFloat(selectedProduct.value.base_price || selectedProduct.value.price)
  return !isNaN(price) && price > 0
})

onMounted(() => {
  nextTick(() => {
    if (lightbox) {
      lightbox.destroy()
    }
    lightbox = GLightbox({
      touchNavigation: true,
      loop: true,
      autoplayVideos: false,
      selector: '.glightbox',
    })
  })
})

const flatProducts = computed(() => {
  const products = []
  props.menuData?.forEach(category => {
    category.products?.forEach(product => {
      products.push({ ...product, categoryName: category.title })
    })
    category.children?.forEach(subcat => {
      subcat.products?.forEach(product => {
        products.push({ ...product, categoryName: subcat.title })
      })
    })
  })
  return products
})

const filteredProducts = computed(() => {
  if (!searchQuery.value) return []
  const query = searchQuery.value.toLowerCase()
  return flatProducts.value.filter(p =>
    p.title.toLowerCase().includes(query) ||
    p.description?.toLowerCase().includes(query)
  )
})

const categories = computed(() => {
  const cats = {}
  let idx = 0
  props.menuData?.forEach((category, catIdx) => {
    const key = 'cat-' + catIdx
    cats[key] = {
      name: category.title,
      description: category.description,
      products: (category.products || []).map(p => ({
        ...p,
        minPrice: p.variants?.length ? Math.min(...p.variants.map(v => parseFloat(v.price))) : p.price
      }))
    }

    if (category.children?.length) {
      category.children.forEach((child, childIdx) => {
        const childKey = 'cat-' + catIdx + '-child-' + childIdx
        cats[childKey] = {
          name: category.title + ' > ' + child.title,
          description: child.description,
          products: (child.products || []).map(p => ({
            ...p,
            minPrice: p.variants?.length ? Math.min(...p.variants.map(v => parseFloat(v.price))) : p.price
          }))
        }
      })
    }
    idx++
  })
  return cats
})

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const formatCurrency = (value) => {
  if (value === null || value === undefined) return ''
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
  }).format(value)
}

const scrollToCategory = (key) => {
  activeCategory.value = key
  const element = document.getElementById('category-' + key)
  if (element) {
    const headerOffset = 100
    const elementPosition = element.getBoundingClientRect().top
    const offsetPosition = elementPosition + window.pageYOffset - headerOffset
    window.scrollTo({ top: offsetPosition, behavior: 'smooth' })
  }
}

const openProductModal = (product) => {
  selectedProduct.value = product
  activeProductImage.value = product.gallery?.[0]?.path || product.image || null
  selectedVariant.value = product.variants?.[0] || null
  addQuantity.value = 1
  nextTick(() => {
    if (lightbox) {
      lightbox.destroy()
    }
    lightbox = GLightbox({
      touchNavigation: true,
      loop: true,
      autoplayVideos: false,
      selector: '.glightbox',
    })
  })
}

const closeProductModal = () => {
  selectedProduct.value = null
  activeProductImage.value = null
  selectedVariant.value = null
  addQuantity.value = 1
}

const decreaseQuantity = () => {
  if (addQuantity.value > 1) {
    addQuantity.value--
  }
}

const addToCart = () => {
  if (!selectedProduct.value) return

  const product = selectedProduct.value
  const variant = selectedVariant.value
  const quantity = addQuantity.value

  cart.addItem({
    id: product.id,
    business_id: props.business.id,
    title: variant ? `${product.title} - ${variant.title}` : product.title,
    image: product.image,
    base_price: variant ? parseFloat(variant.price) : parseFloat(product.base_price || product.price || 0),
  }, {
    productType: 'menu_product',
    variantId: variant?.id || null,
    quantity,
  })

  closeProductModal()
  cart.openCart()
}

const openCheckout = () => {
  showCheckout.value = true
}

const onCheckoutSuccess = () => {
  showCheckout.value = false
  cart.clearCart()
}

onMounted(() => {
  if (props.menuData?.length) {
    activeCategory.value = 'cat-0'
  }
})
</script>

<style lang="less">
.menu-page {
  background: #f8f9fa;
  min-height: 100vh;
}

.menu-header {
  position: sticky;
  top: 64px;
  z-index: 100;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  padding: 8px 16px;
  border-bottom: 1px solid #e9ecef;

  :deep(.breadcrumb) {
    margin: 0;
    padding: 0;
  }
}

.menu-filters {
  background: #fff;
  border-bottom: 1px solid #eee;

  &__search {
    padding: 12px 16px;
  }

  &__categories {
    display: flex;
    overflow-x: auto;
    padding: 12px 16px;
    gap: 8px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;

    &::-webkit-scrollbar {
      display: none;
    }
  }
}

.search-input {
  position: relative;
  display: flex;
  align-items: center;

  i {
    position: absolute;
    left: 12px;
    color: #6c757d;
  }

  input {
    padding-left: 40px;
    padding-right: 40px;
    border-radius: 24px;
    border: 1px solid #dee2e6;
  }

  .btn-clear {
    position: absolute;
    right: 8px;
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 4px 8px;
  }
}

.category-tab {
  flex-shrink: 0;
  padding: 8px 16px;
  border-radius: 20px;
  border: 1px solid #dee2e6;
  background: #fff;
  color: #495057;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;

  &:hover {
    border-color: #0d6efd;
    color: #0d6efd;
  }

  &.active {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
  }
}

.menu-content {
  max-width: 800px;
  margin: 0 auto;
  padding: 16px;
}

.no-results {
  text-align: center;
  padding: 48px 16px;
  color: #6c757d;

  i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
  }
}

.results-title {
  font-size: 1rem;
  color: #6c757d;
  margin-bottom: 16px;
}

.product-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.category-section {
  margin-bottom: 32px;
  scroll-margin-top: 120px;

  &__title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  &__count {
    font-size: 0.75rem;
    font-weight: 500;
    color: #6c757d;
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 12px;
  }

  &__desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0 0 16px;
  }
}

.product-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.product-card,
.product-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: all 0.2s;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }
}

.product-card__info,
.product-item__info {
  flex: 1;
  min-width: 0;
}

.product-card__name,
.product-item__name {
  font-size: 1rem;
  font-weight: 600;
  color: #212529;
  margin: 0 0 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-card__desc,
.product-item__desc {
  font-size: 0.8125rem;
  color: #6c757d;
  margin: 0 0 6px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-card__price,
.product-item__price {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;

  .price {
    font-weight: 600;
    color: #198754;
    font-size: 0.9375rem;
  }

  .variants-hint {
    font-size: 0.75rem;
    color: #6c757d;
  }
}

.product-card__variants-badge {
  font-size: 0.75rem;
  color: #6c757d;
  background: #e9ecef;
  padding: 2px 6px;
  border-radius: 4px;
}

.product-card__image,
.product-item__image {
  flex-shrink: 0;
  width: 72px;
  height: 72px;
  border-radius: 8px;
  overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.product-card__add-btn,
.product-item__add-btn {
  flex-shrink: 0;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #0d6efd;
  color: #fff;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;

  &:hover {
    background: #0b5ed7;
  }
}

.product-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;

  &__content {
    background: #fff;
    border-radius: 16px;
    max-width: 900px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    display: grid;
    grid-template-columns: 1fr 1fr;

    @media (max-width: 768px) {
      grid-template-columns: 1fr;
      max-width: 500px;
    }
  }

  &__close {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    color: #495057;

    &:hover {
      background: #fff;
      color: #dc3545;
    }
  }

  &__gallery {
    position: relative;
    background: #f8f9fa;
  }

  &__main-image {
    width: 100%;
    height: 350px;
    overflow: hidden;

    &.glightbox {
      display: block;
      cursor: zoom-in;
    }

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    @media (max-width: 768px) {
      height: 250px;
    }
  }

  &__placeholder {
    width: 100%;
    height: 350px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 4rem;

    @media (max-width: 768px) {
      height: 250px;
    }
  }

  &__thumbs {
    display: flex;
    gap: 8px;
    padding: 12px;
    overflow-x: auto;
  }

  &__thumb {
    flex: 0 0 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid transparent;
    padding: 0;
    background: none;
    cursor: pointer;
    transition: border-color 0.2s;

    &.active,
    &:hover {
      border-color: #0d6efd;
    }

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__info {
    padding: 24px;
    display: flex;
    flex-direction: column;
  }

  &__name {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 16px;
    color: #212529;
    line-height: 1.2;
  }

  &__price {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #dee2e6;
  }

  &__price-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #198754;
  }

  &__category-badge {
    font-size: 0.75rem;
    color: #6c757d;
    background: #e9ecef;
    padding: 4px 10px;
    border-radius: 12px;
  }

  &__description {
    flex: 1;
    margin-bottom: 20px;

    p {
      font-size: 0.9375rem;
      line-height: 1.7;
      color: #495057;
      margin: 0;
    }
  }

  &__variants {
    border-top: 1px solid #e9ecef;
    padding-top: 16px;
    margin-bottom: 20px;
  }

  &__variants-title {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 12px;
  }

  &__variant {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 8px;

    &:last-child {
      margin-bottom: 0;
    }
  }

  &__variant-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  &__variant-name {
    font-weight: 500;
    color: #212529;
  }

  &__variant-desc {
    font-size: 0.8125rem;
    color: #6c757d;
  }

  &__variant-price {
    font-weight: 600;
    color: #198754;
  }

  &__actions {
    display: flex;
    flex-direction: column;
    gap: 12px;

    .btn {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 14px 24px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 8px;
    }
  }

  &__quantity {
    margin-bottom: 16px;
    padding-top: 16px;
    border-top: 1px solid #e9ecef;

    .form-label {
      font-weight: 600;
      margin-bottom: 8px;
    }
  }
}

.product-modal__variant.active {
  background: #e7f5ff;
  border: 2px solid #0d6efd;
}

.floating-cart-btn {
  position: fixed;
  bottom: 24px;
  right: 24px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #0d6efd;
  color: white;
  border: none;
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  cursor: pointer;
  z-index: 1040;
  transition: transform 0.2s, box-shadow 0.2s;

  &:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(13, 110, 253, 0.5);
  }

  &__badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #dc3545;
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    min-width: 22px;
    height: 22px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
  }
}

.checkout-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  z-index: 1060;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  overflow-y: auto;

  &__content {
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
  }

  &__close {
    position: absolute;
    top: 12px;
    right: 12px;
    background: white;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  }
}
</style>
