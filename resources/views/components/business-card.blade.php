<article class="business-card @if(isset($horizontal))business-card--horizontal @endif @if(isset($compact))business-card--compact @endif">
    @php
        $coverImage = $business->cover_image_path ?? ($business->logo_path ?? 'https://picsum.photos/seed/'.$business->id.'/400/300');
        $logoImage = $business->logo_path ?? 'https://picsum.photos/seed/logo'.$business->id.'/100/100';
        $categoryName = $business->listing_type?->label() ?? 'Negocio';
        $city = $business->locations->first()?->city ?? 'Ciudad';
        $state = $business->locations->first()?->state ?? '';
        $rating = round($business->reviews_avg_rating ?? $business->rating ?? 0, 1);
        $reviewsCount = $business->reviews_count ?? 0;
    @endphp

    <a href="{{ route('directory.show', $business->slug) }}" class="business-card__image-wrap">
        <img
            src="{{ $coverImage }}"
            alt="{{ $business->name }}"
            class="business-card__image"
            loading="lazy"
        >
    </a>

    <div class="business-card__body">
        <a href="{{ route('directory.show', $business->slug) }}">
            <span class="business-card__category">{{ $categoryName }}</span>
            <h3 class="business-card__title">{{ $business->name }}</h3>
        </a>

        <div class="business-card__location">
            <i class="bi bi-geo-alt"></i>
            {{ $city }}, {{ $state }}
        </div>

        <div class="business-card__rating">
            <span class="business-card__rating-score">{{ $rating }}</span>
            <span class="business-card__rating-stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= round($rating) ? 'bi-star-fill' : 'bi-star' }}" style="color: #F59E0B"></i>
                @endfor
            </span>
            <span class="business-card__rating-count">({{ $reviewsCount }})</span>
        </div>

        @if($business->description)
            <p class="business-card__description">{{ Str::limit($business->description, 100) }}</p>
        @endif

        @if(isset($showFooter) && $showFooter)
            <div class="business-card__footer">
                <a href="{{ route('directory.show', $business->slug) }}" class="business-card__action">
                    Ver más <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>
</article>
