<template>
  <div class="min-vh-100 bg-body-tertiary d-flex align-items-center">
    <Head title="Recuperar password" />

    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <h1 class="h4 mb-2">Recuperar password</h1>
              <p class="text-muted mb-4">Ingresa tu correo para recibir el enlace y el codigo.</p>

              <div v-if="flashSuccess" class="alert alert-success">
                {{ flashSuccess }}
              </div>

              <div v-if="flashError" class="alert alert-danger">
                {{ flashError }}
              </div>

              <form @submit.prevent="submit">
                <div class="mb-3">
                  <div class="form-floating">
                    <input
                      id="forgot-email"
                      v-model="form.email"
                      type="email"
                      class="form-control"
                      placeholder="correo@empresa.com"
                      autocomplete="email"
                      :class="{ 'is-invalid': form.errors.email }"
                      required
                    />
                    <label for="forgot-email">Email</label>
                    <div v-if="form.errors.email" class="invalid-feedback">
                      {{ form.errors.email }}
                    </div>
                  </div>
                </div>

                <button class="btn btn-primary w-100" type="submit" :disabled="form.processing">
                  {{ form.processing ? 'Enviando...' : 'Enviar enlace' }}
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

const page = usePage()
const form = useForm({
  email: '',
})

const flashSuccess = computed(() => page.props.flash?.success)
const flashError = computed(() => page.props.flash?.error)

const submit = () => {
  form.post('/forgot-password', {
    preserveScroll: true,
  })
}
</script>
