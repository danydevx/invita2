<template>
  <AdminLayout>
    <Head title="Settings" />

    <PageHeader :title="'Settings'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">General</h2>

            <div class="row g-3">
              <div class="col-12">
                <FieldText
                  id="app-name"
                  label="Nombre de la app"
                  placeholder="Mi SaaS"
                  v-model="form.app.name"
                  :formError="form.errors['app.name']"
                  required
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="app-short-name"
                  label="Nombre corto"
                  placeholder="SaaS"
                  v-model="form.app.short_name"
                  :formError="form.errors['app.short_name']"
                />
              </div>
              <div class="col-12">
                <FieldEmail
                  id="app-email"
                  label="Email"
                  placeholder="contacto@empresa.com"
                  v-model="form.app.email"
                  :formError="form.errors['app.email']"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="app-phone"
                  label="Telefono"
                  placeholder="+52 555 000 0000"
                  v-model="form.app.phone"
                  :formError="form.errors['app.phone']"
                />
              </div>
              <div class="col-12">
                <FieldUrl
                  id="app-website"
                  label="Website"
                  placeholder="https://mi-saas.com"
                  v-model="form.app.website"
                  :formError="form.errors['app.website']"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="app-address"
                  label="Direccion"
                  placeholder="Direccion"
                  v-model="form.app.address"
                  :formError="form.errors['app.address']"
                />
              </div>
              <div class="col-12">
                <FieldTextarea
                  id="app-description"
                  label="Descripcion"
                  placeholder="Describe la app"
                  v-model="form.app.description"
                  :formError="form.errors['app.description']"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Branding</h2>

            <div class="row g-3">
              <div class="col-12">
                <FieldText
                  id="branding-logo"
                  label="Logo (URL)"
                  placeholder="https://.../logo.png"
                  v-model="form.branding.logo"
                  :formError="form.errors['branding.logo']"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="branding-favicon"
                  label="Favicon (URL)"
                  placeholder="https://.../favicon.png"
                  v-model="form.branding.favicon"
                  :formError="form.errors['branding.favicon']"
                />
              </div>
              <div class="col-6">
                <FieldText
                  id="branding-primary"
                  label="Color primario"
                  placeholder="#2563eb"
                  v-model="form.branding.primary_color"
                  :formError="form.errors['branding.primary_color']"
                />
              </div>
              <div class="col-6">
                <FieldText
                  id="branding-secondary"
                  label="Color secundario"
                  placeholder="#0f172a"
                  v-model="form.branding.secondary_color"
                  :formError="form.errors['branding.secondary_color']"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="branding-footer"
                  label="Texto de footer"
                  placeholder="Gracias por confiar en nosotros"
                  v-model="form.branding.footer_text"
                  :formError="form.errors['branding.footer_text']"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="branding-auth"
                  label="Imagen auth (URL)"
                  placeholder="https://.../auth.jpg"
                  v-model="form.branding.auth_image"
                  :formError="form.errors['branding.auth_image']"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="branding-tagline"
                  label="Tagline"
                  placeholder="Tu SaaS en un solo lugar"
                  v-model="form.branding.system_tagline"
                  :formError="form.errors['branding.system_tagline']"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Regional</h2>

            <div class="row g-3">
              <div class="col-12">
                <FieldText
                  id="regional-timezone"
                  label="Timezone"
                  placeholder="America/Mexico_City"
                  v-model="form.regional.timezone"
                  :formError="form.errors['regional.timezone']"
                  required
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="regional-locale"
                  label="Locale"
                  placeholder="es"
                  v-model="form.regional.locale"
                  :formError="form.errors['regional.locale']"
                  required
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="regional-currency"
                  label="Moneda"
                  placeholder="USD"
                  v-model="form.regional.currency"
                  :formError="form.errors['regional.currency']"
                  required
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Registro y acceso</h2>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="auth-allow-registration"
                  label="Permitir registro"
                  v-model="form.auth.allow_registration"
                  :formError="form.errors['auth.allow_registration']"
                />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="auth-require-email"
                  label="Requiere verificacion de email"
                  v-model="form.auth.require_email_verification"
                  :formError="form.errors['auth.require_email_verification']"
                />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="auth-require-approval"
                  label="Requiere aprobacion admin"
                  v-model="form.auth.require_admin_approval"
                  :formError="form.errors['auth.require_admin_approval']"
                />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="auth-login-email"
                  label="Login solo con email"
                  v-model="form.auth.login_with_email_only"
                  :formError="form.errors['auth.login_with_email_only']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldNumber
                  id="auth-password-length"
                  label="Minimo password"
                  v-model="form.auth.password_min_length"
                  :formError="form.errors['auth.password_min_length']"
                  :min="6"
                  :max="32"
                  required
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch
                  id="auth-password-letters"
                  label="Requiere letras"
                  v-model="form.auth.password_require_letters"
                  :formError="form.errors['auth.password_require_letters']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch
                  id="auth-password-numbers"
                  label="Requiere numeros"
                  v-model="form.auth.password_require_numbers"
                  :formError="form.errors['auth.password_require_numbers']"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Sistema</h2>

            <div class="row g-3">
              <div class="col-12 col-md-4">
                <FieldNumber
                  id="system-default-pagination"
                  label="Paginacion por defecto"
                  placeholder="10"
                  v-model="form.system.default_pagination"
                  :formError="form.errors['system.default_pagination']"
                  :min="5"
                  :max="100"
                  required
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch
                  id="system-maintenance"
                  label="Modo mantenimiento"
                  v-model="form.system.maintenance_mode"
                  :formError="form.errors['system.maintenance_mode']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldTextarea
                  id="system-maintenance-message"
                  label="Mensaje mantenimiento"
                  v-model="form.system.maintenance_message"
                  :formError="form.errors['system.maintenance_message']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch
                  id="system-activity-log"
                  label="Activity log"
                  v-model="form.system.enable_activity_log"
                  :formError="form.errors['system.enable_activity_log']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch
                  id="system-errors"
                  label="System errors"
                  v-model="form.system.enable_system_errors"
                  :formError="form.errors['system.enable_system_errors']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch
                  id="system-notifications"
                  label="Notificaciones internas"
                  v-model="form.system.enable_internal_notifications"
                  :formError="form.errors['system.enable_internal_notifications']"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Billing</h2>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="billing-enabled"
                  label="Billing habilitado"
                  v-model="form.billing.enabled"
                  :formError="form.errors['billing.enabled']"
                />
              </div>
              <div class="col-12 col-md-6">
                <FieldSelect
                  id="billing-default-plan"
                  label="Plan por defecto"
                  v-model="form.billing.default_plan_id"
                  :options="planOptions"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldText
                  id="billing-currency"
                  label="Moneda"
                  v-model="form.billing.currency"
                  :formError="form.errors['billing.currency']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldNumber
                  id="billing-trial-days"
                  label="Dias trial"
                  v-model="form.billing.trial_days"
                  :formError="form.errors['billing.trial_days']"
                  :min="0"
                  :max="365"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch
                  id="billing-plan-changes"
                  label="Permitir cambios de plan"
                  v-model="form.billing.allow_plan_changes"
                  :formError="form.errors['billing.allow_plan_changes']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldSwitch
                  id="billing-allow-cancel"
                  label="Permitir cancelaciones"
                  v-model="form.billing.allow_cancellations"
                  :formError="form.errors['billing.allow_cancellations']"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Features globales</h2>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <FieldSwitch id="feature-api" label="API" v-model="form.features.api_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch id="feature-webhooks" label="Webhooks" v-model="form.features.webhooks_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch id="feature-support" label="Soporte" v-model="form.features.support_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch id="feature-help" label="Help center" v-model="form.features.help_center_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch id="feature-exports" label="Exportaciones" v-model="form.features.exports_enabled" />
              </div>
              <div class="col-12 col-md-6">
                <FieldSwitch
                  id="feature-billing"
                  label="Billing member"
                  v-model="form.features.member_billing_enabled"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Email</h2>

            <div class="row g-3">
              <div class="col-12 col-md-4">
                <FieldText
                  id="mail-from-name"
                  label="From name"
                  v-model="form.mail.from_name"
                  :formError="form.errors['mail.from_name']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldEmail
                  id="mail-from-address"
                  label="From address"
                  v-model="form.mail.from_address"
                  :formError="form.errors['mail.from_address']"
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldEmail
                  id="mail-reply-to"
                  label="Reply-to"
                  v-model="form.mail.reply_to_address"
                  :formError="form.errors['mail.reply_to_address']"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="mail-footer"
                  label="Firma footer"
                  v-model="form.mail.footer_signature"
                  :formError="form.errors['mail.footer_signature']"
                />
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
    favicon: props.settings.branding.favicon || '',
    primary_color: props.settings.branding.primary_color || '',
    secondary_color: props.settings.branding.secondary_color || '',
    footer_text: props.settings.branding.footer_text || '',
    auth_image: props.settings.branding.auth_image || '',
    system_tagline: props.settings.branding.system_tagline || '',
  },
  regional: {
    timezone: props.settings.regional.timezone || '',
    locale: props.settings.regional.locale || '',
    currency: props.settings.regional.currency || '',
  },
  auth: {
    allow_registration: !!props.settings.auth.allow_registration,
    require_email_verification: !!props.settings.auth.require_email_verification,
    require_admin_approval: !!props.settings.auth.require_admin_approval,
    login_with_email_only: !!props.settings.auth.login_with_email_only,
    password_min_length: props.settings.auth.password_min_length ?? 8,
    password_require_letters: !!props.settings.auth.password_require_letters,
    password_require_numbers: !!props.settings.auth.password_require_numbers,
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
    currency: props.settings.billing.currency || '',
    trial_days: props.settings.billing.trial_days ?? 0,
    allow_plan_changes: !!props.settings.billing.allow_plan_changes,
    allow_cancellations: !!props.settings.billing.allow_cancellations,
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
    from_name: props.settings.mail.from_name || '',
    from_address: props.settings.mail.from_address || '',
    reply_to_address: props.settings.mail.reply_to_address || '',
    footer_signature: props.settings.mail.footer_signature || '',
  },
})

const planOptions = computed(() => [{ value: '', label: 'Sin plan' }, ...props.plans])

const submit = () => {
  form.put('/admin/settings', {
    preserveScroll: true,
  })
}
</script>
