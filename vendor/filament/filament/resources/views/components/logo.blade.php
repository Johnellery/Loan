<style>
    .logo-container {
    display: flex;
    align-items: center;
}

.logo {
    height: 3rem; /* Adjust the height as needed */
    margin-right: 0.5rem; /* Adjust the spacing between logo and brand name */
}

.brand-name {
    font-size: 1.25rem; /* Adjust the font size as needed */
}

</style>

<div class="logo-container">
    <img src="{{ asset('/images/ritaa.png') }}" alt="Logo" class="logo">
    @if (filled($brand = filament()->getBrandName()))
        <div class="brand-name">
            {{ $brand }}
        </div>
    @endif
</div>
