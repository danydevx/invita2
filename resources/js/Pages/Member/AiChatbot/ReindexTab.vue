<template>
  <div class="reindex-tab">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-search me-2"></i>Contenido Indexado</h5>
        <small class="text-muted">El chatbot utiliza este contenido para responder preguntas de los clientes</small>
      </div>
      <div class="card-body">
        <div v-if="Object.keys(embeddingCounts).length === 0" class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>
          No hay contenido indexado. Activa el chatbot e ingresa tu API key para comenzar.
        </div>
        <div v-else class="row g-3">
          <div class="col-6 col-md-3" v-for="item in contentTypes" :key="item.type">
            <div class="stat-card clickable" @click="viewContent(item.type, item.label)">
              <div class="stat-value">{{ item.count }}</div>
              <div class="stat-label">{{ item.label }}</div>
              <small v-if="item.count > 0" class="text-primary">Ver contenido</small>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
          <div>
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
          <small class="text-muted" v-if="lastReindex">
            Última indexación: {{ lastReindex }}
          </small>
        </div>
      </div>
    </div>

    <div v-if="reindexResult" class="alert alert-success mt-4">
      <i class="bi bi-check-circle me-2"></i>
      <strong>Indexación completada</strong>
      <ul class="mb-0 mt-2">
        <li v-for="(count, key) in reindexResult" :key="key">
          {{ formatLabel(key) }}: {{ count }}
        </li>
      </ul>
    </div>

    <div class="card mt-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>¿Qué es la indexación?</h5>
      </div>
      <div class="card-body">
        <p>La indexación permite que el chatbot conozca información sobre tu negocio para responder preguntas de tus clientes.</p>
        <ul>
          <li><strong>Productos, Servicios, Promociones:</strong> Se indexan automáticamente cuando los creas o actualizas.</li>
          <li><strong>FAQs y Contextos:</strong> Los creas tú y se indexan manualmente.</li>
          <li><strong>Menú:</strong> Los productos del menú se indexan automáticamente.</li>
        </ul>
        <p class="mb-0"><small class="text-muted">Si no ves contenido indexado, verifica que el chatbot esté habilitado y la API key sea correcta.</small></p>
      </div>
    </div>

    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" @click.self="closeModal">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Contenido: {{ modalTitle }}</h5>
            <div class="d-flex align-items-center gap-3">
              <small class="text-muted">{{ embeddings.length }} fragmentos</small>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
          </div>
          <div class="modal-body">
            <div v-if="loadingModal" class="text-center py-4">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
            <div v-else-if="embeddings.length === 0" class="alert alert-info mb-0">
              No hay contenido indexado de este tipo.
            </div>
            <div v-else class="embedding-list">
              <div v-for="emb in embeddings" :key="emb.id" class="embedding-item card mb-2">
                <div class="card-body py-2">
                  <small class="text-muted d-block mb-1">ID: {{ emb.source_id }} · Tipo: {{ emb.source_type }}</small>
                  <div class="chunk-text">{{ emb.chunk_text }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showModal" class="modal-backdrop fade show" @click="closeModal"></div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  business: Object,
  settings: Object,
  embeddingCounts: Object,
})

const emit = defineEmits(['reindex'])

const reindexing = ref(false)
const reindexResult = ref(null)
const lastReindex = ref(null)
const showModal = ref(false)
const modalTitle = ref('')
const embeddings = ref([])
const loadingModal = ref(false)

const contentTypes = computed(() => [
  { type: 'product', label: 'Productos', count: props.embeddingCounts?.product || 0 },
  { type: 'service', label: 'Servicios', count: props.embeddingCounts?.service || 0 },
  { type: 'promotion', label: 'Promociones', count: props.embeddingCounts?.promotion || 0 },
  { type: 'faq', label: 'FAQs', count: props.embeddingCounts?.faq || 0 },
  { type: 'location', label: 'Ubicaciones', count: props.embeddingCounts?.location || 0 },
  { type: 'about', label: 'Acerca de', count: props.embeddingCounts?.about || 0 },
  { type: 'custom', label: 'Contextos', count: props.embeddingCounts?.custom || 0 },
  { type: 'restaurant_menu', label: 'Menú', count: (props.embeddingCounts?.restaurant_category || 0) + (props.embeddingCounts?.restaurant_product || 0) },
  { type: 'social_network', label: 'Redes Sociales', count: props.embeddingCounts?.social_network || 0 },
  { type: 'appointments', label: 'Horarios Citas', count: (props.embeddingCounts?.appointment || 0) + (props.embeddingCounts?.appointment_exception || 0) },
])

const viewContent = (type, label) => {
  modalTitle.value = label
  embeddings.value = []
  showModal.value = true
  loadingModal.value = true

  fetch(`/member/listings/${props.business.id}/ai-chatbot/embeddings-json?type=${type}`, {
    method: 'GET',
    credentials: 'include',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  })
    .then(res => res.json())
    .then(data => {
      embeddings.value = data.embeddings || []
      loadingModal.value = false
    })
    .catch(() => {
      loadingModal.value = false
    })
}

const closeModal = () => {
  showModal.value = false
  embeddings.value = []
}

const reindex = () => {
  if (!props.settings) return

  reindexing.value = true
  reindexResult.value = null

  router.post(`/member/listings/${props.business.id}/ai-chatbot/reindex`, {}, {
    preserveScroll: true,
    onSuccess: (page) => {
      lastReindex.value = new Date().toLocaleString('es-MX')
      if (page.props.jetstream?.flash?.reindexStats) {
        reindexResult.value = page.props.jetstream.flash.reindexStats
      }
      window.location.reload()
    },
    onError: (errors) => {
      alert(Object.values(errors).join('\n') || 'Error al reindexar contenido')
    },
    onFinish: () => {
      reindexing.value = false
    },
  })
}

const formatLabel = (key) => {
  const labels = {
    products: 'Productos',
    services: 'Servicios',
    promotions: 'Promociones',
    faqs: 'FAQs',
    locations: 'Ubicaciones',
    about: 'Acerca de',
    custom: 'Contextos',
    restaurant_menu: 'Menú',
    restaurant_category: 'Categorías menú',
    restaurant_product: 'Productos menú',
    social_networks: 'Redes Sociales',
    social_network: 'Redes Sociales',
    appointments: 'Horarios de citas',
    appointment: 'Horarios de citas',
    appointment_exception: 'Excepciones de horarios',
  }
  return labels[key] || key
}
</script>

<style scoped>
.stat-card {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
}

.stat-card.clickable {
  cursor: pointer;
  transition: all 0.15s ease;
}

.stat-card.clickable:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  background: #e9ecef;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: #212529;
}

.stat-label {
  font-size: 0.875rem;
  color: #6c757d;
  margin-top: 4px;
}

.chunk-text {
  font-size: 0.875rem;
  white-space: pre-wrap;
  word-break: break-word;
}

.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>
