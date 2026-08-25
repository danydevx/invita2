<template>
  <div class="form-section" :id="`section-${section.id}`">
    <div class="row g-3">
      <template v-for="field in nonGalleryFields" :key="field.id">
        <div :class="getFieldColClass(field.field_type)">
          <FieldText
          v-if="field.field_type === 'text'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :placeholder="field.placeholder"
          :helpText="field.help_text"
          :required="field.is_required"
        />

        <FieldTextarea
          v-else-if="field.field_type === 'textarea'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :placeholder="field.placeholder"
          :helpText="field.help_text"
          :required="field.is_required"
          :rows="4"
        />

        <FieldNumber
          v-else-if="field.field_type === 'number' || field.field_type === 'decimal' || field.field_type === 'integer'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :placeholder="field.placeholder"
          :helpText="field.help_text"
          :required="field.is_required"
        />

        <FieldPrice
          v-else-if="field.field_type === 'price'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :placeholder="field.placeholder"
          :helpText="field.help_text"
          :required="field.is_required"
          currencyLabel="Monto"
        />

        <FieldSelect
          v-else-if="field.field_type === 'select'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :helpText="field.help_text"
          :required="field.is_required"
          :options="field.options"
          placeholder="Selecciona una opción"
        />

        <FieldRadio
          v-else-if="field.field_type === 'radio'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :helpText="field.help_text"
          :required="field.is_required"
          :options="field.options"
        />

        <FieldCheckbox
          v-else-if="field.field_type === 'checkbox'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :helpText="field.help_text"
        />

        <FieldDate
          v-else-if="field.field_type === 'date'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :placeholder="field.placeholder"
          :helpText="field.help_text"
          :required="field.is_required"
        />

        <FieldImage
          v-else-if="field.field_type === 'image'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          :modelValue="mainImageFile"
          :helpText="field.help_text"
          :required="field.is_required"
          :maxSizeMb="5"
          :initialPreview="initialMainImageUrl"
          @update:modelValue="$emit('update:mainImageFile', $event)"
          @update:keep="onKeepChange"
          @image-removed="onImageRemoved"
        />

        <FieldSwitch
          v-else-if="field.field_type === 'boolean'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
        />

        <FieldEmail
          v-else-if="field.field_type === 'email'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :placeholder="field.placeholder"
          :helpText="field.help_text"
          :required="field.is_required"
        />

        <FieldPhone
          v-else-if="field.field_type === 'phone'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :placeholder="field.placeholder"
          :helpText="field.help_text"
          :required="field.is_required"
        />

        <FieldUrl
          v-else-if="field.field_type === 'url'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :formError="errors[field.field_key]"
          :placeholder="field.placeholder"
          :helpText="field.help_text"
          :required="field.is_required"
        />

        <FieldFile
          v-else-if="field.field_type === 'file'"
          :id="`field-${field.field_key}`"
          :label="field.label"
          v-model="form[field.field_key]"
          :helpText="field.help_text"
          :required="field.is_required"
          accept=".pdf,.doc,.docx,.xls,.xlsx"
        />
      </div>
      </template>
    </div>
</div>
</template>

<script setup>
import { computed } from 'vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldPrice from '@/Components/Fields/FieldPrice.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldRadio from '@/Components/Fields/FieldRadio.vue'
import FieldCheckbox from '@/Components/Fields/FieldCheckbox.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'
import FieldFile from '@/Components/Fields/FieldFile.vue'

const props = defineProps({
  section: { type: Object, required: true },
  form: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) },
  mainImageFile: { type: [File, null], default: null },
  initialMainImageUrl: { type: String, default: '' },
  keepMainImage: { type: Boolean, default: true },
})
const emit = defineEmits(['update:keep', 'image-removed', 'update:mainImageFile'])

function onKeepChange(val) {
  emit('update:keep', val)
}

function onImageRemoved() {
  emit('image-removed')
}

const nonGalleryFields = computed(() => {
  return (props.section.fields || []).filter(f => f.field_type !== 'gallery')
})

const getFieldColClass = (fieldType) => {
  const fullWidth = ['textarea', 'editor', 'gallery', 'address', 'boolean', 'multiselect', 'checkbox', 'checkboxes']
  if (fullWidth.includes(fieldType)) return 'col-12'
  return 'col-12 col-md-6'
}
</script>
