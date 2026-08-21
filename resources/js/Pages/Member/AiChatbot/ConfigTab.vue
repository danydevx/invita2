<template>
  <div class="config-tab">
    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>{{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = null"></button>
    </div>

    <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>{{ errorMessage }}
      <button type="button" class="btn-close" @click="errorMessage = null"></button>
    </div>

    <form @submit.prevent="saveSettings">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-robot me-2"></i>Configuración del Chatbot</h5>
        </div>
        <div class="card-body">
          <div class="row g-4">
            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Proveedor de IA</label>
                <select v-model="form.provider" class="form-select">
                  <option value="openai">OpenAI</option>
                  <option value="minimax">MiniMax</option>
                </select>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">API Key</label>
                <input
                  type="password"
                  v-model="form.api_key"
                  class="form-control"
                  placeholder="sk-..."
                  autocomplete="off"
                />
                <small class="text-muted">Tu API key se guarda de forma segura y encriptada</small>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Modelo de Chat</label>
                <select v-model="form.model" class="form-select">
                  <option value="gpt-4o-mini">GPT-4o Mini (Recomendado)</option>
                  <option value="gpt-4o">GPT-4o</option>
                  <option value="gpt-4-turbo">GPT-4 Turbo</option>
                  <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                </select>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Modelo de Embeddings</label>
                <select v-model="form.embedding_model" class="form-select">
                  <option value="text-embedding-3-small">text-embedding-3-small (Recomendado)</option>
                  <option value="text-embedding-3-large">text-embedding-3-large</option>
                  <option value="text-embedding-ada-002">text-embedding-ada-002</option>
                </select>
              </div>
            </div>

            <div class="col-12">
              <div class="mb-3">
                <label class="form-label">System Prompt</label>
                <textarea
                  v-model="form.system_prompt"
                  class="form-control"
                  rows="4"
                  placeholder="Eres un asistente amigable de {business_name}..."
                ></textarea>
                <small class="text-muted">
                  Usa <code>{business_name}</code> para incluir el nombre del negocio automáticamente.
                </small>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Preset Principal</label>
                <select v-model="form.preset_id" class="form-select">
                  <option :value="null">Ninguno (personalizado)</option>
                  <option v-for="preset in presets" :key="preset.id" :value="preset.id">
                    {{ preset.name }} {{ preset.business_id ? '(Propio)' : '' }}
                  </option>
                </select>
                <small class="text-muted">
                  <a :href="`/member/listings/${business.id}/ai-chatbot/presets`" target="_blank">
                    Gestionar presets
                  </a>
                </small>
              </div>
            </div>

            <div class="col-12">
              <div class="mb-3">
                <label class="form-label">Presets Adicionales (opcional)</label>
                <div class="d-flex flex-wrap gap-2 mb-2">
                  <div
                    v-for="presetId in form.additional_preset_ids"
                    :key="presetId"
                    class="badge bg-primary d-flex align-items-center gap-1"
                  >
                    {{ getPresetName(presetId) }}
                    <button type="button" class="btn-close btn-close-white" @click="removeAdditionalPreset(presetId)"></button>
                  </div>
                </div>
                <select v-model="newAdditionalPreset" class="form-select" @change="addAdditionalPreset">
                  <option :value="null">Agregar preset adicional...</option>
                  <option
                    v-for="preset in availableAdditionalPresets"
                    :key="preset.id"
                    :value="preset.id"
                  >
                    {{ preset.name }} {{ preset.business_id ? '(Propio)' : '' }}
                  </option>
                </select>
                <small class="text-muted">Los presets adicionales se usan como contexto adicional en las conversaciones</small>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Nombre del Chatbot</label>
                <input
                  type="text"
                  v-model="form.chatbot_name"
                  class="form-control"
                  placeholder="Asistente Virtual"
                  maxlength="100"
                />
                <small class="text-muted">Nombre que aparecerá en el chat</small>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Logo del Chatbot</label>
                <input
                  type="file"
                  accept="image/jpeg,image/png"
                  @change="onAvatarChange"
                  class="form-control"
                />
                <small class="text-muted">JPG o PNG, máximo 1MB</small>
                <div v-if="form.chatbot_avatar_preview || form.chatbot_avatar" class="mt-2">
                  <img
                    :src="form.chatbot_avatar_preview || form.chatbot_avatar"
                    alt="Avatar"
                    class="rounded"
                    style="max-height: 60px; object-fit: contain;"
                  />
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Color del Widget</label>
                <div class="color-input-wrapper">
                  <input
                    type="color"
                    v-model="form.widget_color"
                    class="color-input"
                  />
                  <input
                    type="text"
                    v-model="form.widget_color"
                    class="form-control color-text"
                    pattern="^#[0-9A-Fa-f]{6}$"
                  />
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Tema del Widget</label>
                <div class="theme-selector">
                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      v-model="form.widget_theme"
                      id="themeLight"
                      value="light"
                    />
                    <label class="form-check-label" for="themeLight">
                      <i class="bi bi-sun me-1"></i>Light
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      v-model="form.widget_theme"
                      id="themeDark"
                      value="dark"
                    />
                    <label class="form-check-label" for="themeDark">
                      <i class="bi bi-moon me-1"></i>Dark
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <div class="form-check form-switch mt-4">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="form.allow_reset_chat"
                    id="allowResetChat"
                  />
                  <label class="form-check-label" for="allowResetChat">
                    Permitir reiniciar chat
                  </label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Conversaciones/mes</label>
                <input
                  type="number"
                  v-model.number="form.max_conversations_month"
                  class="form-control"
                  min="1"
                  max="10000"
                />
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Mensajes/conversación</label>
                <input
                  type="number"
                  v-model.number="form.max_messages_conversation"
                  class="form-control"
                  min="1"
                  max="500"
                />
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">Tokens máx. por respuesta</label>
                <input
                  type="number"
                  v-model.number="form.max_tokens_response"
                  class="form-control"
                  min="100"
                  max="4000"
                />
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">
                  Resultados RAG máx.
                  <i class="bi bi-question-circle text-muted ms-1" style="cursor: help;" title="Cantidad de fragmentos de información que se usan como contexto. Más resultados = respuestas más informadas pero más costoso."></i>
                </label>
                <input
                  type="number"
                  v-model.number="form.rag_max_results"
                  class="form-control"
                  min="1"
                  max="20"
                />
                <small class="text-muted">Fragmentos de contexto retrievalados</small>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">
                  Similitud mínima RAG
                  <i class="bi bi-question-circle text-muted ms-1" style="cursor: help;" title="Qué tan similar debe ser el contexto encontrado. 0 = cualquier cosa, 0.7+ = muy similar. Ajusta según la calidad de tus datos."></i>
                </label>
                <input
                  type="number"
                  v-model.number="form.rag_min_similarity"
                  class="form-control"
                  min="0"
                  max="1"
                  step="0.05"
                />
                <small class="text-muted">0 = cualquier cosa, 1 = idéntico</small>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">
                  Personalidad
                  <i class="bi bi-question-circle text-muted ms-1" style="cursor: help;" title="Afecta el tono y estilo de las respuestas. Profesional: formal y directo. Amigable: cálido y cercano. Formal: respetuoso y elaborado. Casual: relajado y conversacional."></i>
                </label>
                <select v-model="form.personality" class="form-select">
                  <option value="professional">Profesional</option>
                  <option value="friendly">Amigable</option>
                  <option value="formal">Formal</option>
                  <option value="casual">Casual</option>
                </select>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">
                  Longitud de Respuesta
                  <i class="bi bi-question-circle text-muted ms-1" style="cursor: help;" title="Controla qué tan detalladas son las respuestas. Corta: 1-3 oraciones. Media: 2-5 oraciones. Larga: respuestas detalladas con ejemplos."></i>
                </label>
                <select v-model="form.response_length" class="form-select">
                  <option value="short">Corta</option>
                  <option value="medium">Media</option>
                  <option value="long">Larga</option>
                </select>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">&nbsp;</label>
                <div class="form-check form-switch mt-2">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="form.expandable_responses"
                    id="expandableResponses"
                  />
                  <label class="form-check-label" for="expandableResponses">
                    Respuestas expandibles
                  </label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="mb-3">
                <label class="form-label">&nbsp;</label>
                <div class="form-check form-switch mt-2">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="form.show_citations"
                    id="showCitations"
                  />
                  <label class="form-check-label" for="showCitations">
                    Mostrar fuentes
                  </label>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check form-switch mb-3">
                <input
                  class="form-check-input"
                  type="checkbox"
                  v-model="form.is_enabled"
                  id="is-enabled"
                />
                <label class="form-check-label" for="is-enabled">
                  <strong>Chatbot habilitado</strong>
                  <small class="d-block text-muted">Cuando está desactivado, el chatbot no aparece en el minisite</small>
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-cursor-fill me-2"></i>Llamadas a la Accion (CTA)</h5>
          </div>
          <div class="card-body">
            <div class="row g-4">
              <div class="col-12">
                <div class="form-check form-switch mb-3">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="form.cta_enabled"
                    id="ctaEnabled"
                  />
                  <label class="form-check-label" for="ctaEnabled">
                    Mostrar botones de accion despues de respuestas
                  </label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Texto del boton principal</label>
                  <input
                    type="text"
                    v-model="form.cta_primary_text"
                    class="form-control"
                    placeholder="Ej: Agendar cita"
                    maxlength="50"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">URL del boton principal</label>
                  <input
                    type="text"
                    v-model="form.cta_primary_url"
                    class="form-control"
                    placeholder="Ej: /contacto o https://..."
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Texto del boton secundario</label>
                  <input
                    type="text"
                    v-model="form.cta_secondary_text"
                    class="form-control"
                    placeholder="Ej: Ver productos"
                    maxlength="50"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">URL del boton secundario</label>
                  <input
                    type="text"
                    v-model="form.cta_secondary_url"
                    class="form-control"
                    placeholder="Ej: /productos"
                  />
                </div>
              </div>

              <div class="col-12">
                <div class="alert alert-info small">
                  <i class="bi bi-info-circle me-1"></i>
                  Los botones CTA apareceran en respuestas relacionadas con: reservas, productos, precios, horarios y consultas generales.
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>CTA por Intencion</h5>
          </div>
          <div class="card-body">
            <div class="alert alert-info small mb-3">
              <i class="bi bi-info-circle me-1"></i>
              Configura botones CTA específicos según la intención de la pregunta del usuario.
            </div>
            <div class="row g-4">
              <div class="col-md-6">
                <div class="intent-cta-item p-3 border rounded">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary">Reservas/Citas</span>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" v-model="form.intent_appointment_enabled" id="intentAppointment" />
                    </div>
                  </div>
                  <input type="text" v-model="form.intent_appointment_text" class="form-control form-control-sm mb-2" placeholder="Texto del botón" />
                  <input type="text" v-model="form.intent_appointment_url" class="form-control form-control-sm mb-2" placeholder="URL (ej: /reservas)" />
                  <input type="text" v-model="form.intent_appointment_keywords" class="form-control form-control-sm" placeholder="Keywords (separadas por coma): agendar, reserva, cita" />
                </div>
              </div>

              <div class="col-md-6">
                <div class="intent-cta-item p-3 border rounded">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-success"> Compras/Precios</span>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" v-model="form.intent_purchase_enabled" id="intentPurchase" />
                    </div>
                  </div>
                  <input type="text" v-model="form.intent_purchase_text" class="form-control form-control-sm mb-2" placeholder="Texto del botón" />
                  <input type="text" v-model="form.intent_purchase_url" class="form-control form-control-sm mb-2" placeholder="URL (ej: /productos)" />
                  <input type="text" v-model="form.intent_purchase_keywords" class="form-control form-control-sm" placeholder="Keywords (separadas por coma): precio, comprar, producto" />
                </div>
              </div>

              <div class="col-md-6">
                <div class="intent-cta-item p-3 border rounded">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-info">Contacto</span>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" v-model="form.intent_contact_enabled" id="intentContact" />
                    </div>
                  </div>
                  <input type="text" v-model="form.intent_contact_text" class="form-control form-control-sm mb-2" placeholder="Texto del botón" />
                  <input type="text" v-model="form.intent_contact_url" class="form-control form-control-sm mb-2" placeholder="URL (ej: /contacto)" />
                  <input type="text" v-model="form.intent_contact_keywords" class="form-control form-control-sm" placeholder="Keywords (separadas por coma): contacto, telefono, email" />
                </div>
              </div>

              <div class="col-md-6">
                <div class="intent-cta-item p-3 border rounded">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-warning text-dark">Soporte/Ayuda</span>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" v-model="form.intent_support_enabled" id="intentSupport" />
                    </div>
                  </div>
                  <input type="text" v-model="form.intent_support_text" class="form-control form-control-sm mb-2" placeholder="Texto del botón" />
                  <input type="text" v-model="form.intent_support_url" class="form-control form-control-sm mb-2" placeholder="URL (ej: /soporte)" />
                  <input type="text" v-model="form.intent_support_keywords" class="form-control form-control-sm" placeholder="Keywords (separadas por coma): ayuda, soporte, problema" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Captura de Leads</h5>
          </div>
          <div class="card-body">
            <div class="row g-4">
              <div class="col-12">
                <div class="form-check form-switch mb-3">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="form.lead_capture_enabled"
                    id="leadCaptureEnabled"
                  />
                  <label class="form-check-label" for="leadCaptureEnabled">
                    <strong>Captura de leads</strong>
                    <small class="d-block text-muted">Muestra un formulario sutil para collects correos electrónicos</small>
                  </label>
                </div>
              </div>

              <div v-if="form.lead_capture_enabled" class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">
                    Titulo del mensaje
                    <i class="bi bi-question-circle text-muted ms-1" style="cursor: help;" title="Titulo que aparecera en el popup de captura de email."></i>
                  </label>
                  <input
                    type="text"
                    v-model="form.lead_capture_title"
                    class="form-control"
                    placeholder="¿Te gustaría recibir noticias sobre nosotros?"
                    maxlength="200"
                  />
                </div>
              </div>

              <div v-if="form.lead_capture_enabled" class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Descripcion</label>
                  <input
                    type="text"
                    v-model="form.lead_capture_description"
                    class="form-control"
                    placeholder="Déjanos tu correo y te mantendremos informado."
                    maxlength="500"
                  />
                </div>
              </div>

              <div v-if="form.lead_capture_enabled" class="col-12">
                <div class="alert alert-info small">
                  <i class="bi bi-info-circle me-1"></i>
                  El formulario de captura aparecera automaticamente despues de 3 mensajes del usuario.
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary" :disabled="saving">
            <span v-if="saving"><i class="bi bi-hourglass-split me-2"></i>Guardando...</span>
            <span v-else><i class="bi bi-check-lg me-2"></i>Guardar Configuración</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { computed, ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  business: Object,
  settings: Object,
  presets: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['saved'])

const saving = ref(false)
const successMessage = ref(null)
const errorMessage = ref(null)

const defaultForm = {
  provider: 'openai',
  api_key: '',
  model: 'gpt-4o-mini',
  embedding_model: 'text-embedding-3-small',
  system_prompt: '',
  chatbot_name: '',
  chatbot_avatar: '',
  chatbot_avatar_preview: '',
  preset_id: null,
  additional_preset_ids: [],
  personality: 'friendly',
  response_length: 'medium',
  expandable_responses: true,
  show_citations: true,
  max_conversations_month: 500,
  max_messages_conversation: 50,
  max_tokens_response: 500,
  widget_color: '#3B82F6',
  widget_theme: 'light',
  is_enabled: false,
  allow_reset_chat: false,
  url_import_max_chars: 5000,
  rag_min_similarity: 0.25,
  rag_max_results: 5,
  cta_enabled: false,
  cta_primary_text: '',
  cta_primary_url: '',
  cta_secondary_text: '',
  cta_secondary_url: '',
  lead_capture_enabled: false,
  lead_capture_title: '¿Te gustaría recibir noticias sobre nosotros?',
  lead_capture_description: 'Déjanos tu correo y te mantendremos informado.',
  intent_appointment_enabled: false,
  intent_appointment_text: 'Agendar cita',
  intent_appointment_url: '',
  intent_appointment_keywords: 'agendar, reserva, cita, turno',
  intent_purchase_enabled: false,
  intent_purchase_text: 'Ver precios',
  intent_purchase_url: '',
  intent_purchase_keywords: 'precio, comprar, producto',
  intent_contact_enabled: false,
  intent_contact_text: 'Contactar',
  intent_contact_url: '',
  intent_contact_keywords: 'contacto, telefono, email',
  intent_support_enabled: false,
  intent_support_text: 'Obtener ayuda',
  intent_support_url: '',
  intent_support_keywords: 'ayuda, soporte, problema',
}

const form = reactive({ ...defaultForm })

  watch(
  () => props.settings,
  (newSettings) => {
    if (newSettings) {
      form.provider = newSettings.provider || 'openai'
      form.api_key = newSettings.api_key || ''
      form.model = newSettings.model || 'gpt-4o-mini'
      form.embedding_model = newSettings.embedding_model || 'text-embedding-3-small'
      form.system_prompt = newSettings.system_prompt || ''
      form.chatbot_name = newSettings.chatbot_name || ''
      form.chatbot_avatar = newSettings.chatbot_avatar || ''
      form.chatbot_avatar_preview = ''
      form.preset_id = newSettings.preset_id || null
      form.additional_preset_ids = newSettings.additional_preset_ids || []
      form.personality = newSettings.personality || 'friendly'
      form.response_length = newSettings.response_length || 'medium'
      form.expandable_responses = newSettings.expandable_responses ?? true
      form.show_citations = newSettings.show_citations ?? true
      form.max_conversations_month = newSettings.max_conversations_month || 500
      form.max_messages_conversation = newSettings.max_messages_conversation || 50
      form.max_tokens_response = newSettings.max_tokens_response || 500
      form.widget_color = newSettings.widget_color || '#3B82F6'
      form.widget_theme = newSettings.widget_theme || 'light'
      form.is_enabled = newSettings.is_enabled || false
      form.allow_reset_chat = newSettings.allow_reset_chat || false
      form.url_import_max_chars = newSettings.url_import_max_chars || 5000
      form.rag_min_similarity = newSettings.rag_min_similarity ?? 0.25
      form.rag_max_results = newSettings.rag_max_results || 5

      const cta = newSettings.cta_settings || {}
      form.cta_enabled = cta.enabled || false
      form.cta_primary_text = cta.primary_text || ''
      form.cta_primary_url = cta.primary_url || ''
      form.cta_secondary_text = cta.secondary_text || ''
      form.cta_secondary_url = cta.secondary_url || ''

      const intentCta = cta.intent_cta || {}
      form.intent_appointment_enabled = intentCta.appointment?.enabled || false
      form.intent_appointment_text = intentCta.appointment?.text || 'Agendar cita'
      form.intent_appointment_url = intentCta.appointment?.url || ''
      form.intent_appointment_keywords = intentCta.appointment?.keywords || 'agendar, reserva, cita, turno'
      form.intent_purchase_enabled = intentCta.purchase?.enabled || false
      form.intent_purchase_text = intentCta.purchase?.text || 'Ver precios'
      form.intent_purchase_url = intentCta.purchase?.url || ''
      form.intent_purchase_keywords = intentCta.purchase?.keywords || 'precio, comprar, producto'
      form.intent_contact_enabled = intentCta.contact?.enabled || false
      form.intent_contact_text = intentCta.contact?.text || 'Contactar'
      form.intent_contact_url = intentCta.contact?.url || ''
      form.intent_contact_keywords = intentCta.contact?.keywords || 'contacto, telefono, email'
      form.intent_support_enabled = intentCta.support?.enabled || false
      form.intent_support_text = intentCta.support?.text || 'Obtener ayuda'
      form.intent_support_url = intentCta.support?.url || ''
      form.intent_support_keywords = intentCta.support?.keywords || 'ayuda, soporte, problema'

      form.lead_capture_enabled = newSettings.lead_capture_enabled || false
      form.lead_capture_title = newSettings.lead_capture_title || '¿Te gustaría recibir noticias sobre nosotros?'
      form.lead_capture_description = newSettings.lead_capture_description || 'Déjanos tu correo y te mantendremos informado.'
    }
  },
  { immediate: true }
)

const newAdditionalPreset = ref(null)

const availableAdditionalPresets = computed(() => {
  return props.presets.filter(p =>
    p.id !== form.preset_id &&
    !form.additional_preset_ids.includes(p.id)
  )
})

const getPresetName = (presetId) => {
  const preset = props.presets.find(p => p.id === presetId)
  return preset ? preset.name : 'Preset #' + presetId
}

const addAdditionalPreset = () => {
  if (newAdditionalPreset.value && !form.additional_preset_ids.includes(newAdditionalPreset.value)) {
    form.additional_preset_ids.push(newAdditionalPreset.value)
  }
  newAdditionalPreset.value = null
}

const removeAdditionalPreset = (presetId) => {
  form.additional_preset_ids = form.additional_preset_ids.filter(id => id !== presetId)
}

const saveSettings = () => {
  saving.value = true
  successMessage.value = null
  errorMessage.value = null

  const formData = new FormData()
  formData.append('provider', form.provider)
  formData.append('api_key', form.api_key)
  formData.append('model', form.model)
  formData.append('embedding_model', form.embedding_model)
  formData.append('system_prompt', form.system_prompt)
  formData.append('chatbot_name', form.chatbot_name)
  formData.append('preset_id', form.preset_id || '')
  form.additional_preset_ids.forEach(id => {
    formData.append('additional_preset_ids[]', id)
  })
  formData.append('personality', form.personality)
  formData.append('response_length', form.response_length)
  formData.append('expandable_responses', form.expandable_responses ? '1' : '0')
  formData.append('show_citations', form.show_citations ? '1' : '0')
  formData.append('max_conversations_month', form.max_conversations_month)
  formData.append('max_messages_conversation', form.max_messages_conversation)
  formData.append('max_tokens_response', form.max_tokens_response)
  formData.append('widget_color', form.widget_color)
  formData.append('widget_theme', form.widget_theme)
  formData.append('is_enabled', form.is_enabled ? '1' : '0')
  formData.append('allow_reset_chat', form.allow_reset_chat ? '1' : '0')
  formData.append('url_import_max_chars', form.url_import_max_chars)
  formData.append('rag_min_similarity', form.rag_min_similarity)
  formData.append('rag_max_results', form.rag_max_results)

  const ctaSettings = JSON.stringify({
    enabled: form.cta_enabled,
    primary_text: form.cta_primary_text,
    primary_url: form.cta_primary_url,
    secondary_text: form.cta_secondary_text,
    secondary_url: form.cta_secondary_url,
    intent_cta: {
      appointment: { enabled: form.intent_appointment_enabled, text: form.intent_appointment_text, url: form.intent_appointment_url, keywords: form.intent_appointment_keywords },
      purchase: { enabled: form.intent_purchase_enabled, text: form.intent_purchase_text, url: form.intent_purchase_url, keywords: form.intent_purchase_keywords },
      contact: { enabled: form.intent_contact_enabled, text: form.intent_contact_text, url: form.intent_contact_url, keywords: form.intent_contact_keywords },
      support: { enabled: form.intent_support_enabled, text: form.intent_support_text, url: form.intent_support_url, keywords: form.intent_support_keywords },
    },
  })
  formData.append('cta_settings', ctaSettings)

  const leadCaptureSettings = JSON.stringify({
    enabled: form.lead_capture_enabled,
    title: form.lead_capture_title,
    description: form.lead_capture_description,
  })
  formData.append('lead_capture_settings', leadCaptureSettings)

  if (form.chatbot_avatar_file) {
    formData.append('chatbot_avatar', form.chatbot_avatar_file)
  }

  router.post(`/member/listings/${props.business.id}/ai-chatbot/settings`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      successMessage.value = 'Configuración guardada correctamente.'
      emit('saved')
      form.chatbot_avatar_preview = ''
      delete form.chatbot_avatar_file
    },
    onError: (errors) => {
      errorMessage.value = Object.values(errors)[0] || 'Error al guardar.'
    },
    onFinish: () => {
      saving.value = false
    },
  })
}

const onAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 1024 * 1024) {
      alert('La imagen debe ser menor a 1MB')
      event.target.value = ''
      return
    }
    if (!['image/jpeg', 'image/png'].includes(file.type)) {
      alert('Solo se permiten archivos JPG o PNG')
      event.target.value = ''
      return
    }
    form.chatbot_avatar_file = file
    form.chatbot_avatar_preview = URL.createObjectURL(file)
  }
}
</script>

<style lang="less" scoped>
.config-tab {
  .card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 16px 20px;
  }

  .card-footer {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    padding: 16px 20px;
  }

  .form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
  }

  .color-input-wrapper {
    display: flex;
    gap: 8px;

    .color-input {
      width: 50px;
      height: 38px;
      padding: 2px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      cursor: pointer;
    }

    .color-text {
      flex: 1;
      max-width: 120px;
    }
  }

  code {
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.875em;
  }

  .stat-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    text-align: center;

    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0d6efd;
    }

    .stat-label {
      font-size: 0.875rem;
      color: #6c757d;
      margin-top: 4px;
    }
  }
}
</style>
