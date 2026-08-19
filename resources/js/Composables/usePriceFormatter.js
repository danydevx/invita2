import { computed } from 'vue'

const defaultCurrency = '$'
const defaultDecimals = 2
const defaultDecimalsSeparator = '.'
const defaultThousandsSeparator = ','

export function usePriceFormatter(options = {}) {
  const currency = computed(() => options.currency || defaultCurrency)
  const decimals = computed(() => options.decimals ?? defaultDecimals)
  const decimalsSeparator = computed(() => options.decimalsSeparator || defaultDecimalsSeparator)
  const thousandsSeparator = computed(() => options.thousandsSeparator || defaultThousandsSeparator)
  const locale = computed(() => options.locale || 'es-MX')

  const formatPrice = (value, opts = {}) => {
    if (value === null || value === undefined || value === '') {
      return null
    }

    const numValue = typeof value === 'string' ? parseFloat(value) : value

    if (isNaN(numValue)) {
      return null
    }

    const useDecimals = opts.decimals ?? decimals.value
    const useCurrency = opts.currency ?? currency.value
    const useLocale = opts.locale || locale.value

    const formatted = new Intl.NumberFormat(useLocale, {
      minimumFractionDigits: useDecimals,
      maximumFractionDigits: useDecimals,
    }).format(numValue)

    return useCurrency ? `${useCurrency}${formatted}` : formatted
  }

  const formatPriceWithSeparators = (value, opts = {}) => {
    if (value === null || value === undefined || value === '') {
      return null
    }

    const numValue = typeof value === 'string' ? parseFloat(value) : value

    if (isNaN(numValue)) {
      return null
    }

    const useDecimals = opts.decimals ?? defaultDecimals

    const parts = numValue.toFixed(useDecimals).split('.')
    const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, defaultThousandsSeparator)
    const decimalPart = parts[1]

    return `${currency.value}${integerPart}${defaultDecimalsSeparator}${decimalPart}`
  }

  const formatCompact = (value, opts = {}) => {
    if (value === null || value === undefined || value === '') {
      return null
    }

    const numValue = typeof value === 'string' ? parseFloat(value) : value

    if (isNaN(numValue)) {
      return null
    }

    const useCurrency = opts.currency ?? currency.value

    if (numValue >= 1000000) {
      return `${useCurrency}${(numValue / 1000000).toFixed(1)}M`
    }
    if (numValue >= 1000) {
      return `${useCurrency}${(numValue / 1000).toFixed(1)}K`
    }
    return formatPrice(numValue, opts)
  }

  const parsePrice = (value) => {
    if (value === null || value === undefined || value === '') {
      return null
    }

    const stringValue = String(value)

    const cleaned = stringValue.replace(/[^\d.,\-]/g, '')

    const normalized = cleaned.replace(/,/g, '')

    const parsed = parseFloat(normalized)

    return isNaN(parsed) ? null : parsed
  }

  return {
    currency,
    decimals,
    decimalsSeparator,
    thousandsSeparator,
    locale,
    formatPrice,
    formatPriceWithSeparators,
    formatCompact,
    parsePrice,
  }
}

export function formatPriceStatic(value, options = {}) {
  const formatter = usePriceFormatter(options)
  return formatter.formatPrice(value)
}
