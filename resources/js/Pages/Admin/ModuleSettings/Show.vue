<template>
  <AdminLayout>
    <Head :title="`Configuracion - ${moduleName}`" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i :class="moduleIcon || 'bi bi-gear'"></i>
          </div>
          <div>
            <h1 class="h4 mb-0">Configuracion: {{ moduleName }}</h1>
            <small class="text-muted">Ajustes generales del modulo</small>
          </div>
        </div>
        <Link href="/admin/business-module-definitions" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
      </div>

      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <div v-if="links && links.length > 0" class="mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="text-muted mb-3"><i class="bi bi-link-45deg me-1"></i>Accesos directos</h6>
            <div class="row g-2">
              <div v-for="link in links" :key="link.href" class="col-auto">
                <Link :href="link.href" class="btn btn-outline-primary btn-sm">
                  <i :class="link.icon" class="me-1"></i>
                  {{ link.label }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="row g-4">
              <div v-for="field in schema" :key="field.key" :class="getFieldCol(field.type)">

                <div v-if="field.type === 'number'" class="mb-3">
                  <label class="form-label">{{ field.label }}</label>
                  <input
                    type="number"
                    class="form-control"
                    :value="settings[field.key]"
                    @input="settings[field.key] = $event.target.value ? parseInt($event.target.value) : null"
                    :min="field.min"
                    :max="field.max"
                  />
                  <small v-if="field.min || field.max" class="text-muted">
                    Min: {{ field.min }}, Max: {{ field.max }}
                  </small>
                </div>

                <div v-else-if="field.type === 'boolean'" class="mb-3">
                  <div class="form-check form-switch">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      :id="`field-${field.key}`"
                      :checked="settings[field.key]"
                      @change="settings[field.key] = $event.target.checked"
                    />
                    <label class="form-check-label" :for="`field-${field.key}`">
                      {{ field.label }}
                    </label>
                  </div>
                </div>

                <div v-else-if="field.type === 'array'" class="mb-3">
                  <label class="form-label">{{ field.label }}</label>
                  <input
                    type="text"
                    class="form-control"
                    :value="(settings[field.key] || []).join(', ')"
                    @blur="settings[field.key] = parseArrayInput($event.target.value)"
                    :placeholder="field.placeholder || 'Valor1, Valor2, ...'"
                  />
                  <small class="text-muted">Separar valores con comas</small>
                </div>

                <div v-else class="mb-3">
                  <label class="form-label">{{ field.label }}</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="settings[field.key]"
                  />
                </div>

              </div>
            </div>

            <div v-if="schema.length === 0" class="text-center py-5">
              <i class="bi bi-gear display-1 text-muted"></i>
              <p class="text-muted mt-3">Este modulo no tiene opciones de configuracion.</p>
            </div>

            <div class="d-flex gap-2 mt-4" v-if="schema.length > 0">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
                <span v-else><i class="bi bi-check me-1"></i>Guardar Configuracion</span>
              </button>
              <button type="button" class="btn btn-outline-secondary" @click="resetDefaults">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar valores por defecto
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  moduleKey: String,
  moduleName: String,
  moduleIcon: String,
  settings: Object,
  schema: Array,
  links: Array,
})

const sending = ref(false)

const settings = reactive({ ...props.settings })

const getFieldCol = (type) => {
  return type === 'boolean' ? 'col-md-6' : 'col-md-6'
}

const parseArrayInput = (value) => {
  if (!value) return []
  return value.split(',').map(v => v.trim()).filter(v => v.length > 0)
}

const resetDefaults = () => {
  props.schema.forEach(field => {
    settings[field.key] = field.default ?? null
  })
}

const submit = () => {
  sending.value = true
  router.put(`/admin/modules/${props.moduleKey}/settings`, settings, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
