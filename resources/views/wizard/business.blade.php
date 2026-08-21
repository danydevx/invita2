@extends('wizard.layout')

@section('content')
<div class="wizard-card">
    <div class="wizard-header">
        <div class="wizard-logo">
            <i class="bi bi-shop"></i>
        </div>
        <h1 class="wizard-title">{{ app_name() }}</h1>
        <p class="wizard-subtitle">Configura tu negocio para comenzar</p>
    </div>

    <div class="wizard-progress">
        <div class="wizard-progress-bar">
            <div class="wizard-progress-fill" id="progressFill" style="width: 50%;"></div>
        </div>
        <div class="wizard-progress-steps">
            <div class="wizard-step active" id="step1Indicator">
                <div class="wizard-step-circle">1</div>
                <span class="wizard-step-label">Tipo</span>
            </div>
            <div class="wizard-step" id="step2Indicator">
                <div class="wizard-step-circle">2</div>
                <span class="wizard-step-label">Confirmar</span>
            </div>
        </div>
    </div>

    <form id="wizardForm" method="POST" action="{{ isset($isRegistrationStep) && $isRegistrationStep ? route('register.wizard.store') : route('wizard.business.store') }}">
        @csrf

        <div id="step1" class="wizard-step-content active">
            <div class="step-title">
                <h2>¿Qué tipo de negocio tienes?</h2>
                <p class="text-muted">Selecciona una categoría</p>
            </div>

            <div class="industry-grid" id="industryGrid">
                @foreach($businessTypes as $type)
                    <button type="button" class="industry-card" data-value="{{ $type->value }}" onclick="selectIndustry(this)">
                        <i class="bi {{ $type->icon() }}" style="color: {{ $type->color() }}"></i>
                        <span>{{ $type->label() }}</span>
                    </button>
                @endforeach
            </div>

            <input type="hidden" name="listing_type" id="selectedType" value="{{ old('business_type') }}">

            <div class="form-floating mt-4">
                <input type="text" name="business_name" id="businessName" class="form-control form-control-lg" placeholder="Nombre del negocio" value="{{ old('business_name') }}" required>
                <label for="businessName">Nombre del negocio</label>
            </div>

            @error('business_name')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
            @error('listing_type')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror

            <div class="wizard-actions">
                <button type="button" class="btn btn-primary btn-lg w-100" onclick="goToStep2()">
                    Continuar <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <div id="step2" class="wizard-step-content">
            <div class="step-title">
                <h2>Revisa y confirma</h2>
                <p class="text-muted">Verifica que los datos sean correctos</p>
            </div>

            <div class="review-card">
                <div class="review-item">
                    <div class="review-icon">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div class="review-content">
                        <span class="review-label">Tipo de negocio</span>
                        <span class="review-value" id="reviewType">-</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-link" onclick="goToStep1()">Editar</button>
                </div>

                <div class="review-item">
                    <div class="review-icon">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div class="review-content">
                        <span class="review-label">Nombre</span>
                        <span class="review-value" id="reviewName">-</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-link" onclick="goToStep1()">Editar</button>
                </div>

                <div class="review-item">
                    <div class="review-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="review-content">
                        <span class="review-label">Email</span>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" name="email" id="reviewEmail" class="form-control" value="{{ $userEmail }}" required>
                    <label for="reviewEmail">Correo electrónico</label>
                </div>

                <div class="review-item">
                    <div class="review-icon">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <div class="review-content">
                        <span class="review-label">Teléfono</span>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <input type="tel" name="phone" id="reviewPhone" class="form-control" placeholder="+52 123 456 7890">
                    <label for="reviewPhone">Teléfono (opcional)</label>
                </div>
            </div>

            @error('email')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror

            <div class="wizard-actions mt-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="goToStep1()">
                    <i class="bi bi-arrow-left me-2"></i> Volver
                </button>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i> Crear negocio
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.wizard-container {
    width: 100%;
    max-width: 600px;
    padding: 0 20px;
}

