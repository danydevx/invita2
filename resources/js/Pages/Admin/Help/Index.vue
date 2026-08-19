<template>
  <AdminLayout>
    <Head title="Ayuda" />

    <PageHeader :title="'Ayuda'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/help/create" class="btn btn-primary">Crear articulo</Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-5">
            <label class="form-label">Buscar</label>
            <input v-model="search" type="text" class="form-control" placeholder="Titulo o slug" />
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label">Estado</label>
            <select v-model="status" class="form-select">
              <option value="">Todos</option>
              <option value="published">Publicado</option>
              <option value="draft">Borrador</option>
            </select>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label">Categoria</label>
            <select v-model="category" class="form-select">
              <option value="">Todas</option>
              <option v-for="item in categories" :key="item" :value="item">{{ item }}</option>
            </select>
          </div>
          <div class="col-12 col-md-1 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
          </div>
          <div class="col-12">
            <button class="btn btn-outline-secondary" type="button" @click="clearFilters">Limpiar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Titulo</th>
              <th scope="col">Slug</th>
              <th scope="col">Categoria</th>
              <th scope="col">Estado</th>
              <th scope="col">Publicado</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="articles.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay articulos registrados.</td>
            </tr>
            <tr v-for="article in articles.data" :key="article.id">
              <td class="fw-semibold">{{ article.title }}</td>
              <td class="text-muted">{{ article.slug }}</td>
              <td class="text-muted">{{ article.category || '-' }}</td>
              <td>
                <span v-if="article.is_published" class="badge text-bg-success">Publicado</span>
                <span v-else class="badge text-bg-secondary">Borrador</span>
              </td>
              <td class="text-muted">{{ article.published_at || '-' }}</td>
              <td class="text-end">
                <Link :href="`/admin/help/${article.id}/edit`" class="btn btn-sm btn-outline-primary">
                  Editar
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ articles.data.length }} de {{ articles.total }} registros
        </div>
        <Pagination :links="articles.links" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  articles: {
    type: Object,
    required: true,
  },
  categories: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')
const category = ref(props.filters.category ?? '')

const breadcrumbs = [
  { label: 'Ayuda' },
  { label: 'Articulos', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/help',
    { search: search.value, status: status.value, category: category.value },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  search.value = ''
  status.value = ''
  category.value = ''
  submitSearch()
}
</script>
