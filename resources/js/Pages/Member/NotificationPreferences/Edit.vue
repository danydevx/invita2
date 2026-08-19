<template>
  <MemberLayout>
    <Head title="Preferencias de notificacion" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Preferencias de notificacion</h1>
        <p class="text-muted mb-0">Elige que categorias quieres recibir y por que canal.</p>
      </div>
      <Link href="/member/notifications" class="btn btn-outline-secondary btn-sm">Ver notificaciones</Link>
    </div>

    <div v-if="flashSuccess" class="alert alert-success">
      {{ flashSuccess }}
    </div>
    <div v-if="form.errors.preferences" class="alert alert-danger">
      {{ form.errors.preferences }}
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Categoria</th>
              <th scope="col">Descripcion</th>
              <th scope="col" class="text-center">In-app</th>
              <th scope="col" class="text-center">Email</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(category, index) in categories" :key="category.category">
              <td class="fw-semibold">{{ category.label }}</td>
              <td class="text-muted">{{ category.description }}</td>
              <td class="text-center">
                <div class="form-check form-switch d-inline-flex align-items-center">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    :id="`inapp-${category.category}`"
                    v-model="form.preferences[index].in_app_enabled"
                    :disabled="category.required_in_app"
                  />
                  <label class="form-check-label ms-2" :for="`inapp-${category.category}`">
                    {{ category.required_in_app ? 'Obligatorio' : '' }}
                  </label>
                </div>
              </td>
              <td class="text-center">
                <div class="form-check form-switch d-inline-flex align-items-center">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    :id="`email-${category.category}`"
                    v-model="form.preferences[index].email_enabled"
                    :disabled="category.required_email"
                  />
                  <label class="form-check-label ms-2" :for="`email-${category.category}`">
                    {{ category.required_email ? 'Obligatorio' : '' }}
                  </label>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="text-muted small">
          Algunas notificaciones de seguridad no pueden desactivarse.
        </div>
        <button class="btn btn-primary" type="button" :disabled="form.processing" @click="submit">
          {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
        </button>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success)
const categories = computed(() => props.categories)

const form = useForm({
  preferences: categories.value.map((category) => ({
    category: category.category,
    in_app_enabled: !!category.in_app_enabled,
    email_enabled: !!category.email_enabled,
  })),
})

const submit = () => {
  form.put('/member/notification-preferences')
}
</script>
