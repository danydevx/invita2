<template>
  <MemberLayout>
    <Head title="Preferencias" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Preferencias</h1>
        <p class="text-muted mb-0">Personaliza tu experiencia en el panel.</p>
      </div>
      <Link href="/member/account" class="btn btn-outline-secondary btn-sm">Ver cuenta</Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldSelect
              id="pref-locale"
              label="Idioma"
              v-model="form.locale"
              :options="localeOptions"
              :formError="form.errors.locale"
            />
          </div>
          <div class="col-12 col-md-6">
            <FieldSelect
              id="pref-timezone"
              label="Zona horaria"
              v-model="form.timezone"
              :options="timezoneOptions"
              :formError="form.errors.timezone"
            />
          </div>

          <div class="col-12">
            <FieldSwitch
              id="pref-email"
              label="Notificaciones por email"
              v-model="form.email_notifications"
            />
          </div>
          <div class="col-12">
            <FieldSwitch
              id="pref-system"
              label="Notificaciones internas"
              v-model="form.system_notifications"
            />
          </div>
          <div class="col-12">
            <FieldSwitch
              id="pref-welcome"
              label="Ocultar bienvenida del dashboard"
              v-model="form.dashboard_welcome_dismissed"
            />
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
            <Link href="/member" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  preferences: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  locale: props.preferences.locale || 'es',
  timezone: props.preferences.timezone || 'America/Mexico_City',
  email_notifications: !!props.preferences.email_notifications,
  system_notifications: !!props.preferences.system_notifications,
  dashboard_welcome_dismissed: !!props.preferences.dashboard_welcome_dismissed,
})

const submit = () => {
  form.put('/member/preferences')
}

const localeOptions = [
  { value: 'es', label: 'Espanol' },
  { value: 'en', label: 'English' },
]

const timezoneOptions = [
  { value: 'America/Mexico_City', label: 'Ciudad de Mexico' },
  { value: 'America/Tijuana', label: 'Tijuana' },
  { value: 'America/Hermosillo', label: 'Hermosillo' },
  { value: 'America/Mazatlan', label: 'Mazatlan' },
  { value: 'America/Chihuahua', label: 'Chihuahua' },
  { value: 'America/Ojinaga', label: 'Ojinaga' },
  { value: 'America/Ciudad_Juarez', label: 'Ciudad Juarez' },
  { value: 'America/Monterrey', label: 'Monterrey' },
  { value: 'America/Merida', label: 'Merida' },
  { value: 'America/Cancun', label: 'Cancun' },
]
</script>
