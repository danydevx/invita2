import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useMemberBreadcrumbs() {
  const page = usePage()

  const businessMenu = computed(() => page.props.businessMenu || [])
  const currentPath = computed(() => window.location.pathname)

  const getBusinessById = (businessId) => {
    return businessMenu.value.find(b => b.id === businessId)
  }

  const getModuleByUrl = (url) => {
    for (const business of businessMenu.value) {
      const module = business.modules?.find(m => m.url === url)
      if (module) {
        return { business, module }
      }
      const editUrl = `/member/businesses/${business.id}/edit`
      if (url === editUrl) {
        return { business, module: { title: 'Editar', url: editUrl } }
      }
    }
    return null
  }

  const getBreadcrumbs = () => {
    const path = currentPath.value
    const result = []

    result.push({ label: 'Mis Negocios', href: '/member/business-modules' })

    const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
    if (businessMatch) {
      const businessId = parseInt(businessMatch[1])
      const business = getBusinessById(businessId)
      if (business) {
        result.push({
          label: business.name,
          href: `/member/businesses/${business.id}/edit`
        })

        const moduleInfo = getModuleByUrl(path)
        if (moduleInfo && moduleInfo.module) {
          const moduleTitle = moduleInfo.module.title
          if (moduleTitle !== 'Editar') {
            result.push({ label: moduleTitle, active: true })
          }
        }
      } else {
        const moduleInfo = getModuleByUrl(path)
        if (moduleInfo && moduleInfo.module) {
          result.push({
            label: moduleInfo.business.name,
            href: `/member/businesses/${moduleInfo.business.id}/edit`
          })
          result.push({ label: moduleInfo.module.title, active: true })
        }
      }
    }

    if (result.length === 1) {
      const moduleInfo = getModuleByUrl(path)
      if (moduleInfo) {
        result.push({
          label: moduleInfo.business.name,
          href: `/member/businesses/${moduleInfo.business.id}/edit`
        })
        result.push({ label: moduleInfo.module.title, active: true })
      }
    }

    return result
  }

  return {
    getBreadcrumbs,
    businessMenu,
    currentPath,
  }
}
