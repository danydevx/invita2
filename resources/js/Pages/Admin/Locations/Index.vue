<template>
  <AdminLayout>
    <Head title="Ubicaciones" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Ubicaciones</h1>
          <p class="text-muted mb-0">Gestión de países, estados y municipios</p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-5">
              <div class="mb-3">
                <i class="bi bi-globe text-primary fs-1"></i>
              </div>
              <h5 class="card-title">Países</h5>
              <p class="card-text text-muted small">Administra los países disponibles</p>
              <Link href="/admin/locations/countries" class="btn btn-outline-primary">
                <i class="bi bi-arrow-right me-1"></i>Gestionar
              </Link>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-5">
              <div class="mb-3">
                <i class="bi bi-map text-success fs-1"></i>
              </div>
              <h5 class="card-title">Estados</h5>
              <p class="card-text text-muted small">Administra los estados por país</p>
              <Link href="/admin/locations/states" class="btn btn-outline-success">
                <i class="bi bi-arrow-right me-1"></i>Gestionar
              </Link>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-5">
              <div class="mb-3">
                <i class="bi bi-geo-alt text-warning fs-1"></i>
              </div>
              <h5 class="card-title">Municipios</h5>
              <p class="card-text text-muted small">Administra los municipios por estado</p>
              <Link href="/admin/locations/municipalities" class="btn btn-outline-warning">
                <i class="bi bi-arrow-right me-1"></i>Gestionar
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
          <h5 class="mb-0">Resumen</h5>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-4">
              <div class="fs-3 fw-bold text-primary">{{ stats.countries }}</div>
              <div class="text-muted small">Países</div>
            </div>
            <div class="col-4">
              <div class="fs-3 fw-bold text-success">{{ stats.states }}</div>
              <div class="text-muted small">Estados</div>
            </div>
            <div class="col-4">
              <div class="fs-3 fw-bold text-warning">{{ stats.municipalities }}</div>
              <div class="text-muted small">Municipios</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  stats: Object,
})
</script>
