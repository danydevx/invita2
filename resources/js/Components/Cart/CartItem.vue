<template>
  <div class="cart-item">
    <div class="cart-item__image">
      <img
        v-if="item.image"
        :src="item.image"
        :alt="item.title"
      />
      <div v-else class="placeholder-image">
        <i class="bi bi-image"></i>
      </div>
    </div>

    <div class="cart-item__details">
      <h6 class="cart-item__title mb-1">{{ item.title }}</h6>
      <p v-if="item.extras && item.extras.length" class="text-muted small mb-1">
        <span v-for="(extra, idx) in item.extras" :key="idx">
          + {{ extra.name }}{{ idx < item.extras.length - 1 ? ', ' : '' }}
        </span>
      </p>
      <div class="cart-item__price text-primary fw-bold">
        ${{ formatPrice(item.unit_price * item.quantity) }}
      </div>
    </div>

    <div class="cart-item__actions">
      <div class="quantity-control">
        <button
          class="btn btn-sm btn-outline-secondary"
          @click="decreaseQuantity"
          :disabled="item.quantity <= 1"
        >
          <i class="bi bi-dash"></i>
        </button>
        <span class="quantity-value">{{ item.quantity }}</span>
        <button
          class="btn btn-sm btn-outline-secondary"
          @click="increaseQuantity"
        >
          <i class="bi bi-plus"></i>
        </button>
      </div>
      <button class="btn btn-sm btn-outline-danger" @click="$emit('remove')">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['update-quantity', 'remove'])

const formatPrice = (value) => {
  return parseFloat(value || 0).toFixed(2)
}

const increaseQuantity = () => {
  emit('update-quantity', props.item.quantity + 1)
}

const decreaseQuantity = () => {
  if (props.item.quantity > 1) {
    emit('update-quantity', props.item.quantity - 1)
  }
}
</script>

<style scoped>
.cart-item {
  display: flex;
  gap: 0.75rem;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 0.5rem;
}

.cart-item__image {
  width: 60px;
  height: 60px;
  flex-shrink: 0;
  border-radius: 0.25rem;
  overflow: hidden;
  background: #e9ecef;
}

.cart-item__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.placeholder-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #adb5bd;
}

.cart-item__details {
  flex: 1;
  min-width: 0;
}

.cart-item__title {
  font-size: 0.9rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.cart-item__price {
  font-size: 0.95rem;
}

.cart-item__actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.5rem;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.quantity-value {
  min-width: 2rem;
  text-align: center;
  font-weight: 600;
}
</style>
