<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <section class="page-header">
      <div class="page-header__inner">
        <BreadcrumbNav
          :baseSlug="business.slug"
          :items="[{ label: 'Promociones', href: `/m/${business.slug}/promociones` }, { label: promotion.name }]"
        />
      </div>
    </section>

    <section class="page-content">
      <div class="page-content__inner">
        <div class="promotion-detail">
          <div class="promotion-detail__image">
            <div v-if="promotion.image" class="promotion-detail__main-image">
              <img :src="promotion.image" :alt="promotion.name" />
              <span v-if="promotion.discount_percent" class="promotion-detail__discount-badge">
                -{{ promotion.discount_percent }}% OFF
              </span>
            </div>
            <div v-else class="promotion-detail__placeholder">
              <i class="bi bi-tag"></i>
            </div>
          </div>

          <div class="promotion-detail__info">
            <h1 class="promotion-detail__name">{{ promotion.name }}</h1>

            <div class="promotion-detail__prices">
              <span v-if="promotion.regular_price" class="promotion-detail__price-original">
                ${{ promotion.regular_price }}
              </span>
              <span v-if="promotion.promotion_price" class="promotion-detail__price-promotion">
                ${{ promotion.promotion_price }}
              </span>
            </div>

            <div v-if="promotion.coupon_code" class="promotion-detail__coupon">
              <div class="promotion-detail__coupon-code">
                <span class="promotion-detail__coupon-label">Codigo de cupon:</span>
                <span class="promotion-detail__coupon-value">{{ promotion.coupon_code }}</span>
                <button class="promotion-detail__coupon-copy" @click="copyCouponCode">
                  <i class="bi bi-clipboard"></i>
                </button>
              </div>
              <p v-if="copied" class="promotion-detail__coupon-copied">
                <i class="bi bi-check-circle"></i> Copiado!
              </p>
            </div>

            <div class="promotion-detail__validity">
              <div v-if="promotion.starts_at" class="promotion-detail__validity-item">
                <i class="bi bi-calendar-start"></i>
                <span><strong>Inicio:</strong> {{ formatDate(promotion.starts_at) }}</span>
              </div>
              <div v-if="promotion.expires_at" class="promotion-detail__validity-item">
                <i class="bi bi-calendar-end"></i>
                <span><strong>Valido hasta:</strong> {{ formatDate(promotion.expires_at) }}</span>
              </div>
            </div>

            <div v-if="promotion.description" class="promotion-detail__description">
              <h3>Detalles de la promocion</h3>
              <p>{{ promotion.description }}</p>
            </div>

            <div class="promotion-detail__actions">
              <a
                v-if="promotion.qr_code_path"
                :href="promotion.qr_code_path"
                target="_blank"
                class="btn btn-outline-primary"
              >
                <i class="bi bi-qr-code me-2"></i>Ver QR Code
              </a>
              <Link :href="`/m/${business.slug}/promociones`" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver a promociones
              </Link>
            </div>
          </div>
        </div>

        <div v-if="relatedPromotions && relatedPromotions.length > 0" class="related-promotions">
          <h2 class="related-promotions__title">Otras promociones</h2>
          <div class="related-promotions__grid">
            <div
              v-for="item in relatedPromotions"
              :key="item.id"
              class="related-promotion-card"
              @click="goToPromotion(item.slug)"
            >
              <div class="related-promotion-card__image">
                <img v-if="item.image" :src="item.image" :alt="item.name" />
                <div v-else class="related-promotion-card__placeholder">
                  <i class="bi bi-tag"></i>
                </div>
                <span v-if="getDiscountPercent(item)" class="related-promotion-card__discount">
                  -{{ getDiscountPercent(item) }}%
                </span>
              </div>
              <div class="related-promotion-card__content">
                <h4 class="related-promotion-card__name">{{ item.name }}</h4>
                <span v-if="item.promotion_price" class="related-promotion-card__price">
                  ${{ item.promotion_price }}
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
  </div>
</template>

<script setup>
import { ref } from 'vue'
import NavigationMenu from '../../components/NavigationMenu.vue'
import Footer from '../../components/Footer.vue'
import BreadcrumbNav from '@/Components/Minisite/BreadcrumbNav.vue'
import { usePriceFormatter } from '@/Composables/usePriceFormatter'

const props = defineProps({
  business: Object,
  setting: Object,
  promotion: Object,
  relatedPromotions: {
    type: Array,
    default: () => [],
  },
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

const copied = ref(false)

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

const getDiscountPercent = (item) => {
  if (item.regular_price && item.promotion_price && item.regular_price > item.promotion_price) {
    return Math.round((1 - item.promotion_price / item.regular_price) * 100)
  }
  return null
}

const copyCouponCode = () => {
  if (props.promotion?.coupon_code) {
    navigator.clipboard.writeText(props.promotion.coupon_code).then(() => {
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2000)
    })
  }
}

const goToPromotion = (slug) => {
  window.location.href = `/m/${props.business.slug}/promociones/${slug}`
}
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

.promotion-detail {
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

  &__image {
    position: relative;
  }

  &__main-image {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    background: #f8f9fa;

    img {
      width: 100%;
      height: 350px;
      object-fit: cover;

      @media (max-width: 768px) {
        height: 250px;
      }
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

  &__discount-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    background: #dc3545;
    color: #fff;
    font-size: 1.25rem;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 8px;
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

  &__price-original {
    font-size: 1.25rem;
    color: #6c757d;
    text-decoration: line-through;
  }

  &__price-promotion {
    font-size: 2.5rem;
    font-weight: 700;
    color: #dc3545;
  }

  &__coupon {
    background: #e7f1ff;
    border: 2px dashed #0d6efd;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 24px;
  }

  &__coupon-code {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  &__coupon-label {
    font-size: 0.875rem;
    color: #495057;
  }

  &__coupon-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0d6efd;
    letter-spacing: 2px;
  }

  &__coupon-copy {
    background: #0d6efd;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 8px 12px;
    cursor: pointer;
    transition: background 0.2s;

    &:hover {
      background: #0b5ed7;
    }
  }

  &__coupon-copied {
    margin: 8px 0 0;
    color: #198754;
    font-size: 0.875rem;
    font-weight: 500;

    i {
      margin-right: 4px;
    }
  }

  &__validity {
    margin-bottom: 24px;
  }

  &__validity-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-size: 0.9375rem;
    color: #495057;

    &:last-child {
      margin-bottom: 0;
    }

    i {
      color: #6c757d;
      font-size: 1.125rem;
    }

    strong {
      color: #212529;
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

.related-promotions {
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

.related-promotion-card {
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
    position: relative;
    width: 100%;
    height: 140px;
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
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 2.5rem;
  }

  &__discount {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #dc3545;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 4px;
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
    color: #dc3545;
  }
}
</style>
