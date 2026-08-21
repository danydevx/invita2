<template>
  <AdminLayout>
    <Head title="Settings" />

    <PageHeader :title="'Settings'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <ul class="nav nav-tabs mb-4" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link" :class="{ active: activeTab === 'general' }" @click="activeTab = 'general'" type="button">
          <i class="bi bi-building me-1"></i>General
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" :class="{ active: activeTab === 'branding' }" @click="activeTab = 'branding'" type="button">
          <i class="bi bi-palette me-1"></i>Branding
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" :class="{ active: activeTab === 'auth' }" @click="activeTab = 'auth'" type="button">
          <i class="bi bi-shield-lock me-1"></i>Auth
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" :class="{ active: activeTab === 'system' }" @click="activeTab = 'system'" type="button">
          <i class="bi bi-gear me-1"></i>Sistema
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" :class="{ active: activeTab === 'billing' }" @click="activeTab = 'billing'" type="button">
          <i class="bi bi-credit-card me-1"></i>Billing
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" :class="{ active: activeTab === 'features' }" @click="activeTab = 'features'" type="button">
          <i class="bi bi-grid me-1"></i>Features
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" :class="{ active: activeTab === 'email' }" @click="activeTab = 'email'" type="button">
          <i class="bi bi-envelope me-1"></i>Email
        </button>
      </li>
    </ul>

    <div class="row g-3">
      <div v-show="activeTab === 'general'" class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Informacion general</h2>
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldText id="app-name" label="Nombre de la app" placeholder="Mi SaaS" v-model="form.app.name" :formError="form.errors['app.name']" required />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="app-short-name" label="Nombre corto" placeholder="SaaS" v-model="form.app.short_name" :formError="form.errors['app.short_name']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldEmail id="app-email" label="Email" placeholder="contacto@empresa.com" v-model="form.app.email" :formError="form.errors['app.email']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="app-phone" label="Telefono" placeholder="+52 555 000 0000" v-model="form.app.phone" :formError="form.errors['app.phone']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldUrl id="app-website" label="Website" placeholder="https://mi-saas.com" v-model="form.app.website" :formError="form.errors['app.website']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="app-address" label="Direccion" placeholder="Direccion" v-model="form.app.address" :formError="form.errors['app.address']" />
              </div>
              <div class="col-12">
                <FieldTextarea id="app-description" label="Descripcion" placeholder="Describe la app" v-model="form.app.description" :formError="form.errors['app.description']" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-show="activeTab === 'branding'" class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Branding</h2>
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldImage id="branding-logo" label="Logo" :initialPreview="form.branding.logo ? '/storage/' + form.branding.logo : null" v-model="form.branding.logo_file" :formError="form.errors['branding.logo']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="branding-favicon" label="Favicon (URL)" placeholder="https://.../favicon.png" v-model="form.branding.favicon" :formError="form.errors['branding.favicon']" />
              </div>
              <div class="col-6">
                <FieldText id="branding-primary" label="Color primario" placeholder="#2563eb" v-model="form.branding.primary_color" :formError="form.errors['branding.primary_color']" />
              </div>
              <div class="col-6">
                <FieldText id="branding-secondary" label="Color secundario" placeholder="#0f172a" v-model="form.branding.secondary_color" :formError="form.errors['branding.secondary_color']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="branding-footer" label="Texto de footer" placeholder="Gracias por confiar en nosotros" v-model="form.branding.footer_text" :formError="form.errors['branding.footer_text']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="branding-auth" label="Imagen auth (URL)" placeholder="https://.../auth.jpg" v-model="form.branding.auth_image" :formError="form.errors['branding.auth_image']" />
              </div>
              <div class="col-12">
                <FieldText id="branding-tagline" label="Tagline" placeholder="Tu SaaS en un solo lugar" v-model="form.branding.system_tagline" :formError="form.errors['branding.system_tagline']" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-show="activeTab === 'auth'" class="col-12">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <h2 class="h6 mb-3">Registro y acceso</h2>
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldSwitch id="auth-allow-registration" label="Permitir registro" v-model="form.auth.allow_registration" :formError="form.errors['auth.allow_registration']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch id="auth-require-email" label="Requiere verificacion de email" v-model="form.auth.require_email_verification" :formError="form.errors['auth.require_email_verification']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch id="auth-require-approval" label="Requiere aprobacion admin" v-model="form.auth.require_admin_approval" :formError="form.errors['auth.require_admin_approval']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch id="auth-login-email" label="Login solo con email" v-model="form.auth.login_with_email_only" :formError="form.errors['auth.login_with_email_only']" />
              </div>
              <div class="col-12 col-md-4">
                <FieldNumber id="auth-password-length" label="Minimo password" v-model="form.auth.password_min_length" :formError="form.errors['auth.password_min_length']" :min="6" :max="32" required />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch id="auth-password-letters" label="Requiere letras" v-model="form.auth.password_require_letters" :formError="form.errors['auth.password_require_letters']" />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch id="auth-password-numbers" label="Requiere numeros" v-model="form.auth.password_require_numbers" :formError="form.errors['auth.password_require_numbers']" />
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">AI APIs</h2>
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldText id="auth-openai-key" label="Open AI API Key" v-model="form.auth.openai_key" :formError="form.errors['auth.openai_key']" type="password" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="auth-minimax-key" label="Minimax API Key" v-model="form.auth.minimax_key" :formError="form.errors['auth.minimax_key']" type="password" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="auth-claude-key" label="Claude API Key" v-model="form.auth.claude_key" :formError="form.errors['auth.claude_key']" type="password" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="auth-gemini-key" label="Gemini API Key" v-model="form.auth.gemini_key" :formError="form.errors['auth.gemini_key']" type="password" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-show="activeTab === 'system'" class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Sistema</h2>
            <div class="row g-3">
              <div class="col-12 col-md-4">
                <FieldNumber id="system-default-pagination" label="Paginacion por defecto" placeholder="10" v-model="form.system.default_pagination" :formError="form.errors['system.default_pagination']" :min="5" :max="100" required />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch id="system-maintenance" label="Modo mantenimiento" v-model="form.system.maintenance_mode" :formError="form.errors['system.maintenance_mode']" />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch id="system-activity-log" label="Activity log" v-model="form.system.enable_activity_log" :formError="form.errors['system.enable_activity_log']" />
              </div>
              <div class="col-12 col-md-6">
                <FieldTextarea id="system-maintenance-message" label="Mensaje mantenimiento" v-model="form.system.maintenance_message" :formError="form.errors['system.maintenance_message']" />
              </div>
              <div class="col-12 col-md-3">
                <FieldSwitch id="system-errors" label="System errors" v-model="form.system.enable_system_errors" :formError="form.errors['system.enable_system_errors']" />
              </div>
              <div class="col-12 col-md-3">
                <FieldSwitch id="system-notifications" label="Notificaciones internas" v-model="form.system.enable_internal_notifications" :formError="form.errors['system.enable_internal_notifications']" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-show="activeTab === 'billing'" class="col-12">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <h2 class="h6 mb-3">Configuracion general</h2>
            <div class="row g-3">
              <div class="col-12 col-md-4">
                <FieldSwitch id="billing-enabled" label="Billing habilitado" v-model="form.billing.enabled" :formError="form.errors['billing.enabled']" />
              </div>
              <div class="col-12 col-md-4">
                <FieldSelect id="billing-default-plan" label="Plan por defecto" v-model="form.billing.default_plan_id" :options="planOptions" />
              </div>
              <div class="col-12 col-md-4">
                <FieldNumber id="billing-trial-days" label="Dias trial" v-model="form.billing.trial_days" :formError="form.errors['billing.trial_days']" :min="0" :max="365" />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch id="billing-plan-changes" label="Permitir cambios de plan" v-model="form.billing.allow_plan_changes" :formError="form.errors['billing.allow_plan_changes']" />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch id="billing-allow-cancel" label="Permitir cancelaciones" v-model="form.billing.allow_cancellations" :formError="form.errors['billing.allow_cancellations']" />
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <h2 class="h6 mb-3">Pasarelas de pago</h2>
            <div class="row g-3 mb-3">
              <div class="col-12 col-md-4">
                <FieldSwitch id="billing-stripe-enabled" label="Stripe" v-model="form.billing.stripe_enabled" :formError="form.errors['billing.stripe_enabled']" />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch id="billing-paypal-enabled" label="PayPal" v-model="form.billing.paypal_enabled" :formError="form.errors['billing.paypal_enabled']" />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch id="billing-mercadopago-enabled" label="Mercado Pago" v-model="form.billing.mercadopago_enabled" :formError="form.errors['billing.mercadopago_enabled']" />
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-12 col-md-4">
                <FieldSelect id="billing-stripe-mode" label="Modo Stripe" v-model="form.billing.stripe_mode" :options="modeOptions" :formError="form.errors['billing.stripe_mode']" :readonly="!form.billing.stripe_enabled" />
              </div>
              <div class="col-12 col-md-4">
                <FieldSelect id="billing-paypal-mode" label="Modo PayPal" v-model="form.billing.paypal_mode" :options="modeOptions" :formError="form.errors['billing.paypal_mode']" :readonly="!form.billing.paypal_enabled" />
              </div>
              <div class="col-12 col-md-4">
                <FieldSelect id="billing-mercadopago-mode" label="Modo Mercado Pago" v-model="form.billing.mercadopago_mode" :options="modeOptions" :formError="form.errors['billing.mercadopago_mode']" :readonly="!form.billing.mercadopago_enabled" />
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-12 col-md-6">
                <FieldText id="billing-stripe-key" label="Stripe Key" v-model="form.billing.stripe_key" :formError="form.errors['billing.stripe_key']" type="password" :readonly="!form.billing.stripe_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="billing-stripe-secret" label="Stripe Secret" v-model="form.billing.stripe_secret" :formError="form.errors['billing.stripe_secret']" type="password" :readonly="!form.billing.stripe_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="billing-paypal-client-id" label="PayPal Client ID" v-model="form.billing.paypal_client_id" :formError="form.errors['billing.paypal_client_id']" type="password" :readonly="!form.billing.paypal_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="billing-paypal-secret" label="PayPal Secret" v-model="form.billing.paypal_secret" :formError="form.errors['billing.paypal_secret']" type="password" :readonly="!form.billing.paypal_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="billing-mercadopago-public-key" label="Mercado Pago Public Key" v-model="form.billing.mercadopago_public_key" :formError="form.errors['billing.mercadopago_public_key']" type="password" :readonly="!form.billing.mercadopago_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldText id="billing-mercadopago-access-token" label="Mercado Pago Access Token" v-model="form.billing.mercadopago_access_token" :formError="form.errors['billing.mercadopago_access_token']" type="password" :readonly="!form.billing.mercadopago_enabled" />
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Pago manual</h2>
            <div class="row g-3">
              <div class="col-12 col-md-4">
                <FieldSwitch id="billing-manual-enabled" label="Activo" v-model="form.billing.manual_enabled" :formError="form.errors['billing.manual_enabled']" />
              </div>
              <div class="col-12">
                <FieldTextarea id="billing-manual-payment-guide" label="Guia de pago" v-model="form.billing.manual_payment_guide" :formError="form.errors['billing.manual_payment_guide']" placeholder="Instrucciones para realizar el pago manual..." :readonly="!form.billing.manual_enabled" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-show="activeTab === 'features'" class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Features globales</h2>
            <div class="row g-3">
              <div class="col-12 col-md-6 col-lg-4">
                <FieldSwitch id="feature-api" label="API" v-model="form.features.api_enabled" />
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <FieldSwitch id="feature-webhooks" label="Webhooks" v-model="form.features.webhooks_enabled" />
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <FieldSwitch id="feature-support" label="Soporte" v-model="form.features.support_enabled" />
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <FieldSwitch id="feature-help" label="Help center" v-model="form.features.help_center_enabled" />
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <FieldSwitch id="feature-exports" label="Exportaciones" v-model="form.features.exports_enabled" />
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <FieldSwitch id="feature-billing" label="Billing member" v-model="form.features.member_billing_enabled" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-show="activeTab === 'email'" class="col-12">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <h2 class="h6 mb-3">Configuracion SMTP</h2>
            <fieldset>
              <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldSelect id="mail-protocol" label="Protocolo" v-model="form.mail.protocol" :options="protocolOptions" :formError="form.errors['mail.protocol']" />
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldSelect id="mail-encryption" label="Encriptacion" v-model="form.mail.encryption" :options="encryptionOptions" :formError="form.errors['mail.encryption']" />
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldText id="mail-host" label="Host" placeholder="smtp.gmail.com" v-model="form.mail.host" :formError="form.errors['mail.host']" />
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldNumber id="mail-port" label="Puerto" placeholder="587" v-model="form.mail.port" :formError="form.errors['mail.port']" />
                </div>
                <div class="col-12 col-md-6">
                  <FieldText id="mail-username" label="Usuario" placeholder="tu@email.com" v-model="form.mail.username" :formError="form.errors['mail.username']" />
                </div>
                <div class="col-12 col-md-6">
                  <FieldText id="mail-password" label="Password" v-model="form.mail.password" :formError="form.errors['mail.password']" type="password" />
                </div>
              </div>
            </fieldset>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Remitente</h2>
            <div class="row g-3">
              <div class="col-12 col-md-4">
                <FieldText id="mail-from-name" label="From name" v-model="form.mail.from_name" :formError="form.errors['mail.from_name']" />
              </div>
              <div class="col-12 col-md-4">
                <FieldEmail id="mail-from-address" label="Sender Email Address" v-model="form.mail.from_address" :formError="form.errors['mail.from_address']" />
              </div>
              <div class="col-12 col-md-4">
                <FieldEmail id="mail-reply-to" label="Reply-to" v-model="form.mail.reply_to_address" :formError="form.errors['mail.reply_to_address']" />
              </div>
              <div class="col-12">
                <FieldText id="mail-footer" label="Firma footer" v-model="form.mail.footer_signature" :formError="form.errors['mail.footer_signature']" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <button type="button" class="btn btn-primary" :disabled="form.processing" @click="submit">
          {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'

const activeTab = ref('general')

const props = defineProps({
  settings: {
    type: Object,
    required: true,
  },
  plans: {
    type: Array,
    default: () => [],
  },
})

const breadcrumbs = [
  { label: 'Settings' },
]

const form = useForm({
  app: {
    name: props.settings.app.name || '',
    short_name: props.settings.app.short_name || '',
    email: props.settings.app.email || '',
    phone: props.settings.app.phone || '',
    website: props.settings.app.website || '',
    address: props.settings.app.address || '',
    description: props.settings.app.description || '',
  },
  branding: {
    logo: props.settings.branding.logo || '',
    logo_file: null,
    favicon: props.settings.branding.favicon || '',
    primary_color: props.settings.branding.primary_color || '',
    secondary_color: props.settings.branding.secondary_color || '',
    footer_text: props.settings.branding.footer_text || '',
    auth_image: props.settings.branding.auth_image || '',
    system_tagline: props.settings.branding.system_tagline || '',
  },
  auth: {
    allow_registration: !!props.settings.auth.allow_registration,
    require_email_verification: !!props.settings.auth.require_email_verification,
    require_admin_approval: !!props.settings.auth.require_admin_approval,
    login_with_email_only: !!props.settings.auth.login_with_email_only,
    password_min_length: props.settings.auth.password_min_length ?? 8,
    password_require_letters: !!props.settings.auth.password_require_letters,
    password_require_numbers: !!props.settings.auth.password_require_numbers,
    openai_key: props.settings.auth.openai_key || '',
    minimax_key: props.settings.auth.minimax_key || '',
    claude_key: props.settings.auth.claude_key || '',
    gemini_key: props.settings.auth.gemini_key || '',
  },
  system: {
    default_pagination: props.settings.system.default_pagination ?? 10,
    maintenance_mode: !!props.settings.system.maintenance_mode,
    maintenance_message: props.settings.system.maintenance_message || '',
    enable_activity_log: !!props.settings.system.enable_activity_log,
    enable_system_errors: !!props.settings.system.enable_system_errors,
    enable_internal_notifications: !!props.settings.system.enable_internal_notifications,
  },
  billing: {
    enabled: !!props.settings.billing.enabled,
    default_plan_id: props.settings.billing.default_plan_id || '',
    trial_days: props.settings.billing.trial_days ?? 0,
    allow_plan_changes: !!props.settings.billing.allow_plan_changes,
    allow_cancellations: !!props.settings.billing.allow_cancellations,
    stripe_enabled: !!props.settings.billing.stripe_enabled,
    stripe_key: props.settings.billing.stripe_key || '',
    stripe_secret: props.settings.billing.stripe_secret || '',
    stripe_mode: props.settings.billing.stripe_mode || 'sandbox',
    paypal_enabled: !!props.settings.billing.paypal_enabled,
    paypal_client_id: props.settings.billing.paypal_client_id || '',
    paypal_secret: props.settings.billing.paypal_secret || '',
    paypal_mode: props.settings.billing.paypal_mode || 'sandbox',
    mercadopago_enabled: !!props.settings.billing.mercadopago_enabled,
    mercadopago_public_key: props.settings.billing.mercadopago_public_key || '',
    mercadopago_access_token: props.settings.billing.mercadopago_access_token || '',
    mercadopago_mode: props.settings.billing.mercadopago_mode || 'sandbox',
    manual_enabled: !!props.settings.billing.manual_enabled,
    manual_payment_guide: props.settings.billing.manual_payment_guide || '',
  },
  features: {
    api_enabled: !!props.settings.features.api_enabled,
    webhooks_enabled: !!props.settings.features.webhooks_enabled,
    support_enabled: !!props.settings.features.support_enabled,
    help_center_enabled: !!props.settings.features.help_center_enabled,
    exports_enabled: !!props.settings.features.exports_enabled,
    member_billing_enabled: !!props.settings.features.member_billing_enabled,
  },
  mail: {
    protocol: props.settings.mail.protocol || 'smtp',
    encryption: props.settings.mail.encryption || 'tls',
    host: props.settings.mail.host || '',
    port: props.settings.mail.port || 587,
    username: props.settings.mail.username || '',
    password: props.settings.mail.password || '',
    from_name: props.settings.mail.from_name || '',
    from_address: props.settings.mail.from_address || '',
    reply_to_address: props.settings.mail.reply_to_address || '',
    footer_signature: props.settings.mail.footer_signature || '',
  },
})

const planOptions = computed(() => [{ value: '', label: 'Sin plan' }, ...props.plans])

const modeOptions = [
  { value: 'sandbox', label: 'Sandbox' },
  { value: 'production', label: 'Produccion' },
]

const protocolOptions = [
  { value: 'smtp', label: 'SMTP' },
]

const encryptionOptions = [
  { value: 'tls', label: 'TLS' },
  { value: 'ssl', label: 'SSL' },
]

const submit = () => {
  form.post('/admin/settings', {
    _method: 'put',
    preserveScroll: true,
    multipart: true,
  })
}
</script>
