<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <MinisiteLayout
      :business="business"
      :heroLayout="setting.hero_layout"
      :heroTitle="setting.hero_title"
      :heroSubtitle="setting.hero_subtitle"
      :heroBackgroundImage="setting.hero_background_image"
      :heroShowSocial="setting.hero_show_social"
      :footerText="setting.footer_text"
      :footerShowSocial="setting.footer_show_social"
      :socialNetworks="socialNetworks"
    >
      <template v-for="section in renderedSections" :key="section.id">
        <SectionServices
          v-if="section.type === 'services'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
          :businessSlug="business.slug"
        />

        <SectionGallery
          v-else-if="section.type === 'gallery'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
        />

        <SectionPromotions
          v-else-if="section.type === 'promotions'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
          :businessSlug="business.slug"
        />

        <SectionContactForm
          v-else-if="section.type === 'contact_form'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :form="section.form"
          :config="section.config"
        />

        <SectionAppointments
          v-else-if="section.type === 'appointments'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :services="section.appointments?.services || []"
          :locations="section.appointments?.locations || []"
          :availableDays="section.appointments?.availableDays || []"
          :config="section.config || {}"
          :businessSlug="business.slug"
        />

        <SectionAvailability
          v-else-if="section.type === 'availability'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :availability="section.availability || {}"
        />

        <SectionLocations
          v-else-if="section.type === 'locations'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
        />

        <SectionAbout
          v-else-if="section.type === 'about'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :content="section.content"
          :config="section.config"
        />

        <SectionFeatures
          v-else-if="section.type === 'features'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
        />

        <SectionFaqs
          v-else-if="section.type === 'faqs'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
        />

        <SectionProducts
          v-else-if="section.type === 'products'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
          :businessSlug="business.slug"
          :orderSettings="orderSettings"
        />

        <SectionFooter
          v-else-if="section.type === 'footer'"
          :business="business"
          :text="section.config?.text"
          :showSocial="section.config?.show_social"
          :socialNetworks="socialNetworks"
        />

        <SectionReviews
          v-else-if="section.type === 'reviews'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
        />

        <SectionRestaurantMenu
          v-else-if="section.type === 'restaurant_menu'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
          :businessSlug="business.slug"
        />

        <SectionProperties
          v-else-if="section.type === 'properties'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
          :businessSlug="business.slug"
        />

        <SectionPackages
          v-else-if="section.type === 'packages'"
          :title="section.title"
          :subtitle="section.subtitle"
          :description="section.description"
          :buttons="section.buttons"
          :items="section.items"
          :config="section.config"
        />
      </template>
    </MinisiteLayout>

    <AiChatWidget
      v-if="aiChatbot && aiChatbot.is_enabled"
      :businessSlug="business.slug"
      :businessName="business.name"
      :chatbotName="aiChatbot.chatbot_name || 'Asistente Virtual'"
      :chatbotAvatar="aiChatbot.chatbot_avatar || ''"
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
import MinisiteLayout from '../../components/MinisiteLayout.vue'
import SectionServices from '../../components/SectionServices.vue'
import SectionGallery from '../../components/SectionGallery.vue'
import SectionPromotions from '../../components/SectionPromotions.vue'
import SectionContactForm from '../../components/SectionContactForm.vue'
import SectionAppointments from '../../components/SectionAppointments.vue'
import SectionAvailability from '../../components/SectionAvailability.vue'
import SectionLocations from '../../components/SectionLocations.vue'
import SectionAbout from '../../components/SectionAbout.vue'
import SectionFeatures from '../../components/SectionFeatures.vue'
import SectionFaqs from '../../components/SectionFaqs.vue'
import SectionProducts from '../../components/SectionProducts.vue'
import SectionFooter from '../../components/SectionFooter.vue'
import SectionReviews from '../../components/SectionReviews.vue'
import SectionRestaurantMenu from '../../components/SectionRestaurantMenu.vue'
import SectionProperties from '../../components/SectionProperties.vue'
import SectionPackages from '../../components/SectionPackages.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'
import CartDrawer from '@/Components/Cart/CartDrawer.vue'
import CheckoutForm from '@/Components/Cart/CheckoutForm.vue'
import { useCart } from '@/composables/useCart'

const props = defineProps({
  business: Object,
  setting: Object,
  sections: {
    type: Array,
    default: () => [],
  },
  socialNetworks: {
    type: Array,
    default: () => [],
  },
  existingSections: {
    type: Array,
    default: () => [],
  },
  aiChatbot: {
    type: Object,
    default: null,
  },
  orderSettings: {
    type: Object,
    default: null,
  },
})

const cart = useCart()
const showCheckout = ref(false)

const openCheckout = () => {
  showCheckout.value = true
}

const onCheckoutSuccess = () => {
  showCheckout.value = false
  cart.clearCart()
}

const renderedSections = computed(() => {
  const sectionComponents = ['services', 'gallery', 'promotions', 'contact_form', 'appointments', 'availability', 'locations', 'about', 'features', 'faqs', 'products', 'footer', 'reviews', 'restaurant_menu', 'properties', 'packages']

  return props.sections
    .filter(section => sectionComponents.includes(section.section_type))
    .map(section => {
      const data = {
        id: section.id,
        type: section.section_type,
        title: section.title,
        subtitle: section.subtitle || section.description || null,
        description: section.description || section.subtitle || null,
        buttons: section.buttons || [],
        config: section.config || {},
      }

      switch (section.section_type) {
        case 'services':
        case 'gallery':
        case 'promotions':
        case 'locations':
        case 'features':
        case 'faqs':
        case 'products':
        case 'reviews':
        case 'restaurant_menu':
        case 'properties':
        case 'packages':
          data.items = section.items || []
          break
        case 'contact_form':
          data.form = section.form || null
          break
        case 'appointments':
          data.appointments = section.appointments || { services: [], locations: [], availableDays: [] }
          break
        case 'availability':
          data.availability = section.availability || { schedule: [], exceptions: [] }
          break
        case 'about':
          data.content = section.content || null
          break
        case 'footer':
          data.config = {
            text: section.config?.text || '',
            show_social: section.config?.show_social ?? true,
          }
          break
      }

      return data
    })
})
</script>

<style lang="less">
.minisite-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
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
