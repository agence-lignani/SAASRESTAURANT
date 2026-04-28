(() => {
    let isInternalDomUpdate = false;

    const ORDER_PREFIXES = ['Ordre des sections', 'Section order'];
    const TAB_TO_PAGE = {
        'Accueil': 'home',
        'Carte': 'carte',
        'Galerie': 'galerie',
        'Contact': 'contact',
        'Réservation': 'reservation',
        'Gestion réservation client': 'reservation_manage',
    };

    const addStyles = () => {
        if (document.getElementById('site-section-dnd-style')) {
            return;
        }

        const style = document.createElement('style');
        style.id = 'site-section-dnd-style';
        style.textContent = `
            [data-section-order-editor] {
                display: none !important;
            }

            .site-section-dnd-actions {
                display: flex;
                gap: .5rem;
                margin: .5rem 0 0;
                padding: .5rem;
                border: 1px solid #e7e5e4;
                border-radius: .65rem;
                background: #fafaf9;
                width: fit-content;
            }

            .site-section-dnd-action {
                border: 1px dashed #d6d3d1;
                border-radius: .5rem;
                padding: .2rem .5rem;
                font-size: .75rem;
                color: #57534e;
                user-select: none;
                background: #fff;
                line-height: 1.2;
            }

            .site-section-dnd-action:disabled {
                opacity: .4;
                cursor: not-allowed;
            }
        `;
        document.head.appendChild(style);
    };

    const getActivePage = () => {
        const activeTab = document.querySelector('[role="tab"][aria-selected="true"], [role="tab"][data-active="true"]');
        const tabLabel = activeTab?.textContent?.trim() ?? '';
        return TAB_TO_PAGE[tabLabel] ?? 'home';
    };

    const hideOrderSection = () => {
        document.querySelectorAll('[data-section-order-editor]').forEach((editor) => {
            const wrapper = editor.closest('[class*="fi-section"], section, div') ?? editor;
            wrapper.hidden = true;
            wrapper.setAttribute('aria-hidden', 'true');
        });

        const headings = [...document.querySelectorAll('h2, h3')];

        headings.forEach((heading) => {
            const text = (heading.textContent ?? '').trim();
            if (!ORDER_PREFIXES.some((prefix) => text.startsWith(prefix))) {
                return;
            }

            let node = heading;
            while (node && node !== document.body) {
                const hasSelect = !!node.querySelector?.('select');
                const hasSectionLabel = /Section\*/i.test(node.textContent ?? '') || (node.textContent ?? '').includes('Bandeau principal');
                if (hasSelect && hasSectionLabel) {
                    node.hidden = true;
                    node.setAttribute('aria-hidden', 'true');
                    break;
                }
                node = node.parentElement;
            }
        });
    };

    const getOrderSelects = (page = null) => {
        if (page) {
            const pageEditor = document.querySelector(`[data-section-order-editor="${page}"]`);
            if (pageEditor) {
                const pageEditorContainer = pageEditor.closest('[class*="fi-section"], section, div') ?? pageEditor;
                const pageSelects = [...pageEditorContainer.querySelectorAll('select')];
                if (pageSelects.length) {
                    return pageSelects;
                }
            }
        }

        const headings = [...document.querySelectorAll('h2, h3')];
        const heading = headings.find((el) => {
            const text = (el.textContent ?? '').trim();
            return ORDER_PREFIXES.some((prefix) => text.startsWith(prefix));
        });
        let node = heading ?? null;
        while (node && node !== document.body) {
            const selects = node.querySelectorAll?.('select');
            if (selects?.length) {
                return [...selects];
            }
            node = node.parentElement;
        }
        return [];
    };

    const syncOrderStorage = (keys, page = null) => {
        const selects = getOrderSelects(page);
        keys.forEach((key, index) => {
            const select = selects[index];
            if (!select) return;
            select.value = key;
            select.dispatchEvent(new Event('input', { bubbles: true }));
            select.dispatchEvent(new Event('change', { bubbles: true }));
        });
    };

    const getPageCards = (page) => {
        return [...document.querySelectorAll('[data-site-section-dnd-card="1"]')]
            .filter((card) => card.dataset.siteSectionPage === page);
    };

    const syncOrderStorageFromPage = (page) => {
        const keys = [];

        getPageCards(page).forEach((card) => {
            const key = card.dataset.siteSectionKey;
            if (key && !keys.includes(key)) {
                keys.push(key);
            }
        });

        syncOrderStorage(keys, page);
    };

    const collectCards = (page) => {
        const cards = [...document.querySelectorAll(`[data-draggable-page-section="1"][data-page="${page}"]`)];

        cards.forEach((card) => {
            const sectionKey = card.dataset.sectionKey;
            if (!sectionKey) return;
            card.dataset.siteSectionDndCard = '1';
            card.dataset.siteSectionKey = sectionKey;
            card.dataset.siteSectionPage = page;
        });

        return cards.filter((card) => card.dataset.siteSectionKey);
    };

    const applyStoredOrderToCards = (page, cards) => {
        if (!cards.length) return;

        const selects = getOrderSelects(page);
        const storedOrder = selects.map((select) => select.value).filter(Boolean);
        if (!storedOrder.length) return;

        const parent = cards[0].parentElement;
        if (!parent) return;

        const byKey = new Map();
        cards.forEach((card) => {
            const key = card.dataset.siteSectionKey;
            if (!key) return;
            if (!byKey.has(key)) {
                byKey.set(key, []);
            }
            byKey.get(key).push(card);
        });

        const orderedCards = [];

        storedOrder.forEach((key) => {
            const keyCards = byKey.get(key) ?? [];
            keyCards.forEach((card) => orderedCards.push(card));
            byKey.delete(key);
        });

        byKey.forEach((keyCards) => {
            keyCards.forEach((card) => orderedCards.push(card));
        });

        const currentOrder = cards.map((card) => card.dataset.siteSectionKey).join('|');
        const targetOrder = orderedCards.map((card) => card.dataset.siteSectionKey).join('|');
        if (currentOrder === targetOrder) {
            return;
        }

        isInternalDomUpdate = true;
        orderedCards.forEach((card) => {
            parent.appendChild(card);
        });
        isInternalDomUpdate = false;
    };

    const refreshActionStates = (page) => {
        const cards = getPageCards(page);
        cards.forEach((card, index) => {
            const upButton = card.querySelector('[data-site-section-action="up"]');
            const downButton = card.querySelector('[data-site-section-action="down"]');
            if (upButton) {
                upButton.disabled = index === 0;
            }
            if (downButton) {
                downButton.disabled = index === cards.length - 1;
            }
        });
    };

    const moveCard = (page, card, direction) => {
        const cards = getPageCards(page);
        const index = cards.indexOf(card);
        if (index === -1) return;

        if (direction === 'up' && index > 0) {
            const previousCard = cards[index - 1];
            previousCard.parentNode?.insertBefore(card, previousCard);
        }

        if (direction === 'down' && index < cards.length - 1) {
            const nextCard = cards[index + 1];
            nextCard.parentNode?.insertBefore(nextCard, card);
        }

        refreshActionStates(page);
        syncOrderStorageFromPage(page);
    };

    const addSectionActions = (page, card) => {
        if (card.querySelector('.site-section-dnd-actions')) return;

        const actions = document.createElement('div');
        actions.className = 'site-section-dnd-actions';

        const upButton = document.createElement('button');
        upButton.type = 'button';
        upButton.className = 'site-section-dnd-action';
        upButton.dataset.siteSectionAction = 'up';
        upButton.textContent = '↑ Monter';
        upButton.addEventListener('click', () => moveCard(page, card, 'up'));

        const downButton = document.createElement('button');
        downButton.type = 'button';
        downButton.className = 'site-section-dnd-action';
        downButton.dataset.siteSectionAction = 'down';
        downButton.textContent = '↓ Descendre';
        downButton.addEventListener('click', () => moveCard(page, card, 'down'));

        actions.appendChild(upButton);
        actions.appendChild(downButton);

        const heading = card.querySelector('h2, h3');
        if (heading) {
            heading.insertAdjacentElement('afterend', actions);
            return;
        }

        card.prepend(actions);
    };

    const initReorderActions = (page, cards) => {
        cards.forEach((card) => {
            addSectionActions(page, card);
        });

        refreshActionStates(page);
    };

    const boot = () => {
        if (!window.location.pathname.includes('/admin/site-contents/')) {
            return;
        }

        addStyles();
        hideOrderSection();
        const page = getActivePage();
        const cards = collectCards(page);
        if (cards.length) {
            applyStoredOrderToCards(page, cards);
            initReorderActions(page, cards);
        }
    };

    const bootWithRetry = () => {
        boot();
        setTimeout(boot, 250);
        setTimeout(boot, 600);
    };

    let bootTimeout = null;
    const scheduleBoot = () => {
        if (bootTimeout) {
            clearTimeout(bootTimeout);
        }

        bootTimeout = setTimeout(() => {
            bootWithRetry();
            bootTimeout = null;
        }, 100);
    };

    const observer = new MutationObserver(() => {
        if (isInternalDomUpdate) {
            return;
        }
        scheduleBoot();
    });

    document.addEventListener('DOMContentLoaded', bootWithRetry);
    document.addEventListener('livewire:navigated', bootWithRetry);
    document.addEventListener('livewire:initialized', bootWithRetry);
    window.addEventListener('load', () => {
        observer.observe(document.body, { childList: true, subtree: true });
        scheduleBoot();
    });
})();
