import HeroSection from './Sections/HeroSection.vue'
import ServicesSection from './Sections/ServicesSection.vue'
import ContactSection from './Sections/ContactSection.vue'
import FooterSection from './Sections/FooterSection.vue'

export default {
  name: 'Bold',
  slug: 'bold',
  version: '1.0.0',

  sections: [
    {
      name: 'hero',
      component: HeroSection,
      order: 1,
      anchorId: null,
      props: {
        layout: 'fullbleed',
        alignment: 'left',
        showLogo: true,
        showContactInfo: true,
        showSocialLinks: false,
        overlayOpacity: 0.7,
        backgroundType: 'gradient',
        gradientStart: '#FF4500',
        gradientEnd: '#FF6B35',
      }
    },
    {
      name: 'services',
      component: ServicesSection,
      order: 2,
      anchorId: 'services',
      props: {
        showTitle: true,
        title: 'Nuestros Servicios',
        subtitle: '',
        showBookingButton: true,
        showWhatsApp: true,
        titleColor: '#1a1a2e',
        subtitleColor: '#6B7280',
        sectionBgColor: '#ffffff',
        cardStyle: 'light',
        buttonStyle: 'primary',
      }
    },
    {
      name: 'gallery',
      order: 3,
      anchorId: 'gallery',
      props: {}
    },
    {
      name: 'products',
      order: 4,
      anchorId: 'products',
      props: {}
    },
    {
      name: 'appointments',
      order: 5,
      anchorId: 'appointments',
      props: {}
    },
    {
      name: 'contact',
      component: ContactSection,
      order: 6,
      anchorId: 'contact',
      props: {
        showTitle: true,
        title: 'Contáctanos',
        subtitle: '',
        titleColor: '#1a1a2e',
        subtitleColor: '#6B7280',
        sectionBgColor: '#f8f9fa',
        showSocial: true,
        showContactInfo: true,
        showLocations: true,
        accentColor: '#FF4500',
      }
    },
    {
      name: 'reviews',
      order: 7,
      anchorId: 'reviews',
      props: {}
    },
    {
      name: 'locations',
      order: 8,
      anchorId: 'locations',
      props: {}
    },
    {
      name: 'promotions',
      order: 9,
      anchorId: 'promotions',
      props: {}
    },
    {
      name: 'footer',
      component: FooterSection,
      order: 10,
      anchorId: null,
      props: {
        showBrand: true,
        showContact: true,
        showSocial: true,
        showLinks: false,
        backgroundColor: '#1a1a2e',
        textColor: '#ffffff',
        accentColor: '#FF4500',
        linkColor: '#b0b0b0',
      }
    }
  ],

  sectionOrder: ['hero', 'services', 'gallery', 'products', 'appointments', 'contact', 'reviews', 'locations', 'promotions', 'footer']
}
