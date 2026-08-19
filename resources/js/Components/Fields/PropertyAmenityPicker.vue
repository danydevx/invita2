<template>
  <div>
    <div v-if="amenities.length === 0" class="text-muted small">
      No hay amenidades disponibles para este tipo de propiedad.
    </div>
    <div v-else class="row g-3">
      <div v-for="amenity in amenities" :key="amenity.id" class="col-md-4 col-lg-3">
        <div class="form-check">
          <input
            :id="'amenity-' + amenity.id"
            v-model="localSelected"
            type="checkbox"
            :value="amenity.id"
            class="form-check-input"
          >
          <label :for="'amenity-' + amenity.id" class="form-check-label d-flex align-items-center gap-2">
            <i :class="amenity.icon || 'bi bi-star'" style="font-size: 1.1rem;"></i>
            {{ amenity.name }}
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  amenities: { type: Array, default: () => [] },
  modelValue: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue'])

const localSelected = ref([...props.modelValue])

watch(localSelected, (val) => {
  emit('update:modelValue', val)
})

watch(() => props.modelValue, (val) => {
  localSelected.value = [...val]
})
</script>
