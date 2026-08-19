@php($name = $brand['name'] ?? config('app.name'))

<div style="margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 16px; text-align: center; color: #64748b; font-size: 12px;">
    <div style="font-weight: 600; color: #0f172a;">{{ $name }}</div>
    <div>{{ $brand['footer_text'] }}</div>
    @if(!empty($brand['website']))
        <div style="margin-top: 6px;">{{ $brand['website'] }}</div>
    @endif
</div>
