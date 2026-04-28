/**
 * Visionneuse galerie : plein écran, navigation, clavier, focus.
 */
export function initBistroGalleryLightbox() {
    const root = document.querySelector('[data-bistro-gallery]');
    const dialog = document.getElementById('bistro-gallery-lightbox');
    if (! root || ! dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    const triggers = [...root.querySelectorAll('.gallery-zoom-trigger')];
    if (triggers.length === 0) {
        return;
    }

    const items = triggers.map((t) => ({
        src: t.dataset.gallerySrc || '',
        alt: t.dataset.galleryAlt || '',
        caption: t.dataset.galleryCaption || '',
    }));

    const img = dialog.querySelector('.bistro-gallery-lightbox__img');
    const stage = dialog.querySelector('.bistro-gallery-lightbox__stage');
    const captionEl = dialog.querySelector('.bistro-gallery-lightbox__caption');
    const btnClose = dialog.querySelector('.bistro-gallery-lightbox__close');
    const btnPrev = dialog.querySelector('.bistro-gallery-lightbox__prev');
    const btnNext = dialog.querySelector('.bistro-gallery-lightbox__next');

    if (! img || ! btnClose || ! btnPrev || ! btnNext) {
        return;
    }

    let index = 0;
    let lastTrigger = null;

    const showNav = items.length > 1;
    btnPrev.toggleAttribute('hidden', ! showNav);
    btnNext.toggleAttribute('hidden', ! showNav);

    img.addEventListener('click', (e) => e.stopPropagation());
    if (stage) {
        stage.addEventListener('click', () => close());
    }

    dialog.addEventListener('close', () => {
        if (lastTrigger && typeof lastTrigger.focus === 'function') {
            lastTrigger.focus({ preventScroll: true });
        }
        lastTrigger = null;
    });

    function render() {
        const item = items[index];
        if (! item?.src) {
            return;
        }
        img.src = item.src;
        img.alt = item.alt;
        if (captionEl) {
            const cap = item.caption?.trim();
            if (cap) {
                captionEl.textContent = cap;
                captionEl.classList.remove('hidden');
            } else {
                captionEl.textContent = '';
                captionEl.classList.add('hidden');
            }
        }
    }

    function openAt(i, triggerEl) {
        index = ((i % items.length) + items.length) % items.length;
        lastTrigger = triggerEl ?? null;
        render();
        dialog.showModal();
        btnClose.focus({ preventScroll: true });
    }

    function close() {
        dialog.close();
    }

    triggers.forEach((trigger, i) => {
        trigger.addEventListener('click', () => openAt(i, trigger));
        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openAt(i, trigger);
            }
        });
    });

    btnClose.addEventListener('click', (e) => {
        e.stopPropagation();
        close();
    });
    btnPrev.addEventListener('click', (e) => {
        e.stopPropagation();
        index = (index - 1 + items.length) % items.length;
        render();
    });
    btnNext.addEventListener('click', (e) => {
        e.stopPropagation();
        index = (index + 1) % items.length;
        render();
    });

    dialog.addEventListener('keydown', (e) => {
        if (! dialog.open) {
            return;
        }
        if (e.key === 'Escape') {
            return;
        }
        if (e.key === 'ArrowLeft' && showNav) {
            e.preventDefault();
            index = (index - 1 + items.length) % items.length;
            render();
        }
        if (e.key === 'ArrowRight' && showNav) {
            e.preventDefault();
            index = (index + 1) % items.length;
            render();
        }
    });
}
