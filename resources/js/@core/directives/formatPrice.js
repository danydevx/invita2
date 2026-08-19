const defaultCurrency = '$'
const defaultDecimals = 2
const defaultLocale = 'es-MX'

const currencyConfig = {
  mxn: { currency: '$', locale: 'es-MX' },
  usd: { currency: '$', locale: 'en-US' },
  eur: { currency: '€', locale: 'de-DE' },
}

function formatPrice(value, currency = 'mxn') {
  if (value === null || value === undefined || value === '') {
    return null
  }

  const numValue = typeof value === 'string' ? parseFloat(value) : value

  if (isNaN(numValue)) {
    return null
  }

  const config = currencyConfig[currency] || currencyConfig.mxn

  const formatted = new Intl.NumberFormat(config.locale, {
    minimumFractionDigits: defaultDecimals,
    maximumFractionDigits: defaultDecimals,
  }).format(numValue)

  return `${config.currency}${formatted}`
}

export { formatPrice }
