<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <section class="page-header">
      <div class="page-header__inner">
        <BreadcrumbNav
          :baseSlug="business.slug"
          :items="[{ label: 'Productos', href: `/m/${business.slug}/productos` }, { label: product.name }]"
        />
      </div>
    </section>

    <section class="page-content">
      <div class="page-content__inner">
        <div class="product-detail">
          <div class="product-detail__gallery">
            <div class="product-gallery">
              <div v-if="activeImage" class="product-gallery__main">
                <a
                  :href="activeImage"
                  class="glightbox"
                  data-gallery="product-detail-gallery"
                >
                  <img :src="activeImage" :alt="product.name" class="product-gallery__main-image" />
                </a>
                <span v-if="product.compare_at_price && product.price" class="product-gallery__discount-badge">
                  -{{ discountPercent }}% OFF
                </span>
              </div>
              <div v-else class="product-gallery__placeholder">
                <i class="bi bi-image"></i>
              </div>
              <div v-if="product.gallery && product.gallery.length > 1" class="product-gallery__thumbs">
                <a
                  v-for="(img, index) in product.gallery"
                  :key="img.id"
                  :href="img.path"
                  class="product-gallery__thumb glightbox"
                  :class="{ active: activeImage === img.path }"
                  data-gallery="product-detail-gallery"
                  @click.prevent="activeImage = img.path"
                >
                  <img :src="img.path" :alt="img.title || product.name" />
                </a>
              </div>
            </div>
          </div>

          <div class="product-detail__info">
            <h1 class="product-detail__name">{{ product.name }}</h1>

            <div class="product-detail__prices">
              <span v-if="product.price" class="product-detail__price">
                {{ formatCurrency(product.price) }}
              </span>
              <span v-if="product.compare_at_price" class="product-detail__price-compare">
                {{ formatCurrency(product.compare_at_price) }}
              </span>
            </div>

            <div class="product-detail__meta">
              <div v-if="product.sku" class="product-detail__meta-item">
                <i class="bi bi-upc"></i>
                <span><strong>SKU:</strong> {{ product.sku }}</span>
              </div>
              <div v-if="product.barcode" class="product-detail__meta-item">
                <i class="bi bi-barcode"></i>
                <span><strong>EAN:</strong> {{ product.barcode }}</span>
              </div>
              <div v-if="product.quantity !== null" class="product-detail__meta-item">
                <i class="bi bi-box-seam"></i>
                <span>
                  <strong>Disponibilidad:</strong>
                  <span v-if="product.quantity > 0" class="text-success">En stock ({{ product.quantity }} unidades)</span>
                  <span v-else class="text-danger">Agotado</span>
                </span>
              </div>
            </div>

            <div v-if="product.description" class="product-detail__description">
              <h3>Descripcion</h3>
              <p>{{ product.description }}</p>
            </div>

            <div class="product-detail__actions">
              <button
                v-if="orderSettings?.is_active && hasValidPrice"
                class="btn btn-primary btn-lg w-100 mb-2"
                @click="addToCart"
              >
                <i class="bi bi-cart-plus me-2"></i>Agregar al carrito
              </button>
              <a
                v-if="product.whatsapp_contact"
                :href="`https://wa.me/${product.whatsapp_contact}?text=Hola, me interesa el producto: ${product.name}`"
                target="_blank"
                class="btn btn-success btn-lg"
              >
                <i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp
              </a>
              <Link :href="`/m/${business.slug}/productos`" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i>Volver a productos
              </Link>
            </div>
          </div>
        </div>

        <div v-if="relatedProducts && relatedProducts.length > 0" class="related-products">
          <h2 class="related-products__title">Productos relacionados</h2>
          <div class="related-products__grid">
            <div
              v-for="item in relatedProducts"
              :key="item.id"
              class="related-product-card"
              @click="goToProduct(item.slug)"
            >
              <div class="related-product-card__image">
                <img v-if="item.image" :src="item.image" :alt="item.name" />
                <div v-else class="related-product-card__placeholder">
                  <i class="bi bi-image"></i>
                </div>
              </div>
              <div class="related-product-card__content">
                <h4 class="related-product-card__name">{{ item.name }}</h4>
                <span v-if="item.price" class="related-product-card__price">
                  {{ formatCurrency(item.price) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

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
import Footer from '../../components/Footer.vue'
import BreadcrumbNav from '@/Components/Minisite/BreadcrumbNav.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'
import CartDrawer from '@/Components/Cart/CartDrawer.vue'
import CheckoutForm from '@/Components/Cart/CheckoutForm.vue'
import { useCart } from '@/composables/useCart'
import { usePriceFormatter } from '@/Composables/usePriceFormatter'
import GLightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.min.css'

const props = defineProps({
  business: Object,
  setting: Object,
  product: Object,
  relatedProducts: {
    type: Array,
    default: () => [],
  },
  socialNetworks: Array,
  existingSections: Array,
  aiChatbot: Object,
  orderSettings: Object,
})

const cart = useCart()
const showCheckout = ref(false)

const { formatPrice } = usePriceFormatter({
  locale: 'es-MX',
  currency: '$',
  decimals: 2,
})

const formatCurrency = (value) => {
  if (value === null || value === undefined) return ''
  return formatPrice(value) || ''
}

const activeImage = computed(() => {
  if (props.product?.gallery && props.product.gallery.length > 0) {
    return props.product.gallery[0].path
  }
  return props.product?.image || null
})

const discountPercent = computed(() => {
  if (!props.product?.compare_at_price || !props.product?.price) return 0
  return Math.round((1 - props.product.price / props.product.compare_at_price) * 100)
})

const hasValidPrice = computed(() => {
  if (!props.product) return false
  const price = parseFloat(props.product.price)
  return !isNaN(price) && price > 0
})

const goToProduct = (slug) => {
  window.location.href = `/m/${props.business.slug}/productos/${slug}`
}

const addToCart = () => {
  if (!props.product) return
  cart.addItem({
    id: props.product.id,
    business_id: props.product.business_id || props.business.id,
    title: props.product.name,
    image: props.product.image,
    base_price: props.product.price,
  }, {
    productType: 'product',
    quantity: 1,
  })
  cart.openCart()
}

const openCheckout = () => {
  showCheckout.value = true
}

const onCheckoutSuccess = () => {
  showCheckout.value = false
  cart.clearCart()
}

let lightbox = null

onMounted(() => {
  nextTick(() => {
    lightbox = GLightbox({
      touchNavigation: true,
      loop: true,
      autoplayVideos: false,
      selector: '.glightbox',
    })
  })
})
</script>

<style lang="less">
.page-header {
  padding: 16px 0;
  margin-top: 64px;
  background: transparent;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
    padding: 0 16px;
  }
}

.page-content {
  padding: 24px 16px 48px;
  background: #f8f9fa;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }
}

.product-detail {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  background: #fff;
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 48px;

  @media (max-width: 768px) {
    grid-template-columns: 1fr;
    gap: 24px;
    padding: 20px;
  }

  &__gallery {
    position: relative;
  }

  &__info {
    display: flex;
    flex-direction: column;
  }

  &__name {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 20px;
    color: #212529;
    line-height: 1.2;
  }

  &__prices {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #dee2e6;
  }

  &__price {
    font-size: 2.5rem;
    font-weight: 700;
    color: #198754;
  }

  &__price-compare {
    font-size: 1.25rem;
    color: #6c757d;
    text-decoration: line-through;
  }

  &__meta {
    margin-bottom: 24px;
  }

  &__meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-size: 0.9375rem;
    color: #495057;

    i {
      color: #6c757d;
      font-size: 1.125rem;
      width: 24px;
    }

    strong {
      color: #212529;
      margin-right: 4px;
    }
  }

  &__description {
    flex: 1;
    margin-bottom: 24px;

    h3 {
      font-size: 1.125rem;
      font-weight: 600;
      margin: 0 0 12px;
      color: #212529;
    }

    p {
      font-size: 0.9375rem;
      line-height: 1.7;
      color: #495057;
      margin: 0;
    }
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
}

.product-gallery {
  &__main {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    background: #f8f9fa;
    margin-bottom: 12px;

    &.glightbox {
      display: block;
      cursor: zoom-in;
    }
  }

  &__main-image {
    width: 100%;
    height: 400px;
    object-fit: cover;

    @media (max-width: 768px) {
      height: 280px;
    }
  }

  &__placeholder {
    width: 100%;
    height: 400px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 4rem;
    border-radius: 12px;
  }

  &__discount-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    background: #dc3545;
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 6px;
  }

  &__thumbs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 8px;
  }

  &__thumb {
    flex: 0 0 72px;
    height: 72px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid transparent;
    padding: 0;
    background: none;
    cursor: pointer;
    transition: border-color 0.2s;
    display: block;

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
}

.related-products {
  &__title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 24px;
    text-align: center;
    color: #212529;
  }

  &__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
  }
}

.related-product-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  }

  &__image {
    width: 100%;
    height: 160px;
    background: #f8f9fa;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__placeholder {
    width: 100%;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 2.5rem;
  }

  &__content {
    padding: 16px;
  }

  &__name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 8px;
    color: #212529;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__price {
    font-size: 1.125rem;
    font-weight: 700;
    color: #198754;
  }
}

.floating-cart-btn {
  position: fixed;
  bottom: 24px;
  right: 24px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #198754;
  color: #fff;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  transition: transform 0.2s, box-shadow 0.2s;

  &:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
  }

  i {
    font-size: 1.5rem;
  }

  &__badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #dc3545;
    color: #fff;
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
  background: rgba(0, 0, 0, 0.7);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;

  &__content {
    background: #fff;
    border-radius: 16px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
  }

  &__close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    color: #495057;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);

    &:hover {
      background: #fff;
      color: #dc3545;
    }
  }
}
</style>
