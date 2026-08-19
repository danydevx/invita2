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
            <FieldText
              id="pref-locale"
              label="Idioma"
              v-model="form.locale"
              :formError="form.errors.locale"
              placeholder="es"
            />
          </div>
          <div class="col-12 col-md-6">
            <FieldText
              id="pref-timezone"
              label="Zona horaria"
              v-model="form.timezone"
              :formError="form.errors.timezone"
              placeholder="America/Mexico_City"
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
import FieldText from '@/Components/Fields/FieldText.vue'
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
</script>
