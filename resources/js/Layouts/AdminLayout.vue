<template>
  <div>
    <nav class="navbar navbar-expand-lg bg-dark border-bottom" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Mi SaaS</a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#mainNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div id="mainNav" class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <Link href="/admin/dashboard" class="nav-link" prefetch="hover">Dashboard</Link>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                Usuarios
              </a>
              <ul class="dropdown-menu">
                <li><Link href="/admin/users" class="dropdown-item" prefetch="hover">Usuarios</Link></li>
                <li v-if="canViewInvitations"><Link href="/admin/invitations" class="dropdown-item" prefetch="hover">Invitaciones</Link></li>
                <li><Link href="/admin/roles" class="dropdown-item" prefetch="hover">Roles</Link></li>
                <li><Link href="/admin/permissions" class="dropdown-item" prefetch="hover">Permisos</Link></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                Listados
              </a>
              <ul class="dropdown-menu">
                <li><Link href="/admin/listings" class="dropdown-item" prefetch="hover">Listados</Link></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-robot me-1"></i>AI Chatbot
              </a>
              <ul class="dropdown-menu">
                <li><Link href="/admin/modules/ai_chatbot/settings" class="dropdown-item" prefetch="hover">Configuración</Link></li>
                <li><Link href="/admin/modules/ai_chatbot/presets" class="dropdown-item" prefetch="hover">Presets</Link></li>
                <li><Link href="/admin/modules/ai_chatbot/personalities" class="dropdown-item" prefetch="hover">Personalidades</Link></li>
              </ul>
            </li>
            <li class="nav-item">
              <Link href="/admin/business-module-definitions" class="nav-link" prefetch="hover">
                <i class="bi bi-grid me-1"></i>Modulos
              </Link>
            </li>
            <li class="nav-item">
              <Link href="/admin/features" class="nav-link" prefetch="hover">
                <i class="bi bi-sliders me-1"></i>Settings
              </Link>
            </li>
            <li v-if="showBillingMenu" class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                Comercial
              </a>
              <ul class="dropdown-menu">
                <li v-if="canViewPlans"><Link href="/admin/plans" class="dropdown-item" prefetch="hover">Planes</Link></li>
                <li v-if="canViewSubscriptions"><Link href="/admin/subscriptions" class="dropdown-item" prefetch="hover">Suscripciones</Link></li>
                <li v-if="canViewCoupons"><Link href="/admin/coupons" class="dropdown-item" prefetch="hover">Cupones</Link></li>
                <li v-if="canViewPayments"><Link href="/admin/payments" class="dropdown-item" prefetch="hover">Pagos</Link></li>
                <li v-if="canViewInvoices"><Link href="/admin/invoices" class="dropdown-item" prefetch="hover">Comprobantes</Link></li>
              </ul>
            </li>
            <li v-if="showSupportMenu" class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                Soporte
              </a>
              <ul class="dropdown-menu">
                <li v-if="canViewSupport"><Link href="/admin/support" class="dropdown-item" prefetch="hover">Tickets</Link></li>
                <li v-if="canViewHelp"><Link href="/admin/help" class="dropdown-item" prefetch="hover">Ayuda</Link></li>
              </ul>
            </li>
            <li v-if="showDataMenu" class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                Datos
              </a>
              <ul class="dropdown-menu">
                <li v-if="canViewReports"><Link href="/admin/reports" class="dropdown-item" prefetch="hover">Reportes</Link></li>
                <li v-if="canViewExports"><Link href="/admin/exports" class="dropdown-item" prefetch="hover">Exportaciones</Link></li>
              </ul>
            </li>
            <li v-if="showIntegrationsMenu" class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                Integraciones
              </a>
              <ul class="dropdown-menu">
                <li v-if="canViewApiKeys"><Link href="/admin/api-keys" class="dropdown-item" prefetch="hover">API Keys</Link></li>
                <li><Link href="/admin/api-explorer" class="dropdown-item" prefetch="hover">API Explorer</Link></li>
                <li v-if="canViewWebhooks"><Link href="/admin/webhooks" class="dropdown-item" prefetch="hover">Webhooks</Link></li>
              </ul>
            </li>
            <li v-if="showSystemMenu" class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                Sistema
              </a>
              <ul class="dropdown-menu">
                <li v-if="canViewMonitor"><Link href="/admin/system-monitor" class="dropdown-item" prefetch="hover">Monitor</Link></li>
                <li v-if="canViewQueues && modules.queues !== false"><Link href="/admin/queues" class="dropdown-item" prefetch="hover">Colas</Link></li>
                <li v-if="canViewActivity && modules.activity !== false"><Link href="/admin/activity" class="dropdown-item" prefetch="hover">Actividad</Link></li>
                <li v-if="canViewSecurity && modules['security-events'] !== false"><Link href="/admin/security-events" class="dropdown-item" prefetch="hover">Seguridad</Link></li>
                <li v-if="canViewSystemErrors && modules['system-errors'] !== false"><Link href="/admin/system-errors" class="dropdown-item" prefetch="hover">Errores</Link></li>
                <li v-if="canViewFeatureFlags && modules['feature-flags'] !== false"><Link href="/admin/feature-flags" class="dropdown-item" prefetch="hover">Feature Flags</Link></li>
                <li v-if="canViewAnnouncements && modules.announcements !== false"><Link href="/admin/announcements" class="dropdown-item" prefetch="hover">Anuncios</Link></li>
                <li v-if="canViewAutomations && modules.automations !== false"><Link href="/admin/automations" class="dropdown-item" prefetch="hover">Automatizaciones</Link></li>
                <li v-if="canViewTemplates"><Link href="/admin/message-templates" class="dropdown-item" prefetch="hover">Plantillas</Link></li>
                <li v-if="isSuperAdmin && canViewModules"><Link href="/admin/modules" class="dropdown-item" prefetch="hover">Modulos</Link></li>
                <li v-if="canViewLegalDocuments && modules.legal !== false"><Link href="/admin/legal-documents" class="dropdown-item" prefetch="hover">Legales</Link></li>
                <li><Link href="/admin/locations" class="dropdown-item" prefetch="hover">Ubicaciones</Link></li>
                <li><Link href="/admin/features" class="dropdown-item" prefetch="hover">Caracteristicas</Link></li>
                <li v-if="canViewSettings"><Link href="/admin/settings" class="dropdown-item" prefetch="hover">Settings</Link></li>
              </ul>
            </li>
          </ul>

          <div class="dropdown">
            <button
              class="btn btn-outline-secondary btn-sm dropdown-toggle"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-person"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <Link href="/profile" class="dropdown-item" prefetch="hover">Perfil</Link>
              </li>
              <li v-if="canViewSettings"><Link href="/admin/settings" class="dropdown-item" prefetch="hover">Settings</Link></li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <Link href="/logout" method="post" as="button" class="dropdown-item">
                  Salir
                </Link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <main class="py-4">
      <div class="container-fluid">
        <slot />
      </div>
    </main>

  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useFlashToast } from '@/Composables/useFlashToast'

