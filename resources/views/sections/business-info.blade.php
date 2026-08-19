<section class="business-section">
    @php
        $location = $business->locations->first();
        $city = $location ? $location->city : '';
        $state = $location ? $location->state : '';
    @endphp

    <div class="container">
        <div class="business-info">
            @if($business->description)
                <p class="business-info__description">{{ $business->description }}</p>
            @endif

            <div class="business-info__grid">
                @if($business->phone)
                    <div class="business-info__item">
                        <div class="business-info__icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div>
                            <span class="business-info__label">Teléfono</span>
                            <span class="business-info__value">
                                <a href="tel:{{ $business->phone }}">{{ $business->phone }}</a>
                            </span>
                        </div>
                    </div>
                @endif

                @if($business->phone)
                    <div class="business-info__item">
                        <div class="business-info__icon">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <span class="business-info__label">WhatsApp</span>
                            <span class="business-info__value">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->phone) }}" target="_blank">
                                    {{ $business->phone }}
                                </a>
                            </span>
                        </div>
                    </div>
                @endif

                @if($location)
                    <div class="business-info__item">
                        <div class="business-info__icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <span class="business-info__label">Dirección</span>
                            <span class="business-info__value">{{ $location->address_line_1 }}</span>
                        </div>
                    </div>
                @endif

                @if($city || $state)
                    <div class="business-info__item">
                        <div class="business-info__icon">
                            <i class="bi bi-globe"></i>
                        </div>
                        <div>
                            <span class="business-info__label">Ciudad</span>
                            <span class="business-info__value">{{ $city }}, {{ $state }}</span>
                        </div>
                    </div>
                @endif

                @if($business->website)
                    <div class="business-info__item">
                        <div class="business-info__icon">
                            <i class="bi bi-globe"></i>
                        </div>
                        <div>
                            <span class="business-info__label">Website</span>
                            <span class="business-info__value">
                                <a href="{{ $business->website }}" target="_blank">{{ Str::limit($business->website, 30) }}</a>
                            </span>
                        </div>
                    </div>
                @endif

                @if($business->email)
                    <div class="business-info__item">
                        <div class="business-info__icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <span class="business-info__label">Email</span>
                            <span class="business-info__value">
                                <a href="mailto:{{ $business->email }}">{{ $business->email }}</a>
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
