<template>
  <MemberLayout>
    <Head title="Ticket" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">{{ ticket.subject }}</h1>
        <p class="text-muted mb-0">Ticket #{{ ticket.id }}</p>
      </div>
      <Link href="/member/support" class="btn btn-outline-secondary btn-sm">Volver</Link>
    </div>

    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Estado</h2>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Estado</span>
              <span class="badge" :class="statusClass(ticket.status)">{{ ticket.status }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Prioridad</span>
              <span>{{ ticket.priority || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Categoria</span>
              <span>{{ ticket.category || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Ultima respuesta</span>
              <span>{{ ticket.last_reply_at || '-' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Conversacion</h2>
            <div v-if="ticket.messages.length === 0" class="text-muted">Sin mensajes.</div>
            <div v-else class="d-flex flex-column gap-3">
              <div v-for="message in ticket.messages" :key="message.id" class="border rounded-3 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="fw-semibold">
                    {{ message.is_admin ? 'Soporte' : (message.author?.name || 'Usuario') }}
                  </div>
                  <div class="text-muted small">{{ message.created_at }}</div>
                </div>
                <div>{{ message.message }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm mt-3" v-if="ticket.status !== 'closed'">
          <div class="card-body">
            <h3 class="h6 mb-3">Responder</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <FieldTextarea
                  id="ticket-reply"
                  label="Mensaje"
                  v-model="form.message"
                  :formError="form.errors.message"
                  required
                />
              </div>
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                {{ form.processing ? 'Enviando...' : 'Enviar respuesta' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
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

const statusClass = (value) => {
  if (value === 'open') return 'text-bg-success'
  if (value === 'pending') return 'text-bg-warning'
  if (value === 'answered') return 'text-bg-primary'
  if (value === 'closed') return 'text-bg-secondary'
  return 'text-bg-secondary'
}
</script>
