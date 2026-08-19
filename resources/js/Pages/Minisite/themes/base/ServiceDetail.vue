<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <section class="page-header">
      <div class="page-header__inner">
        <BreadcrumbNav
          :baseSlug="business.slug"
          :items="[{ label: 'Servicios', href: `/m/${business.slug}/servicios` }, { label: service.name }]"
        />
      </div>
    </section>

    <section class="page-content">
      <div class="page-content__inner">
        <div class="service-detail">
          <div class="service-detail__gallery">
            <div class="service-gallery">
              <div v-if="activeImage" class="service-gallery__main">
                <a
                  :href="activeImage"
                  class="glightbox"
                  data-gallery="service-detail-gallery"
                >
                  <img :src="activeImage" :alt="service.name" class="service-gallery__main-image" />
                </a>
              </div>
              <div v-else class="service-gallery__placeholder">
                <i class="bi bi-briefcase"></i>
              </div>
              <div v-if="service.gallery && service.gallery.length > 1" class="service-gallery__thumbs">
                <button
                  v-for="(img, index) in service.gallery"
                  :key="img.id"
                  class="service-gallery__thumb"
                  :class="{ active: activeImage === img.path }"
                  @click="activeImage = img.path"
                >
                  <img :src="img.path" :alt="img.title || service.name" />
                </button>
              </div>
            </div>
          </div>

          <div class="service-detail__info">
            <h1 class="service-detail__name">{{ service.name }}</h1>

            <div class="service-detail__price">
              <span v-if="service.price" class="service-detail__price-value">
                {{ formatCurrency(service.price) }}
              </span>
              <span v-if="service.price" class="service-detail__price-label">por servicio</span>
            </div>

            <div class="service-detail__meta">
              <div v-if="service.duration_minutes" class="service-detail__meta-item">
                <i class="bi bi-clock"></i>
                <span><strong>Duracion:</strong> {{ service.duration_minutes }} minutos</span>
              </div>
              <div v-if="service.deposit_required" class="service-detail__meta-item">
                <i class="bi bi-currency-dollar"></i>
                <span>
                  <strong>Anticipo requerido:</strong>
                  <span v-if="service.deposit_amount">{{ formatCurrency(service.deposit_amount) }}</span>
                  <span v-else>Si</span>
                </span>
              </div>
              <div v-if="service.allows_online_booking" class="service-detail__meta-item service-detail__meta-item--highlight">
                <i class="bi bi-check-circle"></i>
                <span>Reservas online disponibles</span>
              </div>
            </div>

            <div v-if="service.description" class="service-detail__description">
              <h3>Descripcion del servicio</h3>
              <p>{{ service.description }}</p>
            </div>

            <div class="service-detail__actions">
              <a
                v-if="service.whatsapp_contact"
                :href="`https://wa.me/${service.whatsapp_contact}?text=Hola, me interesa el servicio: ${service.name}`"
                target="_blank"
                class="btn btn-success btn-lg"
              >
                <i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp
              </a>
              <a
                v-if="service.allows_online_booking"
                :href="`/m/${business.slug}/citas`"
                class="btn btn-primary btn-lg"
              >
                <i class="bi bi-calendar-check me-2"></i>Reservar ahora
              </a>
              <Link :href="`/m/${business.slug}/servicios`" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i>Volver a servicios
              </Link>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import NavigationMenu from '../../components/NavigationMenu.vue'
import Footer from '../../components/Footer.vue'
import BreadcrumbNav from '@/Components/Minisite/BreadcrumbNav.vue'
import { usePriceFormatter } from '@/Composables/usePriceFormatter'
import GLightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.min.css'

const props = defineProps({
  business: Object,
  setting: Object,
  service: Object,
  socialNetworks: Array,
  existingSections: Array,
})

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
  if (props.service?.gallery && props.service.gallery.length > 0) {
    return props.service.gallery[0].path
  }
  return props.service?.image || null
})

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

.service-detail {
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

  &__price {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #dee2e6;
  }

  &__price-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #198754;
  }

  &__price-label {
    font-size: 0.875rem;
    color: #6c757d;
  }

  &__meta {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
  }

  &__meta-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    font-size: 0.9375rem;
    color: #495057;

    &:last-child {
      margin-bottom: 0;
    }

    i {
      font-size: 1.25rem;
      color: #6c757d;
      width: 28px;
      text-align: center;
    }

    strong {
      color: #212529;
      margin-right: 4px;
    }

    &--highlight {
      i {
        color: #198754;
      }
      color: #198754;
      font-weight: 500;
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

.service-gallery {
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
