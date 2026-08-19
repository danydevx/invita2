<template>
  <div class="min-vh-100 bg-body-tertiary d-flex align-items-center">
    <Head title="Iniciar sesion" />

    <div class="container py-5">
      <div class="row align-items-center g-4 g-lg-5">
        <div class="col-lg-6">
          <span class="badge text-bg-secondary">Mi SaaS</span>
          <h1 class="display-6 fw-semibold mt-3 mb-2">Bienvenido al dashboard</h1>
          <p class="text-muted">
            Accede a indicadores claros, tareas prioritarias y reportes listos para decisiones rapidas.
          </p>

          <div class="d-flex flex-column gap-3 mt-4">
            <div class="d-flex gap-3 align-items-start">
              <i class="bi bi-speedometer2 fs-4 text-primary"></i>
              <div>
                <div class="fw-semibold">Metricas en tiempo real</div>
                <div class="text-muted">Visualiza el estado del negocio en un solo lugar.</div>
              </div>
            </div>
            <div class="d-flex gap-3 align-items-start">
              <i class="bi bi-people fs-4 text-primary"></i>
              <div>
                <div class="fw-semibold">Equipos alineados</div>
                <div class="text-muted">Permisos y roles listos para cada area.</div>
              </div>
            </div>
            <div class="d-flex gap-3 align-items-start">
              <i class="bi bi-shield-check fs-4 text-primary"></i>
              <div>
                <div class="fw-semibold">Acceso confiable</div>
                <div class="text-muted">Entradas seguras con trazabilidad y control.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5 offset-lg-1">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <div class="d-flex align-items-start justify-content-between mb-4">
                <div>
                  <h2 class="h4 mb-1">Iniciar sesion</h2>
                  <p class="text-muted mb-0">Entra para continuar al panel.</p>
                </div>
                <span class="badge text-bg-success">Acceso seguro</span>
              </div>

              <div v-if="flashSuccess" class="alert alert-success">
                {{ flashSuccess }}
              </div>

              <div v-if="flashError" class="alert alert-danger">
                {{ flashError }}
              </div>

              <form @submit.prevent="submit">
                <div class="mb-3">
                  <label class="form-label">Correo</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text">
                      <i class="bi bi-envelope"></i>
                    </span>
                    <input
                      v-model="form.email"
                      type="email"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.email }"
                      placeholder="tu@empresa.com"
                      autocomplete="email"
                      required
                    />
                  </div>
                  <div v-if="form.errors.email" class="invalid-feedback d-block">
                    {{ form.errors.email }}
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Clave</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text">
                      <i class="bi bi-lock"></i>
                    </span>
                    <input
                      v-model="form.password"
                      type="password"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.password }"
                      placeholder="••••••••"
                      autocomplete="current-password"
                      required
                    />
                  </div>
                  <div v-if="form.errors.password" class="invalid-feedback d-block">
                    {{ form.errors.password }}
                  </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                  <div class="form-check">
                    <input
                      id="remember"
                      v-model="form.remember"
                      class="form-check-input"
                      type="checkbox"
                    />
                    <label class="form-check-label" for="remember">Recordarme</label>
                  </div>
                  <Link href="/forgot-password" class="text-decoration-none small">Olvidaste tu clave?</Link>
                </div>

                <button class="btn btn-primary w-100 btn-lg" type="submit" :disabled="form.processing">
                  {{ form.processing ? 'Entrando...' : 'Entrar al dashboard' }}
                </button>
              </form>

              <p class="small text-muted mt-3 mb-0">
                Si no tienes acceso, solicita alta con tu administrador.
              </p>
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
  password: '',
  remember: true,
})

const submit = () => {
  form.post('/login', {
    preserveScroll: true,
    onFinish: () => form.reset('password'),
  })
}

const flashSuccess = computed(() => page.props.flash?.success)
const flashError = computed(() => page.props.flash?.error)
</script>
