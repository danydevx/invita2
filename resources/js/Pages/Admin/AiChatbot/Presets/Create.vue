<template>
  <AdminLayout>
    <Head title="Crear Preset de Chatbot" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-0">Crear Preset de Chatbot</h1>
          <small class="text-muted">Configura una nueva plantilla de chatbot</small>
        </div>
        <Link href="/admin/modules/ai_chatbot/presets" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
      </div>

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
                <h5 class="mb-0">Informacion General</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Nombre del Preset *</label>
                  <input v-model="form.name" type="text" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Slug *</label>
                  <input v-model="form.slug" type="text" class="form-control" required />
                  <small class="text-muted">Identificador unico (ej: support-assistant)</small>
                </div>

                <div class="mb-3">
                  <label class="form-label">Descripcion</label>
                  <textarea v-model="form.description" class="form-control" rows="2"></textarea>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo de Negocio</label>
                    <select v-model="form.listing_type" class="form-select">
                      <option :value="null">Todos los tipos</option>
                      <option v-for="type in listingTypes" :key="type" :value="type">
                        {{ type }}
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

                <div class="mb-3">
                  <label class="form-label">Personalidad *</label>
                  <select v-model="form.personality" class="form-select" required>
                    <option v-for="p in personalities" :key="p.key" :value="p.key">
                      {{ p.display_name }}
                    </option>
                  </select>
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
                  <small class="text-muted">Ej: Asistente de Soporte, Tu Ayuda Virtual</small>
                </div>

                <div class="mb-3">
                  <label class="form-label">Mensaje de Bienvenida</label>
                  <textarea v-model="form.greeting_message" class="form-control" rows="2"></textarea>
                  <small class="text-muted">Usa {business_name} para el nombre del negocio</small>
                </div>

                <div class="mb-3">
                  <label class="form-label">Mensaje de Fallback</label>
                  <textarea v-model="form.fallback_message" class="form-control" rows="2"></textarea>
                  <small class="text-muted">Cuando no se puede responder la pregunta</small>
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
                    Usa {'{business_name}'} para el nombre, {'{services}'} para servicios, {'{greeting_addition}'} para adicionales.
                  </small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0">Configuracion</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Longitud de Respuesta</label>
                  <select v-model="form.configuration.response_length" class="form-select">
                    <option value="short">Corta</option>
                    <option value="medium">Media</option>
                    <option value="long">Larga</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label">Temperatura</label>
                  <input
                    v-model.number="form.configuration.temperature"
                    type="number"
                    step="0.1"
                    min="0"
                    max="1"
                    class="form-control"
                  />
                  <small class="text-muted">0 = determinista, 1 = creativo</small>
                </div>

                <div class="form-check form-switch mb-3">
                  <input v-model="form.configuration.show_citations" class="form-check-input" type="checkbox" />
                  <label class="form-check-label">Mostrar Fuentes</label>
                </div>

                <div class="form-check form-switch mb-3">
                  <input v-model="form.configuration.expandable_responses" class="form-check-input" type="checkbox" />
                  <label class="form-check-label">Respuestas Expandibles</label>
                </div>

                <div class="mb-3">
                  <label class="form-label">Max. Conversaciones/Mes</label>
                  <input
                    v-model.number="form.configuration.max_conversations_month"
                    type="number"
                    class="form-control"
                  />
                </div>

                <div class="mb-3">
                  <label class="form-label">RAG Max. Resultados</label>
                  <input
                    v-model.number="form.configuration.rag_max_results"
                    type="number"
                    class="form-control"
                  />
                </div>

                <div class="mb-3">
                  <label class="form-label">RAG Min. Similitud</label>
                  <input
                    v-model.number="form.configuration.rag_min_similarity"
                    type="number"
                    step="0.01"
                    min="0"
                    max="1"
                    class="form-control"
                  />
                </div>
              </div>
            </div>

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
              <div class="card-body">
                <div class="form-check form-switch">
                  <input v-model="form.is_active" class="form-check-input" type="checkbox" />
                  <label class="form-check-label">Activo</label>
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100" :disabled="saving">
              <span v-if="saving">Guardando...</span>
              <span v-else>Crear Preset</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const listingTypes = page.props.listingTypes || []
const personalities = page.props.personalities || ['professional', 'friendly', 'formal', 'casual']
const languages = page.props.languages || ['es', 'en', 'pt', 'fr']

const saving = ref(false)

const form = reactive({
  name: '',
  slug: '',
  description: '',
  listing_type: null,
  personality: 'friendly',
  language: 'es',
  chatbot_name_template: '',
  greeting_message: '',
  fallback_message: '',
  system_prompt_template: `Eres {business_name}, un asistente virtual amigable y profesional. Tu objetivo es ayudar a los clientes con cualquier consulta.

{greeting_addition}`,
  configuration: {
    response_length: 'medium',
    temperature: 0.7,
    show_citations: true,
    expandable_responses: true,
    max_conversations_month: 1000,
    rag_max_results: 5,
    rag_min_similarity: 0.25,
  },
  initial_suggestions: ['', '', ''],
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

  router.post('/admin/chatbot-presets', data, {
    onFinish: () => {
      saving.value = false
    },
  })
}
</script>
