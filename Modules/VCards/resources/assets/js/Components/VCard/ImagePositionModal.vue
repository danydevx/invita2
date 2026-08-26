<template>
  <Teleport to="body">
    <div v-if="show" class="modal fade show d-block" tabindex="-1" @click.self="close">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Ajustar posición de imagen</h5>
            <button type="button" class="btn-close" @click="close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small mb-3">Arrastra la imagen para posicionarla dentro del círculo</p>
            <div class="image-position-container" ref="containerRef">
              <div
                class="image-position-circle"
                :style="circleStyle"
                @mousedown="startDrag"
                @touchstart="startDrag"
              >
                <img
                  :src="imageUrl"
                  alt="Preview"
                  class="image-position-img"
                  :style="imageStyle"
                  draggable="false"
                >
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" @click="resetPosition">
              <i class="bi bi-arrow-counterclockwise me-1"></i>
              Reiniciar
            </button>
            <button type="button" class="btn btn-primary" @click="savePosition">
              <i class="bi bi-check-lg me-1"></i>
              Aplicar
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="show" class="modal-backdrop fade show"></div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  imageUrl: {
    type: String,
    required: true,
  },
  currentX: {
    type: Number,
    default: 0,
  },
  currentY: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['close', 'save', 'update:x', 'update:y'])

const containerRef = ref(null)
const positionX = ref(props.currentX)
const positionY = ref(props.currentY)
const isDragging = ref(false)
const startPos = ref({ x: 0, y: 0 })
const startOffset = ref({ x: 0, y: 0 })

const circleSize = 200

watch(() => props.currentX, (val) => { positionX.value = val })
watch(() => props.currentY, (val) => { positionY.value = val })

const imageStyle = computed(() => ({
  transform: `translate(${positionX.value}px, ${positionY.value}px)`,
  width: '100%',
  height: '100%',
  objectFit: 'cover',
  cursor: isDragging.value ? 'grabbing' : 'grab',
  userSelect: 'none',
}))

const circleStyle = computed(() => ({
  width: circleSize + 'px',
  height: circleSize + 'px',
  borderRadius: '50%',
  overflow: 'hidden',
  border: '3px solid #fff',
  boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
}))

function startDrag(e) {
  e.preventDefault()
  isDragging.value = true
  const clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX
  const clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY
  startPos.value = { x: clientX, y: clientY }
  startOffset.value = { x: positionX.value, y: positionY.value }

  document.addEventListener('mousemove', onDrag)
  document.addEventListener('mouseup', stopDrag)
  document.addEventListener('touchmove', onDrag)
  document.addEventListener('touchend', stopDrag)
}

function onDrag(e) {
  if (!isDragging.value) return
  e.preventDefault()
  const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX
  const clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY
  const deltaX = clientX - startPos.value.x
  const deltaY = clientY - startPos.value.y
  positionX.value = startOffset.value.x + deltaX
  positionY.value = startOffset.value.y + deltaY
}

function stopDrag() {
  isDragging.value = false
  document.removeEventListener('mousemove', onDrag)
  document.removeEventListener('mouseup', stopDrag)
  document.removeEventListener('touchmove', onDrag)
  document.removeEventListener('touchend', stopDrag)
}

function resetPosition() {
  positionX.value = 0
  positionY.value = 0
}

function savePosition() {
  emit('update:x', positionX.value)
  emit('update:y', positionY.value)
  emit('save', { x: positionX.value, y: positionY.value })
  close()
}

function close() {
  emit('close')
}

onBeforeUnmount(() => {
  document.removeEventListener('mousemove', onDrag)
  document.removeEventListener('mouseup', stopDrag)
  document.removeEventListener('touchmove', onDrag)
  document.removeEventListener('touchend', stopDrag)
})
</script>

<style scoped>
.modal {
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  border-radius: 12px;
  overflow: hidden;
}

.image-position-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
  background: #f5f5f5;
  border-radius: 8px;
}

.image-position-circle {
  background: #e5e7eb;
}
</style>
