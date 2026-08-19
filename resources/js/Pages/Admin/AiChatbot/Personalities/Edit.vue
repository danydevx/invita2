<template>
  <AdminLayout>
    <Head :title="`Editar Personalidad - ${personality?.display_name || 'Cargando...'}`" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-0">Editar Personalidad</h1>
          <small class="text-muted" v-if="personality">{{ personality.display_name }}</small>
        </div>
        <Link href="/admin/modules/ai_chatbot/personalities" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
      </div>

      <div v-if="$page.props.flash?.error" class="alert alert-danger">
        {{ $page.props.flash.error }}
      </div>

      <div v-if="$page.props.errors && Object.keys($page.props.errors).length" class="alert alert-danger">
        <ul class="mb-0">
          <li v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</li>
        </ul>
      </div>

      <form v-if="personality" @submit.prevent="submit">
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0">Información General</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Key *</label>
                  <input v-model="form.key" type="text" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Nombre para Mostrar *</label>
                  <input v-model="form.display_name" type="text" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Descripción</label>
                  <textarea v-model="form.description" class="form-control" rows="2"></textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label">Hint para System Prompt</label>
                  <textarea v-model="form.system_prompt_hint" class="form-control" rows="3"></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0">Configuración</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Temperatura por Defecto</label>
                  <input
                    v-model.number="form.default_temperature"
                    type="number"
                    step="0.05"
                    min="0"
                    max="1"
                    class="form-control"
                  />
                </div>

                <div class="mb-3">
                  <label class="form-label">Longitud de Respuesta por Defecto</label>
                  <select v-model="form.default_response_length" class="form-select">
                    <option value="short">Corta</option>
                    <option value="medium">Media</option>
                    <option value="long">Larga</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label">Orden</label>
                  <input v-model.number="form.sort_order" type="number" class="form-control" min="0" />
                </div>

                <div class="form-check form-switch">
                  <input v-model="form.is_active" class="form-check-input" type="checkbox" />
                  <label class="form-check-label">Activa</label>
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100" :disabled="saving">
              <span v-if="saving">Guardando...</span>
              <span v-else>Actualizar Personalidad</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref, watchEffect } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const personality = computed(() => page.props.personality)

const saving = ref(false)

const form = reactive({
  key: '',
  display_name: '',
  description: '',
  system_prompt_hint: '',
  default_temperature: 0.70,
  default_response_length: 'medium',
  is_active: true,
  sort_order: 0,
})

const initializeForm = () => {
  if (personality.value) {
    form.key = personality.value.key || ''
    form.display_name = personality.value.display_name || ''
    form.description = personality.value.description || ''
    form.system_prompt_hint = personality.value.system_prompt_hint || ''
    form.default_temperature = parseFloat(personality.value.default_temperature) || 0.70
    form.default_response_length = personality.value.default_response_length || 'medium'
    form.is_active = personality.value.is_active ?? true
    form.sort_order = personality.value.sort_order || 0
  }
}

watchEffect(() => {
  if (personality.value) {
    initializeForm()
  }
})

const submit = () => {
  saving.value = true
  router.put(`/admin/modules/ai_chatbot/personalities/${personality.value.id}`, form, {
    onFinish: () => {
      saving.value = false
    },
  })
}
</script>
