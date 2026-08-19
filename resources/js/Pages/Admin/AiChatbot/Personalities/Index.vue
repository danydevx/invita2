<template>
  <AdminLayout>
    <Head title="Personalidades del Chatbot" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-0">Personalidades del Chatbot</h1>
          <small class="text-muted">Catálogo global de personalidades</small>
        </div>
        <Link href="/admin/modules/ai_chatbot/personalities/create" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i>Nueva Personalidad
        </Link>
      </div>

      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Key</th>
                <th scope="col">Descripción</th>
                <th scope="col">Temp.</th>
                <th scope="col">Longitud</th>
                <th scope="col">Estado</th>
                <th scope="col" class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="personality in personalities" :key="personality.id">
                <td>
                  <div class="fw-semibold">{{ personality.display_name }}</div>
                </td>
                <td>
                  <code>{{ personality.key }}</code>
                </td>
                <td>
                  <small class="text-muted">{{ personality.description?.substring(0, 80) || '-' }}</small>
                </td>
                <td>{{ personality.default_temperature }}</td>
                <td>
                  <span class="badge text-bg-secondary">{{ personality.default_response_length }}</span>
                </td>
                <td>
                  <span :class="personality.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                    {{ personality.is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <Link :href="`/admin/modules/ai_chatbot/personalities/${personality.id}/edit`" class="btn btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <button
                      type="button"
                      class="btn btn-outline-danger"
                      @click="deletePersonality(personality)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="personalities.length === 0">
                <td colspan="7" class="text-center text-muted py-4">No hay personalidades registradas.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const personalities = page.props.personalities || []

const deletePersonality = (personality) => {
  if (confirm(`¿Eliminar la personalidad "${personality.display_name}"?`)) {
    router.delete(`/admin/modules/ai_chatbot/personalities/${personality.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
