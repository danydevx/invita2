<section class="business-section">
    <div class="container">
        <header class="section-header section-header--center section-header--compact">
            <span class="section-header__eyebrow">Galería</span>
            <h2 class="section-header__title">Fotos</h2>
        </header>

        @if($images->count() > 0)
            <div class="business-gallery__grid">
                @foreach($images as $image)
                    <div class="business-gallery__item">
                        <img src="{{ $image->path }}" alt="{{ $image->title ?? 'Imagen' }}" loading="lazy">
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted">No hay imágenes en la galería.</p>
        @endif
    </div>
</section>
