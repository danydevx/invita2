<template>
  <div class="min-vh-100 bg-body-tertiary d-flex align-items-center">
    <Head title="Documentos legales" />

    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <h1 class="h4 mb-2">Documentos legales</h1>
              <p class="text-muted mb-4">Debes aceptar estos documentos para continuar.</p>

              <div v-if="form.errors.documents || flashError" class="alert alert-danger">
                {{ form.errors.documents || flashError }}
              </div>

              <div v-for="doc in documents" :key="doc.id" class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 mb-0">{{ doc.title }}</h2>
                    <span class="badge text-bg-light">v{{ doc.version }}</span>
                  </div>
                  <div class="bg-light border rounded p-3" style="white-space: pre-wrap">
                    {{ doc.content }}
                  </div>
                  <div class="form-check mt-3">
                    <input
                      :id="`doc-${doc.id}`"
                      v-model="form.documents"
                      class="form-check-input"
                      type="checkbox"
                      :value="doc.id"
                    />
                    <label class="form-check-label" :for="`doc-${doc.id}`">
                      Acepto {{ doc.title }}
                    </label>
                  </div>
                </div>
              </div>

              <button class="btn btn-primary w-100" type="button" :disabled="!allChecked || form.processing" @click="submit">
                {{ form.processing ? 'Enviando...' : 'Aceptar y continuar' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
  documents: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const form = useForm({
  documents: [],
})

const flashError = computed(() => page.props.flash?.error)
const allChecked = computed(() => form.documents.length === props.documents.length && props.documents.length > 0)

const submit = () => {
  form.post('/legal/accept')
}
</script>
