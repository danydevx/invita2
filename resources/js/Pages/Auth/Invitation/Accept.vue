<template>
  <div class="min-vh-100 bg-body-tertiary d-flex align-items-center">
    <Head title="Invitacion" />

    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5 text-center">
              <h1 class="h4 mb-2">Invitacion</h1>

              <div v-if="status === 'pending'">
                <p class="text-muted">Email: {{ email }}</p>
                <div class="d-grid gap-2">
                  <button class="btn btn-primary" type="button" @click="accept">Aceptar invitacion</button>
                  <Link href="/login" class="btn btn-outline-secondary">Iniciar sesion</Link>
                  <Link :href="`/register?email=${encodeURIComponent(email)}&invite=${token}`" class="btn btn-link">
                    Crear cuenta
                  </Link>
                </div>
              </div>

              <div v-else-if="status === 'expired'" class="text-muted">
                La invitacion ha expirado.
              </div>
              <div v-else-if="status === 'revoked'" class="text-muted">
                La invitacion fue revocada.
              </div>
              <div v-else-if="status === 'accepted'" class="text-muted">
                La invitacion ya fue utilizada.
              </div>
              <div v-else class="text-muted">
                Invitacion invalida.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
  email: {
    type: String,
    default: '',
  },
  token: {
    type: String,
    default: '',
  },
})

const accept = () => {
  router.post(`/invite/${props.token}/accept`)
}
</script>
