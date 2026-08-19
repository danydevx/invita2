<a href="{{ route('directory.index', ['type' => $type->value]) }}" class="category-card category-card--{{ $type->value }}">
    <span class="category-card__icon" style="color: {{ $type->color() }}">
        <i class="bi {{ $type->icon() }}"></i>
    </span>
    <span class="category-card__title">{{ $type->label() }}</span>
</a>
