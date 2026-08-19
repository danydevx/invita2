<template>
  <AdminLayout>
    <Head :title="`Preguntas Frecuentes - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Preguntas Frecuentes</h1>
      </div>
      <div class="d-flex gap-2">
        <Link :href="`/admin/businesses/${business.id}/faq-categories`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-folder me-1"></i>Categorias
        </Link>
        <Link :href="`/admin/businesses/${business.id}/faqs/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Pregunta
        </Link>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Pregunta</th>
                <th scope="col">Respuesta</th>
                <th scope="col">Categoria</th>
                <th scope="col">Activo</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="faqs.data.length === 0">
                <td colspan="5" class="text-center text-muted py-4">
                  No hay preguntas frecuentes registradas.
                </td>
              </tr>
              <tr v-for="faq in faqs.data" :key="faq.id">
                <td class="fw-semibold">{{ faq.question }}</td>
                <td class="text-muted small">{{ faq.answer.substring(0, 60) }}{{ faq.answer.length > 60 ? '...' : '' }}</td>
                <td>
                  <span v-if="faq.category" class="badge bg-light text-dark">{{ faq.category.name }}</span>
                  <span v-else class="text-muted">-</span>
                </td>
                <td>
                  <span v-if="faq.is_active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </td>
                <td class="text-end">
                  <Link :href="`/admin/businesses/${business.id}/faqs/${faq.id}/edit`" class="btn btn-sm btn-outline-primary">
                    Editar
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="faqs.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="faqs.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const page = usePage()
const business = computed(() => page.props.business)
const faqs = computed(() => page.props.faqs || { data: [], links: [] })
</script>
