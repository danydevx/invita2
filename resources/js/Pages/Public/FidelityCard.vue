<template>
  <div class="min-h-screen bg-light d-flex align-items-center justify-content-center py-4">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-5">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
              <div class="text-center mb-4">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                  <i class="bi bi-credit-card text-white fs-4"></i>
                </div>
                <h4 class="mb-1">{{ business?.name }}</h4>
                <p class="text-muted small mb-0">Tarjeta de Fidelidad</p>
              </div>

              <div class="text-center mb-4">
                <h5 class="fw-bold">{{ card?.client_name }}</h5>
                <span v-if="card?.is_completed" class="badge bg-success px-3 py-2">
                  <i class="bi bi-check-circle me-1"></i>Completada
                </span>
              </div>

              <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Progreso</span>
                  <span class="fw-bold">{{ card?.current_visits }} / {{ card?.max_visits }}</span>
                </div>
                <div class="progress" style="height: 12px;">
                  <div
                    class="progress-bar"
                    :class="card?.is_completed ? 'bg-success' : 'bg-primary'"
                    :style="{ width: card?.progress_percentage + '%' }"
                  ></div>
                </div>
                <p class="text-center text-muted small mt-2 mb-0">
                  {{ card?.visits_remaining }} visitas restantes
                </p>
              </div>

              <div v-if="card?.description" class="alert alert-info mb-4">
                <p class="mb-0 small">{{ card.description }}</p>
              </div>

              <div class="text-center">
                <p class="text-muted small mb-2">Código de tu tarjeta</p>
                <div class="bg-light rounded p-3 d-inline-block">
                  <span class="fw-bold fs-5 letter-spacing">{{ card?.public_code }}</span>
                </div>
                <p class="text-muted small mt-2">
                  Muestra este código al negocio para registrar tus visitas
                </p>
              </div>
            </div>
          </div>

          <p class="text-center text-muted small mt-4">
            Powered by <strong>SaaS</strong>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  business: Object,
  card: Object,
})
</script>

<style scoped>
.letter-spacing {
  letter-spacing: 2px;
}
</style>