const page = usePage()
useFlashToast()
const permissions = computed(() => page.props.auth?.permissions || [])
const roles = computed(() => page.props.auth?.roles || [])
const userId = computed(() => page.props.auth?.user?.id)
const modules = computed(() => page.props.modules || {})
const canViewSettings = computed(() => permissions.value.includes('settings.view') || userId.value === 1)
const canViewPlans = computed(() => permissions.value.includes('plans.view') || userId.value === 1)
const canViewActivity = computed(() => permissions.value.includes('activity.view') || userId.value === 1)
const canViewPayments = computed(() => permissions.value.includes('payments.view') || userId.value === 1)
const canViewCoupons = computed(() => permissions.value.includes('coupons.view') || userId.value === 1)
const canViewInvoices = computed(() => permissions.value.includes('invoices.view') || userId.value === 1)

const canViewSubscriptions = computed(() => permissions.value.includes('subscriptions.view') || userId.value === 1)
const canViewInvitations = computed(() => permissions.value.includes('invitations.view') || userId.value === 1)
const canViewSupport = computed(() => permissions.value.includes('support.view') || userId.value === 1)
const canViewHelp = computed(() => permissions.value.includes('help.view') || userId.value === 1)
const canViewReports = computed(() => permissions.value.includes('reports.view') || userId.value === 1)
const canViewExports = computed(() => permissions.value.includes('exports.view') || userId.value === 1)
const canViewSystemErrors = computed(() => permissions.value.includes('system-errors.view') || userId.value === 1)
const canViewApiKeys = computed(() => permissions.value.includes('api-keys.view') || userId.value === 1)
const canViewWebhooks = computed(() => permissions.value.includes('webhooks.view') || userId.value === 1)
const canViewQueues = computed(() => permissions.value.includes('queues.view') || userId.value === 1)
const canViewFeatureFlags = computed(() => permissions.value.includes('feature-flags.view') || userId.value === 1)
const canViewAnnouncements = computed(() => permissions.value.includes('announcements.view') || userId.value === 1)
const canViewAutomations = computed(() => permissions.value.includes('automations.view') || userId.value === 1)
const canViewTemplates = computed(() => permissions.value.includes('templates.view') || userId.value === 1)
const canViewMonitor = computed(() => permissions.value.includes('reports.view') || userId.value === 1)
const canViewSecurity = computed(() => permissions.value.includes('security-events.view') || userId.value === 1)
const canViewLegalDocuments = computed(() => permissions.value.includes('legal-documents.view') || userId.value === 1)
const canViewModules = computed(() => permissions.value.includes('modules.view') || userId.value === 1)
const isSuperAdmin = computed(() => roles.value.includes('superadmin') || userId.value === 1)

const showBillingMenu = computed(() =>
  modules.value.billing !== false &&
  (canViewPlans.value || canViewSubscriptions.value || canViewCoupons.value || canViewPayments.value || canViewInvoices.value)
)
const showSupportMenu = computed(() => modules.value.support !== false && (canViewSupport.value || canViewHelp.value))
const showDataMenu = computed(() => modules.value.exports !== false && (canViewReports.value || canViewExports.value))
const showIntegrationsMenu = computed(() =>
  modules.value.integrations !== false &&
  ((modules.value.api !== false && canViewApiKeys.value) || (modules.value.webhooks !== false && canViewWebhooks.value))
)
const showSystemMenu = computed(() =>
  canViewMonitor.value ||
  (canViewQueues.value && modules.value.queues !== false) ||
  (canViewActivity.value && modules.value.activity !== false) ||
  (canViewSecurity.value && modules['security-events'] !== false) ||
  (canViewSystemErrors.value && modules['system-errors'] !== false) ||
  (canViewFeatureFlags.value && modules['feature-flags'] !== false) ||
  (canViewAnnouncements.value && modules.announcements !== false) ||
  (canViewAutomations.value && modules.automations !== false) ||
  canViewTemplates.value ||
  (isSuperAdmin.value && canViewModules.value) ||
  (canViewLegalDocuments.value && modules.legal !== false) ||
  canViewSettings.value
)
</script>
