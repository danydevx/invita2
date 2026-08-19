<template>
  <div class="nav-menu">
    <header class="nav-menu__header" :class="{ 'nav-menu__header--open': isOpen }">
      <button class="nav-menu__toggle" @click="toggleMenu" aria-label="Abrir menú">
        <span class="nav-menu__hamburger">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </button>
      <div class="nav-menu__brand">
        <img v-if="business?.logo" :src="business.logo" :alt="business.name" class="nav-menu__logo" />
        <span class="nav-menu__name">{{ business?.name }}</span>
      </div>
    </header>

    <Transition name="overlay">
      <div v-if="isOpen" class="nav-menu__overlay" @click="closeMenu"></div>
    </Transition>

    <Transition name="drawer">
      <nav v-if="isOpen" class="nav-menu__drawer">
        <div class="nav-menu__drawer-header">
          <button class="nav-menu__close" @click="closeMenu" aria-label="Cerrar menú">
            <span>&times;</span>
          </button>
          <div class="nav-menu__drawer-brand">
            <img v-if="business?.logo" :src="business.logo" :alt="business.name" class="nav-menu__drawer-logo" />
            <span class="nav-menu__drawer-name">{{ business?.name }}</span>
          </div>
        </div>
        <ul class="nav-menu__list">
          <li v-for="item in menuItems" :key="item.url" class="nav-menu__item">
            <a :href="item.url" class="nav-menu__link" @click="closeMenu">
              <i v-if="item.icon" :class="item.icon"></i>
              {{ item.name }}
            </a>
          </li>
        </ul>
      </nav>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  business: {
    type: Object,
    default: () => ({}),
  },
  existingSections: {
    type: Array,
    default: () => [],
  },
})

const isOpen = ref(false)

const menuOrder = [
  { key: 'services', name: 'Servicios', url: '/servicios', icon: 'bi bi-briefcase' },
  { key: 'products', name: 'Productos', url: '/productos', icon: 'bi bi-box' },
  { key: 'restaurant_menu', name: 'Menú', url: '/menu', icon: 'bi bi-cup-hot' },
  { key: 'gallery', name: 'Galería', url: '/galeria', icon: 'bi bi-images' },
  { key: 'appointments', name: 'Citas y Reservas', url: '/citas', icon: 'bi bi-calendar-check' },
  { key: 'promotions', name: 'Promociones', url: '/promociones', icon: 'bi bi-tag' },
  { key: 'locations', name: 'Ubicaciones', url: '/ubicaciones', icon: 'bi bi-geo-alt' },
  { key: 'reviews', name: 'Reseñas', url: '/resenas', icon: 'bi bi-star' },
  { key: 'properties', name: 'Propiedades', url: '/propiedades', icon: 'bi bi-building' },
  { key: 'faqs', name: 'Preguntas Frecuentes', url: '/preguntas-frecuentes', icon: 'bi bi-question-circle' },
  { key: 'contact_form', name: 'Contacto', url: '/contacto', icon: 'bi bi-envelope' },
]

const menuItems = computed(() => {
  const baseUrl = `/m/${props.business?.slug || ''}`
  const items = [{ key: 'home', name: 'Inicio', url: baseUrl, icon: 'bi bi-house' }]

  for (const menuItem of menuOrder) {
    const sectionExists = props.existingSections.includes(menuItem.key)
    if (sectionExists) {
      items.push({ ...menuItem, url: baseUrl + menuItem.url })
    }
  }

  return items
})

const toggleMenu = () => {
  isOpen.value = !isOpen.value
  document.body.style.overflow = isOpen.value ? 'hidden' : ''
}

const closeMenu = () => {
  isOpen.value = false
  document.body.style.overflow = ''
}
</script>

<style lang="less">
.nav-menu {
  &__header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 64px;
    background: #fff;
    display: flex;
    align-items: center;
    padding: 0 16px;
    z-index: 1001;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
  }

  &__header--open {
    transform: translateX(280px);
  }

  &__toggle {
    background: none;
    border: none;
    padding: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  &__hamburger {
    display: flex;
    flex-direction: column;
    gap: 5px;
    width: 24px;

    span {
      display: block;
      height: 2px;
      background: #212529;
      border-radius: 2px;
      transition: background 0.2s;
    }
  }

  &__brand {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-left: 12px;
    flex: 1;
    min-width: 0;
  }

  &__logo {
    width: 40px;
    height: 40px;
    object-fit: contain;
    border-radius: 8px;
  }

  &__name {
    font-weight: 600;
    font-size: 1.1rem;
    color: #212529;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1002;
  }

  &__drawer {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    background: #fff;
    z-index: 1003;
    overflow-y: auto;
    box-shadow: 4px 0 16px rgba(0, 0, 0, 0.15);
  }

  &__drawer-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    border-bottom: 1px solid #eee;
  }

  &__close {
    background: none;
    border: none;
    font-size: 2rem;
    line-height: 1;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  &__drawer-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
  }

  &__drawer-logo {
    width: 36px;
    height: 36px;
    object-fit: contain;
    border-radius: 6px;
  }

  &__drawer-name {
    font-weight: 600;
    font-size: 1rem;
    color: #212529;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  &__item {
    border-bottom: 1px solid #f0f0f0;
  }

  &__link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    color: #212529;
    text-decoration: none;
    font-size: 1rem;
    transition: background 0.2s;

    i {
      font-size: 1.2rem;
      color: #6c757d;
      width: 24px;
      text-align: center;
    }

    &:hover {
      background: #f8f9fa;
    }
  }
}

.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.3s ease;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}

.drawer-enter-active,
.drawer-leave-active {
  transition: transform 0.3s ease;
}

.drawer-enter-from,
.drawer-leave-to {
  transform: translateX(-100%);
}
</style>
