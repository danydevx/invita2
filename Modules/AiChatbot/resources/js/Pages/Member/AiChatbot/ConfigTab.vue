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
                <label class="form-label">Límite import. URL (caracteres)</label>
                <input
                  type="number"
                  v-model.number="form.url_import_max_chars"
                  class="form-control"
                  min="100"
                  max="50000"
                />
                <small class="text-muted">Máximo de caracteres al importar contenido desde URLs</small>
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
      <div class="card-footer">
        <button type="submit" class="btn btn-primary" :disabled="saving">
          <span v-if="saving"><i class="bi bi-hourglass-split me-2"></i>Guardando...</span>
          <span v-else><i class="bi bi-check-lg me-2"></i>Guardar Configuración</span>
        </button>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-cursor-fill me-2"></i>Llamadas a la Acción (CTAs)</h5>
      </div>
      <div class="card-body">
        <div class="alert alert-info mb-3">
          <i class="bi bi-info-circle me-2"></i>
          Los CTAs aparecen automáticamente cuando el chatbot detecta intención en sus respuestas. Por ejemplo, si menciona "agendar", muestra el CTA de reservas.
        </div>

        <div class="form-check form-switch mb-4">
          <input
            class="form-check-input"
            type="checkbox"
            v-model="form.cta_settings.enabled"
            id="cta-enabled"
          />
          <label class="form-check-label" for="cta-enabled">
            <strong>Habilitar CTAs</strong>
          </label>
        </div>

        <div v-if="form.cta_settings.enabled" class="cta-config">
          <div class="row g-3">
            <div class="col-12 col-md-6 col-lg-3">
              <div class="card h-100 border-primary">
                <div class="card-body">
                  <div class="form-check form-switch mb-2">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      v-model="form.cta_settings.intent_cta.appointment.enabled"
                      id="cta-appointment"
                    />
                    <label class="form-check-label fw-bold" for="cta-appointment">
                      <i class="bi bi-calendar-event me-1"></i>Reservas
                    </label>
                  </div>
                  <small class="text-muted d-block mb-2">Se muestra cuando el bot menciona agendar, cita, horario</small>
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.appointment.text"
                    class="form-control form-control-sm mb-2"
                    placeholder="Texto del botón"
                  />
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.appointment.url"
                    class="form-control form-control-sm mb-2"
                    placeholder="/url"
                  />
                  <small class="text-muted d-block mb-1">Keywords (separadas por coma):</small>
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.appointment.keywords"
                    class="form-control form-control-sm"
                    placeholder="agendar, cita, reserva"
                  />
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <div class="card h-100 border-success">
                <div class="card-body">
                  <div class="form-check form-switch mb-2">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      v-model="form.cta_settings.intent_cta.purchase.enabled"
                      id="cta-purchase"
                    />
                    <label class="form-check-label fw-bold" for="cta-purchase">
                      <i class="bi bi-bag me-1"></i>Compras
                    </label>
                  </div>
                  <small class="text-muted d-block mb-2">Se muestra cuando menciona precio, producto, comprar</small>
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.purchase.text"
                    class="form-control form-control-sm mb-2"
                    placeholder="Texto del botón"
                  />
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.purchase.url"
                    class="form-control form-control-sm mb-2"
                    placeholder="/url"
                  />
                  <small class="text-muted d-block mb-1">Keywords (separadas por coma):</small>
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.purchase.keywords"
                    class="form-control form-control-sm"
                    placeholder="precio, comprar, producto"
                  />
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <div class="card h-100 border-info">
                <div class="card-body">
                  <div class="form-check form-switch mb-2">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      v-model="form.cta_settings.intent_cta.contact.enabled"
                      id="cta-contact"
                    />
                    <label class="form-check-label fw-bold" for="cta-contact">
                      <i class="bi bi-telephone me-1"></i>Contacto
                    </label>
                  </div>
                  <small class="text-muted d-block mb-2">Se muestra cuando menciona contacto, teléfono, email</small>
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.contact.text"
                    class="form-control form-control-sm mb-2"
                    placeholder="Texto del botón"
                  />
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.contact.url"
                    class="form-control form-control-sm mb-2"
                    placeholder="/url"
                  />
                  <small class="text-muted d-block mb-1">Keywords (separadas por coma):</small>
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.contact.keywords"
                    class="form-control form-control-sm"
                    placeholder="contacto, telefono, email"
                  />
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
              <div class="card h-100 border-warning">
                <div class="card-body">
                  <div class="form-check form-switch mb-2">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      v-model="form.cta_settings.intent_cta.support.enabled"
                      id="cta-support"
                    />
                    <label class="form-check-label fw-bold" for="cta-support">
                      <i class="bi bi-question-circle me-1"></i>Soporte
                    </label>
                  </div>
                  <small class="text-muted d-block mb-2">Se muestra cuando menciona ayuda, problema, error</small>
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.support.text"
                    class="form-control form-control-sm mb-2"
                    placeholder="Texto del botón"
                  />
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.support.url"
                    class="form-control form-control-sm mb-2"
                    placeholder="/url"
                  />
                  <small class="text-muted d-block mb-1">Keywords (separadas por coma):</small>
                  <input
                    type="text"
                    v-model="form.cta_settings.intent_cta.support.keywords"
                    class="form-control form-control-sm"
                    placeholder="ayuda, soporte, problema"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </form>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-search me-2"></i>Contenido Indexado</h5>
      </div>
      <div class="card-body">
        <div v-if="Object.keys(embeddingCounts).length === 0" class="text-muted">
          <p>No hay contenido indexado. Activa el chatbot e ingresa tu API key para comenzar.</p>
        </div>
        <div v-else class="row g-3">
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.product || 0 }}</div>
              <div class="stat-label">Productos</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.service || 0 }}</div>
              <div class="stat-label">Servicios</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.promotion || 0 }}</div>
              <div class="stat-label">Promociones</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.faq || 0 }}</div>
              <div class="stat-label">FAQs</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.location || 0 }}</div>
              <div class="stat-label">Ubicaciones</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.about || 0 }}</div>
              <div class="stat-label">Acerca de</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ embeddingCounts.custom || 0 }}</div>
              <div class="stat-label">Contextos</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ (embeddingCounts.restaurant_category || 0) + (embeddingCounts.restaurant_product || 0) }}</div>
              <div class="stat-label">Menú</div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <button
          type="button"
          class="btn btn-outline-primary"
          @click="reindex"
          :disabled="reindexing || !settings"
        >
          <span v-if="reindexing"><i class="bi bi-hourglass-split me-2"></i>Reindexando...</span>
          <span v-else><i class="bi bi-arrow-repeat me-2"></i>Reindexar Contenido</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  business: Object,
  settings: Object,
  embeddingCounts: Object,
})

