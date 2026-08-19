<section class="directory-section directory-section--recent">
    <div class="container">
        <header class="section-header section-header--center">
            <span class="section-header__eyebrow">Nuevos</span>
            <h2 class="section-header__title">Negocios Recientes</h2>
            <p class="section-header__description">Las últimas incorporaciones a nuestro directorio</p>
        </header>

        <div class="recent-grid">
            @foreach($businesses as $business)
                <x-business-card :business="$business" />
            @endforeach
        </div>
    </div>
</section>
