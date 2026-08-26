<template>
  <MemberLayout>
    <Head :title="`${listing.name} - Editar Preset`" />
    <PageHeader
      title="Editar Preset"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing.id}/ai-chatbot/presets`" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
      </template>
    </PageHeader>

      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <div v-if="$page.props.errors && Object.keys($page.props.errors).length" class="alert alert-danger">
        <ul class="mb-0">
          <li v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</li>
        </ul>
      </div>

      <form v-if="preset" @submit.prevent="submit">
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
              :submitText="'Actualizar Preset'"
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
import { computed, reactive, ref, watchEffect } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = page.props.listing
const preset = computed(() => page.props.preset)
const personalities = page.props.personalities || []
const languages = page.props.languages || ['es', 'en', 'pt', 'fr']
const contexts = page.props.contexts || []

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Chatbot', href: `/member/listings/${listing?.id}/ai-chatbot` },
  { label: 'Presets', href: `/member/listings/${listing?.id}/ai-chatbot/presets` },
  { label: preset.value?.name || 'Editar', active: true },
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
  initial_suggestions: [],
  context_ids: [],
  is_active: true,
})

const initializeForm = () => {
  if (preset.value) {
    form.name = preset.value.name || ''
    form.description = preset.value.description || ''
    form.personality = preset.value.personality || 'friendly'
    form.language = preset.value.language || 'es'
    form.chatbot_name_template = preset.value.chatbot_name_template || ''
    form.greeting_message = preset.value.greeting_message || ''
    form.fallback_message = preset.value.fallback_message || ''
    form.system_prompt_template = preset.value.system_prompt_template || ''

    const suggestions = preset.value.initial_suggestions
    if (Array.isArray(suggestions)) {
      form.initial_suggestions = suggestions.filter(s => typeof s === 'string')
    } else if (typeof suggestions === 'string') {
      try {
        const parsed = JSON.parse(suggestions)
        form.initial_suggestions = Array.isArray(parsed) ? parsed.filter(s => typeof s === 'string') : ['', '', '']
      } catch {
        form.initial_suggestions = ['', '', '']
      }
    } else {
      form.initial_suggestions = ['', '', '']
    }

    const contextIds = preset.value.context_ids
    if (Array.isArray(contextIds)) {
      form.context_ids = contextIds.filter(c => typeof c === 'string')
    } else if (typeof contextIds === 'string') {
      try {
        const parsed = JSON.parse(contextIds)
        form.context_ids = Array.isArray(parsed) ? parsed.filter(c => typeof c === 'string') : []
      } catch {
        form.context_ids = []
      }
    } else {
      form.context_ids = []
    }

    form.is_active = preset.value.is_active ?? true
  }
}

watchEffect(() => {
  if (preset.value) {
    initializeForm()
  }
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

  router.put(`/member/listings/${listing.id}/ai-chatbot/presets/${preset.value.id}`, data, {
    onFinish: () => {
      saving.value = false
    },
  })
}
</script>
