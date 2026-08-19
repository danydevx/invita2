<template>
  <div class="widget-tab">
    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>{{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = null"></button>
    </div>

    <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>{{ errorMessage }}
      <button type="button" class="btn-close" @click="errorMessage = null"></button>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-code-square me-2"></i>Widget Embebible</h5>
        <small class="text-muted">Instala el chatbot en cualquier sitio web</small>
      </div>
      <div class="card-body">
        <div class="form-check form-switch mb-4">
          <input
            class="form-check-input"
            type="checkbox"
            v-model="form.is_enabled"
            id="widget-enabled"
          />
          <label class="form-check-label" for="widget-enabled">
            <strong>Habilitar Widget</strong>
            <small class="d-block text-muted">Permite insertar el chatbot en sitios externos</small>
          </label>
        </div>

        <div v-if="form.is_enabled" class="widget-config">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Dominio Permitido</label>
                <input
                  type="text"
                  v-model="form.allowed_domain"
                  class="form-control"
                  placeholder="misitio.com"
                />
                <small class="text-muted">
                  Dominio donde se podrá usar el widget (ej: misitio.com). Leave empty para permitir cualquier dominio.
                </small>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label">Posición del Botón</label>
                <select v-model="form.position" class="form-select">
                  <option value="right">Bottom-Right (Inferior Derecha)</option>
                  <option value="left">Bottom-Left (Inferior Izquierda)</option>
                </select>
              </div>
            </div>

            <div class="col-12">
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="form.show_intent_buttons"
                    id="show-intent-buttons"
                  />
                  <label class="form-check-label" for="show-intent-buttons">
                    <strong>Botones de Intención</strong>
                    <small class="d-block text-muted">Mostrar botones de Reservas, Precios, Contacto, Ayuda al abrir el chat</small>
                  </label>
                </div>
              </div>
            </div>

            <div v-if="form.show_intent_buttons" class="col-12">
              <div class="card bg-light mb-3">
                <div class="card-body">
                  <h6 class="mb-3"><i class="bi bi-chat-dots me-2"></i>Configuración de Intenciones</h6>

                  <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                      <label class="form-label small fw-bold text-primary">
                        <i class="bi bi-calendar-event me-1"></i>Reservas
                      </label>
                      <input
                        type="text"
                        v-model="form.intent_cta.appointment.text"
                        class="form-control form-control-sm mb-2"
                        placeholder="Texto del botón"
                      />
                      <input
                        type="text"
                        v-model="form.intent_cta.appointment.url"
                        class="form-control form-control-sm mb-2"
                        placeholder="/url"
                      />
                      <input
                        type="text"
                        v-model="form.intent_cta.appointment.keywords"
                        class="form-control form-control-sm"
                        placeholder="agendar, reserva, cita"
                      />
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                      <label class="form-label small fw-bold text-success">
                        <i class="bi bi-bag me-1"></i>Compras
                      </label>
                      <input
                        type="text"
                        v-model="form.intent_cta.purchase.text"
                        class="form-control form-control-sm mb-2"
                        placeholder="Texto del botón"
                      />
                      <input
                        type="text"
                        v-model="form.intent_cta.purchase.url"
                        class="form-control form-control-sm mb-2"
                        placeholder="/url"
                      />
                      <input
                        type="text"
                        v-model="form.intent_cta.purchase.keywords"
                        class="form-control form-control-sm"
                        placeholder="precio, comprar, producto"
                      />
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                      <label class="form-label small fw-bold text-info">
                        <i class="bi bi-telephone me-1"></i>Contacto
                      </label>
                      <input
                        type="text"
                        v-model="form.intent_cta.contact.text"
                        class="form-control form-control-sm mb-2"
                        placeholder="Texto del botón"
                      />
                      <input
                        type="text"
                        v-model="form.intent_cta.contact.url"
                        class="form-control form-control-sm mb-2"
                        placeholder="/url"
                      />
                      <input
                        type="text"
                        v-model="form.intent_cta.contact.keywords"
                        class="form-control form-control-sm"
                        placeholder="contacto, telefono, email"
                      />
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                      <label class="form-label small fw-bold text-warning">
                        <i class="bi bi-question-circle me-1"></i>Soporte
                      </label>
                      <input
                        type="text"
                        v-model="form.intent_cta.support.text"
                        class="form-control form-control-sm mb-2"
                        placeholder="Texto del botón"
                      />
                      <input
                        type="text"
                        v-model="form.intent_cta.support.url"
                        class="form-control form-control-sm mb-2"
                        placeholder="/url"
                      />
                      <input
                        type="text"
                        v-model="form.intent_cta.support.keywords"
                        class="form-control form-control-sm"
                        placeholder="ayuda, soporte, problema"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label">Public Key</label>
            <div class="input-group">
              <input
                type="text"
                :value="widget?.public_key || 'No generado'"
                class="form-control"
                readonly
              />
              <button
                class="btn btn-outline-warning"
                type="button"
                @click="regenerateKey"
                :disabled="regenerating"
              >
                <span v-if="regenerating"><i class="bi bi-hourglass-split me-2"></i>Generando...</span>
                <span v-else><i class="bi bi-arrow-repeat me-2"></i>Regenerar</span>
              </button>
            </div>
            <small class="text-muted">
              Identificador único del widget. Regenerar solo si es necesario.
            </small>
          </div>

          <div class="mb-4">
            <label class="form-label">Código de Instalación</label>
            <textarea
              :value="installCode"
              class="form-control font-monospace"
              rows="3"
              readonly
            ></textarea>
            <small class="text-muted d-block mt-2">
              Copia este código y pégalo en el HTML de tu sitio web, justo antes del cierre de la etiqueta &lt;/body&gt;
            </small>
          </div>

          <button
            class="btn btn-primary"
            type="button"
            @click="copyCode"
            :disabled="copied"
          >
            <span v-if="copied"><i class="bi bi-check me-2"></i>¡Copiado!</span>
            <span v-else><i class="bi bi-clipboard me-2"></i>Copiar Código</span>
          </button>
        </div>
      </div>
    </div>

    <div v-if="widget && widget.is_enabled" class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Estadísticas</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ stats.loads || 0 }}</div>
              <div class="stat-label">Cargas</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ stats.messages || 0 }}</div>
              <div class="stat-label">Mensajes</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ stats.opens || 0 }}</div>
              <div class="stat-label">Chats Abiertos</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="stat-card">
              <div class="stat-value">{{ stats.cta_clicks || 0 }}</div>
              <div class="stat-label">Clicks CTA</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  business: Object,
  widget: Object,
  stats: Object,
  intentCta: Object,
})

