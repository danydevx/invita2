@extends('layouts.auth')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h4 mb-2">Crear cuenta</h1>
                    <p class="text-muted mb-4">Completa tus datos para registrarte.</p>

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

                    <form class="row g-3" method="POST" action="{{ route('register.store') }}">
                        @csrf

                        <div class="col-12">
                            <label for="register-name" class="form-label">Nombre completo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input
                                    id="register-name"
                                    name="name"
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Tu nombre completo"
                                    autocomplete="name"
                                    value="{{ old('name') }}"
                                    required
                                />
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="register-email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input
                                    id="register-email"
                                    name="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="correo@ejemplo.com"
                                    autocomplete="email"
                                    value="{{ old('email', $prefill['email'] ?? '') }}"
                                    required
                                />
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="register-password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input
                                    id="register-password"
                                    name="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Minimo 8 caracteres, con letras y numeros"
                                    autocomplete="new-password"
                                    required
                                />
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('register-password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Minimo 8 caracteres, con letras y numeros.</small>
                                <button type="button" class="btn btn-sm btn-link text-decoration-none" onclick="generatePassword()">
                                    <i class="bi bi-magic me-1"></i>Generar
                                </button>
                            </div>
                            <div id="password-strength" class="mt-2">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted"></small>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="register-password-confirmation" class="form-label">Confirmar contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input
                                    id="register-password-confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    placeholder="Repite la contraseña"
                                    autocomplete="new-password"
                                    required
                                />
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('register-password-confirmation', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12" aria-hidden="true">
                            <input
                                name="company"
                                type="text"
                                tabindex="-1"
                                autocomplete="off"
                                class="visually-hidden"
                                placeholder=""
                            />
                            <input name="form_started_at" type="hidden" value="{{ $formStartedAt }}" />
                        </div>

                        @if(isset($prefill['invite']) && $prefill['invite'])
                            <input type="hidden" name="invite" value="{{ $prefill['invite'] }}" />
                        @endif

                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="bi bi-person-plus me-2"></i>Crear cuenta
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Ya tienes cuenta? Inicia sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generatePassword() {
    const length = 16;
    const lowercase = 'abcdefghijklmnopqrstuvwxyz';
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const numbers = '0123456789';
    const symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    const allChars = lowercase + uppercase + numbers + symbols;

    let password = '';

    password += lowercase[Math.floor(Math.random() * lowercase.length)];
    password += uppercase[Math.floor(Math.random() * uppercase.length)];
    password += numbers[Math.floor(Math.random() * numbers.length)];
    password += symbols[Math.floor(Math.random() * symbols.length)];

    for (let i = 4; i < length; i++) {
        password += allChars[Math.floor(Math.random() * allChars.length)];
    }

    password = password.split('').sort(() => Math.random() - 0.5).join('');

    document.getElementById('register-password').value = password;
    document.getElementById('register-password-confirmation').value = password;

    updatePasswordStrength(password);
}

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

function updatePasswordStrength(password) {
    const strengthBar = document.querySelector('#password-strength .progress-bar');
    const strengthText = document.querySelector('#password-strength small');

    let strength = 0;

    if (password.length >= 8) strength += 20;
    if (password.length >= 12) strength += 10;
    if (password.length >= 16) strength += 10;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 20;
    if (/\d/.test(password)) strength += 20;
    if (/[^a-zA-Z0-9]/.test(password)) strength += 20;

    let barClass = 'bg-danger';
    let text = 'Débil';

    if (strength >= 60) {
        barClass = 'bg-warning';
        text = 'Media';
    }
    if (strength >= 80) {
        barClass = 'bg-success';
        text = 'Fuerte';
    }

    strengthBar.style.width = strength + '%';
    strengthBar.className = 'progress-bar ' + barClass;
    strengthText.textContent = text;
}

document.getElementById('register-password').addEventListener('input', function(e) {
    updatePasswordStrength(e.target.value);
});
</script>
@endpush