const emit = defineEmits(['saved', 'reindex'])

const saving = ref(false)
const reindexing = ref(false)
const successMessage = ref(null)
const errorMessage = ref(null)

const defaultForm = {
  provider: 'openai',
  api_key: '',
  model: 'gpt-4o-mini',
  embedding_model: 'text-embedding-3-small',
  system_prompt: '',
  max_conversations_month: 500,
  max_messages_conversation: 50,
  max_tokens_response: 500,
  url_import_max_chars: 5000,
  widget_color: '#3B82F6',
  is_enabled: false,
  cta_settings: {
    enabled: false,
    intent_cta: {
      appointment: { enabled: false, text: 'Agendar Cita', url: '/appointments', keywords: 'agendar, reserva, cita, turno' },
      purchase: { enabled: false, text: 'Ver Productos', url: '/products', keywords: 'precio, comprar, producto' },
      contact: { enabled: false, text: 'Contactar', url: '/contact', keywords: 'contacto, telefono, email' },
      support: { enabled: false, text: 'Obtener Ayuda', url: '/support', keywords: 'ayuda, soporte, problema' },
    },
  },
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
      form.max_conversations_month = newSettings.max_conversations_month || 500
      form.max_messages_conversation = newSettings.max_messages_conversation || 50
      form.max_tokens_response = newSettings.max_tokens_response || 500
      form.url_import_max_chars = newSettings.url_import_max_chars || 5000
      form.widget_color = newSettings.widget_color || '#3B82F6'
      form.is_enabled = newSettings.is_enabled || false

        if (newSettings.cta_enabled !== undefined) {
          form.cta_settings = {
            enabled: newSettings.cta_enabled || false,
            intent_cta: {
              appointment: { enabled: false, text: 'Agendar Cita', url: '/appointments', keywords: 'agendar, reserva, cita, turno' },
              purchase: { enabled: false, text: 'Ver Productos', url: '/products', keywords: 'precio, comprar, producto' },
              contact: { enabled: false, text: 'Contactar', url: '/contact', keywords: 'contacto, telefono, email' },
              support: { enabled: false, text: 'Obtener Ayuda', url: '/support', keywords: 'ayuda, soporte, problema' },
            },
          }
        if (newSettings.intent_cta) {
          try {
            const parsedIntent = typeof newSettings.intent_cta === 'string'
              ? JSON.parse(newSettings.intent_cta)
              : newSettings.intent_cta
            if (parsedIntent.appointment) form.cta_settings.intent_cta.appointment = { ...form.cta_settings.intent_cta.appointment, ...parsedIntent.appointment }
            if (parsedIntent.purchase) form.cta_settings.intent_cta.purchase = { ...form.cta_settings.intent_cta.purchase, ...parsedIntent.purchase }
            if (parsedIntent.contact) form.cta_settings.intent_cta.contact = { ...form.cta_settings.intent_cta.contact, ...parsedIntent.contact }
            if (parsedIntent.support) form.cta_settings.intent_cta.support = { ...form.cta_settings.intent_cta.support, ...parsedIntent.support }
          } catch (e) {}
        }
      }
    }
  },
  { immediate: true }
)

const saveSettings = () => {
  saving.value = true
  successMessage.value = null
  errorMessage.value = null

  const formData = {
    ...form,
    cta_settings: JSON.stringify(form.cta_settings),
  }

  router.post(`/member/businesses/${props.business.id}/ai-chatbot/settings`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      successMessage.value = 'Configuración guardada correctamente.'
      emit('saved')
    },
    onError: (errors) => {
      errorMessage.value = Object.values(errors)[0] || 'Error al guardar.'
    },
    onFinish: () => {
      saving.value = false
    },
  })
}

const reindex = () => {
  reindexing.value = true
  successMessage.value = null
  errorMessage.value = null

  router.post(`/member/businesses/${props.business.id}/ai-chatbot/reindex`, {}, {
    preserveScroll: true,
    onSuccess: (page) => {
      if (page.props.flash?.success) {
        successMessage.value = page.props.flash.success
      }
      emit('reindex')
    },
    onError: (errors) => {
      errorMessage.value = Object.values(errors)[0] || 'Error al reindexar.'
    },
    onFinish: () => {
      reindexing.value = false
    },
  })
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
