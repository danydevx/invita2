<template>
  <Teleport to="body">
    <Transition name="drawer">
      <div v-if="isOpen" class="cart-drawer-overlay" @click.self="close">
        <div class="cart-drawer">
          <div class="cart-drawer__header">
            <h5 class="mb-0">
              <i class="bi bi-cart3 me-2"></i>
              Carrito de pedido
            </h5>
            <button type="button" class="btn-close" @click="close"></button>
          </div>

          <div class="cart-drawer__body">
            <div v-if="cart.isEmpty.value" class="text-center py-5">
              <i class="bi bi-cart-x fs-1 text-muted"></i>
              <p class="text-muted mt-2 mb-0">Tu carrito está vacío</p>
            </div>

            <div v-else class="cart-items">
              <CartItem
                v-for="item in cart.items.value"
                :key="item.id"
                :item="item"
                @update-quantity="(qty) => cart.updateQuantity(item.id, qty)"
                @remove="cart.removeItem(item.id)"
              />
            </div>
          </div>

          <div v-if="!cart.isEmpty.value" class="cart-drawer__footer">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="fw-bold">Subtotal:</span>
              <span class="fw-bold fs-5">${{ formatPrice(cart.subtotal.value) }}</span>
            </div>
            <button class="btn btn-primary w-100" @click="goToCheckout">
              <i class="bi bi-credit-card me-1"></i>
              Finalizar pedido
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useCart } from '@/composables/useCart'
import CartItem from './CartItem.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['close', 'checkout'])

const cart = useCart()

const close = () => {
  emit('close')
  cart.closeCart()
}

const formatPrice = (value) => {
  return parseFloat(value || 0).toFixed(2)
}

const goToCheckout = () => {
  emit('checkout')
  close()
}
</script>

<style scoped>
.cart-drawer-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1050;
}

.cart-drawer {
  position: absolute;
  top: 0;
  right: 0;
  width: 400px;
  max-width: 100%;
  height: 100%;
  background: white;
  display: flex;
  flex-direction: column;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
}

.cart-drawer__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e9ecef;
  background: #f8f9fa;
}

.cart-drawer__body {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
}

.cart-drawer__footer {
  padding: 1rem;
  border-top: 1px solid #e9ecef;
  background: #f8f9fa;
}

.cart-items {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.drawer-enter-active,
.drawer-leave-active {
  transition: opacity 0.3s ease;
}

.drawer-enter-active .cart-drawer,
.drawer-leave-active .cart-drawer {
  transition: transform 0.3s ease;
}

.drawer-enter-from,
.drawer-leave-to {
  opacity: 0;
}

.drawer-enter-from .cart-drawer,
.drawer-leave-to .cart-drawer {
  transform: translateX(100%);
}
</style>
