<template>
  <AdminLayout>
    <Head title="Crear articulo" />

    <PageHeader :title="'Crear articulo'" :breadcrumbs="breadcrumbs" backHref="/admin/help" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="article-title"
              label="Titulo"
              v-model="form.title"
              :formError="form.errors.title"
              required
            />
          </div>
          <div class="col-12 col-md-6">
            <FieldText
              id="article-slug"
              label="Slug"
              v-model="form.slug"
              :formError="form.errors.slug"
              required
            />
          </div>

          <div class="col-12">
            <FieldTextarea
              id="article-excerpt"
              label="Extracto"
              v-model="form.excerpt"
              :formError="form.errors.excerpt"
            />
          </div>

          <div class="col-12">
            <FieldTextarea
              id="article-content"
              label="Contenido"
              v-model="form.content"
              :formError="form.errors.content"
              required
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldText
              id="article-category"
              label="Categoria"
              v-model="form.category"
              :formError="form.errors.category"
            />
          </div>
          <div class="col-12 col-md-4">
            <FieldNumber
              id="article-sort"
              label="Orden"
              v-model="form.sort_order"
              :formError="form.errors.sort_order"
              :min="0"
            />
          </div>
          <div class="col-12 col-md-4">
            <FieldDate
              id="article-published-at"
              label="Publicado el"
              v-model="form.published_at"
              :formError="form.errors.published_at"
            />
          </div>

          <div class="col-12">
            <FieldSwitch
              id="article-published"
              label="Publicado"
              v-model="form.is_published"
            />
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar' }}
            </button>
            <Link href="/admin/help" class="btn btn-outline-secondary">Cancelar</Link>
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
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const form = useForm({
  title: '',
  slug: '',
  excerpt: '',
  content: '',
  category: '',
  sort_order: '',
  published_at: '',
  is_published: false,
})

const breadcrumbs = [
  { label: 'Ayuda', href: '/admin/help' },
  { label: 'Crear', active: true },
]

const submit = () => {
  form.post('/admin/help')
}
</script>
