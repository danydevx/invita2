<template>
  <div class="member-layout">
    <aside class="sidebar bg-dark text-white">
      <div class="sidebar-header p-3 border-bottom border-secondary d-flex align-items-center justify-content-between">
        <Link href="/member" class="text-white text-decoration-none fw-semibold">
          Mi SaaS
        </Link>
        <button
          class="btn btn-link text-white p-0 d-lg-none"
          type="button"
          data-bs-toggle="offcanvas"
          data-bs-target="#sidebarOffcanvas"
        >
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <nav class="sidebar-nav py-2">
        <div class="sidebar-section">
          <Link href="/member/dashboard" class="sidebar-link" :class="{ active: isActive('/member/dashboard') }">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
          </Link>
        </div>

        <div class="sidebar-section">
          <div class="sidebar-section-title">Mi Negocio</div>

            <template v-if="primaryBusiness">
            <Link
              v-if="hasRealListing"
              :href="`/member/listings/${primaryBusiness.id}/modules`"
              class="sidebar-link"
              :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/modules`) }"
            >
              <i class="bi bi-grid"></i>
              <span>Ver módulos</span>
            </Link>

            <Link
              v-if="hasRealListing"
              :href="`/member/listings/${primaryBusiness.id}/edit`"
              class="sidebar-link"
              :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/edit`) }"
            >
              <i class="bi bi-pencil"></i>
              <span>Editar negocio</span>
            </Link>

            <Link
              v-else
              href="/member/listings/create"
              class="sidebar-link"
              :class="{ active: isActive('/member/listings/create') }"
            >
              <i class="bi bi-plus-circle"></i>
              <span>Crear negocio</span>
            </Link>

            <a
              v-if="hasRealListing"
              href="#"
              class="sidebar-link"
              :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/team-members`) || isActive(`/member/listings/${primaryBusiness.id}/team-member-positions`) }"
              @click.prevent="teamSubmenuOpen = !teamSubmenuOpen"
            >
              <i class="bi bi-people"></i>
              <span>Mi Equipo</span>
              <i class="bi bi-chevron-right ms-auto" :class="{ 'rotate-90': teamSubmenuOpen }"></i>
            </a>
            <div v-show="teamSubmenuOpen" class="sidebar-submenu">
              <Link
                :href="`/member/listings/${primaryBusiness.id}/team-members`"
                class="sidebar-link sidebar-link-sub"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/team-members`) }"
              >
                <i class="bi bi-person-badge"></i>
                <span>Miembros</span>
              </Link>
              <Link
                :href="`/member/listings/${primaryBusiness.id}/team-member-positions`"
                class="sidebar-link sidebar-link-sub"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/team-member-positions`) }"
              >
                <i class="bi bi-folder"></i>
                <span>Puestos</span>
              </Link>
            </div>

            <template v-if="hasRealListing">
              <Link
                :href="`/member/listings/${primaryBusiness.id}/packages`"
                class="sidebar-link"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/packages`) }"
              >
                <i class="bi bi-box-seam"></i>
                <span>Paquetes</span>
              </Link>

              <a
                href="#"
                class="sidebar-link"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/products`) || isActive(`/member/listings/${primaryBusiness.id}/product-categories`) }"
                @click.prevent="productsSubmenuOpen = !productsSubmenuOpen"
              >
                <i class="bi bi-cart"></i>
                <span>Productos</span>
                <i class="bi bi-chevron-right ms-auto" :class="{ 'rotate-90': productsSubmenuOpen }"></i>
              </a>
              <div v-show="productsSubmenuOpen" class="sidebar-submenu">
                <Link
                  :href="`/member/listings/${primaryBusiness.id}/products`"
                  class="sidebar-link sidebar-link-sub"
                  :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/products`) }"
                >
                  <i class="bi bi-list"></i>
                  <span>Lista</span>
                </Link>
                <Link
                  :href="`/member/listings/${primaryBusiness.id}/product-categories`"
                  class="sidebar-link sidebar-link-sub"
                  :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/product-categories`) }"
                >
                  <i class="bi bi-folder"></i>
                  <span>Categorías</span>
                </Link>
              </div>

              <a
                href="#"
                class="sidebar-link"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/services`) || isActive(`/member/listings/${primaryBusiness.id}/service-categories`) }"
                @click.prevent="servicesSubmenuOpen = !servicesSubmenuOpen"
              >
                <i class="bi bi-briefcase"></i>
                <span>Servicios</span>
                <i class="bi bi-chevron-right ms-auto" :class="{ 'rotate-90': servicesSubmenuOpen }"></i>
              </a>
              <div v-show="servicesSubmenuOpen" class="sidebar-submenu">
                <Link
                  :href="`/member/listings/${primaryBusiness.id}/services`"
                  class="sidebar-link sidebar-link-sub"
                  :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/services`) }"
                >
                  <i class="bi bi-list"></i>
                  <span>Lista</span>
                </Link>
                <Link
                  :href="`/member/listings/${primaryBusiness.id}/service-categories`"
                  class="sidebar-link sidebar-link-sub"
                  :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/service-categories`) }"
                >
                  <i class="bi bi-folder"></i>
                  <span>Categorías</span>
                </Link>
              </div>

              <a
                href="#"
                class="sidebar-link"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/menu-products`) || isActive(`/member/listings/${primaryBusiness.id}/menu-categories`) }"
                @click.prevent="menuSubmenuOpen = !menuSubmenuOpen"
              >
                <i class="bi bi-cup-hot"></i>
                <span>Menú Restaurante</span>
                <i class="bi bi-chevron-right ms-auto" :class="{ 'rotate-90': menuSubmenuOpen }"></i>
              </a>
              <div v-show="menuSubmenuOpen" class="sidebar-submenu">
                <Link
                  :href="`/member/listings/${primaryBusiness.id}/menu-products`"
                  class="sidebar-link sidebar-link-sub"
                  :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/menu-products`) }"
                >
                  <i class="bi bi-list"></i>
                  <span>Productos</span>
                </Link>
                <Link
                  :href="`/member/listings/${primaryBusiness.id}/menu-categories`"
                  class="sidebar-link sidebar-link-sub"
                  :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/menu-categories`) }"
                >
                  <i class="bi bi-folder"></i>
                  <span>Categorías</span>
                </Link>
              </div>
            </template>

            <template v-if="primaryBusinessModules.length">
              <Link
                v-for="mod in primaryBusinessModules"
                :key="mod.key"
                :href="mod.url"
                class="sidebar-link"
                :class="{ active: isActive(mod.url) }"
              >
                <i :class="mod.icon || 'bi bi-grid'"></i>
                <span>{{ mod.title }}</span>
              </Link>
            </template>

            <a
              v-if="hasRealListing"
              href="#"
              class="sidebar-link"
              :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/fidelity-cards`) || isActive(`/member/listings/${primaryBusiness.id}/fidelity-rewards`) || isActive(`/member/listings/${primaryBusiness.id}/fidelity-cards/history`) }"
              @click.prevent="fidelitySubmenuOpen = !fidelitySubmenuOpen"
            >
              <i class="bi bi-heart"></i>
              <span>Fidelización</span>
              <i class="bi bi-chevron-right ms-auto" :class="{ 'rotate-90': fidelitySubmenuOpen }"></i>
            </a>
            <div v-show="fidelitySubmenuOpen" class="sidebar-submenu">
              <Link
                :href="`/member/listings/${primaryBusiness.id}/fidelity-cards`"
                class="sidebar-link sidebar-link-sub"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/fidelity-cards`) && !isActive(`/member/listings/${primaryBusiness.id}/fidelity-cards/history`) && !isActive(`/member/listings/${primaryBusiness.id}/fidelity-cards/create`)}"
              >
                <i class="bi bi-card-text"></i>
                <span>Tarjetas</span>
              </Link>
              <Link
                :href="`/member/listings/${primaryBusiness.id}/fidelity-rewards`"
                class="sidebar-link sidebar-link-sub"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/fidelity-rewards`) }"
              >
                <i class="bi bi-gift"></i>
                <span>Recompensas</span>
              </Link>
              <Link
                :href="`/member/listings/${primaryBusiness.id}/fidelity-cards/history`"
                class="sidebar-link sidebar-link-sub"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/fidelity-cards/history`) }"
              >
                <i class="bi bi-clock-history"></i>
                <span>Historial</span>
              </Link>
            </div>

            <a
              v-if="currentVcardId"
              href="#"
              class="sidebar-link"
              :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/vcards/${currentVcardId}/edit`) }"
              @click.prevent="vcardSubmenuOpen = !vcardSubmenuOpen"
            >
              <i class="bi bi-person-vcard"></i>
              <span>vCard</span>
              <i class="bi bi-chevron-right ms-auto" :class="{ 'rotate-90': vcardSubmenuOpen }"></i>
            </a>
            <div v-show="vcardSubmenuOpen && currentVcardId" class="sidebar-submenu">
              <Link
                :href="`/member/listings/${primaryBusiness.id}/vcards/${currentVcardId}/edit`"
                class="sidebar-link sidebar-link-sub"
                :class="{ active: isActive(`/member/listings/${primaryBusiness.id}/vcards/${currentVcardId}/edit`) }"
              >
                <i class="bi bi-pencil"></i>
                <span>Editar</span>
              </Link>
            </div>
          </template>

          <div v-else class="sidebar-link text-muted">
            <span class="small">Sin negocio configurado</span>
          </div>
        </div>

        <div v-if="canBilling" class="sidebar-section">
          <div class="sidebar-section-title">Facturación</div>
          <Link href="/member/payments" class="sidebar-link" :class="{ active: isActive('/member/payments') }">
            <i class="bi bi-credit-card"></i>
            <span>Pagos</span>
          </Link>
          <Link href="/member/invoices" class="sidebar-link" :class="{ active: isActive('/member/invoices') }">
            <i class="bi bi-file-earmark-text"></i>
            <span>Comprobantes</span>
          </Link>
        </div>

        <div v-if="canSupport" class="sidebar-section">
          <div class="sidebar-section-title">Soporte</div>
          <Link href="/member/support" class="sidebar-link" :class="{ active: isActive('/member/support') }">
            <i class="bi bi-headset"></i>
            <span>Tickets</span>
          </Link>
          <Link href="/member/help" class="sidebar-link" :class="{ active: isActive('/member/help') }">
            <i class="bi bi-question-circle"></i>
            <span>Ayuda</span>
          </Link>
        </div>

        <div class="sidebar-section">
          <div class="sidebar-section-title">Cuenta</div>
          <Link href="/member/account" class="sidebar-link" :class="{ active: isActive('/member/account') }">
            <i class="bi bi-wallet2"></i>
            <span>Detalles</span>
          </Link>
          <Link href="/member/profile" class="sidebar-link" :class="{ active: isActive('/member/profile') }">
            <i class="bi bi-person"></i>
            <span>Perfil</span>
          </Link>
          <Link href="/member/password" class="sidebar-link" :class="{ active: isActive('/member/password') }">
            <i class="bi bi-lock"></i>
            <span>Password</span>
          </Link>
          <Link href="/member/preferences" class="sidebar-link" :class="{ active: isActive('/member/preferences') }">
            <i class="bi bi-gear"></i>
            <span>Preferencias</span>
          </Link>
        </div>

        <div class="sidebar-section">
          <div class="sidebar-section-title">Recursos</div>
          <Link href="/member/notifications" class="sidebar-link" :class="{ active: isActive('/member/notifications') }">
            <i class="bi bi-bell"></i>
            <span>Notificaciones</span>
            <span v-if="unreadCount > 0" class="badge bg-primary ms-auto">{{ unreadCount }}</span>
          </Link>
          <Link href="/member/files" class="sidebar-link" :class="{ active: isActive('/member/files') }">
            <i class="bi bi-folder"></i>
            <span>Archivos</span>
          </Link>
        </div>
      </nav>
    </aside>

    <div class="main-wrapper">
      <header class="topbar bg-body border-bottom d-flex align-items-center px-3">
        <button
          class="btn btn-outline-secondary me-3 d-lg-none"
          type="button"
          data-bs-toggle="offcanvas"
          data-bs-target="#sidebarOffcanvas"
        >
          <i class="bi bi-list"></i>
        </button>
        <div class="flex-grow-1">
          <slot name="topbar" />
        </div>
        <div class="d-flex align-items-center gap-2">
          <div class="dropdown">
            <button
              class="btn btn-outline-secondary btn-sm dropdown-toggle"
              type="button"
              data-bs-toggle="dropdown"
            >
              <i class="bi bi-person-circle me-1"></i>
              <span class="d-none d-sm-inline">{{ userName }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <Link href="/member/profile" class="dropdown-item" prefetch="hover">Perfil</Link>
              </li>
              <li>
                <Link href="/member/password" class="dropdown-item" prefetch="hover">Cambiar password</Link>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <Link href="/logout" method="post" as="button" class="dropdown-item">
                  Salir
                </Link>
              </li>
            </ul>
          </div>
        </div>
      </header>

      <main class="main-content p-4">
        <div v-if="announcements.length" class="mb-4">
          <div
            v-for="announcement in announcements"
            :key="announcement.id"
            class="alert d-flex align-items-start gap-3 mb-2"
            :class="alertClass(announcement.type, announcement.priority)"
          >
            <div class="flex-grow-1">
              <div class="fw-semibold">{{ announcement.title }}</div>
              <div class="small">{{ announcement.message }}</div>
              <Link
                v-if="announcement.action_label && announcement.action_url"
                :href="announcement.action_url"
                class="btn btn-sm btn-outline-secondary mt-2"
              >
                {{ announcement.action_label }}
              </Link>
            </div>
            <button
              v-if="announcement.dismissible"
              type="button"
              class="btn-close"
              aria-label="Close"
              @click="dismiss(announcement.id)"
            ></button>
          </div>
        </div>
        <slot />
      </main>
    </div>

    <div
      class="offcanvas offcanvas-start bg-dark text-white d-lg-none"
      tabindex="-1"
      id="sidebarOffcanvas"
      aria-labelledby="sidebarOffcanvasLabel"
    >
      <div class="offcanvas-header border-bottom border-secondary">
        <Link href="/member" class="text-white text-decoration-none fw-semibold">
          Mi SaaS
        </Link>
        <button
          type="button"
          class="btn-close btn-close-white text-reset"
          data-bs-dismiss="offcanvas"
          aria-label="Close"
        ></button>
      </div>
      <div class="offcanvas-body p-0">
        <nav class="sidebar-nav py-2">
          <div class="sidebar-section">
            <Link href="/member/dashboard" class="sidebar-link" :class="{ active: isActive('/member/dashboard') }">
              <i class="bi bi-speedometer2"></i>
              <span>Dashboard</span>
            </Link>
            <Link href="/member/account" class="sidebar-link" :class="{ active: isActive('/member/account') }">
              <i class="bi bi-wallet2"></i>
              <span>Cuenta</span>
            </Link>
          </div>

          <div class="sidebar-section">
            <div 
              class="sidebar-section-title d-flex align-items-center justify-content-between"
              style="cursor: pointer;"
              @click="toggleBusinessesMenu"
            >
              <span>Negocios</span>
              <i class="bi" :class="businessesMenuOpen ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
            </div>

            <template v-if="businessesMenuOpen && businessMenu.length">
              <div v-for="business in businessMenu" :key="business.id">
                <div 
                  class="sidebar-link d-flex align-items-center justify-content-between"
                  style="cursor: pointer;"
                  @click="toggleBusiness(business.id)"
                >
                  <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-building"></i>
                    <span class="text-truncate">{{ business.name }}</span>
                  </span>
                  <i class="bi" :class="openBusiness === business.id ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                
                <template v-if="openBusiness === business.id">
                  <Link
                    v-if="business.id !== 'personal'"
                    :href="`/member/listings/${business.id}/edit`"
                    class="sidebar-link ps-4"
                    :class="{ active: isActive(`/member/listings/${business.id}/edit`) }"
                  >
                    <span><i class="bi bi-pencil"></i> Editar</span>
                  </Link>
                  <Link
                    v-if="business.id === 'personal'"
                    href="/member/listings/create"
                    class="sidebar-link ps-4"
                    :class="{ active: isActive('/member/listings/create') }"
                  >
                    <span><i class="bi bi-plus-circle"></i> Crear negocio</span>
                  </Link>
                  <Link
                    v-for="mod in business.modules"
                    :key="mod.key"
                    :href="mod.url"
                    class="sidebar-link ps-4"
                    :class="{ active: isActive(mod.url) }"
                  >
                    <span>{{ mod.title }}</span>
                  </Link>
                </template>
              </div>
            </template>

            <Link 
              v-else
              href="/member/listings" 
              class="sidebar-link" 
              :class="{ active: isActive('/member/listings') }"
            >
              <i class="bi bi-building"></i>
              <span>Mis Negocios</span>
            </Link>
          </div>

          <div v-if="canBilling" class="sidebar-section">
            <div class="sidebar-section-title">Facturación</div>
            <Link href="/member/payments" class="sidebar-link" :class="{ active: isActive('/member/payments') }">
              <i class="bi bi-credit-card"></i>
              <span>Pagos</span>
            </Link>
            <Link href="/member/invoices" class="sidebar-link" :class="{ active: isActive('/member/invoices') }">
              <i class="bi bi-file-earmark-text"></i>
              <span>Comprobantes</span>
            </Link>
          </div>

          <div v-if="canSupport" class="sidebar-section">
            <div class="sidebar-section-title">Soporte</div>
            <Link href="/member/support" class="sidebar-link" :class="{ active: isActive('/member/support') }">
              <i class="bi bi-headset"></i>
              <span>Tickets</span>
            </Link>
            <Link href="/member/help" class="sidebar-link" :class="{ active: isActive('/member/help') }">
              <i class="bi bi-question-circle"></i>
              <span>Ayuda</span>
            </Link>
          </div>

          <div class="sidebar-section">
            <div class="sidebar-section-title">Cuenta</div>
            <Link href="/member/profile" class="sidebar-link" :class="{ active: isActive('/member/profile') }">
              <i class="bi bi-person"></i>
              <span>Perfil</span>
            </Link>
            <Link href="/member/password" class="sidebar-link" :class="{ active: isActive('/member/password') }">
              <i class="bi bi-lock"></i>
              <span>Password</span>
            </Link>
            <Link href="/member/preferences" class="sidebar-link" :class="{ active: isActive('/member/preferences') }">
              <i class="bi bi-gear"></i>
              <span>Preferencias</span>
            </Link>
          </div>

          <div class="sidebar-section">
            <div class="sidebar-section-title">Recursos</div>
            <Link href="/member/notifications" class="sidebar-link" :class="{ active: isActive('/member/notifications') }">
              <i class="bi bi-bell"></i>
              <span>Notificaciones</span>
              <span v-if="unreadCount > 0" class="badge bg-primary ms-auto">{{ unreadCount }}</span>
            </Link>
            <Link href="/member/files" class="sidebar-link" :class="{ active: isActive('/member/files') }">
              <i class="bi bi-folder"></i>
              <span>Archivos</span>
            </Link>
          </div>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, provide, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useFlashToast } from '@/Composables/useFlashToast'

