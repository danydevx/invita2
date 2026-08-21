<template>
  <MemberLayout>
    <Head :title="`Ticket #${ticket.id}`" />

    <PageHeader :title="ticket.subject" :breadcrumbs="breadcrumbs">
      <template #actions>
        <Link href="/member/support" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
      </template>
    </PageHeader>

    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0 text-muted">Informacion del ticket</h2>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label small text-muted text-uppercase">Estado</label>
              <div>
                <span class="badge" :class="statusClass(ticket.status)">{{ ticket.status }}</span>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label small text-muted text-uppercase">Prioridad</label>
              <div class="fw-semibold">{{ ticket.priority || '-' }}</div>
            </div>

            <div class="mb-3">
              <label class="form-label small text-muted text-uppercase">Categoria</label>
              <div class="fw-semibold">{{ ticket.department || '-' }}</div>
            </div>

            <div class="mb-3">
              <label class="form-label small text-muted text-uppercase">Creado</label>
              <div class="fw-semibold">{{ ticket.created_at }}</div>
            </div>

            <div class="mb-0">
              <label class="form-label small text-muted text-uppercase">Ultima respuesta</label>
              <div class="fw-semibold">{{ ticket.last_reply_at || '-' }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-8 d-flex flex-column">
        <div class="card border-0 shadow-sm flex-grow-1 d-flex flex-column">
          <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0 text-muted">
              <i class="bi bi-chat-left-text me-2"></i>Conversacion
            </h2>
          </div>

          <div class="card-body flex-grow-1 p-0" style="min-height: 400px;">
            <div v-if="ticket.messages.length === 0" class="text-center text-muted py-5">
              <i class="bi bi-chat-dots display-4"></i>
              <p class="mt-3">Sin mensajes aun. Inicia la conversacion.</p>
            </div>

            <div v-else class="chat-messages p-3">
              <div
                v-for="(message, index) in ticket.messages"
                :key="message.id"
                class="chat-bubble mb-3"
                :class="message.is_admin ? 'chat-bubble-admin' : 'chat-bubble-user'"
              >
                <div class="chat-header">
                  <div class="chat-avatar">
                    <i :class="message.is_admin ? 'bi bi-headset' : 'bi bi-person'"></i>
                  </div>
                  <div class="chat-meta">
                    <span class="chat-author">{{ message.is_admin ? 'Soporte' : (message.author?.name || 'Usuario') }}</span>
                    <span class="chat-time">{{ message.created_at }}</span>
                  </div>
                </div>
                <div class="chat-content">
                  {{ message.message }}
                </div>
              </div>
            </div>
          </div>

          <div class="card-footer bg-white" v-if="ticket.status !== 'closed'">
            <form @submit.prevent="submit">
              <div class="mb-3">
                <FieldTextarea
                  id="ticket-reply"
                  label="Tu respuesta"
                  v-model="form.message"
                  :formError="form.errors.message"
                  placeholder="Escribe tu mensaje aqui..."
                  rows="3"
                  required
                />
              </div>
              <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.message">
                  <i class="bi bi-send me-1"></i>
                  {{ form.processing ? 'Enviando...' : 'Enviar respuesta' }}
                </button>
              </div>
            </form>
          </div>

          <div class="card-footer bg-light text-center" v-else>
            <span class="text-muted">
              <i class="bi bi-lock me-1"></i>Este ticket esta cerrado
            </span>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  message: '',
})

const submit = () => {
  form.post(`/member/support/${props.ticket.id}/reply`)
}

const breadcrumbs = computed(() => [
  { label: 'Soporte', href: '/member/support' },
  { label: `Ticket #${props.ticket.id}`, active: true },
])

const statusClass = (value) => {
  if (value === 'open') return 'bg-success'
  if (value === 'pending') return 'bg-warning text-dark'
  if (value === 'answered') return 'bg-primary'
  if (value === 'closed') return 'bg-secondary'
  return 'bg-secondary'
}
</script>

<style scoped>
.chat-messages {
  max-height: 500px;
  overflow-y: auto;
}

.chat-bubble {
  max-width: 85%;
  padding: 12px 16px;
  border-radius: 12px;
  position: relative;
}

.chat-bubble-user {
  background: #f8f9fa;
  border-bottom-left-radius: 4px;
  margin-right: auto;
}

.chat-bubble-admin {
  background: #e7f1ff;
  border-bottom-right-radius: 4px;
  margin-left: auto;
}

.chat-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
}

.chat-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}

.chat-bubble-user .chat-avatar {
  background: #dee2e6;
  color: #6c757d;
}

.chat-bubble-admin .chat-avatar {
  background: #0d6efd;
  color: white;
}

.chat-meta {
  display: flex;
  flex-direction: column;
}

.chat-author {
  font-weight: 600;
  font-size: 13px;
}

.chat-bubble-user .chat-author {
  color: #495057;
}

.chat-bubble-admin .chat-author {
  color: #0d6efd;
}

.chat-time {
  font-size: 11px;
  color: #6c757d;
}

.chat-content {
  font-size: 14px;
  line-height: 1.5;
  color: #212529;
  white-space: pre-wrap;
}
</style>
