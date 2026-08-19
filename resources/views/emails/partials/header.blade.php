@php($name = $brand['name'] ?? config('app.name'))
@php($logo = $brand['logo'] ?? null)

<div style="text-align: center; margin-bottom: 24px;">
    @if($logo)
        <img src="{{ $logo }}" alt="{{ $name }}" style="max-width: 160px; height: auto;" />
    @else
        <div style="font-size: 20px; font-weight: 700; color: {{ $brand['secondary_color'] }};">
            {{ $name }}
        </div>
    @endif
</div>
