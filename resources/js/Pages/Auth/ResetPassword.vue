<template>
  <div class="min-vh-100 bg-body-tertiary d-flex align-items-center">
    <Head title="Nueva password" />

    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <h1 class="h4 mb-2">Nueva password</h1>
              <p class="text-muted mb-4">Crea una nueva password para tu cuenta.</p>

              <div v-if="flashError" class="alert alert-danger">
                {{ flashError }}
              </div>

              <form @submit.prevent="submit">
                <div class="mb-3">
                  <div class="form-floating">
                    <input
                      id="new-password"
                      v-model="form.password"
                      type="password"
                      class="form-control"
                      placeholder="********"
                      autocomplete="new-password"
                      :class="{ 'is-invalid': form.errors.password }"
                      required
                    />
                    <label for="new-password">Password</label>
                    <div class="form-text">
                      Minimo 8 caracteres, con letras y numeros.
                    </div>
                    <div v-if="form.errors.password" class="invalid-feedback">
                      {{ form.errors.password }}
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <div class="form-floating">
                    <input
                      id="new-password-confirmation"
                      v-model="form.password_confirmation"
                      type="password"
                      class="form-control"
                      placeholder="********"
                      autocomplete="new-password"
                      :class="{ 'is-invalid': form.errors.password_confirmation }"
                      required
                    />
                    <label for="new-password-confirmation">Confirmar password</label>
                    <div v-if="form.errors.password_confirmation" class="invalid-feedback">
                      {{ form.errors.password_confirmation }}
                    </div>
                  </div>
                </div>

                <button class="btn btn-primary w-100" type="submit" :disabled="form.processing">
                  {{ form.processing ? 'Guardando...' : 'Guardar password' }}
                </button>
              </form>

              <div class="mt-3 text-center">
                <Link href="/login" class="text-decoration-none">Volver al login</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
  token: {
    type: String,
    required: true,
  },
})

const page = usePage()
const form = useForm({
  password: '',
  password_confirmation: '',
})

const flashError = computed(() => page.props.flash?.error)

const submit = () => {
  form.post(`/reset-password/${props.token}`, {
    preserveScroll: true,
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>