useFlashToast()

const page = usePage()
const userName = computed(() => page.props.auth?.user?.name || 'Usuario')
const unreadCount = computed(() => page.props.notificationUnreadCount || 0)
const features = computed(() => page.props.features || {})
const modules = computed(() => page.props.modules || {})
const announcements = computed(() => page.props.systemAnnouncements || [])
const businessMenu = computed(() => page.props.businessMenu || [])
const currentPath = computed(() => window.location.pathname)

const canBilling = computed(() => modules.value.billing !== false)
const canSupport = computed(() => modules.value.support !== false && features.value.module_support !== false)

const businessesMenuOpen = ref(true)
const openBusiness = ref(null)
const teamSubmenuOpen = ref(false)
const productsSubmenuOpen = ref(false)
const menuSubmenuOpen = ref(false)
const servicesSubmenuOpen = ref(false)
const fidelitySubmenuOpen = ref(false)
const vcardSubmenuOpen = ref(false)

const checkAndOpenSubmenu = (path) => {
  if (!primaryBusiness.value) return
  const bizId = primaryBusiness.value.id

  if (path.startsWith(`/member/listings/${bizId}/team-members`) ||
      path.startsWith(`/member/listings/${bizId}/team-member-positions`)) {
    teamSubmenuOpen.value = true
  }
  if (path.startsWith(`/member/listings/${bizId}/products`) ||
      path.startsWith(`/member/listings/${bizId}/product-categories`)) {
    productsSubmenuOpen.value = true
  }
  if (path.startsWith(`/member/listings/${bizId}/menu-products`) ||
      path.startsWith(`/member/listings/${bizId}/menu-categories`)) {
    menuSubmenuOpen.value = true
  }
  if (path.startsWith(`/member/listings/${bizId}/services`) ||
      path.startsWith(`/member/listings/${bizId}/service-categories`)) {
    servicesSubmenuOpen.value = true
  }
  if (path.startsWith(`/member/listings/${bizId}/fidelity-cards`) ||
      path.startsWith(`/member/listings/${bizId}/fidelity-rewards`) ||
      path.startsWith(`/member/listings/${bizId}/client-fidelity`)) {
    fidelitySubmenuOpen.value = true
  }
  if (path.match(/\/member\/listings\/\d+\/vcards\/\d+\/edit/) ||
      path.match(/\/member\/listings\/\d+\/vcards\/\d+\/seo/)) {
    vcardSubmenuOpen.value = true
  }
}

