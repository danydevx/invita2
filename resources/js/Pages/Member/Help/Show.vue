<template>
  <MemberLayout>
    <Head :title="article.title" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">{{ article.title }}</h1>
        <p class="text-muted mb-0">{{ article.category || 'General' }} · {{ article.published_at || '-' }}</p>
      </div>
      <Link href="/member/help" class="btn btn-outline-secondary btn-sm">Volver</Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <p v-if="article.excerpt" class="text-muted">{{ article.excerpt }}</p>
        <div class="help-content">{{ article.content }}</div>
      </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">
      <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
          <div class="fw-semibold">¿No resolvio tu duda?</div>
          <div class="text-muted small">Crea un ticket para que te ayudemos.</div>
        </div>
        <Link href="/member/support/create" class="btn btn-primary">Crear ticket</Link>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

defineProps({
  article: {
    type: Object,
    required: true,
  },
})
</script>
