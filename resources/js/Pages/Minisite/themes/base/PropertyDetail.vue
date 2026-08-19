<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <section class="page-header">
      <div class="page-header__inner">
        <BreadcrumbNav
          :baseSlug="business.slug"
          :items="[{ label: 'Propiedades', href: `/m/${business.slug}/propiedades` }, { label: property.title }]"
        />
      </div>
    </section>

    <section class="page-content">
      <div class="page-content__inner">
        <div class="property-detail">
          <div class="property-detail__gallery">
            <div class="property-gallery">
              <div v-if="activeImage" class="property-gallery__main">
                <a
                  :href="activeImage"
                  class="glightbox"
                  data-gallery="property-detail-gallery"
                >
                  <img :src="activeImage" :alt="property.title" class="property-gallery__main-image" />
                </a>
              </div>
              <div v-else class="property-gallery__placeholder">
                <i class="bi bi-house"></i>
              </div>
              <div v-if="property.gallery && property.gallery.length > 1" class="property-gallery__thumbs">
                <a
                  v-for="(img, index) in property.gallery"
                  :key="img.id"
                  :href="img.path"
                  class="property-gallery__thumb glightbox"
                  data-gallery="property-detail-gallery"
                  :class="{ active: activeImage === img.path }"
                >
                  <img :src="img.path" :alt="img.title || property.title" />
                </a>
              </div>
            </div>
          </div>

          <div class="property-detail__info">
            <div class="property-detail__header">
              <span v-if="property.operation_label" class="property-detail__operation">
                {{ property.operation_label }}
              </span>
              <h1 class="property-detail__name">{{ property.title }}</h1>
            </div>

            <div v-if="property.full_address" class="property-detail__location">
              <i class="bi bi-geo-alt"></i>
              <span>{{ property.full_address }}</span>
            </div>

            <div class="property-detail__price">
              <span class="property-detail__price-value">
                {{ property.formatted_price }}
              </span>
              <span v-if="property.price_period && property.price_period !== 'single'" class="property-detail__price-period">
                {{ getPricePeriodLabel(property.price_period) }}
              </span>
            </div>

            <div v-if="property.property_type" class="property-detail__type">
              <span class="badge bg-light text-dark">
                <i class="bi bi-tag me-1"></i>{{ property.property_type }}
              </span>
            </div>

            <div v-if="property.description" class="property-detail__description">
              <h3>Descripción</h3>
              <p>{{ property.description }}</p>
            </div>

            <div v-if="property.amenities && property.amenities.length > 0" class="property-detail__amenities">
              <h3>Características</h3>
              <div class="property-detail__amenities-grid">
                <div
                  v-for="amenity in property.amenities"
                  :key="amenity.id"
                  class="property-detail__amenity-item"
                >
                  <i :class="amenity.icon || 'bi bi-check-circle'"></i>
                  <span>{{ amenity.name }}</span>
                </div>
              </div>
            </div>

            <div v-if="formSchema && formSchema.length > 0" class="property-detail__sections">
              <div
                v-for="section in formSchema"
                :key="section.id"
                class="property-detail__section"
              >
                <h3 v-if="section.name">{{ section.name }}</h3>
                <div class="property-detail__fields-grid">
                  <template v-for="field in section.fields" :key="field.id">
                    <div
                      v-if="field.value !== null && field.value !== undefined && field.value !== ''"
                      class="property-detail__field-item"
                    >
                      <span class="property-detail__field-label">{{ field.label }}:</span>
                      <span class="property-detail__field-value">{{ formatFieldValue(field) }}</span>
                    </div>
                  </template>
                </div>
              </div>
            </div>

            <div class="property-detail__actions">
              <a
                v-if="business.whatsapp"
                :href="`https://wa.me/${business.whatsapp}?text=Hola, me interesa la propiedad: ${property.title}`"
                target="_blank"
                class="btn btn-success btn-lg"
              >
                <i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp
              </a>
              <a
                v-if="business.phone"
                :href="`tel:${business.phone}`"
                class="btn btn-outline-primary btn-lg"
              >
                <i class="bi bi-telephone me-2"></i>Llamar
              </a>
              <Link :href="`/m/${business.slug}/propiedades`" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i>Volver a propiedades
              </Link>
            </div>
          </div>
        </div>

        <div v-if="property.latitude && property.longitude && property.show_exact_location" class="property-detail__map">
          <h3>Ubicación</h3>
          <div class="property-detail__map-container">
            <iframe
              v-if="property.latitude && property.longitude"
              :src="`https://www.google.com/maps?q=${property.latitude},${property.longitude}&output=embed`"
              width="100%"
              height="300"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
            ></iframe>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import NavigationMenu from '../../components/NavigationMenu.vue'
