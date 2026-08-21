<template>
  <MemberLayout>
    <Head :title="`${listing.name} - Nuevo Preset`" />
    <PageHeader
      title="Nuevo Preset"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing.id}/ai-chatbot/presets`" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
      </template>
    </PageHeader>

      <div v-if="$page.props.errors && Object.keys($page.props.errors).length" class="alert alert-danger">
        <ul class="mb-0">
          <li v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</li>
        </ul>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0">Información General</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Nombre del Preset *</label>
                  <input v-model="form.name" type="text" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Descripción</label>
                  <textarea v-model="form.description" class="form-control" rows="2"></textarea>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Personalidad *</label>
                    <select v-model="form.personality" class="form-select" required>
                      <option v-for="p in personalities" :key="p.key" :value="p.key">
                        {{ p.display_name }}
                      </option>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Idioma *</label>
                    <select v-model="form.language" class="form-select" required>
                      <option v-for="lang in languages" :key="lang" :value="lang">
                        {{ lang.toUpperCase() }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0">Mensajes</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Nombre del Chatbot</label>
                  <input v-model="form.chatbot_name_template" type="text" class="form-control" />
                </div>

                <div class="mb-3">
                  <label class="form-label">Mensaje de Bienvenida</label>
                  <textarea v-model="form.greeting_message" class="form-control" rows="2"></textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label">Mensaje de Fallback</label>
                  <textarea v-model="form.fallback_message" class="form-control" rows="2"></textarea>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0">System Prompt</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Plantilla de System Prompt *</label>
                  <textarea v-model="form.system_prompt_template" class="form-control" rows="8" required></textarea>
                  <small class="text-muted">
                    Usa {business_name} como placeholder para el nombre del negocio.
                  </small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0">Sugerencias Iniciales</h5>
              </div>
              <div class="card-body">
                <div
                  v-for="(suggestion, index) in form.initial_suggestions"
                  :key="index"
                  class="input-group mb-2"
                >
                  <input
                    v-model="form.initial_suggestions[index]"
                    type="text"
                    class="form-control"
                    placeholder="Sugerencia..."
                  />
                  <button
                    type="button"
                    class="btn btn-outline-danger"
                    @click="removeSuggestion(index)"
                  >
                    <i class="bi bi-x"></i>
                  </button>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm w-100" @click="addSuggestion">
                  <i class="bi bi-plus-lg me-1"></i>Agregar Sugerencia
                </button>
              </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0">Contextos RAG</h5>
              </div>
              <div class="card-body">
                <small class="text-muted d-block mb-2">
                  Selecciona los contextos que este preset usará para buscar información relevante (RAG).
                </small>
                <div v-if="contexts && contexts.length > 0">
                  <div
                    v-for="context in contexts"
                    :key="context.id"
                    class="form-check"
                  >
                    <input
                      :id="'context-' + context.id"
                      v-model="form.context_ids"
                      class="form-check-input"
                      type="checkbox"
                      :value="context.id"
                    />
                    <label class="form-check-label" :for="'context-' + context.id">
                      {{ context.title }}
                    </label>
                  </div>
                </div>
                <small v-else class="text-muted">
                  No hay contextos disponibles. Crea contextos en la sección de Configuración.
                </small>
              </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
              <div class="card-body">
                <div class="form-check form-switch">
                  <input v-model="form.is_active" class="form-check-input" type="checkbox" />
                  <label class="form-check-label">Activo</label>
                </div>
              </div>
            </div>

            <FormActions
              :submitText="'Crear Preset'"
              :submittingText="'Guardando...'"
              :cancelHref="`/member/listings/${listing.id}/ai-chatbot/presets`"
              :sending="saving"
            />
          </div>
        </div>
      </form>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = page.props.listing
const personalities = page.props.personalities || []
const languages = page.props.languages || ['es', 'en', 'pt', 'fr']
const contexts = page.props.contexts || []

const breadcrumbs = computed(() => [
  { label: 'Mis Negocios', href: '/member/business-modules' },
   { label: listing?.name || 'Negocio', href: `/member/listings/${listing?.id}/edit` },
   { label: 'AI Chatbot', href: `/member/listings/${listing?.id}/ai-chatbot` },
   { label: 'Presets', href: `/member/listings/${listing?.id}/ai-chatbot/presets` },
  { label: 'Nuevo Preset', active: true },
])

const saving = ref(false)

const form = reactive({
  name: '',
  description: '',
  personality: 'friendly',
  language: 'es',
  chatbot_name_template: '',
  greeting_message: '',
  fallback_message: '',
  system_prompt_template: '',
  initial_suggestions: ['', '', ''],
  context_ids: [],
  is_active: true,
})

const addSuggestion = () => {
  form.initial_suggestions.push('')
}

const removeSuggestion = (index) => {
  form.initial_suggestions.splice(index, 1)
}

const submit = () => {
  saving.value = true

  const data = {
    ...form,
    initial_suggestions: form.initial_suggestions.filter(s => s.trim() !== ''),
  }

  router.post(`/member/listings/${listing.id}/ai-chatbot/presets`, data, {
    onFinish: () => {
      saving.value = false
    },
  })
}
</script>
