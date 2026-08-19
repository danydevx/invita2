@extends('layouts.auth')

@section('content')
<div class="container py-5">
    <div class="row align-items-center g-4 g-lg-5">
        <div class="col-lg-6">
            <span class="badge text-bg-secondary">Tu SaaS</span>
            <h1 class="display-6 fw-semibold mt-3 mb-2">Bienvenido al dashboard</h1>
            <p class="text-muted">
                Accede a indicadores claros, tareas prioritarias y reportes listos para decisiones rapidas.
            </p>

            <div class="d-flex flex-column gap-3 mt-4">
                <div class="d-flex gap-3 align-items-start">
                    <i class="bi bi-speedometer2 fs-4 text-primary"></i>
                    <div>
                        <div class="fw-semibold">Metricas en tiempo real</div>
                        <div class="text-muted">Visualiza el estado del negocio en un solo lugar.</div>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-start">
                    <i class="bi bi-people fs-4 text-primary"></i>
                    <div>
                        <div class="fw-semibold">Equipos alineados</div>
                        <div class="text-muted">Permisos y roles listos para cada area.</div>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-start">
                    <i class="bi bi-shield-check fs-4 text-primary"></i>
                    <div>
                        <div class="fw-semibold">Acceso confiable</div>
                        <div class="text-muted">Entradas seguras con trazabilidad y control.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 offset-lg-1">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div>
                            <h2 class="h4 mb-1">Iniciar sesión</h2>
                            <p class="text-muted mb-0">Entra para continuar al panel.</p>
                        </div>
                        <span class="badge text-bg-success">Acceso seguro</span>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex gap-3 mb-4">
                        <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="btn btn-outline-secondary flex-grow-1">
                            <i class="bi bi-google me-2"></i>
                            Google
                        </a>
                        <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}" class="btn btn-outline-secondary flex-grow-1">
                            <i class="bi bi-facebook me-2"></i>
                            Facebook
                        </a>
                    </div>

                    <div class="text-center mb-4">
                        <span class="text-muted">o continúa con tu email</span>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="login-email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input
                                    id="login-email"
                                    name="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="tu@ejemplo.com"
                                    autocomplete="email"
                                    value="{{ old('email') }}"
                                    required
                                />
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="login-password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input
                                    id="login-password"
                                    name="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="••••••••"
                                    autocomplete="current-password"
                                    required
                                />
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('login-password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="form-check">
                                <input
                                    id="remember"
                                    name="remember"
                                    class="form-check-input"
                                    type="checkbox"
                                    value="1"
                                    checked
                                />
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                <i class="bi bi-question-circle me-1"></i>Olvidaste tu clave?
                            </a>
                        </div>

                        <button class="btn btn-primary w-100 btn-lg" type="submit">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Entrar al dashboard
                        </button>
                    </form>

                    <p class="small text-muted mt-3 mb-0 text-center">
                        Si no tienes cuenta, <a href="{{ route('register') }}" class="text-decoration-none">regístrate aquí</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endpush
