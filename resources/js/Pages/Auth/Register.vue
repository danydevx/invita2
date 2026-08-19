<template>
  <div class="min-vh-100 bg-body-tertiary d-flex align-items-center">
    <Head title="Registro" />

    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <h1 class="h4 mb-2">Crear cuenta</h1>
              <p class="text-muted mb-4">Completa tus datos para registrarte.</p>

              <div v-if="flashSuccess" class="alert alert-success">
                {{ flashSuccess }}
              </div>

              <div v-if="registerError" class="alert alert-danger">
                {{ registerError }}
              </div>

              <form class="row g-3" @submit.prevent="submit">
                <div class="col-12">
                  <div class="form-floating">
                    <input
                      id="register-name"
                      v-model="form.name"
                      type="text"
                      class="form-control"
                      placeholder="Tu nombre"
                      autocomplete="name"
                      :class="{ 'is-invalid': form.errors.name }"
                      required
                    />
                    <label for="register-name">Nombre</label>
                    <div v-if="form.errors.name" class="invalid-feedback">
                      {{ form.errors.name }}
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-floating">
                    <input
                      id="register-email"
                      v-model="form.email"
                      type="email"
                      class="form-control"
                      placeholder="correo@empresa.com"
                      autocomplete="email"
                      :class="{ 'is-invalid': form.errors.email }"
                      required
                    />
                    <label for="register-email">Email</label>
                    <div v-if="form.errors.email" class="invalid-feedback">
                      {{ form.errors.email }}
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-floating">
                    <input
                      id="register-password"
                      v-model="form.password"
                      type="password"
                      class="form-control"
                      placeholder="********"
                      autocomplete="new-password"
                      :class="{ 'is-invalid': form.errors.password }"
                      required
                    />
                    <label for="register-password">Password</label>
                    <div class="form-text">
                      Minimo 8 caracteres, con letras y numeros.
                    </div>
                    <div v-if="form.errors.password" class="invalid-feedback">
                      {{ form.errors.password }}
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-floating">
                    <input
                      id="register-password-confirmation"
                      v-model="form.password_confirmation"
                      type="password"
                      class="form-control"
                      placeholder="********"
                      autocomplete="new-password"
                      :class="{ 'is-invalid': form.errors.password_confirmation }"
                      required
                    />
                    <label for="register-password-confirmation">Confirmar password</label>
                    <div v-if="form.errors.password_confirmation" class="invalid-feedback">
                      {{ form.errors.password_confirmation }}
                    </div>
                  </div>
                </div>


                <div class="col-12" aria-hidden="true">
                  <input
                    v-model="form.company"
                    type="text"
                    tabindex="-1"
                    autocomplete="off"
                    class="visually-hidden"
                  />
                  <input v-model="form.form_started_at" type="hidden" />
                </div>

                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creando...' : 'Crear cuenta' }}
                  </button>
                </div>
              </form>

              <div class="mt-3 text-center">
                <Link href="/login" class="text-decoration-none">Ya tienes cuenta? Inicia sesion</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const page = usePage()

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  invite: '',
  company: '',
  form_started_at: 0,
})

const prefill = computed(() => page.props.prefill || {})
const registerError = computed(() => form.errors.register || page.props.flash?.error)
const flashSuccess = computed(() => page.props.flash?.success)

onMounted(() => {
  form.form_started_at = Math.floor(Date.now() / 1000)
  if (prefill.value.email) {
    form.email = prefill.value.email
  }
  if (prefill.value.invite) {
    form.invite = prefill.value.invite
  }
})

const submit = () => {
  form.post('/register', {
    preserveScroll: true,
    onFinish: () => {
      form.reset('password', 'password_confirmation')
    },
  })
}
</script>
