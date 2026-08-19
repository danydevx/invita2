<template>
  <AdminLayout>
    <Head title="Editar anuncio" />

    <PageHeader :title="'Editar anuncio'" :breadcrumbs="breadcrumbs" backHref="/admin/announcements">
      <template #actions>
        <button class="btn btn-outline-danger" type="button" @click="destroy">Eliminar</button>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <label class="form-label">Titulo</label>
            <input v-model="form.title" type="text" class="form-control" :class="{ 'is-invalid': form.errors.title }" />
            <div v-if="form.errors.title" class="invalid-feedback">{{ form.errors.title }}</div>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Tipo</label>
            <select v-model="form.type" class="form-select" :class="{ 'is-invalid': form.errors.type }">
              <option value="info">info</option>
              <option value="success">success</option>
              <option value="warning">warning</option>
              <option value="danger">danger</option>
            </select>
            <div v-if="form.errors.type" class="invalid-feedback">{{ form.errors.type }}</div>
          </div>
          <div class="col-12">
            <label class="form-label">Mensaje</label>
            <textarea v-model="form.message" class="form-control" rows="6" :class="{ 'is-invalid': form.errors.message }"></textarea>
            <div v-if="form.errors.message" class="invalid-feedback">{{ form.errors.message }}</div>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Audiencia</label>
            <select v-model="form.audience" class="form-select" :class="{ 'is-invalid': form.errors.audience }">
              <option value="all">all</option>
              <option value="members">members</option>
              <option value="admins">admins</option>
            </select>
            <div v-if="form.errors.audience" class="invalid-feedback">{{ form.errors.audience }}</div>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Prioridad</label>
            <select v-model="form.priority" class="form-select" :class="{ 'is-invalid': form.errors.priority }">
              <option value="">normal</option>
              <option value="low">low</option>
              <option value="normal">normal</option>
              <option value="high">high</option>
              <option value="critical">critical</option>
            </select>
            <div v-if="form.errors.priority" class="invalid-feedback">{{ form.errors.priority }}</div>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Activo</label>
            <select v-model="form.is_active" class="form-select">
              <option :value="true">Si</option>
              <option :value="false">No</option>
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Inicio</label>
            <input v-model="form.starts_at" type="datetime-local" class="form-control" :class="{ 'is-invalid': form.errors.starts_at }" />
            <div v-if="form.errors.starts_at" class="invalid-feedback">{{ form.errors.starts_at }}</div>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Fin</label>
            <input v-model="form.ends_at" type="datetime-local" class="form-control" :class="{ 'is-invalid': form.errors.ends_at }" />
            <div v-if="form.errors.ends_at" class="invalid-feedback">{{ form.errors.ends_at }}</div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-check">
              <input id="announcement-dismissible" v-model="form.dismissible" type="checkbox" class="form-check-input" />
              <label class="form-check-label" for="announcement-dismissible">Permite cerrar</label>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">CTA (label)</label>
            <input v-model="form.action_label" type="text" class="form-control" :class="{ 'is-invalid': form.errors.action_label }" />
            <div v-if="form.errors.action_label" class="invalid-feedback">{{ form.errors.action_label }}</div>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">CTA (url)</label>
            <input v-model="form.action_url" type="url" class="form-control" :class="{ 'is-invalid': form.errors.action_url }" />
            <div v-if="form.errors.action_url" class="invalid-feedback">{{ form.errors.action_url }}</div>
          </div>
          <div class="col-12">
            <button class="btn btn-primary" type="submit" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  announcement: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  title: props.announcement.title,
  message: props.announcement.message,
  type: props.announcement.type,
  audience: props.announcement.audience,
  is_active: props.announcement.is_active,
  starts_at: props.announcement.starts_at || '',
  ends_at: props.announcement.ends_at || '',
  dismissible: props.announcement.dismissible,
  priority: props.announcement.priority || 'normal',
  action_label: props.announcement.action_label || '',
  action_url: props.announcement.action_url || '',
})

const breadcrumbs = [
  { label: 'Anuncios', href: '/admin/announcements' },
  { label: props.announcement.title, active: true },
]

const submit = () => {
  form.put(`/admin/announcements/${props.announcement.id}`)
}

const destroy = () => {
  if (!confirm('Eliminar este anuncio?')) return
  router.delete(`/admin/announcements/${props.announcement.id}`)
}
</script>
