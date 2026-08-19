<header class="section-header @if(isset($center))section-header--center @endif @if(isset($compact))section-header--compact @endif @if(isset($large))section-header--large @endif">
    @if(isset($eyebrow))
        <span class="section-header__eyebrow">{{ $eyebrow }}</span>
    @endif
    <h2 class="section-header__title">{{ $title }}</h2>
    @if(isset($description))
        <p class="section-header__description">{{ $description }}</p>
    @endif
</header>
