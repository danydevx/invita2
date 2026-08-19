<template>
  <div class="checkout-form">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
          <i class="bi bi-cart-check me-2"></i>
          Finalizar Pedido
        </h5>
      </div>

      <div class="card-body">
        <div v-if="Object.keys(errors).length" class="alert alert-danger mb-3">
          <ul class="mb-0">
            <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
          </ul>
        </div>

        <div class="mb-4">
          <label class="form-label fw-bold">Tipo de entrega</label>
          <div class="d-flex gap-3">
            <div
              class="form-check flex-fill"
              :class="{ 'border border-primary rounded p-3': orderType === 'delivery' }"
            >
              <input
                class="form-check-input"
                type="radio"
                id="typeDelivery"
                value="delivery"
                v-model="orderType"
              />
              <label class="form-check-label w-100" for="typeDelivery">
                <i class="bi bi-truck me-1"></i>
                Delivery
              </label>
            </div>
            <div
              class="form-check flex-fill"
              :class="{ 'border border-primary rounded p-3': orderType === 'pickup' }"
            >
              <input
                class="form-check-input"
                type="radio"
                id="typePickup"
                value="pickup"
                v-model="orderType"
              />
              <label class="form-check-label w-100" for="typePickup">
                <i class="bi bi-shop me-1"></i>
                Recoger en local
              </label>
            </div>
          </div>
        </div>

        <div v-if="orderType === 'delivery' && businessLocations.length > 0" class="mb-3">
          <label class="form-label">Ubicación del negocio</label>
          <select class="form-select" v-model="selectedLocationId">
            <option value="">Selecciona una ubicación</option>
            <option v-for="loc in businessLocations" :key="loc.id" :value="loc.id">
              {{ loc.name }}
            </option>
          </select>
        </div>

        <div v-if="orderType === 'delivery'" class="mb-3">
          <label class="form-label fw-bold">Dirección de entrega</label>
          <div class="row g-2">
            <div class="col-12">
              <div class="input-group">
                <input
                  type="text"
                  class="form-control"
                  v-model="deliveryAddress"
                  placeholder="Calle, número, colonia..."
                  :class="{ 'is-invalid': errors.delivery_address }"
                />
                <button
                  class="btn btn-outline-secondary"
                  type="button"
                  @click="useCurrentLocation"
                  title="Usar mi ubicación actual"
                >
                  <i class="bi bi-crosshair"></i>
                </button>
              </div>
            </div>
            <div class="col-12">
              <input
                type="text"
                class="form-control"
                v-model="deliveryReferences"
                placeholder="Referencias (entre calles, color de casa, etc.)"
              />
            </div>
          </div>

          <div v-if="distanceKm !== null" class="mt-2">
            <div class="alert" :class="canDeliver ? 'alert-success' : 'alert-danger'">
              <i :class="canDeliver ? 'bi bi-check-circle' : 'bi bi-x-circle'"></i>
              <span v-if="canDeliver">
                Distancia: {{ distanceKm.toFixed(1) }} km - Costo de envío: ${{ deliveryFee.toFixed(2) }}
              </span>
              <span v-else>
                No hacemos deliveries a tu zona ({{ distanceKm.toFixed(1) }} km, máximo: {{ deliveryRadiusKm }} km)
              </span>
            </div>
          </div>
        </div>

        <div v-if="orderType === 'pickup'" class="mb-3">
          <label class="form-label fw-bold">Datos de recogida</label>
          <div class="mb-2">
            <select class="form-select" v-model="selectedPickupLocationId">
              <option value="">Selecciona punto de recogida</option>
              <option v-for="loc in businessLocations" :key="loc.id" :value="loc.id">
                {{ loc.name }} - {{ loc.address }}
              </option>
            </select>
          </div>
          <input
            type="time"
            class="form-control"
            v-model="pickupTime"
            placeholder="Hora estimada de recogida"
          />
        </div>

        <hr />

        <div class="mb-3">
          <label class="form-label fw-bold">Tus datos</label>
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <input
                type="text"
                class="form-control"
                v-model="customerName"
                placeholder="Nombre completo *"
                :class="{ 'is-invalid': errors.customer_name }"
              />
            </div>
            <div class="col-12 col-md-6">
              <input
                type="tel"
                class="form-control"
                v-model="customerPhone"
                placeholder="Teléfono *"
                :class="{ 'is-invalid': errors.customer_phone }"
              />
            </div>
            <div class="col-12">
              <input
                type="email"
                class="form-control"
                v-model="customerEmail"
                placeholder="Email (opcional)"
              />
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Notas del pedido</label>
          <textarea
            class="form-control"
            v-model="notes"
            rows="2"
            placeholder="Alguna indicación especial..."
          ></textarea>
        </div>

        <hr />

        <div class="order-summary mb-3">
          <h6 class="fw-bold mb-2">Resumen del pedido</h6>
          <div v-for="item in cartItems" :key="item.id" class="d-flex justify-content-between mb-1">
            <span>{{ item.quantity }}x {{ item.title }}</span>
            <span>${{ (item.unit_price * item.quantity).toFixed(2) }}</span>
          </div>
          <hr />
          <div class="d-flex justify-content-between mb-1">
            <span>Subtotal:</span>
            <span>${{ subtotal.toFixed(2) }}</span>
          </div>
          <div v-if="orderType === 'delivery'" class="d-flex justify-content-between mb-1">
            <span>Delivery:</span>
            <span>${{ deliveryFee.toFixed(2) }}</span>
          </div>
          <div class="d-flex justify-content-between fw-bold fs-5 mt-2">
            <span>Total:</span>
            <span>${{ total.toFixed(2) }}</span>
          </div>
        </div>

        <button
          class="btn btn-success w-100 btn-lg"
          @click="submitOrder"
          :disabled="submitting || !canSubmit"
        >
          <i class="bi bi-whatsapp me-2"></i>
          {{ submitting ? 'Enviando...' : 'Enviar por WhatsApp' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useCart } from '@/composables/useCart'
import axios from 'axios'

const props = defineProps({
  businessId: {
    type: [Number, String],
    required: true,
  },
  businessLocations: {
    type: Array,
    default: () => [],
  },
  orderSettings: {
    type: Object,
    default: () => ({}),
  },
  whatsappNumber: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['success', 'error'])

const cart = useCart()

const orderType = ref(props.orderSettings.order_type === 'pickup' ? 'pickup' : 'delivery')
const selectedLocationId = ref('')
const selectedPickupLocationId = ref('')
const pickupTime = ref('')
const deliveryAddress = ref('')
const deliveryReferences = ref('')
const customerName = ref('')
const customerPhone = ref('')
const customerEmail = ref('')
const notes = ref('')
const submitting = ref(false)
const errors = ref({})
const distanceKm = ref(null)
const deliveryLat = ref(null)
const deliveryLng = ref(null)

const cartItems = computed(() => cart.items.value)
const subtotal = computed(() => cart.subtotal.value)

const deliveryRadiusKm = computed(() => parseFloat(props.orderSettings.delivery_radius_km) || 10)
const deliveryFeeBase = computed(() => parseFloat(props.orderSettings.delivery_fee_base) || 30)
const freeDeliveryThreshold = computed(() => parseFloat(props.orderSettings.free_delivery_threshold) || null)

const canDeliver = computed(() => {
  if (distanceKm.value === null) return true
  return distanceKm.value <= deliveryRadiusKm.value
})

const deliveryFee = computed(() => {
  if (orderType.value !== 'delivery') return 0
  if (distanceKm.value === null) return 0
  if (freeDeliveryThreshold.value && subtotal.value >= freeDeliveryThreshold.value) return 0

  const extraKm = Math.max(0, distanceKm.value - 1)
  return deliveryFeeBase.value + (extraKm * 3)
})

const total = computed(() => subtotal.value + deliveryFee.value)

const canSubmit = computed(() => {
  if (!customerName.value || !customerPhone.value) return false
  if (cart.isEmpty.value) return false
  if (orderType.value === 'delivery') {
    if (!deliveryAddress.value) return false
    if (!canDeliver.value) return false
  }
  return true
})

watch([deliveryAddress], async () => {
  if (orderType.value === 'delivery' && deliveryAddress.value && props.businessLocations.length > 0) {
    await calculateDistance()
  }
})

const calculateDistance = async () => {
  if (!navigator.geolocation) return

  try {
    const position = await new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject)
    })

    const userLat = position.coords.latitude
    const userLng = position.coords.longitude
    deliveryLat.value = userLat
    deliveryLng.value = userLng

    const businessLocation = props.businessLocations.find(l => l.id == selectedLocationId.value)
    const loc = businessLocation || props.businessLocations[0]

    if (loc && loc.latitude && loc.longitude) {
      distanceKm.value = cart.calculateDistance(
        parseFloat(loc.latitude),
        parseFloat(loc.longitude),
        userLat,
        userLng
      )
    }
  } catch (e) {
    console.log('Could not get location:', e)
    distanceKm.value = null
  }
}