const emit = defineEmits(['saved'])

const saving = ref(false)
const regenerating = ref(false)
const copied = ref(false)
const successMessage = ref(null)
const errorMessage = ref(null)

const form = reactive({
  is_enabled: false,
  allowed_domain: '',
  position: 'right',
  show_intent_buttons: true,
  intent_cta: {
    appointment: { enabled: true, text: 'Agendar Cita', url: '/appointments', keywords: 'agendar, reserva, cita, turno' },
    purchase: { enabled: true, text: 'Ver Precios', url: '/products', keywords: 'precio, comprar, producto' },
    contact: { enabled: true, text: 'Contactar', url: '/contact', keywords: 'contacto, telefono, email' },
    support: { enabled: true, text: 'Obtener Ayuda', url: '/support', keywords: 'ayuda, soporte, problema' },
  },
})

onMounted(() => {
  if (props.widget) {
    form.is_enabled = props.widget.is_enabled || false
    form.allowed_domain = props.widget.allowed_domain || ''
  }
  if (props.intentCta) {
    form.intent_cta = { ...form.intent_cta, ...props.intentCta }
  }
})

  const installCode = computed(() => {
  if (!props.widget?.public_key) {
    return '<!-- Genera una public key primero -->'
  }

  const position = form.position || 'right'
  const intentButtons = form.show_intent_buttons ? 'true' : 'false'
  const baseUrl = window.location.origin

  return `<script>
  src="${baseUrl}/api/widget/${props.widget.public_key}/widget.js"
  data-public-key="${props.widget.public_key}"
  data-position="${position}"
  data-intent-buttons="${intentButtons}"
><\/script>`
})

const copyCode = async () => {
  try {
    await navigator.clipboard.writeText(installCode.value)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (err) {
    const textarea = document.querySelector('textarea.font-monospace')
    if (textarea) {
      textarea.select()
      document.execCommand('copy')
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2000)
    }
  }
}

const regenerateKey = () => {
  if (!confirm('¿Estás seguro? Esto invalidará el código de instalación actual.')) {
    return
  }

  regenerating.value = true

  router.post(`/member/businesses/${props.business.id}/ai-chatbot/widget/regenerate`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      successMessage.value = 'Public key regenerada correctamente.'
      emit('saved')
    },
    onError: (errors) => {
      errorMessage.value = Object.values(errors)[0] || 'Error al regenerar.'
    },
    onFinish: () => {
      regenerating.value = false
    },
  })
}

const saveSettings = () => {
  saving.value = true
  successMessage.value = null
  errorMessage.value = null

  router.post(`/member/businesses/${props.business.id}/ai-chatbot/widget/settings`, form, {
    preserveScroll: true,
    onSuccess: () => {
      successMessage.value = 'Configuración del widget guardada.'
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
</script>

<style lang="less" scoped>
.widget-tab {
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

  .form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
  }

  .font-monospace {
    font-family: 'SF Mono', Monaco, 'Courier New', monospace;
    font-size: 0.85rem;
  }

  .stat-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    text-align: center;

    .stat-value {
      font-size: 1.75rem;
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