onMounted(() => {
  checkAndOpenSubmenu(window.location.pathname)
})

const toggleBusinessesMenu = () => {
  businessesMenuOpen.value = !businessesMenuOpen.value
  if (!businessesMenuOpen.value) {
    openBusiness.value = null
  }
}

const hasBusinessModules = computed(() => {
  return businessMenu.value.some(biz => biz.modules && biz.modules.length > 0)
})

const toggleBusiness = (id) => {
  openBusiness.value = openBusiness.value === id ? null : id
}

const currentBusinessId = computed(() => {
  const match = currentPath.value.match(/^\/member\/listings\/(\d+)/)
  return match ? parseInt(match[1]) : null
})

const currentVcardId = computed(() => {
  const match = currentPath.value.match(/\/vcards\/(\d+)\/edit/)
  return match ? parseInt(match[1]) : null
})

const primaryBusiness = computed(() => {
  if (currentBusinessId.value) {
    return businessMenu.value.find(biz => biz.id === currentBusinessId.value) || businessMenu.value[0] || null
  }

  return businessMenu.value[0] || null
})

const hasRealListing = computed(() => {
  return primaryBusiness.value && primaryBusiness.value.id !== 'personal'
})

const primaryBusinessModules = computed(() => {
  const excludeKeys = ['team_members', 'packages', 'products', 'restaurant_menu', 'services', 'fidelity_cards', 'fidelity_rewards', 'client_fidelity', 'client-fidelity']
  return (primaryBusiness.value?.modules || []).filter(m => !excludeKeys.includes(m.key))
})