const useCurrentLocation = async () => {
  await calculateDistance()
}

const generateWhatsAppMessage = () => {
  let message = `🍽️ *Nuevo Pedido!*\n\n`
  message += `📦 *Items:*\n`
  cartItems.value.forEach(item => {
    let itemLine = `• ${item.quantity}x ${item.title}`
    if (item.extras && item.extras.length) {
      itemLine += ` (${item.extras.map(e => e.name).join(', ')})`
    }
    itemLine += ` - $${(item.unit_price * item.quantity).toFixed(2)}\n`
    message += itemLine
  })

  message += `\n📍 *Tipo:* ${orderType.value === 'delivery' ? 'Delivery' : 'Recoger en local'}`

  if (orderType.value === 'delivery') {
    message += `\n🏠 *Dirección:* ${deliveryAddress.value}`
    if (deliveryReferences.value) {
      message += `\n📝 *Referencias:* ${deliveryReferences.value}`
    }
    if (distanceKm.value !== null) {
      message += `\n📏 *Distancia:* ${distanceKm.value.toFixed(1)} km`
    }
  } else if (selectedPickupLocationId.value) {
    const loc = props.businessLocations.find(l => l.id == selectedPickupLocationId.value)
    if (loc) {
      message += `\n🏪 *Punto de recogida:* ${loc.name}`
      if (pickupTime.value) {
        message += `\n🕐 *Hora de recogida:* ${pickupTime.value}`
      }
    }
  }

  message += `\n\n💰 *Resumen:*`
  message += `\n• Subtotal: $${subtotal.value.toFixed(2)}`
  if (orderType.value === 'delivery') {
    message += `\n• Delivery: $${deliveryFee.value.toFixed(2)}`
  }
  message += `\n• *TOTAL: $${total.value.toFixed(2)}*`

  message += `\n\n👤 *Cliente:* ${customerName.value}`
  message += `\n📱 *Tel:* ${customerPhone.value}`
  if (customerEmail.value) {
    message += `\n📧 *Email:* ${customerEmail.value}`
  }

  if (notes.value) {
    message += `\n\n📋 *Notas:* ${notes.value}`
  }

  return message
}

