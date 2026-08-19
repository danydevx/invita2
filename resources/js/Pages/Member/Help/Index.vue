<template>
  <MemberLayout>
    <Head title="Ayuda" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Ayuda</h1>
        <p class="text-muted mb-0">Encuentra respuestas rapidas.</p>
      </div>
      <Link href="/member/support/create" class="btn btn-outline-secondary btn-sm">Crear ticket</Link>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input v-model="search" type="text" class="form-control" placeholder="Buscar en ayuda" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Categoria</label>
            <select v-model="category" class="form-select">
              <option value="">Todas</option>
              <option v-for="item in categories" :key="item" :value="item">{{ item }}</option>
            </select>
          </div>
          <div class="col-12 col-md-2 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
            <button class="btn btn-outline-secondary" type="button" @click="clearFilters">Limpiar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="row g-3">
      <div v-if="articles.data.length === 0" class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center text-muted py-5">No hay articulos publicados.</div>
        </div>
      </div>

      <div v-for="article in articles.data" :key="article.id" class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="badge text-bg-light border">{{ article.category || 'General' }}</span>
              <span class="text-muted small">{{ article.published_at || '-' }}</span>
            </div>
            <h2 class="h5 mb-2">{{ article.title }}</h2>
            <p class="text-muted mb-3">{{ article.excerpt || 'Consulta el detalle del articulo.' }}</p>
            <Link :href="`/member/help/${article.slug}`" class="btn btn-outline-primary btn-sm">Leer mas</Link>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mt-3">
      <div class="text-muted small">
        Mostrando {{ articles.data.length }} de {{ articles.total }} registros
      </div>
      <Pagination :links="articles.links" />
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import Pagination from '@/Components/Member/Pagination.vue'

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
const category = ref(props.filters.category ?? '')

const submitSearch = () => {
  router.get(
    '/member/help',
    { search: search.value, category: category.value },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  search.value = ''
  category.value = ''
  submitSearch()
}
</script>
