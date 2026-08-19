<template>
  <MinisiteLayout
    :business="business"
    :theme="theme"
    :modules="modules"
    :branding="branding"
  >
    <nav class="navbar navbar-expand-lg sticky-top" :style="navbarStyle">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" :style="{ color: navbarTextColor }" :href="`/${business.slug}`">
          <img v-if="business.logo_path" :src="business.logo_path" :alt="business.name" class="me-2" style="height: 40px;">
          <span v-else>{{ business.name }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic" :style="{ borderColor: navbarTextColor }">
          <span class="navbar-toggler-icon" :style="{ filter: navbarTogglerIconFilter }"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarPublic">
          <ul class="navbar-nav ms-auto">
            <li v-if="isModuleEnabled('about')" class="nav-item">
              <a :href="`#about`" class="nav-link" :style="{ color: navbarTextColor }">Nosotros</a>
            </li>
            <li v-if="isModuleEnabled('services')" class="nav-item">
              <a :href="`#services`" class="nav-link" :style="{ color: navbarTextColor }">Servicios</a>
            </li>
            <li v-if="isModuleEnabled('gallery')" class="nav-item">
              <a :href="`#gallery`" class="nav-link" :style="{ color: navbarTextColor }">Galería</a>
            </li>
            <li v-if="isModuleEnabled('products')" class="nav-item">
              <a :href="`#products`" class="nav-link" :style="{ color: navbarTextColor }">Productos</a>
            </li>
            <li v-if="isModuleEnabled('packages')" class="nav-item">
              <a :href="`#packages`" class="nav-link" :style="{ color: navbarTextColor }">Paquetes</a>
            </li>
            <li v-if="isModuleEnabled('restaurant_menu')" class="nav-item">
              <a :href="`#restaurant_menu`" class="nav-link" :style="{ color: navbarTextColor }">Menú</a>
            </li>
            <li v-if="isModuleEnabled('appointments')" class="nav-item">
              <a :href="`#appointments`" class="nav-link" :style="{ color: navbarTextColor }">Reservar</a>
            </li>
            <li v-if="isModuleEnabled('reviews')" class="nav-item">
              <a :href="`#reviews`" class="nav-link" :style="{ color: navbarTextColor }">Reseñas</a>
            </li>
            <li v-if="isModuleEnabled('locations')" class="nav-item">
              <a :href="`#locations`" class="nav-link" :style="{ color: navbarTextColor }">Ubicaciones</a>
            </li>
            <li v-if="isModuleEnabled('contact_form')" class="nav-item">
              <a :href="`#contact`" class="nav-link" :style="{ color: navbarTextColor }">Contacto</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <HeroSection
      v-if="hero?.is_active !== false"
      :title="hero?.title || business.name"
      :subtitle="hero?.subtitle"
      :text-aux="hero?.text_aux"
      :buttons="hero?.buttons || []"
      :background-type="hero?.background_type || 'gradient'"
      :background-color="hero?.background_color"
      :gradient-start="hero?.background_gradient_start"
      :gradient-end="hero?.background_gradient_end"
      :background-image="hero?.background_image_path"
      :alignment="hero?.alignment || 'left'"
      :show-logo="true"
      :show-contact-info="hero?.show_contact_info !== false"
      :show-social-links="hero?.show_social_links || (isModuleEnabled('socialmedia') && socialNetworks.length > 0)"
      :social-links="getHeroSocialLinks()"
      :logo-path="business.logo_path"
      :business-name="business.name"
      :phone="business.phone"
      :email="business.email"
    />

    <section v-if="about" id="about" class="py-5" :class="'section--' + (sectionSchemes.about || 'light')">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <img v-if="about.image_path" :src="about.image_path" class="img-fluid rounded shadow" :alt="about.title">
          </div>
          <div class="col-lg-6">
            <h2 class="fw-bold mb-3">{{ about.title || 'Acerca de nosotros' }}</h2>
            <p v-if="about.subtitle" class="text-site-muted mb-3">{{ about.subtitle }}</p>
            <p v-if="about.description" class="text-site-muted">{{ about.description }}</p>
            <img v-if="about.logo_path" :src="about.logo_path" class="img-fluid mt-3" style="max-height: 80px;" :alt="business.name">
          </div>
        </div>
      </div>
    </section>

    <ServicesSection
      id="services"
      v-if="isModuleEnabled('services') && services.length"
      :services="services"
      :business-slug="business.slug"
      :variant="sectionVariants.services || 'cards'"
      :scheme="sectionSchemes.services || 'neutral'"
    />

    <section id="appointments" v-if="isModuleEnabled('appointments')" class="py-5 text-center" :class="'section--' + (sectionSchemes.appointments || 'primary')">
      <div class="container">
        <h2 class="fw-bold mb-3">¿Listo para tu cita?</h2>
        <p class="mb-4 lead">Reserva online de forma rápida y sencilla</p>
        <Link :href="`/b/${business.slug}/book`" class="btn-site btn-site-light btn-site-lg">
          <i class="bi bi-calendar-check me-2"></i>Reservar ahora
        </Link>
      </div>
    </section>

    <section id="gallery" v-if="isModuleEnabled('gallery') && gallery.length" class="py-5" :class="'section--' + (sectionSchemes.gallery || 'dark')">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold">Galería</h2>
          <p class="text-site-muted">Mira nuestro trabajo</p>
        </div>
        <div class="row g-3" id="gallery-grid">
          <div v-for="(image, index) in gallery.slice(0, 8)" :key="image.id" class="col-6 col-md-4 col-lg-3" :class="animateClass">
            <a href="#" class="gallery-item d-block overflow-hidden" :style="{ animationDelay: `${index * 0.1}s` }" @click.prevent="openGallery(index)">
              <img :src="image.path" class="img-fluid w-100 img-cover" :alt="image.title || 'Galería'" style="height: 150px;">
            </a>
          </div>
        </div>
        <div class="text-center mt-4">
          <Link :href="`/b/${business.slug}/gallery`" class="btn-site btn-site-outline">
            Ver toda la galería <i class="bi bi-arrow-right ms-2"></i>
          </Link>
        </div>
      </div>
    </section>

    <section id="products" v-if="isModuleEnabled('products') && products.length" class="py-5" :class="'section--' + (sectionSchemes.products || 'light')">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold">Productos</h2>
          <p class="text-site-muted">Productos para tu cuidado personal</p>
        </div>
        <div class="row g-4">
          <div v-for="product in products.slice(0, 8)" :key="product.id" class="col-6 col-md-4 col-lg-3">
            <div class="card h-100" :class="cardHoverClass">
              <div class="card-body">
                <h5 class="card-title card-title-custom">{{ product.name }}</h5>
                <p v-if="product.description" class="card-text text-site-muted small">{{ product.description.substring(0, 60) }}...</p>
                <div v-if="product.price" class="fw-bold accent-color">
                  {{ formatPrice(product.price) }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="products.length > 8" class="text-center mt-4">
          <Link :href="`/b/${business.slug}/products`" class="btn-site btn-site-outline">
            Ver todos los productos <i class="bi bi-arrow-right ms-2"></i>
          </Link>
        </div>
      </div>
    </section>

    <section id="packages" v-if="isModuleEnabled('packages') && packages.length" class="py-5" :class="'section--' + (sectionSchemes.packages || 'light')">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold">Paquetes</h2>
          <p class="text-site-muted">Explora nuestros paquetes especiales</p>
        </div>
        <div class="row g-4">
          <div v-for="pkg in packages.slice(0, 8)" :key="pkg.id" class="col-6 col-md-4 col-lg-3">
            <div class="card h-100" :class="cardHoverClass">
              <img v-if="pkg.image" :src="pkg.image" class="card-img-top img-cover" :alt="pkg.title" style="height: 120px;">
              <div class="card-body">
                <h5 class="card-title card-title-custom">{{ pkg.title }}</h5>
                <p v-if="pkg.short_description" class="card-text text-site-muted small">{{ pkg.short_description }}</p>
                <div class="mt-auto">
                  <div v-if="pkg.promo_price" class="fw-bold accent-color">
                    {{ formatPrice(pkg.promo_price) }}
                    <small class="text-muted text-decoration-line-through">{{ formatPrice(pkg.price) }}</small>
                  </div>
                  <div v-else-if="pkg.price" class="fw-bold accent-color">
                    {{ formatPrice(pkg.price) }}
                  </div>
                  <a v-if="pkg.whatsapp" :href="`https://wa.me/${pkg.whatsapp}?text=${encodeURIComponent(pkg.whatsapp_message || 'Hola, me interesa este paquete')}`" target="_blank" class="btn btn-success btn-sm mt-2">
                    <i class="bi bi-whatsapp"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="restaurant_menu" v-if="isModuleEnabled('restaurant_menu') && (menuCategories.length || menuProducts.length)" class="py-5" :class="'section--' + (sectionSchemes.menu || 'neutral')">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold">Nuestro Menú</h2>
          <p class="text-site-muted">Explora nuestras opciones</p>
        </div>
        <div v-if="menuCategories.length" class="mb-4">
          <div v-for="category in menuCategories" :key="category.id" class="mb-4">
            <h4 class="mb-3">{{ category.title }}</h4>
            <div class="row g-3">
              <div v-for="product in category.products?.slice(0, 4) || []" :key="product.id" class="col-6 col-md-3">
                <div class="card h-100" :class="cardHoverClass">
                  <img v-if="product.image" :src="product.image" class="card-img-top img-cover" :alt="product.title" style="height: 100px;">
                  <div class="card-body p-2">
                    <h6 class="card-title mb-1 card-title-sm">{{ product.title }}</h6>
                    <div v-if="product.display_price" class="fw-bold small accent-color">
                      {{ product.display_price }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else-if="menuProducts.length" class="row g-3">
          <div v-for="product in menuProducts.slice(0, 8)" :key="product.id" class="col-6 col-md-4 col-lg-3">
            <div class="card h-100" :class="cardHoverClass">
              <img v-if="product.image" :src="product.image" class="card-img-top img-cover" :alt="product.title" style="height: 100px;">
              <div class="card-body p-2">
                <h6 class="card-title mb-1 card-title-sm">{{ product.title }}</h6>
                <div v-if="product.display_price" class="fw-bold small accent-color">
                  {{ product.display_price }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center mt-4">
          <Link :href="`/b/${business.slug}/menu`" class="btn-site btn-site-outline">
            Ver menú completo <i class="bi bi-arrow-right ms-2"></i>
          </Link>
        </div>
      </div>
    </section>

    <section id="locations" v-if="isModuleEnabled('locations') && locations.length" class="py-5" :class="'section--' + (sectionSchemes.locations || 'neutral')">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold">Ubicaciones</h2>
          <p class="text-site-muted">Visitanos en nuestras sucursales</p>
        </div>
        <div class="row g-4">
          <div v-for="location in locations" :key="location.id" class="col-md-6">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title fw-bold">
                  <i class="bi bi-geo-alt me-2 accent-color"></i>{{ location.name }}
                </h5>
                <p class="card-text text-site-muted">
                  {{ location.address_line_1 }}<br v-if="location.address_line_2">{{ location.address_line_2 }}<br>
                  {{ location.city }}
                </p>
                <a v-if="location.directions_url" :href="location.directions_url" target="_blank" class="btn-site btn-site-outline btn-site-sm">
                  <i class="bi bi-signpost-2 me-1"></i>Cómo llegar
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="reviews" v-if="isModuleEnabled('reviews') && reviews.length" class="py-5" :class="'section--' + (sectionSchemes.reviews || 'light')">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold">Reseñas</h2>
          <p class="text-site-muted">Lo que dicen nuestros clientes</p>
        </div>
        <div class="row g-4">
          <div v-for="review in reviews" :key="review.id" class="col-md-6 col-lg-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <div class="bg-site-primary rounded-circle d-flex align-items-center justify-content-center text-site-white fw-bold avatar-circle">
                    {{ review.customer_name?.charAt(0)?.toUpperCase() || 'A' }}
                  </div>
                  <div class="ms-3">
                    <h6 class="mb-0 fw-bold">{{ review.customer_name }}</h6>
                    <div class="text-site-warning small">
                      <i v-for="i in (review.rating || 5)" :key="i" class="bi bi-star-fill"></i>
                    </div>
                  </div>
                </div>
                <p class="card-text text-site-muted">{{ review.comment }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="promotions" v-if="isModuleEnabled('promotions') && promotions.length" class="py-5" :class="'section--' + (sectionSchemes.promotions || 'accent')">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold">Promociones</h2>
          <p class="opacity-75">Ofertas especiales para vos</p>
        </div>
        <div class="row g-4">
          <div v-for="promo in promotions" :key="promo.id" class="col-md-6">
            <div class="card h-100 text-site-dark">
              <div class="card-body">
                <h5 class="card-title fw-bold">{{ promo.name }}</h5>
                <p class="card-text">{{ promo.description }}</p>
                <div v-if="promo.regular_price && promo.promotion_price" class="display-6 fw-bold text-site-danger">
                  {{ Math.round(((promo.regular_price - promo.promotion_price) / promo.regular_price) * 100) }}% OFF
                </div>
                <small class="text-site-muted">
                  Válido hasta: {{ formatDate(promo.expires_at) }}
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <ContactSection
      id="contact"
      v-if="isModuleEnabled('contact_form')"
      :contact-info="{
        phone: business.phone,
        email: business.email,
        address: locations.length > 0 ? locations[0].address_line_1 + ', ' + locations[0].city : '',
        website: business.website
      }"
      :social-links="getContactSocialLinks()"
      :locations="locations"
      :show-social="isModuleEnabled('socialmedia')"
      :business-slug="business.slug"
    />

    <FooterSection
      v-if="isModuleEnabled('socialmedia') || business.phone || business.email"
      :business-name="business.name"
      :logo-path="business.logo_path"
      :contact-info="{
        phone: business.phone,
        email: business.email,
        address: locations.length > 0 ? locations[0].address_line_1 + ', ' + locations[0].city : '',
        website: business.website
      }"
      :social-links="getFooterSocialLinks()"
      :background-color="footerColors.bg"
      :text-color="footerColors.text"
      :link-color="footerColors.link"
      :accent-color="footerColors.linkHover"
    />
  </MinisiteLayout>
</template>

<script setup>
import { computed, onMounted, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'
import MinisiteLayout from '@/Layouts/MinisiteLayout.vue'
import GLightbox from 'glightbox'
import HeroSection from '@/Minisites/Themes/Bold/Sections/HeroSection.vue'
import ServicesSection from '@/Minisites/Themes/Bold/Sections/ServicesSection.vue'
import ContactSection from '@/Minisites/Themes/Bold/Sections/ContactSection.vue'
import FooterSection from '@/Minisites/Themes/Bold/Sections/FooterSection.vue'
import 'glightbox/dist/css/glightbox.min.css'

const page = usePage()

const business = computed(() => page.props.business)
const theme = computed(() => page.props.theme)
const modules = computed(() => page.props.modules || [])
const hero = computed(() => page.props.hero)
const services = computed(() => page.props.services || [])
const locations = computed(() => page.props.locations || [])
const gallery = computed(() => page.props.gallery || [])
const reviews = computed(() => page.props.reviews || [])
const promotions = computed(() => page.props.promotions || [])
const products = computed(() => page.props.products || [])
const packages = computed(() => page.props.packages || [])
const menuCategories = computed(() => page.props.menuCategories || [])
const menuProducts = computed(() => page.props.menuProducts || [])
const socialNetworks = computed(() => page.props.socialNetworks || [])
const about = computed(() => page.props.about || null)
const branding = computed(() => page.props.branding || null)
const sectionVariants = computed(() => page.props.sectionVariants || { services: 'cards' })
const sectionSchemes = computed(() => page.props.sectionSchemes || {
  hero: 'gradient',
  about: 'light',
  services: 'neutral',
  products: 'light',
  gallery: 'dark',
  menu: 'neutral',
  appointments: 'primary',
  reviews: 'light',
  locations: 'neutral',
  contact: 'dark',
  promotions: 'accent',
  packages: 'light',
})

const schemePalettes = computed(() => theme.value?.scheme_palettes || [])

const brandColors = computed(() => theme.value?.css_variables?.colors || {})

const resolveColor = (colorKey) => {
  if (!colorKey) return '#000000'
  if (colorKey.startsWith('#') || colorKey.startsWith('rgba') || colorKey === 'transparent') return colorKey
  return brandColors.value[colorKey] || colorKey
}

const headerColors = computed(() => {
  const header = schemePalettes.value.header || {}
  return {
    bg: resolveColor(header.bg || 'white'),
    text: resolveColor(header.text || 'brand_text'),
    heading: resolveColor(header.heading || 'brand_primary'),
    link: resolveColor(header.link || 'brand_primary'),
    linkHover: resolveColor(header.link_hover || 'brand_accent'),
    border: resolveColor(header.border || 'rgba(0,0,0,0.1)'),
    navBg: resolveColor(header.nav_bg || 'white'),
    navText: resolveColor(header.nav_text || 'brand_text'),
    navLink: resolveColor(header.nav_link || 'brand_primary'),
    navLinkHover: resolveColor(header.nav_link_hover || 'brand_accent'),
    navBorder: resolveColor(header.nav_border || 'rgba(0,0,0,0.1)'),
  }
})

const footerColors = computed(() => {
  const footer = schemePalettes.value.footer || {}
  return {
    bg: resolveColor(footer.bg || 'brand_text'),
    text: resolveColor(footer.text || 'white'),
    heading: resolveColor(footer.heading || 'white'),
    link: resolveColor(footer.link || 'white'),
    linkHover: resolveColor(footer.link_hover || 'rgba(255,255,255,0.8)'),
    border: resolveColor(footer.border || 'rgba(255,255,255,0.1)'),
  }
})

const isModuleEnabled = (moduleKey) => {
  if (!moduleKey) return true
  return modules.value.includes(moduleKey)
}

const navbarStyle = computed(() => {
  return {
    backgroundColor: headerColors.value.navBg,
    borderBottom: `1px solid ${headerColors.value.navBorder}`,
    color: headerColors.value.navText,
  }
})

const navbarTextColor = computed(() => {
  return headerColors.value.navText
})

const isColorDark = (hexColor) => {
  if (!hexColor || !hexColor.startsWith('#')) return false
  const hex = hexColor.replace('#', '')
  if (hex.length < 6) return false
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255
  return luminance < 0.5
}

const navbarTogglerIconFilter = computed(() => {
  return isColorDark(headerColors.value.navBg) ? 'invert(100%)' : 'invert(0%)'
})

const heroBackgroundStyles = computed(() => {
  if (hero.value?.is_active) {
    const h = hero.value
    if (h.background_type === 'color') {
      return { backgroundColor: h.background_color }
    } else if (h.background_type === 'gradient') {
      return { background: `linear-gradient(135deg, ${h.background_gradient_start} 0%, ${h.background_gradient_end} 100%)` }
    } else if (h.background_type === 'image' && h.background_image_path) {
      return { backgroundImage: `url(${h.background_image_path})`, backgroundSize: 'cover', backgroundPosition: 'center' }
    }
  }
  if (business.value.cover_image_path) {
    return { backgroundImage: `url(${business.value.cover_image_path})`, backgroundSize: 'cover', backgroundPosition: 'center' }
  }
  return heroBackgroundStyle.value
})

const heroContentClass = computed(() => {
  return ''
})

const heroTextAlignmentClass = computed(() => {
  if (hero.value?.alignment === 'center') return 'text-center mx-auto'
  if (hero.value?.alignment === 'right') return 'text-end ms-auto'
  return ''
})

const getButtonClass = (style) => {
  if (style === 'primary') return 'btn-site btn-site-primary'
  if (style === 'secondary') return 'btn-site btn-site-secondary'
  if (style === 'outline') return 'btn-site btn-site-outline'
  return 'btn-site btn-site-primary'
}

const heroBackgroundStyle = computed(() => {
  if (!theme.value?.layout_config?.hero_style) return {}
  const heroStyle = theme.value.layout_config.hero_style

  if (heroStyle === 'fullbleed' || heroStyle === 'fullwidth') {
    return {
      background: `linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%)`,
    }
  }

  return {
    background: 'transparent',
  }
})

const heroClass = computed(() => {
  if (!theme.value?.layout_config?.hero_style) return 'hero-default'
  return `hero-${theme.value.layout_config.hero_style}`
})

const buttonClasses = computed(() => {
  if (!theme.value?.css_variables?.button_style) return 'btn-site btn-site-primary'

  const style = theme.value.css_variables.button_style
  if (style === 'rounded-pill') return 'btn-site btn-site-primary rounded-pill-site'
  if (style === 'rounded-0') return 'btn-site btn-site-primary rounded-0'
  if (style === 'rounded-20') return 'btn-site btn-site-primary rounded-20'
  if (style === 'rounded-8') return 'btn-site btn-site-primary rounded-8'
  return 'btn-site btn-site-primary'
})

const cardHoverClass = computed(() => {
  if (!theme.value?.layout_config?.animations?.cards_hover) return ''

  const animation = theme.value.layout_config.animations.cards_hover
  if (animation.includes('lift')) return 'animate-lift'
  if (animation.includes('glow')) return 'animate-glow'
  if (animation.includes('bounce')) return 'animate-bounce'
  if (animation.includes('neon')) return 'animate-neon-glow'
  return ''
})

const animateClass = computed(() => {
  if (!theme.value?.layout_config?.animations?.sections) return ''
  return ''
})

const getHeroSocialLinks = () => {
  if (!isModuleEnabled('socialmedia')) return []
  return socialNetworks.value
    .filter(sn => sn.show_on_hero)
    .map(sn => ({
      platform: sn.platform,
      url: sn.url,
      name: sn.username || sn.platform,
    }))
}

const getContactSocialLinks = () => {
  if (!isModuleEnabled('socialmedia')) return []
  return socialNetworks.value
    .filter(sn => sn.show_on_contact)
    .map(sn => ({
      platform: sn.platform,
      url: sn.url,
      name: sn.username || sn.platform,
    }))
}

const getFooterSocialLinks = () => {
  if (!isModuleEnabled('socialmedia')) return []
  return socialNetworks.value.filter(sn => sn.show_on_footer)
}

const formatPrice = (price) => {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-AR', { day: 'numeric', month: 'short', year: 'numeric' })
}

const openGallery = (index) => {
  if (lightbox) {
    lightbox.openAt(index)
  }
}

let lightbox = null

onMounted(() => {
  nextTick(() => {
    const images = gallery.value.map(img => ({
      href: img.path,
      title: img.title || 'Galería',
      type: 'image'
    }))
    if (images.length > 0) {
      lightbox = GLightbox({
        elements: images,
        touchNavigation: true,
        loop: true,
        autoplayVideos: true,
      })
    }
  })
})
</script>

<style scoped>
.minisite-hero {
  position: relative;
  padding: 80px 0;
  color: white;
  overflow: hidden;
}

.minisite-hero.hero-fullwidth,
.minisite-hero.hero-fullbleed {
  background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
}

.minisite-hero.hero-centered {
  background: var(--brand-primary);
  text-align: center;
}

.minisite-hero.hero-centered .hero-content {
  display: flex;
  justify-content: center;
}

.minisite-hero.hero-split {
  background: var(--brand-primary);
}

.minisite-hero.hero-split .hero-content {
  display: flex;
  justify-content: flex-end;
}

.minisite-hero.hero-boxed {
  background: transparent;
}

.minisite-hero.hero-boxed .hero-content {
  display: flex;
  justify-content: center;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 0;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 0;
}

.hero-content {
  position: relative;
  z-index: 1;
}

.gallery-item {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: var(--brand-card-radius, 12px);
}

.gallery-item:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.card-title-custom { font-size: 1rem; font-weight: bold; }
.card-title-sm { font-size: 0.9rem; }
.accent-color { color: var(--scheme-accent, var(--brand-accent)); }
.accent-bg { background-color: var(--scheme-accent, var(--brand-accent)); }
.avatar-circle { width: 48px; height: 48px; }
.img-cover { object-fit: cover; display: block; }
</style>
