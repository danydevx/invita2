<template>
  <MemberLayout>
    <Head :title="`Escanear - ${listing?.name || ''}`" />

    <PageHeader
      title="Escanear Tarjeta"
      :breadcrumbs="breadcrumbs"
      backHref="/member/dashboard"
      backLabel="Regresar"
    />

    <div class="mb-3 d-flex gap-2">
      <Link
        :href="`/member/listings/${listing?.id}/fidelity-cards`"
        class="btn btn-outline-secondary btn-sm"
      >
        <i class="bi bi-credit-card me-1"></i>Tarjetas
      </Link>
      <Link
        :href="`/member/listings/${listing?.id}/fidelity-cards/scan-view`"
        class="btn btn-secondary btn-sm"
      >
        <i class="bi bi-qr-code-scan me-1"></i>Escanear
      </Link>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="mb-3">Escanear código QR</h6>
            <div class="text-center mb-3">
              <div ref="scannerContainer" class="d-inline-block"></div>
            </div>
            <p v-if="scannerError" class="text-danger small">{{ scannerError }}</p>
            <p class="text-muted small text-center">
              Apunta la cámara al código QR del cliente
            </p>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="mb-3">Ingresar código manualmente</h6>
            <form @submit.prevent="submitCode">
              <div class="mb-3">
                <FieldText
                  id="public-code"
                  label="Código de tarjeta"
                  placeholder="Ej: A3B7K9X2M1P"
                  v-model="form.public_code"
                  :formError="form.errors.public_code"
                  required
                />
              </div>
              <button type="submit" class="btn btn-primary btn-sm" :disabled="form.processing || !form.public_code">
                <i class="bi bi-qr-code-scan me-1"></i>
                {{ form.processing ? 'Buscando...' : 'Buscar y Registrar Visita' }}
              </button>
            </form>
          </div>
        </div>

        <div v-if="lastScannedCard" class="card border-0 shadow-sm mt-3">
          <div class="card-body">
            <h6 class="mb-3">Última tarjeta escaneada</h6>
            <div class="d-flex align-items-center gap-3">
              <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="bi bi-person text-muted"></i>
              </div>
              <div class="flex-grow-1">
                <strong>{{ lastScannedCard.client_name }}</strong>
                <p class="text-muted small mb-0">
                  {{ lastScannedCard.visits_remaining }} visitas restantes
                </p>
              </div>
              <span v-if="lastScannedCard.is_completed" class="badge bg-success">Completada</span>
              <span v-else class="badge bg-primary">Activa</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Head, Link, usePage, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const businessMenu = computed(() => page.props.businessMenu || [])

const scannerContainer = ref(null)
const scannerError = ref(null)
const lastScannedCard = ref(null)
let html5QrCode = null

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Fidelización', href: `/member/listings/${listing.value?.id}/fidelity-cards` },
  { label: 'Escanear' },
])

const form = useForm({
  public_code: '',
})

const startScanner = async () => {
  try {
    const { Html5Qrcode } = await import('html5-qrcode')

    html5QrCode = new Html5Qrcode('scanner-container')

    await html5QrCode.start(
      { facingMode: 'environment' },
      {
        fps: 10,
        qrbox: { width: 250, height: 250 },
      },
      onScanSuccess,
      onScanFailure
    )
  } catch (err) {
    console.error('Error starting scanner:', err)
    scannerError.value = 'No se pudo iniciar la cámara. Verifica los permisos.'
  }
}

const onScanSuccess = (decodedText) => {
  form.public_code = decodedText
  submitCode()
  stopScanner()
}

const onScanFailure = (error) => {
  // Silently ignore scan failures (no QR found in frame)
}

const stopScanner = async () => {
  if (html5QrCode && html5QrCode.isScanning) {
    try {
      await html5QrCode.stop()
    } catch (err) {
      console.error('Error stopping scanner:', err)
    }
  }
}

const submitCode = () => {
  if (!form.public_code) return

  form.post(`/member/listings/${listing.value.id}/fidelity-cards/scan-by-code`, {
    preserveScroll: true,
    onSuccess: (page) => {
      if (page.props.lastScannedCard) {
        lastScannedCard.value = page.props.lastScannedCard
      }
      form.public_code = ''
    },
  })
}

onMounted(() => {
  if (scannerContainer.value) {
    startScanner()
  }
})

onUnmounted(() => {
  stopScanner()
})
</script>
