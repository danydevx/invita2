<template>
  <Teleport to="body">
    <div v-if="show" class="modal fade show d-block" tabindex="-1" @click.self="$emit('close')">
      <div class="modal-dialog" :class="[`modal-${size}`, size === 'lg' ? 'modal-lg' : '', size === 'sm' ? 'modal-sm' : '']">
        <div class="modal-content">
          <div class="modal-header">
            <slot name="header">
              <h5 class="modal-title"><slot name="title"></slot></h5>
            </slot>
            <button type="button" class="btn-close" @click="$emit('close')"></button>
          </div>
          <div class="modal-body">
            <slot></slot>
          </div>
          <div class="modal-footer" v-if="$slots.footer">
            <slot name="footer"></slot>
          </div>
        </div>
      </div>
    </div>
    <div v-if="show" class="modal-backdrop fade show"></div>
  </Teleport>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'md',
  },
})

defineEmits(['close'])
</script>
