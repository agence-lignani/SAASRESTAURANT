document.addEventListener('DOMContentLoaded', async () => {
    const pending = [];

    if (document.querySelector('[data-bistro-gallery]') && document.getElementById('bistro-gallery-lightbox')) {
        pending.push(
            import('./gallery-lightbox').then(({ initBistroGalleryLightbox }) => {
                initBistroGalleryLightbox();
            }),
        );
    }

    const chatRoot = document.getElementById('bistro-chat-root');
    if (chatRoot?.dataset.endpoint) {
        pending.push(
            import('./chat-widget').then(({ initBistroChatWidget }) => {
                initBistroChatWidget();
            }),
        );
    }

    if (pending.length > 0) {
        await Promise.all(pending);
    }

    // Mobile nav toggle for inner pages
    const toggle = document.querySelector('.bistro-inner-nav__toggle');
    const nav = document.querySelector('.bistro-inner-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(isOpen));
            toggle.setAttribute(
                'aria-label',
                isOpen ? 'Fermer le menu de navigation' : 'Ouvrir le menu de navigation',
            );
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (nav.classList.contains('is-open') && !nav.contains(e.target)) {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Ouvrir le menu de navigation');
            }
        });

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && nav.classList.contains('is-open')) {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Ouvrir le menu de navigation');
                toggle.focus();
            }
        });
    }
});
