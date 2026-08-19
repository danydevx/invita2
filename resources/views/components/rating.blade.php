<div class="rating @if(isset($size))rating--{{ $size }} @endif">
    <div class="rating__stars">
        @for($i = 1; $i <= 5; $i++)
            <span class="rating__star rating__star--{{ $i <= round($score) ? 'filled' : 'empty' }}">
                <i class="bi {{ $i <= round($score) ? 'bi-star-fill' : 'bi-star' }}"></i>
            </span>
        @endfor
    </div>
    @if(isset($count))
        <span class="rating__score">{{ $score }}</span>
        <span class="rating__count">({{ $count }} reseñas)</span>
    @endif
</div>
