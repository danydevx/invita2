<template>
  <MemberLayout>
    <Head title="Checkout cancelado" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          <span class="badge text-bg-warning">Cancelado</span>
          <h1 class="h4 mb-0">Checkout cancelado</h1>
        </div>

        <p class="text-muted">
          No se realizo ningun cobro. Puedes volver a intentarlo cuando quieras.
        </p>

        <div class="d-flex flex-wrap gap-2 mt-4">
          <Link href="/pricing" class="btn btn-primary">Ver planes</Link>
          <Link href="/member/plan-selection" class="btn btn-outline-secondary">Volver</Link>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
</script>
