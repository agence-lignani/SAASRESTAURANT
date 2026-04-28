<style>
    .site-section-dnd-handle {
        cursor: grab;
        border: 1px dashed #d6d3d1;
        border-radius: .5rem;
        padding: .2rem .5rem;
        font-size: .75rem;
        color: #57534e;
        user-select: none;
    }

    [data-site-section-dnd-card].is-dragging {
        opacity: .65;
    }
</style>
<script>
(() => {
    console.info('[site-content-dnd] loaded');
    const ORDER_PREFIX = 'Ordre des sections';
    const TAB_TO_PAGE = {
        'Accueil': 'home',
        'Carte': 'carte',
        'Galerie': 'galerie',
        'Contact': 'contact',
        'Réservation': 'reservation',
        'Gestion réservation client': 'reservation_manage',
    };

    const SECTION_KEYS_BY_PAGE = {
        home: {
            'Accueil — bandeau principal': 'hero',
            'À propos / Histoire / Chef': 'manifesto',
            'Carte mise en avant (plats phares)': 'carte_narrative',
            'Menus & formules': 'menus',
            'Galerie ambiance': 'gallery_highlights',
            'Événements & privatisation': 'espaces',
            'Widget avis (TripAdvisor)': 'reviews_widget',
            'FAQ pratique': 'faq',
            'CTA réservation finale': 'spotlight',
            'Accès, contact & horaires': 'practical',
        },
        carte: {
            'En-tête de page': 'header',
            'Messages si liste vide': 'menu_list',
            'Libellés dans la liste': 'menu_list',
        },
        galerie: {
            'En-tête': 'header',
            'État vide': 'gallery',
            'Visionneuse (lightbox)': 'gallery',
        },
        contact: {
            'Page contact': 'header',
            'Messages après envoi': 'feedback',
            'Libellés du formulaire': 'form',
        },
        reservation: {
            'En-tête de page': 'booking_form',
            'Accroche': 'booking_form',
            'Libellés des volets (accordion)': 'booking_form',
            'Libellés à l’intérieur des volets': 'booking_form',
            'Placeholders des champs contact': 'booking_form',
            'Messages de retour': 'feedback',
            'Aide & envoi': 'booking_form',
            'Textes JavaScript (chargement des créneaux)': 'booking_form',
        },
        reservation_manage: {
            'En-tête': 'header',
            'Libellés du récapitulatif': 'summary',
            'Reprogrammation': 'actions',
            'Textes JavaScript (créneaux de reprogrammation)': 'actions',
        },
    };

    const getActivePage = () => {
        const activeTab = document.querySelector('[role="tab"][aria-selected="true"], [role="tab"][data-active="true"]');
        const tabLabel = activeTab?.textContent?.trim() ?? '';
        return TAB_TO_PAGE[tabLabel] ?? 'home';
    };

    const findOrderSection = () => {
        const headings = [...document.querySelectorAll('h2, h3')];
        const heading = headings.find((el) => (el.textContent ?? '').trim().startsWith(ORDER_PREFIX));
        return heading?.closest('[class*="fi-section"], section, div') ?? null;
    };

    const hideOrderSection = () => {
        const orderSection = findOrderSection();
        if (!orderSection) return;
        orderSection.style.display = 'none';
    };

    const getOrderSelects = () => {
        const orderSection = findOrderSection();
        if (!orderSection) return [];
        return [...orderSection.querySelectorAll('select')];
    };

    const syncOrderStorage = (keys) => {
        const selects = getOrderSelects();
        if (!selects.length) return;

        keys.forEach((key, index) => {
            const select = selects[index];
            if (!select) return;
            select.value = key;
            select.dispatchEvent(new Event('input', { bubbles: true }));
            select.dispatchEvent(new Event('change', { bubbles: true }));
        });
    };

    const collectCards = (page) => {
        const mapper = SECTION_KEYS_BY_PAGE[page] ?? {};
        const cards = [];
        const headings = [...document.querySelectorAll('h2')];

        headings.forEach((heading) => {
            const title = (heading.textContent ?? '').trim();
            if (!title || title.startsWith(ORDER_PREFIX) || !(title in mapper)) return;

            const card = heading.closest('[class*="fi-section"], section, div');
            if (!card || card.dataset.siteSectionDndCard === '1') return;

            card.dataset.siteSectionDndCard = '1';
            card.dataset.siteSectionKey = mapper[title];
            cards.push(card);
        });

        return cards;
    };

    const addHandle = (card) => {
        const heading = card.querySelector('h2');
        if (!heading || card.querySelector('.site-section-dnd-handle')) return;

        const handle = document.createElement('button');
        handle.type = 'button';
        handle.className = 'site-section-dnd-handle';
        handle.textContent = '↕ Déplacer la section';
        heading.parentElement?.appendChild(handle);
        handle.addEventListener('mousedown', () => {
            card.draggable = true;
        });
        handle.addEventListener('mouseup', () => {
            card.draggable = false;
        });
    };

    const initDrag = (cards) => {
        let dragged = null;

        cards.forEach((card) => {
            addHandle(card);

            card.addEventListener('dragstart', () => {
                dragged = card;
                card.classList.add('is-dragging');
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('is-dragging');
                card.draggable = false;
                dragged = null;

                const all = [...document.querySelectorAll('[data-site-section-dnd-card="1"]')];
                const keys = [];
                all.forEach((el) => {
                    const key = el.dataset.siteSectionKey;
                    if (key && !keys.includes(key)) keys.push(key);
                });
                syncOrderStorage(keys);
            });

            card.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (!dragged || dragged === card) return;

                const rect = card.getBoundingClientRect();
                const before = event.clientY < rect.top + rect.height / 2;
                card.parentNode?.insertBefore(dragged, before ? card : card.nextSibling);
            });
        });
    };

    const boot = () => {
        hideOrderSection();
        const page = getActivePage();
        const cards = collectCards(page);
        if (cards.length) initDrag(cards);
    };

    document.addEventListener('DOMContentLoaded', boot);
    document.addEventListener('livewire:navigated', () => setTimeout(boot, 100));
    document.addEventListener('livewire:initialized', () => setTimeout(boot, 100));
})();
</script>
