<template>
  <AdminLayout>
    <Head title="Nuevo Modulo" />

    <PageHeader title="Nuevo Modulo" :breadcrumbs="breadcrumbs" backHref="/admin/business-module-definitions" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <FieldText
                id="def-key"
                label="Key"
                placeholder="appointments"
                v-model="form.key"
                :formError="form.errors.key"
                required
              />
              <small class="text-muted">Identificador unico (solo letras, guiones bajos)</small>
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="def-name"
                label="Nombre"
                placeholder="Citas y Reservas"
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12">
              <FieldText
                id="def-description"
                label="Descripcion"
                placeholder="Describe el modulo..."
                v-model="form.description"
                :formError="form.errors.description"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="def-icon"
                label="Icono (Bootstrap Icons)"
                placeholder="bi bi-calendar-check"
                v-model="form.icon"
                :formError="form.errors.icon"
              />
              <small class="text-muted">
                <a href="https://icons.getbootstrap.com/" target="_blank">Ver iconos disponibles</a>
              </small>
            </div>

            <div class="col-12 col-md-6">
              <FieldNumber
                id="def-sort"
                label="Orden"
                placeholder="0"
                v-model="form.sort_order"
                :formError="form.errors.sort_order"
                :min="0"
              />
            </div>

            <div class="col-12">
              <FieldSwitch
                id="def-settings"
                label="Tiene configuraciones propias"
                v-model="form.has_settings"
              />
            </div>

            <div class="col-12">
              <FieldSwitch
                id="def-premium"
                label="Modulo premium"
                v-model="form.is_premium"
              />
              <small class="text-muted d-block mb-2">Requiere plan especifico para acceder</small>
            </div>

            <div class="col-12">
              <FieldSwitch
                id="def-show-menu"
                label="Mostrar en menu lateral"
                v-model="form.show_in_menu"
              />
              <small class="text-muted d-block mb-2">Aparecera en el sidebar de los miembros</small>
            </div>

            <div v-if="form.show_in_menu" class="col-12">
              <FieldText
                id="def-menu-title"
                label="Titulo en el menu"
                placeholder="Ej: Galeria, Citas, Clientes"
                v-model="form.menu_title"
                :formError="form.errors.menu_title"
              />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Creando...' : 'Crear Modulo' }}
            </button>
            <Link href="/admin/business-module-definitions" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const form = useForm({
  key: '',
  name: '',
  description: '',
  icon: '',
  sort_order: 0,
  has_settings: false,
  is_premium: false,
  show_in_menu: true,
  menu_title: '',
})

const breadcrumbs = [
  { label: 'Modulos de Negocio', href: '/admin/business-module-definitions' },
  { label: 'Nuevo', active: true },
]

const submit = () => {
  form.post('/admin/business-module-definitions')
}
</script>