const submitOrder = async () => {
  errors.value = {}

  if (!customerName.value.trim()) {
    errors.value.customer_name = 'El nombre es obligatorio'
    return
  }
  if (!customerPhone.value.trim()) {
    errors.value.customer_phone = 'El teléfono es obligatorio'
    return
  }
  if (orderType.value === 'delivery' && !deliveryAddress.value.trim()) {
    errors.value.delivery_address = 'La dirección es obligatoria'
    return
  }

  submitting.value = true

  try {
    const orderData = {
      business_id: props.businessId,
      customer_name: customerName.value,
      customer_email: customerEmail.value || null,
      customer_phone: customerPhone.value,
      order_type: orderType.value,
      items: cartItems.value.map(item => ({
        product_type: item.product_type,
        product_id: item.product_id,
        variant_id: item.variant_id,
        title: item.title,
        quantity: item.quantity,
        unit_price: item.unit_price,
        options: item.extras ? { extras: item.extras } : null,
      })),
      notes: notes.value || null,
    }

    if (orderType.value === 'delivery') {
      orderData.delivery_address = {
        full_address: deliveryAddress.value,
        references: deliveryReferences.value || null,
        latitude: deliveryLat.value,
        longitude: deliveryLng.value,
        distance_km: distanceKm.value,
      }
    }

    if (orderType.value === 'pickup') {
      orderData.pickup_location_id = selectedPickupLocationId.value || null
      orderData.pickup_time = pickupTime.value || null
    }

    await axios.post('/api/orders', orderData)

    const message = generateWhatsAppMessage()
    const whatsappUrl = `https://web.whatsapp.com/send?phone=${props.whatsappNumber}&text=${encodeURIComponent(message)}`

    window.open(whatsappUrl, '_blank')

    cart.clearCart()
    emit('success')
  } catch (error) {
    if (error.response?.data?.error) {
      errors.value.general = error.response.data.error
    } else {
      errors.value.general = 'Error al crear el pedido. Intenta de nuevo.'
    }
    emit('error', errors.value)
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.checkout-form .card {
  border: none;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.checkout-form .form-check-input:checked {
  background-color: #0d6efd;
  border-color: #0d6efd;
}
</style>
