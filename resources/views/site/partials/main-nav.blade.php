<nav class="bistro-main-nav" aria-label="Navigation principale">
    <div class="bistro-main-nav-links">
        <a href="{{ route('site.home') }}" class="{{ ($isBakeryTheme ?? false) ? 'text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-white/85 hover:text-white' : 'bistro-main-nav-link' }}">ACCUEIL</a>
        <a href="{{ route('site.carte') }}" class="{{ ($isBakeryTheme ?? false) ? 'text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-white/85 hover:text-white' : 'bistro-main-nav-link' }}">MENU</a>
        <a href="{{ route('site.home') }}#manifesto-heading" class="{{ ($isBakeryTheme ?? false) ? 'text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-white/85 hover:text-white' : 'bistro-main-nav-link' }}">A PROPOS</a>
    </div>
    <a href="{{ route('site.reservation') }}" class="{{ ($isBakeryTheme ?? false) ? 'inline-flex h-10 items-center justify-center bg-[#d97a3a] px-5 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-white transition hover:brightness-105' : 'bistro-main-nav-cta' }}">RÉSERVER</a>
</nav>
