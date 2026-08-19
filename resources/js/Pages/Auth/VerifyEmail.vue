<template>
  <div class="min-vh-100 bg-body-tertiary d-flex align-items-center">
    <Head title="Verificar email" />

    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <h1 class="h4 mb-2">Verifica tu email</h1>
              <p class="text-muted mb-4">
                Te enviamos un enlace de verificacion. Revisa tu correo y sigue las instrucciones.
              </p>

              <div v-if="flashSuccess" class="alert alert-success">
                {{ flashSuccess }}
              </div>

              <div v-if="flashError" class="alert alert-danger">
                {{ flashError }}
              </div>

              <form @submit.prevent="resend">
                <button class="btn btn-primary w-100" type="submit" :disabled="form.processing">
                  {{ form.processing ? 'Enviando...' : 'Reenviar correo' }}
                </button>
              </form>

              <div class="mt-3 text-center">
                <Link href="/logout" method="post" as="button" class="btn btn-link p-0">
                  Cerrar sesion
                </Link>
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

const form = useForm({})

const flashSuccess = computed(() => page.props.flash?.success)
const flashError = computed(() => page.props.flash?.error)

const resend = () => {
  form.post('/email/verification-notification', {
    preserveScroll: true,
  })
}
</script>
