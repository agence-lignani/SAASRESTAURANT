/**
 * Barre de progression type Pronote (segment vert animé) pendant les navigations Livewire / Filament.
 */
(() => {
    const BAR_ID = 'fi-pronote-nav-progress';

    const ensureBar = () => {
        let bar = document.getElementById(BAR_ID);
        if (bar) {
            return bar;
        }

        bar = document.createElement('div');
        bar.id = BAR_ID;
        bar.setAttribute('role', 'progressbar');
        bar.setAttribute('aria-hidden', 'true');
        bar.setAttribute('aria-valuemin', '0');
        bar.setAttribute('aria-valuemax', '100');
        bar.setAttribute('aria-valuenow', '0');
        bar.className = 'fi-pronote-nav-progress';
        bar.innerHTML =
            '<div class="fi-pronote-nav-progress__track"><div class="fi-pronote-nav-progress__wave"></div></div>';
        document.body.appendChild(bar);

        return bar;
    };

    const setActive = (active) => {
        const bar = ensureBar();
        bar.classList.toggle('fi-pronote-nav-progress--active', active);
        bar.setAttribute('aria-hidden', active ? 'false' : 'true');
        bar.setAttribute('aria-valuenow', active ? '50' : '0');
    };

    document.addEventListener('livewire:navigate', () => setActive(true));
    document.addEventListener('livewire:navigating', () => setActive(true));
    document.addEventListener('livewire:navigated', () => setActive(false));

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            setActive(false);
        }
    });

    if (document.readyState === 'loading') {
        setActive(true);
        document.addEventListener(
            'DOMContentLoaded',
            () => {
                setActive(false);
            },
            { once: true },
        );
    }
})();
