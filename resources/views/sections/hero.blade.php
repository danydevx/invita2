<section class="directory-hero">
    <div class="container">
        <div class="directory-hero__content">
            <span class="directory-hero__eyebrow">Directorio Local</span>
            <h1 class="directory-hero__title">Encuentra negocios y profesionales cerca de ti</h1>
            <p class="directory-hero__description">
                Explora miles de negocios locales, compara opciones y conecta directamente.
            </p>

            <form action="{{ route('directory.index') }}" method="GET" class="directory-search">
                <div class="directory-search__field">
                    <input
                        type="search"
                        name="search"
                        class="directory-search__input"
                        placeholder="Busca negocios, servicios..."
                    >
                </div>
                <button type="submit" class="btn btn-light btn-lg">
                    <i class="bi bi-search me-2"></i>
                    Buscar
                </button>
            </form>
        </div>
    </div>
</section>
