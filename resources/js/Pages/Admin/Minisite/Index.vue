<template>
  <AdminLayout>
    <Head :title="`Minisite - ${listing.name}`" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Minisite</h1>
          <p class="text-muted mb-0">{{ listing.name }}</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label">Theme</label>
                <input v-model="form.theme" type="text" class="form-control" placeholder="default">
              </div>

              <div class="col-md-3">
                <label class="form-label">Primary Color</label>
                <div class="input-group">
                  <input v-model="form.primary_color" type="color" class="form-control form-control-color">
                  <input v-model="form.primary_color" type="text" class="form-control">
                </div>
              </div>

              <div class="col-md-3">
                <label class="form-label">Secondary Color</label>
                <div class="input-group">
                  <input v-model="form.secondary_color" type="color" class="form-control form-control-color">
                  <input v-model="form.secondary_color" type="text" class="form-control">
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Font Family</label>
                <input v-model="form.font_family" type="text" class="form-control" placeholder="Inter, sans-serif">
              </div>

              <div class="col-12">
                <label class="form-label">Custom CSS</label>
                <textarea v-model="form.custom_css" class="form-control" rows="4" placeholder=".minisite-custom { ... }"></textarea>
              </div>
            </div>

            <div class="mt-4">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
                <span v-else>Guardar cambios</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  listing: Object,
  setting: Object,
})

const form = useForm({
  theme: props.setting?.theme || 'default',
  primary_color: props.setting?.primary_color || '#0d6efd',
  secondary_color: props.setting?.secondary_color || '#6c757d',
  font_family: props.setting?.font_family || 'Inter, sans-serif',
  custom_css: props.setting?.custom_css || '',
})

let sending = false

const submit = () => {
  sending = true
  form.post(`/admin/listings/${props.listing.id}/minisite`, {
    onFinish: () => {
      sending = false
    },
  })
}
</script>