import Footer from '../../components/Footer.vue'
import BreadcrumbNav from '@/Components/Minisite/BreadcrumbNav.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'
import GLightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.min.css'

const props = defineProps({
  business: Object,
  setting: Object,
  property: Object,
  formSchema: Array,
  socialNetworks: Array,
  existingSections: Array,
  aiChatbot: Object,
})

const activeImage = computed(() => {
  if (props.property?.gallery && props.property.gallery.length > 0) {
    return props.property.gallery[0].path
  }
  return props.property?.main_image || null
})

const formatFieldValue = (field) => {
  const value = field.value

  if (value === null || value === undefined || value === '') {
    return '-'
  }

  if (field.field_type === 'boolean') {
    return value ? 'Sí' : 'No'
  }

  if (field.field_type === 'multiselect' || field.field_type === 'checkbox') {
    if (Array.isArray(value)) {
      return value.join(', ')
    }
    return value
  }

  if (field.field_type === 'select' || field.field_type === 'radio') {
    const option = field.options?.find(opt => opt.value === value)
    return option ? option.label : value
  }

  if (field.field_type === 'price' || field.field_type === 'decimal') {
    return Number(value).toLocaleString('es-MX')
  }

  if (field.field_type === 'date' || field.field_type === 'datetime') {
    if (typeof value === 'string' && value.includes('T')) {
      const date = new Date(value)
      if (!isNaN(date)) {
        return date.toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' })
      }
    }
    return value
  }

  return value
}

const getPricePeriodLabel = (period) => {
  const labels = {
    monthly: 'mensuales',
    weekly: 'semanales',
    daily: 'diarios',
  }
  return labels[period] || ''
}

let lightbox = null

const initLightbox = () => {
  if (lightbox) {
    lightbox.destroy()
  }
  lightbox = GLightbox({
    touchNavigation: true,
    loop: true,
    autoplayVideos: false,
    selector: '.glightbox',
  })
}

onMounted(() => {
  nextTick(() => {
    initLightbox()
  })
})

onUnmounted(() => {
  if (lightbox) {
    lightbox.destroy()
  }
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

.property-detail {
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

  &__header {
    margin-bottom: 16px;
  }

  &__operation {
    display: inline-block;
    padding: 4px 12px;
    background: #198754;
    color: #fff;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  &__name {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #212529;
    line-height: 1.2;
  }

  &__location {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6c757d;
    font-size: 0.9375rem;
    margin-bottom: 24px;

    i {
      color: #dc3545;
    }
  }

  &__price {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #dee2e6;
  }

  &__price-value {
    font-size: 2rem;
    font-weight: 700;
    color: #198754;
  }

  &__price-period {
    font-size: 0.875rem;
    color: #6c757d;
  }

  &__type {
    margin-bottom: 24px;
  }

  &__description {
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

  &__amenities {
    margin-bottom: 24px;

    h3 {
      font-size: 1.125rem;
      font-weight: 600;
      margin: 0 0 16px;
      color: #212529;
    }

    &-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
      gap: 12px;
    }
  }

  &__amenity-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    color: #495057;

    i {
      color: #198754;
      font-size: 1.125rem;
    }
  }

  &__sections {
    margin-bottom: 24px;

    h3 {
      font-size: 1.125rem;
      font-weight: 600;
      margin: 0 0 16px;
      color: #212529;
      padding-bottom: 8px;
      border-bottom: 2px solid #dee2e6;
    }
  }

  &__section {
    margin-bottom: 24px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;

    h3 {
      font-size: 1rem;
      font-weight: 600;
      margin: 0 0 12px;
      color: #212529;
      border-bottom: 1px dashed #dee2e6;
      padding-bottom: 8px;
    }

    &:last-child {
      margin-bottom: 0;
    }
  }

  &__fields-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;

    @media (max-width: 576px) {
      grid-template-columns: 1fr;
    }
  }

  &__field-item {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    padding-bottom: 8px;
    border-bottom: 1px dashed #dee2e6;

    &:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }
  }

  &__field-label {
    color: #6c757d;
  }

  &__field-value {
    font-weight: 600;
    color: #212529;
    text-align: right;
  }

  &__actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: auto;

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

  &__map {
    background: #fff;
    border-radius: 16px;
    padding: 32px;

    h3 {
      font-size: 1.25rem;
      font-weight: 600;
      margin: 0 0 16px;
      color: #212529;
    }

    &-container {
      border-radius: 12px;
      overflow: hidden;
    }
  }
}

.property-gallery {
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
    height: 350px;
    object-fit: cover;

    @media (max-width: 768px) {
      height: 250px;
    }
  }

  &__placeholder {
    width: 100%;
    height: 350px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 4rem;
    border-radius: 12px;
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
</style>
