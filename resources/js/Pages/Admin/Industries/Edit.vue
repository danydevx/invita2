<template>
  <AdminLayout>
    <Head title="Editar Industria" />

    <PageHeader title="Editar Industria" :breadcrumbs="breadcrumbs" backHref="/admin/industries" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <FieldText
                id="industry-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="industry-slug"
                label="Slug"
                v-model="form.slug"
                :formError="errors.slug"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="industry-icon"
                label="Icono (Bootstrap Icons)"
                v-model="form.icon"
                :formError="errors.icon"
              />
              <small class="text-muted">
                <a href="https://icons.getbootstrap.com/" target="_blank">Ver iconos disponibles</a>
              </small>
            </div>

            <div class="col-12 col-md-6">
              <FieldSwitch
                id="industry-active"
                label="Industria activa"
                v-model="form.is_active"
              />
            </div>

            <div class="col-12">
              <FieldText
                id="industry-description"
                label="Descripcion"
                v-model="form.description"
                :formError="errors.description"
              />
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">
                Modulos de la Industria
                <span class="text-muted fw-normal">(selecciona los modulos disponibles para esta industria)</span>
              </label>

              <div v-if="errors.module_ids" class="text-danger small mb-2">{{ errors.module_ids }}</div>

              <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                  <div
                    v-for="mod in availableModules"
                    :key="mod.id"
                    class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom"
                    :class="{ 'bg-light': form.module_ids.includes(mod.id) }"
                    :style="{ borderColor: '#dee2e6 !important' }"
                  >
                    <div class="d-flex align-items-center gap-2">
                      <i :class="mod.icon || 'bi bi-box'"></i>
                      <span>{{ mod.name }}</span>
                      <span v-if="mod.is_premium" class="badge bg-warning text-dark">Premium</span>
                    </div>
                    <div class="form-check form-switch m-0">
                      <input
                        type="checkbox"
                        class="form-check-input"
                        role="switch"
                        :id="`mod-${mod.id}`"
                        :checked="form.module_ids.includes(mod.id)"
                        @change="toggleModule(mod.id)"
                      >
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-2">
                <button type="button" class="btn btn-link btn-sm p-0" @click="selectAll">
                  Seleccionar todos
                </button>
                <span class="text-muted mx-2">|</span>
                <button type="button" class="btn btn-link btn-sm p-0" @click="selectNone">
                  Deseleccionar todos
                </button>
              </div>
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Actualizando...' : 'Actualizar Industria' }}
            </button>
            <Link href="/admin/industries" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const industry = page.props.industry
const availableModules = page.props.availableModules
const errors = page.props.errors || {}
const sending = ref(false)

const form = reactive({
  name: industry.name,
  slug: industry.slug,
  icon: industry.icon || '',
  description: industry.description || '',
  is_active: !!industry.is_active,
  module_ids: industry.module_ids || [],
})

const breadcrumbs = [
  { label: 'Dashboard', href: '/admin' },
  { label: 'Configuracion', href: '/admin/settings' },
  { label: 'Industrias', href: '/admin/industries' },
  { label: 'Editar', active: true },
]

const toggleModule = (id) => {
  const idx = form.module_ids.indexOf(id)
  if (idx === -1) {
    form.module_ids.push(id)
  } else {
    form.module_ids.splice(idx, 1)
  }
}

const selectAll = () => {
  form.module_ids = availableModules.map(m => m.id)
}

const selectNone = () => {
  form.module_ids = []
}

const submit = () => {
  sending.value = true
  router.put(`/admin/industries/${industry.id}`, form, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
