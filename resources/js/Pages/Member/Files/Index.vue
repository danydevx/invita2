<template>
  <MemberLayout>
    <Head title="Archivos" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Archivos</h1>
        <p class="text-muted mb-0">Sube y gestiona tus archivos.</p>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-8">
            <label class="form-label">Archivo</label>
            <input ref="fileInput" type="file" class="form-control" @change="handleFile" />
            <div v-if="form.errors.file" class="text-danger small mt-1">{{ form.errors.file }}</div>
            <div class="text-muted small mt-1">
              Max {{ maxSizeKb / 1024 }} MB · Tipos: {{ allowedTypes.join(', ') }}
            </div>
          </div>
          <div class="col-12 col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100" type="submit" :disabled="form.processing">
              {{ form.processing ? 'Subiendo...' : 'Subir archivo' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col">Tipo</th>
              <th scope="col">Mime</th>
              <th scope="col">Tamaño</th>
              <th scope="col">Fecha</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="files.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay archivos.</td>
            </tr>
            <tr v-for="file in files.data" :key="file.id">
              <td class="fw-semibold">{{ file.original_name }}</td>
              <td class="text-muted">{{ file.type || '-' }}</td>
              <td class="text-muted">{{ file.mime_type || '-' }}</td>
              <td class="text-muted">{{ formatSize(file.size) }}</td>
              <td class="text-muted">{{ file.created_at }}</td>
              <td class="text-end">
                <div class="d-inline-flex gap-2">
                  <Link :href="`/member/files/${file.id}`" class="btn btn-sm btn-outline-secondary">Ver</Link>
                  <Link
                    :href="`/member/files/${file.id}/download`"
                    class="btn btn-sm btn-outline-primary"
                  >
                    Descargar
                  </Link>
                  <button class="btn btn-sm btn-outline-danger" type="button" @click="remove(file)">
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ files.data.length }} de {{ files.total }} registros</div>
        <Pagination :links="files.links" />
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  files: {
    type: Object,
    required: true,
  },
  maxSizeKb: {
    type: Number,
    default: 5120,
  },
  allowedTypes: {
    type: Array,
    default: () => [],
  },
})

const fileInput = ref(null)
const form = useForm({
  file: null,
})

const handleFile = (event) => {
  form.file = event.target.files[0]
}

const submit = () => {
  form.post('/member/files', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('file')
      if (fileInput.value) fileInput.value.value = ''
    },
  })
}

const remove = (file) => {
  if (!confirm('Eliminar este archivo?')) return
  router.delete(`/member/files/${file.id}`, { preserveScroll: true })
}

const formatSize = (bytes) => {
  if (!bytes) return '0 KB'
  const kb = bytes / 1024
  if (kb < 1024) return `${kb.toFixed(0)} KB`
  return `${(kb / 1024).toFixed(2)} MB`
}
</script>
