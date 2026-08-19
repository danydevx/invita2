import { ref, computed, watch } from 'vue'

const CART_STORAGE_KEY = 'saas_cart'

const cartItems = ref([])
const businessId = ref(null)
const isCartOpen = ref(false)

function loadFromStorage() {
  if (typeof window === 'undefined') return

  try {
    const stored = localStorage.getItem(CART_STORAGE_KEY)
    if (stored) {
      const data = JSON.parse(stored)
      cartItems.value = data.items || []
      businessId.value = data.businessId || null
    }
  } catch (e) {
    console.error('Error loading cart from storage:', e)
  }
}

function saveToStorage() {
  if (typeof window === 'undefined') return

  try {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify({
      items: cartItems.value,
      businessId: businessId.value,
    }))
  } catch (e) {
    console.error('Error saving cart to storage:', e)
  }
}

watch([cartItems, businessId], () => {
  saveToStorage()
}, { deep: true })

loadFromStorage()

export function useCart() {
  const itemCount = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => {
      return sum + (item.unit_price * item.quantity)
    }, 0)
  })

  const items = computed(() => cartItems.value)

  const isEmpty = computed(() => cartItems.value.length === 0)

  function addItem(product, options = {}) {
    const {
      productType = 'menu_product',
      variantId = null,
      quantity = 1,
      unitPrice = product.base_price || product.price || 0,
      title = product.title || product.name,
      image = product.image || null,
      extras = [],
    } = options

    if (businessId.value && businessId.value !== product.business_id) {
      cartItems.value = []
    }

    businessId.value = product.business_id

    const existingIndex = cartItems.value.findIndex(item =>
      item.product_id === product.id &&
      item.variant_id === variantId &&
      JSON.stringify(item.extras || []) === JSON.stringify(extras)
    )

    if (existingIndex >= 0) {
      cartItems.value[existingIndex].quantity += quantity
    } else {
      cartItems.value.push({
        id: Date.now(),
        product_type: productType,
        product_id: product.id,
        variant_id: variantId,
        title,
        image,
        unit_price: parseFloat(unitPrice),
        quantity,
        extras,
      })
    }
  }

  function removeItem(itemId) {
    const index = cartItems.value.findIndex(item => item.id === itemId)
    if (index >= 0) {
      cartItems.value.splice(index, 1)
    }
  }

  function updateQuantity(itemId, quantity) {
    const item = cartItems.value.find(item => item.id === itemId)
    if (item) {
      if (quantity <= 0) {
        removeItem(itemId)
      } else {
        item.quantity = quantity
      }
    }
  }

  function clearCart() {
    cartItems.value = []
    businessId.value = null
  }

  function openCart() {
    isCartOpen.value = true
  }

  function closeCart() {
    isCartOpen.value = false
  }

  function toggleCart() {
    isCartOpen.value = !isCartOpen.value
  }

  function getCartData() {
    return {
      business_id: businessId.value,
      items: cartItems.value.map(item => ({
        product_type: item.product_type,
        product_id: item.product_id,
        variant_id: item.variant_id,
        title: item.title,
        quantity: item.quantity,
        unit_price: item.unit_price,
        options: item.extras ? { extras: item.extras } : null,
      })),
    }
  }

  function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371
    const dLat = (lat2 - lat1) * Math.PI / 180
    const dLon = (lon2 - lon1) * Math.PI / 180
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2)
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
    return R * c
  }

  return {
    items,
    itemCount,
    subtotal,
    isEmpty,
    isCartOpen,
    businessId,
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    openCart,
    closeCart,
    toggleCart,
    getCartData,
    calculateDistance,
  }
}
