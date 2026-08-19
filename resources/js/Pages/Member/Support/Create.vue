<template>
  <MemberLayout>
    <Head title="Nuevo ticket" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Nuevo ticket</h1>
        <p class="text-muted mb-0">Describe tu solicitud para ayudarte mejor.</p>
      </div>
      <Link href="/member/support" class="btn btn-outline-secondary btn-sm">Volver</Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12">
            <FieldText
              id="ticket-subject"
              label="Asunto"
              v-model="form.subject"
              :formError="form.errors.subject"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="ticket-category"
              label="Categoria"
              v-model="form.category"
              :formError="form.errors.category"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldSelect
              id="ticket-priority"
              label="Prioridad"
              v-model="form.priority"
              :options="priorityOptions"
              :formError="form.errors.priority"
            />
          </div>

          <div class="col-12">
            <FieldTextarea
              id="ticket-message"
              label="Mensaje"
              v-model="form.message"
              :formError="form.errors.message"
              required
            />
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Enviando...' : 'Crear ticket' }}
            </button>
            <Link href="/member/support" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'

const form = useForm({
  subject: '',
  category: '',
  priority: '',
  message: '',
})

const priorityOptions = [
  { value: '', label: 'Sin prioridad' },
  { value: 'low', label: 'low' },
  { value: 'medium', label: 'medium' },
  { value: 'high', label: 'high' },
]

const submit = () => {
  form.post('/member/support')
}
</script>