.wizard-card {
    background: white;
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.wizard-header {
    text-align: center;
    margin-bottom: 32px;
}

.wizard-logo {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.wizard-logo i {
    font-size: 32px;
    color: white;
}

.wizard-title {
    font-size: 24px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 4px;
}

.wizard-subtitle {
    color: #6B7280;
    margin: 0;
}

.wizard-progress {
    margin-bottom: 32px;
}

.wizard-progress-bar {
    height: 4px;
    background: #E5E7EB;
    border-radius: 2px;
    margin-bottom: 16px;
}

.wizard-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4F46E5 0%, #7C3AED 100%);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.wizard-progress-steps {
    display: flex;
    justify-content: space-between;
}

.wizard-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.wizard-step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #E5E7EB;
    color: #6B7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.wizard-step.active .wizard-step-circle {
    background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
    color: white;
}

.wizard-step.completed .wizard-step-circle {
    background: #10B981;
    color: white;
}

.wizard-step-label {
    font-size: 12px;
    color: #6B7280;
}

.wizard-step.active .wizard-step-label {
    color: #4F46E5;
    font-weight: 600;
}

.wizard-step-content {
    display: none;
}

.wizard-step-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.step-title {
    text-align: center;
    margin-bottom: 24px;
}

.step-title h2 {
    font-size: 20px;
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 4px;
}

.industry-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.industry-card {
    background: #F9FAFB;
    border: 2px solid #E5E7EB;
    border-radius: 12px;
    padding: 16px 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.industry-card:hover {
    border-color: #4F46E5;
    background: #EEF2FF;
}

.industry-card.selected {
    border-color: #4F46E5;
    background: #EEF2FF;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}

.industry-card i {
    font-size: 28px;
}

.industry-card span {
    font-size: 11px;
    font-weight: 500;
    color: #374151;
    text-align: center;
}

.wizard-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.wizard-actions .btn {
    padding: 12px 24px;
    font-weight: 600;
    border-radius: 12px;
}

.review-card {
    background: #F9FAFB;
    border-radius: 16px;
    padding: 20px;
}

.review-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #E5E7EB;
}

.review-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.review-item:first-child {
    padding-top: 0;
}

.review-icon {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.review-icon i {
    font-size: 18px;
    color: #4F46E5;
}

.review-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.review-label {
    font-size: 12px;
    color: #6B7280;
}

.review-value {
    font-size: 14px;
    font-weight: 600;
    color: #1F2937;
}

@media (max-width: 576px) {
    .wizard-card {
        padding: 24px;
    }

    .industry-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .wizard-actions {
        flex-direction: column;
    }
}
</style>

@push('scripts')
<script>
const typeLabels = {
    @foreach($listingTypes as $type)
    '{{ $type->value }}': '{{ $type->label() }}',
    @endforeach
};

function selectType(card) {
    document.querySelectorAll('.industry-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('selectedType').value = card.dataset.value;
}

function goToStep2() {
    const type = document.getElementById('selectedType').value;
    const name = document.getElementById('businessName').value;

    if (!type) {
        alert('Por favor selecciona un tipo de negocio');
        return;
    }

    if (!name || name.trim().length < 2) {
        alert('Por favor ingresa el nombre de tu negocio');
        return;
    }

    document.getElementById('step1').classList.remove('active');
    document.getElementById('step2').classList.add('active');
    document.getElementById('progressFill').style.width = '100%';
    document.getElementById('step1Indicator').classList.add('completed');
    document.getElementById('step1Indicator').classList.remove('active');
    document.getElementById('step2Indicator').classList.add('active');

    document.getElementById('reviewType').textContent = typeLabels[type] || type;
    document.getElementById('reviewName').textContent = name;
}

function goToStep1() {
    document.getElementById('step2').classList.remove('active');
    document.getElementById('step1').classList.add('active');
    document.getElementById('progressFill').style.width = '50%';
    document.getElementById('step1Indicator').classList.remove('completed');
    document.getElementById('step1Indicator').classList.add('active');
    document.getElementById('step2Indicator').classList.remove('active');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const selected = document.querySelector('.industry-card[data-value="{{ old('listing_type') }}"]');
    if (selected) {
        selectType(selected);
    }
});
</script>
@endpush
@endsection
