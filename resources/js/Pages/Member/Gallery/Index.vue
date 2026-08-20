<template>
  <MemberLayout>
    <Head title="Galería" />

    <div class="d-flex justify-content-center" style="margin-top: 4rem;">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

onMounted(() => {
  router.replace('/member/listings')
})
</script>