<template>
  <div class="history-tab">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h5 class="mb-1">Historial de Conversaciones</h5>
        <small class="text-muted">Últimas {{ conversations.length }} conversaciones</small>
      </div>
      <button class="btn btn-outline-secondary btn-sm" @click="loadConversations" :disabled="loading">
        <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
      </button>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div v-else-if="conversations.length === 0" class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>
      No hay conversaciones registradas aún.
    </div>

    <div v-else class="conversation-list">
      <div
        v-for="conv in conversations"
        :key="conv.id"
        class="conversation-card card mb-3"
        @click="viewConversation(conv)"
      >
        <div class="card-body py-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-secondary">{{ conv.messages_count }} msgs</span>
              <span class="session-id text-muted small">{{ conv.session_id }}</span>
            </div>
            <small class="text-muted">{{ formatDate(conv.last_activity_at) }}</small>
          </div>

          <div class="location-info mb-2">
            <span class="me-3">
              <i class="bi bi-globe me-1"></i>
              {{ conv.country }}{{ conv.city ? `, ${conv.city}` : '' }}
            </span>
            <span class="me-3">
              <i :class="deviceIcon(conv.device_type)" class="me-1"></i>
              {{ conv.device_type || 'unknown' }}
            </span>
            <span class="text-muted small">
              <i class="bi bi-hdd me-1"></i>{{ conv.ip_address || 'N/A' }}
            </span>
          </div>

          <div v-if="conv.preview" class="preview-text text-muted small">
            {{ conv.preview }}
          </div>
        </div>
      </div>
    </div>

    <div v-if="selectedConversation" class="modal fade show d-block" tabindex="-1" @click.self="closeModal">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Conversación</h5>
            <div class="d-flex align-items-center gap-3">
              <small class="text-muted">
                <i class="bi bi-globe me-1"></i>{{ selectedConversation.country }} {{ selectedConversation.city ? `, ${selectedConversation.city}` : '' }}
                <i :class="deviceIcon(selectedConversation.device_type)" class="ms-2 me-1"></i>{{ selectedConversation.device_type || 'unknown' }}
                <i class="bi bi-hdd ms-2 me-1"></i>{{ selectedConversation.ip_address }}
              </small>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
          </div>
          <div class="modal-body">
            <div v-if="detailLoading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
            <div v-else class="messages-list">
              <div
                v-for="msg in detailMessages"
                :key="msg.id"
                class="message-item mb-3"
                :class="msg.role"
              >
                <div class="message-header">
                  <span class="badge" :class="msg.role === 'user' ? 'bg-primary' : 'bg-success'">
                    {{ msg.role === 'user' ? 'Usuario' : 'Asistente' }}
                  </span>
                  <small class="text-muted ms-2">{{ formatDate(msg.created_at) }}</small>
                  <small v-if="msg.tokens_used" class="text-muted ms-auto">{{ msg.tokens_used }} tokens</small>
                </div>
                <div class="message-content card mt-2">
                  <div class="card-body">{{ msg.content }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <small class="text-muted me-auto">
              Iniciada: {{ formatDate(selectedConversation.started_at) }}
            </small>
            <button type="button" class="btn btn-secondary" @click="closeModal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="selectedConversation" class="modal-backdrop fade show" @click="closeModal"></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  business: Object,
})

const conversations = ref([])
const loading = ref(true)
const selectedConversation = ref(null)
const detailMessages = ref([])
const detailLoading = ref(false)

const loadConversations = () => {
  loading.value = true
  fetch(`/member/listings/${props.business.id}/ai-chatbot/history-json`, {
    method: 'GET',
    credentials: 'include',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  })
    .then(res => res.json())
    .then(data => {
      conversations.value = data.conversations || []
      loading.value = false
    })
    .catch(() => {
      loading.value = false
    })
}

const viewConversation = (conv) => {
  selectedConversation.value = conv
  detailMessages.value = []
  detailLoading.value = true

  fetch(`/member/listings/${props.business.id}/ai-chatbot/history-json/${conv.session_id}`, {
    method: 'GET',
    credentials: 'include',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  })
    .then(res => res.json())
    .then(data => {
      detailMessages.value = data.messages || []
      detailLoading.value = false
    })
    .catch(() => {
      detailLoading.value = false
    })
}

const closeModal = () => {
  selectedConversation.value = null
  detailMessages.value = []
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleString('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
}

const deviceIcon = (deviceType) => {
  switch (deviceType) {
    case 'mobile': return 'bi bi-phone'
    case 'tablet': return 'bi bi-tablet'
    case 'desktop': return 'bi bi-display'
    default: return 'bi bi-question-circle'
  }
}

onMounted(() => {
  loadConversations()
})
</script>

<style lang="less" scoped>
.history-tab {
  .conversation-card {
    cursor: pointer;
    transition: all 0.15s ease;

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
  }

  .session-id {
    font-family: monospace;
    font-size: 0.75rem;
  }

  .location-info {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .preview-text {
    border-left: 2px solid #dee2e6;
    padding-left: 0.75rem;
    font-style: italic;
  }

  .modal {
    background: rgba(0, 0, 0, 0.5);
  }

  .messages-list {
    max-height: 60vh;
    overflow-y: auto;
  }

  .message-item {
    &.user {
      .message-content {
        background: #e7f1ff;
      }
    }

    &.assistant {
      .message-content {
        background: #f0f9f0;
      }
    }

    .message-header {
      display: flex;
      align-items: center;
    }

    .message-content {
      .card-body {
        white-space: pre-wrap;
        word-break: break-word;
      }
    }
  }
}
</style>
