<section class="business-section">
    <div class="container">
        <header class="section-header section-header--center section-header--compact">
            <span class="section-header__eyebrow">Reseñas</span>
            <h2 class="section-header__title">Lo que dicen nuestros clientes</h2>
        </header>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="review-summary">
                    <div class="review-summary__score">
                        <span class="score-value">{{ $avgRating }}</span>
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= round($avgRating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                        <span class="score-count">{{ $business->reviews_count ?? 0 }} reseñas</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                @if($reviews->count() > 0)
                    <div class="reviews-list">
                        @foreach($reviews as $review)
                            <x-review-card :review="$review" />
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Aún no hay reseñas. ¡Sé el primero en compartir tu experiencia!</p>
                @endif

                <div class="review-cta">
                    <h3 class="review-cta__title">¿Has visitado este negocio?</h3>
                    <p class="review-cta__text">Comparte tu experiencia con otros usuarios</p>
                    <a href="#" class="btn btn-outline-primary">Escribir una reseña</a>
                </div>
            </div>
        </div>
    </div>
</section>
