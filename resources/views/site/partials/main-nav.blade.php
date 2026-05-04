<nav class="bistro-inner-nav" aria-label="Navigation principale" id="bistro-main-nav">
    <div class="bistro-inner-nav__links" id="bistro-nav-links">
        <a href="{{ route('site.home') }}" class="bistro-inner-nav__link">Accueil</a>
        <a href="{{ route('site.carte') }}" class="bistro-inner-nav__link">Menu</a>
        <a href="{{ route('site.galerie') }}" class="bistro-inner-nav__link">Galerie</a>
        <a href="{{ route('site.contact') }}" class="bistro-inner-nav__link">Contact</a>
        <a href="{{ route('site.reservation') }}" class="bistro-inner-nav__cta">Réserver</a>
    </div>
    <button
        class="bistro-inner-nav__toggle"
        aria-controls="bistro-nav-links"
        aria-expanded="false"
        aria-label="Ouvrir le menu de navigation"
        type="button"
    >
        <svg width="20" height="14" viewBox="0 0 20 14" fill="none" aria-hidden="true">
            <rect y="0" width="20" height="2" rx="1" fill="currentColor"/>
            <rect y="6" width="20" height="2" rx="1" fill="currentColor"/>
            <rect y="12" width="20" height="2" rx="1" fill="currentColor"/>
        </svg>
    </button>
</nav>
