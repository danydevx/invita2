import { formatPrice } from './formatPrice'

export const vFormatPrice = {
  mounted(el, binding) {
    const currency = binding.arg || 'mxn'
    const value = binding.value

    if (value === null || value === undefined || value === '') {
      el.textContent = '—'
      return
    }

    const formatted = formatPrice(value, currency)
    el.textContent = formatted
  },
  updated(el, binding) {
    const currency = binding.arg || 'mxn'
    const value = binding.value

    if (value === null || value === undefined || value === '') {
      el.textContent = '—'
      return
    }

    const formatted = formatPrice(value, currency)
    el.textContent = formatted
  },
}

export const formatPriceDirective = (app) => {
  app.directive('format-price', vFormatPrice)
}
