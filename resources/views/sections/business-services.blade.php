<section class="business-section business-section--alt">
    <div class="container">
        <header class="section-header section-header--center section-header--compact">
            <span class="section-header__eyebrow">Servicios</span>
            <h2 class="section-header__title">Nuestros Servicios</h2>
        </header>

        @if($services->count() > 0)
            <div class="services-grid">
                @foreach($services as $service)
                    <x-service-card :service="$service" :show-book="true" />
                @endforeach
            </div>
        @else
            <p class="text-center text-muted">No hay servicios disponibles.</p>
        @endif
    </div>
</section>
