<template>
  <MemberLayout>
    <Head title="Detalle de Conversación" />
    <PageHeader
      title="Detalle de Conversación"
      :breadcrumbs="breadcrumbs"
    />
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1">Detalle de Conversación</h4>
        <p class="text-muted small mb-0">Session: {{ conversation.session_id }}</p>
      </div>
      <Link
        :href="`/member/listings/${listing.id}/ai-chatbot/history`"
        class="btn btn-outline-secondary"
      >
        <i class="bi bi-arrow-left me-1"></i>Volver
      </Link>
    </div>

    <div class="info-card card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 col-6 mb-3">
            <div class="text-muted small">Ubicación</div>
            <div>
              <span v-if="conversation.country_code && conversation.country_code !== 'XX'">
                {{ getFlagEmoji(conversation.country_code) }}
              </span>
              {{ conversation.country }}{{ conversation.city ? `, ${conversation.city}` : '' }}
            </div>
          </div>
          <div class="col-md-3 col-6 mb-3">
            <div class="text-muted small">IP</div>
            <div>{{ conversation.ip_address || 'N/A' }}</div>
          </div>
          <div class="col-md-3 col-6 mb-3">
            <div class="text-muted small">Mensajes</div>
            <div>{{ conversation.messages_count }}</div>
          </div>
          <div class="col-md-3 col-6 mb-3">
            <div class="text-muted small">Última actividad</div>
            <div>{{ formatDate(conversation.last_activity_at) }}</div>
          </div>
        </div>
        <div v-if="conversation.user_agent" class="mt-2">
          <div class="text-muted small">User Agent</div>
          <div class="small text-break">{{ conversation.user_agent }}</div>
        </div>
      </div>
    </div>

    <div class="messages-container">
      <div
        v-for="msg in messages"
        :key="msg.id"
        class="message-item mb-3"
        :class="msg.role"
      >
        <div class="message-header">
          <span class="badge" :class="msg.role === 'user' ? 'bg-primary' : 'bg-success'">
            {{ msg.role === 'user' ? 'Usuario' : 'Asistente' }}
          </span>
          <small class="text-muted ms-2">{{ formatDate(msg.created_at) }}</small>
          <small v-if="msg.tokens_used" class="text-muted ms-auto">
            {{ msg.tokens_used }} tokens
          </small>
        </div>
        <div class="message-content card mt-2">
            <div class="card-body">
            {{ msg.content }}
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
  conversation: Object,
  messages: Array,
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Chatbot', href: `/member/listings/${props.listing?.id}/ai-chatbot` },
  { label: 'Historial', href: `/member/listings/${props.listing?.id}/ai-chatbot/history` },
  { label: props.conversation?.session_id?.substring(0, 8) || 'Detalle', active: true },
])

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleString('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
}

const getFlagEmoji = (countryCode) => {
  if (!countryCode || countryCode.length !== 2) return ''
  const codePoints = countryCode
    .toUpperCase()
    .split('')
    .map(char => 127397 + char.charCodeAt(0))
  return String.fromCodePoint(...codePoints) + ' '
}
</script>

<style lang="less" scoped>
.conversation-detail {
  .info-card {
    background: #f8f9fa;
  }

  .messages-container {
    max-width: 800px;
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
