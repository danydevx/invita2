<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <HeroSimple
      :title="pageTitle"
      :backgroundImage="business.cover_image"
      :businessSlug="business.slug"
    />

    <section class="page-header">
      <div class="page-header__inner">
        <BreadcrumbNav
          :baseSlug="business.slug"
          :items="[{ label: pageTitle }]"
        />
      </div>
    </section>

    <section class="page-content">
      <div class="page-content__inner">
        <div v-if="categories && categories.length > 0" class="category-filter">
          <button
            class="category-badge"
            :class="{ active: selectedCategory === null }"
            @click="selectedCategory = null"
          >
            Todos
          </button>
          <button
            v-for="cat in categories"
            :key="cat.id"
            class="category-badge"
            :class="{ active: selectedCategory === cat.id }"
            @click="toggleCategory(cat.id)"
          >
            {{ cat.name }}
          </button>
        </div>

        <SectionProducts
          v-if="filteredItems && filteredItems.length"
          :title="pageTitle"
          :items="filteredItems"
          :config="{ view_mode: 'grid', show_description: true, show_compare_price: true, show_stock: true }"
          :businessSlug="business.slug"
          :orderSettings="orderSettings"
        />
        <div v-else class="text-muted text-center py-5">
          No hay productos disponibles.
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
import { ref, computed } from 'vue'
import NavigationMenu from '../../components/NavigationMenu.vue'
import HeroSimple from '../../components/HeroSimple.vue'
import SectionProducts from '../../components/SectionProducts.vue'
import Footer from '../../components/Footer.vue'
import BreadcrumbNav from '@/Components/Minisite/BreadcrumbNav.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'
import CartDrawer from '@/Components/Cart/CartDrawer.vue'
import CheckoutForm from '@/Components/Cart/CheckoutForm.vue'
import { useCart } from '@/composables/useCart'

const props = defineProps({
  business: Object,
  setting: Object,
  pageTitle: String,
  sectionData: Object,
  socialNetworks: Array,
  existingSections: Array,
  aiChatbot: Object,
  orderSettings: Object,
})

const cart = useCart()
const showCheckout = ref(false)

const categories = computed(() => props.sectionData?.categories || [])
const selectedCategory = ref(null)

const allItems = computed(() => props.sectionData?.items || [])

const filteredItems = computed(() => {
  if (!selectedCategory.value) {
    return allItems.value
  }
  return allItems.value.filter(item => item.category_id === selectedCategory.value)
})

const toggleCategory = (categoryId) => {
  if (selectedCategory.value === categoryId) {
    selectedCategory.value = null
  } else {
    selectedCategory.value = categoryId
  }
}

const openCheckout = () => {
  showCheckout.value = true
}

const onCheckoutSuccess = () => {
  showCheckout.value = false
  cart.clearCart()
}
</script>

<style lang="less">
.category-filter {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 24px;
  justify-content: center;
}

.category-badge {
  padding: 8px 16px;
  border-radius: 20px;
  border: 2px solid #dee2e6;
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

.page-content {
  padding: 32px 16px;
  background: #f8f9fa;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }
}

.page-header {
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
    padding: 0 16px;
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
