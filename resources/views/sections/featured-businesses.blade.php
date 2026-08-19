<section class="directory-section directory-section--featured">
    <div class="container">
        <header class="section-header section-header--center">
            <span class="section-header__eyebrow">Recomendados</span>
            <h2 class="section-header__title">Negocios Destacados</h2>
            <p class="section-header__description">Los mejores negocios de tu zona</p>
        </header>

        <div class="featured-grid">
            @foreach($businesses as $business)
                <x-business-card :business="$business" :show-footer="true" />
            @endforeach
        </div>
    </div>
</section>
