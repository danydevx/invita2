<section class="directory-section directory-section--results">
    <div class="container">
        <div class="results-layout">
            <div class="results-layout__list">
                <div class="results-count">
                    <span class="results-count__text">
                        <i class="bi bi-shop me-1"></i>
                        {{ $businesses->total() }} negocios encontrados
                    </span>
                </div>

                <div class="business-card-list">
                    @forelse($businesses as $business)
                        <x-business-card :business="$business" :show-footer="true" />
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="mt-3 text-muted">No se encontraron negocios con esos criterios.</p>
                            <a href="{{ route('directory.index') }}" class="btn btn-outline-primary">Ver todos</a>
                        </div>
                    @endforelse
                </div>

                @if($businesses->hasPages())
                    <nav class="directory-pagination">
                        {{ $businesses->links() }}
                    </nav>
                @endif
            </div>

            <div class="results-layout__map">
                <div id="directory-map" style="height: 100%; width: 100%;"></div>
            </div>
        </div>
    </div>
</section>
