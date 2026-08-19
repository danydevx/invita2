<template>
  <div class="min-vh-100 bg-body-tertiary d-flex align-items-center">
    <Head title="Verificar codigo" />

    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <h1 class="h4 mb-2">Verifica tu codigo</h1>
              <p class="text-muted mb-4">
                Ingresa el codigo de 4 digitos que enviamos a tu correo.
              </p>

              <div v-if="flashError" class="alert alert-danger">
                {{ flashError }}
              </div>

              <form @submit.prevent="submit">
                <div class="mb-3">
                  <div class="form-floating">
                    <input
                      id="reset-code"
                      v-model="form.code"
                      type="text"
                      inputmode="numeric"
                      class="form-control"
                      placeholder="0000"
                      :class="{ 'is-invalid': form.errors.code }"
                      maxlength="4"
                      required
                    />
                    <label for="reset-code">Codigo</label>
                    <div v-if="form.errors.code" class="invalid-feedback">
                      {{ form.errors.code }}
                    </div>
                  </div>
                </div>

                <button class="btn btn-primary w-100" type="submit" :disabled="form.processing">
                  {{ form.processing ? 'Validando...' : 'Continuar' }}
                </button>
              </form>

              <div class="mt-3 text-center">
                <Link href="/forgot-password" class="text-decoration-none">Solicitar otro codigo</Link>
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
  code: '',
})

const flashError = computed(() => page.props.flash?.error)

const submit = () => {
  form.post(`/reset-password/${props.token}/verify-code`, {
    preserveScroll: true,
  })
}
</script>
