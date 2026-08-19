<template>
  <AdminLayout>
    <Head title="Ticket" />

    <PageHeader :title="`Ticket #${ticket.id}`" :breadcrumbs="breadcrumbs" backHref="/admin/support" />

    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Detalle</h2>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Asunto</span>
              <span class="fw-semibold">{{ ticket.subject }}</span>
            </div>
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
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Usuario</span>
              <span>{{ ticket.user?.name || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Email</span>
              <span>{{ ticket.user?.email || '-' }}</span>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
          <div class="card-body">
            <h3 class="h6 mb-3">Actualizar</h3>
            <form @submit.prevent="submitUpdate">
              <div class="mb-2">
                <FieldSelect
                  id="ticket-status"
                  label="Estado"
                  v-model="form.status"
                  :options="statusOptions"
                  :formError="form.errors.status"
                  required
                />
              </div>
              <div class="mb-2">
                <FieldSelect
                  id="ticket-priority"
                  label="Prioridad"
                  v-model="form.priority"
                  :options="priorityOptions"
                  :formError="form.errors.priority"
                />
              </div>
              <div class="mb-3">
                <FieldText
                  id="ticket-category"
                  label="Categoria"
                  v-model="form.category"
                  :formError="form.errors.category"
                />
              </div>
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                {{ form.processing ? 'Guardando...' : 'Actualizar' }}
              </button>
            </form>
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
            <form @submit.prevent="submitReply">
              <div class="mb-3">
                <FieldTextarea
                  id="ticket-reply"
                  label="Mensaje"
                  v-model="replyForm.message"
                  :formError="replyForm.errors.message"
                  required
                />
              </div>
              <button type="submit" class="btn btn-primary" :disabled="replyForm.processing">
                {{ replyForm.processing ? 'Enviando...' : 'Enviar respuesta' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  status: props.ticket.status,
  priority: props.ticket.priority || '',
  category: props.ticket.category || '',
})

const replyForm = useForm({
  message: '',
})

const statusOptions = [
  { value: 'open', label: 'open' },
  { value: 'pending', label: 'pending' },
  { value: 'answered', label: 'answered' },
  { value: 'closed', label: 'closed' },
]

const priorityOptions = [
  { value: '', label: 'Sin prioridad' },
  { value: 'low', label: 'low' },
  { value: 'medium', label: 'medium' },
  { value: 'high', label: 'high' },
]

const breadcrumbs = [
  { label: 'Soporte', href: '/admin/support' },
  { label: `Ticket #${props.ticket.id}`, active: true },
]

const submitUpdate = () => {
  form.put(`/admin/support/${props.ticket.id}`)
}

const submitReply = () => {
  replyForm.post(`/admin/support/${props.ticket.id}/reply`)
}

const statusClass = (value) => {
  if (value === 'open') return 'text-bg-success'
  if (value === 'pending') return 'text-bg-warning'
  if (value === 'answered') return 'text-bg-primary'
  if (value === 'closed') return 'text-bg-secondary'
  return 'text-bg-secondary'
}
</script>
