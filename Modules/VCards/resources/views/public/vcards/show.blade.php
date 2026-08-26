@php
$primaryColor = $vcard->primary_color ?? '#2563EB';
$font = $vcard->font ?? 'Inter';
$design = $design ?? 'classic';
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $vcard->headline ?? 'Tarjeta digital de ' . $vcard->name }}">
    <meta property="og:title" content="{{ $vcard->name }}">
    <meta property="og:description" content="{{ $vcard->headline ?? $vcard->title . ($vcard->company ? ' @ ' . $vcard->company : '') }}">
    @if($vcard->profile_photo)
        <meta property="og:image" content="{{ $vcard->profile_photo }}">
    @endif
    <meta property="og:type" content="profile">
    <title>{{ $vcard->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($font) }}:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vcards.css') }}" rel="stylesheet">
    <style>
        :root {
            --vcard-primary: {{ $primaryColor }};
            --vcard-font: '{{ $font }}', sans-serif;
        }
    </style>
</head>
<body class="vcard vcard--{{ $design }}">
    <div class="vcard-wrapper">
        <div class="vcard-container">
            <div class="vcard__hero">
                <div class="vcard__hero-bg" style="background-color: {{ $primaryColor }}20;"></div>
                <div class="vcard__profile">
                    @if($vcard->profile_photo)
                        <img src="{{ $vcard->profile_photo }}" alt="{{ $vcard->name }}" class="vcard__profile-photo">
                    @else
                        <div class="vcard__profile-placeholder" style="background-color: {{ $primaryColor }};">
                            <i class="bi bi-person"></i>
                        </div>
                    @endif
                </div>
            </div>

            <div class="vcard__content">
                <div class="vcard__header text-center">
                    <h1 class="vcard__name">{{ $fullName ?: $vcard->name }}</h1>
                    @if($vcard->accreditations)
                        <p class="vcard__accreditations">{{ $vcard->accreditations }}</p>
                    @endif
                    @if($displayTitle)
                        <p class="vcard__title">{{ $displayTitle }}</p>
                    @endif
                    @if($vcard->headline)
                        <p class="vcard__headline">{{ $vcard->headline }}</p>
                    @endif
                </div>

                @if($vcard->contacts && $vcard->contacts->count() > 0)
                    <div class="vcard__contacts">
                        @foreach($vcard->contacts as $contact)
                            <a href="{{ $contact->tel_link }}"
                               class="vcard__contact vcard__contact--{{ $contact->type }}"
                               target="_blank"
                               rel="noopener">
                                <i class="{{ $contact->type === 'whatsapp' ? 'bi-whatsapp' : ($contact->type === 'email' ? 'bi-envelope' : 'bi-telephone') }}"></i>
                                <span>{{ $contact->display_value }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($vcard->activeFields && $vcard->activeFields->count() > 0)
                    <div class="vcard__fields">
                        @foreach($vcard->activeFields as $field)
                            @if($field->action_url)
                                <a href="{{ $field->action_url }}"
                                   class="vcard__field"
                                   target="_blank"
                                   rel="noopener nofollow">
                                    <i class="{{ $field->field_type_definition['icon'] ?? 'bi-link' }}"></i>
                                    <span>{{ $field->label ?: $field->field_type_definition['name'] }}</span>
                                </a>
                            @else
                                <div class="vcard__field vcard__field--static">
                                    <i class="{{ $field->field_type_definition['icon'] ?? 'bi-link' }}"></i>
                                    <span>{{ $field->label ?: $field->display_value }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <div class="vcard__actions">
                    <a href="/v/{{ $vcard->slug }}/download" class="vcard__action vcard__action--primary" download>
                        <i class="bi bi-download me-2"></i>
                        Guardar contacto
                    </a>
                    <button type="button" class="vcard__action" onclick="copyVCardLink()">
                        <i class="bi bi-link me-2"></i>
                        Copiar enlace
                    </button>
                </div>

                <div class="vcard__qr text-center mt-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($vcard->public_url) }}"
                         alt="QR Code"
                         class="vcard__qr-image">
                    <p class="vcard__qr-text small text-muted mt-2">Escanea para guardar mi contacto</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyVCardLink() {
            navigator.clipboard.writeText(window.location.href);
            alert('Enlace copiado al portapapeles');
        }
    </script>
</body>
</html>