const isActive = (url) => {
  return window.location.pathname.startsWith(url)
}

const dismiss = (id) => {
  router.put(`/member/announcements/${id}/dismiss`, {}, { preserveScroll: true })
}

const getBusinessById = (businessId) => {
  return businessMenu.value.find(b => b.id === businessId)
}

const getModuleByUrl = (url) => {
  for (const business of businessMenu.value) {
    const module = business.modules?.find(m => m.url === url)
    if (module) {
      return { business, module }
    }
    const editUrl = `/member/listings/${business.id}/edit`
    if (url === editUrl) {
      return { business, module: { title: 'Editar', url: editUrl } }
    }
  }
  return null
}

const dynamicBreadcrumbs = computed(() => {
  const path = currentPath.value
  const result = []

  result.push({ label: 'Mis Negocios', href: '/member/listings' })

  const businessMatch = path.match(/^\/member\/listings\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const business = getBusinessById(businessId)
    if (business) {
      result.push({
        label: business.name,
        href: `/member/listings/${business.id}/edit`
      })

      const moduleInfo = getModuleByUrl(path)
      if (moduleInfo && moduleInfo.module) {
        const moduleTitle = moduleInfo.module.title
        if (moduleTitle !== 'Editar') {
          result.push({ label: moduleTitle, active: true })
        } else {
          result[result.length - 1].active = true
        }
      }
    } else {
      const moduleInfo = getModuleByUrl(path)
      if (moduleInfo && moduleInfo.module) {
        result.push({
          label: moduleInfo.business.name,
          href: `/member/listings/${moduleInfo.business.id}/edit`
        })
        result.push({ label: moduleInfo.module.title, active: true })
      }
    }
  }

  if (result.length === 1) {
    const moduleInfo = getModuleByUrl(path)
    if (moduleInfo) {
      result.push({
        label: moduleInfo.business.name,
        href: `/member/listings/${moduleInfo.business.id}/edit`
      })
      result.push({ label: moduleInfo.module.title, active: true })
    }
  }

  return result
})

