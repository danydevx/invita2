<article class="service-card">
    <div class="service-card__header">
        <div>
            <h4 class="service-card__title">{{ $service->name }}</h4>
        </div>
        @if($service->price)
            <span class="service-card__price">${{ number_format($service->price, 0) }}</span>
        @endif
    </div>

    @if($service->description)
        <p class="service-card__description">{{ $service->description }}</p>
    @endif

    <div class="service-card__footer">
        @if($service->duration_minutes)
            <span class="service-card__duration">
                <i class="bi bi-clock"></i>
                {{ $service->duration_minutes }} min
            </span>
        @endif
        @if(isset($showBook) && $showBook)
            <a href="#book" class="service-card__action">Reservar</a>
        @endif
    </div>
</article>
