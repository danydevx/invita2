<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <HeroSimple
      :title="pageTitle"
      :backgroundImage="business.cover_image"
      :businessSlug="business.slug"
    />

    <section class="page-header">
      <div class="page-header__inner">
        <BreadcrumbNav
          :baseSlug="business.slug"
          :items="[{ label: pageTitle }]"
        />
      </div>
    </section>

    <section class="page-content">
      <div class="page-content__inner">
        <SectionPromotions
          v-if="sectionData.items && sectionData.items.length"
          :title="pageTitle"
          :items="sectionData.items"
          :config="{}"
          :businessSlug="business.slug"
        />
        <div v-else class="text-muted text-center py-5">
          No hay promociones disponibles.
        </div>
      </div>
    </section>

    <Footer
      :business="business"
      :text="setting.footer_text"
      :showSocial="setting.footer_show_social"
      :socialNetworks="socialNetworks"
    />
  </div>

  <AiChatWidget
    v-if="aiChatbot && aiChatbot.is_enabled"
    :businessSlug="business.slug"
    :businessName="business.name"
    :widgetColor="aiChatbot.widget_color || '#3B82F6'"
    :widgetTheme="aiChatbot.widget_theme || 'light'"
    :allowReset="aiChatbot.allow_reset_chat"
  />
</template>

<script setup>
import NavigationMenu from '../../components/NavigationMenu.vue'
import HeroSimple from '../../components/HeroSimple.vue'
import SectionPromotions from '../../components/SectionPromotions.vue'
import Footer from '../../components/Footer.vue'
import BreadcrumbNav from '@/Components/Minisite/BreadcrumbNav.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'

defineProps({
  business: Object,
  setting: Object,
  pageTitle: String,
  sectionData: Object,
  socialNetworks: Array,
  existingSections: Array,
  aiChatbot: Object,
})
</script>

<style lang="less">
.page-content {
  padding: 32px 16px;
  background: #fff;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }
}

.page-header {
  background: #fff;
  border-bottom: 1px solid #e9ecef;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
    padding: 0 16px;
  }
}
</style>
