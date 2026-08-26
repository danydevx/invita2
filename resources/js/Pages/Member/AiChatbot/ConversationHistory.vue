<template>
  <MemberLayout>
    <Head title="Historial de Conversaciones" />
    <PageHeader
      title="Historial de Conversaciones"
      :breadcrumbs="breadcrumbs"
    />
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1">Historial de Conversaciones</h4>
        <p class="text-muted small mb-0">Últimas 100 conversaciones del chatbot</p>
      </div>
      <Link
        :href="`/member/listings/${listing.id}/ai-chatbot`"
        class="btn btn-outline-secondary"
      >
        <i class="bi bi-arrow-left me-1"></i>Volver
      </Link>
    </div>

    <div v-if="conversations.length === 0" class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>
      No hay conversaciones registradas aún.
    </div>

    <div v-else class="conversation-list">
      <div
        v-for="conv in conversations"
        :key="conv.id"
        class="conversation-card card mb-3"
        @click="viewConversation(conv.session_id)"
      >
        <div class="card-body">
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
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const props = defineProps({
  listing: Object,
  conversations: Array,
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Chatbot', href: `/member/listings/${props.listing?.id}/ai-chatbot` },
  { label: 'Historial', active: true },
])

const viewConversation = (sessionId) => {
  window.location.href = `/member/listings/${props.listing.id}/ai-chatbot/history/${sessionId}`
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleString('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
}
</script>

<style lang="less" scoped>
.conversation-history {
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
}
</style>
