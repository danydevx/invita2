<section class="business-section business-section--alt">
    <div class="container">
        <header class="section-header section-header--center section-header--compact">
            <span class="section-header__eyebrow">Ubicación</span>
            <h2 class="section-header__title">Encuéntranos</h2>
        </header>

        <div class="business-location__grid">
            <div class="business-location__map">
                <div id="business-map" style="height: 300px; background: #f0f0f0;"></div>
            </div>

            <div class="business-location__details">
                @foreach($locations as $location)
                    <div class="business-location__address">
                        <h3 class="business-location__address-title">{{ $location->name ?? 'Dirección' }}</h3>
                        <p class="business-location__address-text">
                            {{ $location->address_line_1 }}<br>
                            @if($location->address_line_2)
                                {{ $location->address_line_2 }}<br>
                            @endif
                            {{ $location->city }}, {{ $location->state }} {{ $location->postal_code }}
                        </p>
                    </div>

                    @if($location->phone)
                        <div class="business-location__phone">
                            <i class="bi bi-telephone"></i>
                            <a href="tel:{{ $location->phone }}">{{ $location->phone }}</a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
