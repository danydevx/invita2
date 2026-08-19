<section class="directory-section directory-section--categories">
    <div class="container">
        <header class="section-header section-header--center">
            <span class="section-header__eyebrow">Explorar</span>
            <h2 class="section-header__title">Categorías</h2>
            <p class="section-header__description">Encuentra por tipo de servicio</p>
        </header>

        <div class="categories-grid">
            @foreach($businessTypes as $type)
                <x-category-card :type="$type" />
            @endforeach
        </div>
    </div>
</section>
