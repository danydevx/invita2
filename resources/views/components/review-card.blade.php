<article class="review-card">
    <div class="review-card__header">
        <div class="review-card__author">
            <div class="review-card__avatar">
                {{ strtoupper(substr($review->client_name, 0, 1)) }}
            </div>
            <div>
                <h4 class="review-card__name">{{ $review->client_name }}</h4>
                <span class="review-card__date">{{ $review->created_at->format('d M Y') }}</span>
            </div>
        </div>
        <div class="review-card__rating">
            @for($i = 1; $i <= 5; $i++)
                <i class="bi {{ $i <= round($review->rating) ? 'bi-star-fill' : 'bi-star' }}" style="color: #F59E0B"></i>
            @endfor
        </div>
    </div>

    <p class="review-card__comment">{{ $review->comment }}</p>
</article>
