<section class="business-hero">
    @php
        $coverImage = $business->cover_image_path ?? 'https://picsum.photos/seed/bg'.$business->id.'/1200/400';
        $logoImage = $business->logo_path ?? 'https://picsum.photos/seed/logo'.$business->id.'/150/150';
        $categoryName = $business->listing_type?->label() ?? 'Negocio';
        $rating = round($avgRating ?? $business->reviews_avg_rating ?? 0, 1);
        $reviewsCount = $business->reviews_count ?? 0;
        $location = $business->locations->first();
        $address = $location ? $location->address_line_1 : 'Dirección no disponible';
        $phone = $business->phone ?? ($location?->phone ?? '');
    @endphp

    <div class="business-hero__cover" style="background-image: url('{{ $coverImage }}')"></div>

    <div class="container">
        <div class="business-hero__content">
            <div class="business-hero__top">
                <div class="business-hero__logo-wrap">
                    <img
                        src="{{ $logoImage }}"
                        alt="{{ $business->name }}"
                        class="business-hero__logo"
                    >
                </div>

                <div class="business-hero__info">
                    <span class="business-hero__category">{{ $categoryName }}</span>
                    <h1 class="business-hero__title">{{ $business->name }}</h1>

                    <div class="business-hero__rating">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($rating) ? 'bi-star-fill' : 'bi-star' }}" style="color: #F59E0B"></i>
                        @endfor
                        <span style="color: rgba(255,255,255,0.8); margin-left: 0.5rem;">
                            {{ $rating }} ({{ $reviewsCount }} reseñas)
                        </span>
                    </div>

                    <div class="business-hero__location">
                        <i class="bi bi-geo-alt"></i>
                        {{ $address }}
                    </div>
                </div>
            </div>

            <div class="business-hero__actions">
                @if($phone)
                    <a href="tel:{{ $phone }}" class="business-hero-btn">
                        <i class="bi bi-telephone me-2"></i>
                        Llamar
                    </a>
                @endif
                @if($business->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->phone) }}" class="business-hero-btn business-hero-btn--whatsapp" target="_blank">
                        <i class="bi bi-whatsapp me-2"></i>
                        WhatsApp
                    </a>
                @endif
                <a href="#contact" class="business-hero-btn business-hero-btn--primary">
                    <i class="bi bi-envelope me-2"></i>
                    Contactar
                </a>
            </div>
        </div>
    </div>
</section>
