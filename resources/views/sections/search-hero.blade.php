<section class="search-hero">
    <div class="container">
        <h1 class="search-hero__title">Explora Negocios</h1>

        <form method="GET" action="{{ route('directory.index') }}" class="directory-filters">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Nombre del negocio..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label for="type" class="form-label fw-semibold">Categoría</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Todas</option>
                        @foreach($businessTypes as $type)
                            <option value="{{ $type->value }}" {{ ($filters['type'] ?? '') == $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label for="location" class="form-label fw-semibold">Ubicación</label>
                    <input type="text" name="location" id="location" class="form-control" placeholder="Ciudad, CP..." value="{{ $filters['location'] ?? '' }}">
                </div>

                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100 h-100">
                        <i class="bi bi-search me-1"></i> Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
