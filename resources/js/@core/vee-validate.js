import { configure } from 'vee-validate'
import { required, email, max, min, numeric, url } from '@vee-validate/rules'

configure({
  generateMessage: (context) => {
    const messages = {
      required: `El campo ${context.label} es obligatorio.`,
      email: `El campo ${context.label} debe ser un correo electrónico válido.`,
      max: `El campo ${context.label} no puede tener más de ${context.rule?.params?.[0] || 255} caracteres.`,
      min: `El campo ${context.label} debe tener al menos ${context.rule?.params?.[0] || 0} caracteres.`,
      numeric: `El campo ${context.label} debe ser un número.`,
      url: `El campo ${context.label} debe ser una URL válida.`,
    }

    const ruleName = context.rule?.name
    if (messages[ruleName]) {
      return messages[ruleName]
    }

    return `El campo ${context.label} es inválido.`
  },
})

export const rules = {
  required,
  email,
  max,
  min,
  numeric,
  url,
}