provide('dynamicBreadcrumbs', dynamicBreadcrumbs)

const alertClass = (type, priority) => {
  const classes = {
    info: 'alert-info',
    success: 'alert-success',
    warning: 'alert-warning',
    danger: 'alert-danger',
  }
  const base = classes[type] || 'alert-info'
  if (priority === 'critical') {
    return `${base} border border-2 border-danger`
  }
  if (priority === 'high') {
    return `${base} border border-1 border-warning`
  }
  return base
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');

.member-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'DM Sans', sans-serif;
}

.sidebar {
  width: 240px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  position: sticky;
  top: 0;
  height: 100vh;
}

@media (max-width: 991.98px) {
  .sidebar {
    display: none;
  }
}

.sidebar-header {
  padding: 1rem;
}

.sidebar-nav {
  flex: 1;
  padding: 0.5rem 0;
}

.sidebar-section {
  padding: 0.5rem 0;
}

.sidebar-section-title {
  padding: 0.5rem 1rem;
  font-size: 0.75rem;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.5);
  letter-spacing: 0.05em;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.625rem 1rem;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: all 0.15s ease;
  font-size: 0.9rem;
}

.sidebar-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.sidebar-link.active {
  background-color: rgba(255, 255, 255, 0.15);
  color: #fff;
  border-left: 3px solid #fff;
}

.sidebar-link i {
  font-size: 1.1rem;
  width: 20px;
  text-align: center;
}

.sidebar-link .badge {
  font-size: 0.7rem;
  padding: 0.2rem 0.5rem;
}

.main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.topbar {
  height: 56px;
  flex-shrink: 0;
}

.main-content {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
}

.offcanvas {
  width: 280px;
}

.offcanvas .sidebar-link {
  padding: 0.75rem 1rem;
}

.sidebar-submenu {
  padding-left: 1rem;
}

.sidebar-submenu-title {
  letter-spacing: 0.05em;
}

.sidebar-link-sub {
  padding-left: 2.5rem;
  font-size: 0.85rem;
}

.rotate-90 {
  transform: rotate(90deg);
}
</style>
