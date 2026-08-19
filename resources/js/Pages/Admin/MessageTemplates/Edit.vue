<template>
  <AdminLayout>
    <Head title="Editar plantilla" />

    <PageHeader :title="'Editar plantilla'" :breadcrumbs="breadcrumbs" backHref="/admin/message-templates">
      <template #actions>
        <button class="btn btn-outline-danger" type="button" @click="destroy">Eliminar</button>
      </template>
    </PageHeader>

    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form class="row g-3" @submit.prevent="submit">
              <div class="col-12 col-md-6">
                <label class="form-label">Clave</label>
                <input v-model="form.key" type="text" class="form-control" :class="{ 'is-invalid': form.errors.key }" />
                <div v-if="form.errors.key" class="invalid-feedback">{{ form.errors.key }}</div>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Nombre</label>
                <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }" />
                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
              </div>
              <div class="col-12">
                <label class="form-label">Descripcion</label>
                <input v-model="form.description" type="text" class="form-control" :class="{ 'is-invalid': form.errors.description }" />
                <div v-if="form.errors.description" class="invalid-feedback">{{ form.errors.description }}</div>
              </div>
              <div class="col-12">
                <label class="form-label">Asunto (email)</label>
                <input v-model="form.subject" type="text" class="form-control" :class="{ 'is-invalid': form.errors.subject }" />
                <div v-if="form.errors.subject" class="invalid-feedback">{{ form.errors.subject }}</div>
              </div>
              <div class="col-12">
                <label class="form-label">Contenido</label>
                <textarea v-model="form.content" class="form-control" rows="8" :class="{ 'is-invalid': form.errors.content }"></textarea>
                <div v-if="form.errors.content" class="invalid-feedback">{{ form.errors.content }}</div>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">Activo</label>
                <select v-model="form.is_active" class="form-select">
                  <option :value="true">Si</option>
                  <option :value="false">No</option>
                </select>
              </div>
              <div class="col-12">
                <button class="btn btn-primary" type="submit" :disabled="form.processing">
                  {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6">Variables disponibles</h2>
            <p class="text-muted small">Las variables que no existan se reemplazan por texto vacio.</p>
            <div class="d-flex flex-wrap gap-2">
              <button
                v-for="variable in variables"
                :key="variable"
                type="button"
                class="btn btn-sm btn-outline-secondary"
                @click="copy(variable)"
              >
                {{ variable }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  template: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  key: props.template.key,
  name: props.template.name,
  description: props.template.description || '',
  subject: props.template.subject || '',
  content: props.template.content,
  is_active: props.template.is_active,
})

const variables = [
  '{{user_name}}',
  '{{user_email}}',
  '{{plan_name}}',
  '{{date}}',
  '{{ticket_id}}',
  '{{app_name}}',
  '{{support_email}}',
]

const breadcrumbs = [
  { label: 'Plantillas', href: '/admin/message-templates' },
  { label: props.template.name, active: true },
]

const submit = () => {
  form.put(`/admin/message-templates/${props.template.id}`)
}

const destroy = () => {
  if (!confirm('Eliminar esta plantilla?')) return
  router.delete(`/admin/message-templates/${props.template.id}`)
}

const copy = async (text) => {
  try {
    await navigator.clipboard.writeText(text)
  } catch (error) {
    // No bloquea el flujo si el navegador no permite copiar.
  }
}
</script>
