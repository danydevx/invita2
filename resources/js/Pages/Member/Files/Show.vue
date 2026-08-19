<template>
  <MemberLayout>
    <Head title="Detalle de archivo" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">{{ file.original_name }}</h1>
        <p class="text-muted mb-0">Detalle del archivo</p>
      </div>
      <Link href="/member/files" class="btn btn-outline-secondary btn-sm">Volver</Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-4 text-muted">Tipo</dt>
          <dd class="col-8">{{ file.type || '-' }}</dd>
          <dt class="col-4 text-muted">Mime</dt>
          <dd class="col-8">{{ file.mime_type || '-' }}</dd>
          <dt class="col-4 text-muted">Extension</dt>
          <dd class="col-8">{{ file.extension || '-' }}</dd>
          <dt class="col-4 text-muted">Tamaño</dt>
          <dd class="col-8">{{ formatSize(file.size) }}</dd>
          <dt class="col-4 text-muted">Creado</dt>
          <dd class="col-8">{{ file.created_at }}</dd>
        </dl>

        <div class="mt-3 d-flex gap-2">
          <Link :href="`/member/files/${file.id}/download`" class="btn btn-primary">Descargar</Link>
          <button class="btn btn-outline-danger" type="button" @click="remove">Eliminar</button>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  file: {
    type: Object,
    required: true,
  },
})

const remove = () => {
  if (!confirm('Eliminar este archivo?')) return
  router.delete(`/member/files/${props.file.id}`)
}

const formatSize = (bytes) => {
  if (!bytes) return '0 KB'
  const kb = bytes / 1024
  if (kb < 1024) return `${kb.toFixed(0)} KB`
  return `${(kb / 1024).toFixed(2)} MB`
}
</script>
