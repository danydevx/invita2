<template>
  <MemberLayout>
    <Head title="Cambiar password" />

    <PageHeader :title="'Cambiar password'" :breadcrumbs="breadcrumbs" backHref="/member" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <div class="form-floating">
              <input
                id="user-email"
                type="email"
                class="form-control"
                :value="$page.props.auth?.user?.email"
                readonly
                placeholder="email"
              />
              <label for="user-email">Email registrado</label>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="form-floating position-relative">
              <input
                id="current-password"
                v-model="form.current_password"
                :type="showCurrent ? 'text' : 'password'"
                class="form-control"
                placeholder="********"
                autocomplete="current-password"
                :class="{ 'is-invalid': form.errors.current_password }"
                required
              />
              <label for="current-password">Password actual</label>
              <button
                type="button"
                class="btn btn-link password-visibility position-absolute"
                :title="showCurrent ? 'Ocultar' : 'Mostrar'"
                @click="showCurrent = !showCurrent"
              >
                <i :class="showCurrent ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
              </button>
              <div v-if="form.errors.current_password" class="invalid-feedback">
                {{ form.errors.current_password }}
              </div>
            </div>
          </div>

          <div class="col-12">
            <FieldGeneratePass
              id="new-password"
              confirm-id="new-password-confirmation"
              label="Nuevo password"
              confirm-label="Confirmar password"
              v-model="form.password"
              v-model:confirmation="form.password_confirmation"
              :form-error="form.errors.password"
              :confirm-form-error="form.errors.password_confirmation"
              :min-length="12"
              :default-length="16"
            />
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldGeneratePass from '@/Components/Fields/FieldGeneratePass.vue'

const breadcrumbs = [
  { label: 'Password' },
]

const showCurrent = ref(false)

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.put('/member/password', {
    preserveScroll: true,
    onFinish: () => {
      form.reset('current_password', 'password', 'password_confirmation')
    },
  })
}
</script>

<style scoped>
.password-visibility {
  right: .5rem;
  top: 50%;
  transform: translateY(-50%);
  z-index: 5;
}
</style>
