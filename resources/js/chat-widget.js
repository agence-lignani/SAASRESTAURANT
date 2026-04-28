/**
 * F20 — widget chat public (accessibilité de base, §5.8).
 */
export function initBistroChatWidget() {
    const root = document.getElementById('bistro-chat-root');
    if (!root?.dataset.endpoint) {
        return;
    }

    const endpoint = root.dataset.endpoint;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    if (!csrf) {
        return;
    }
    const position = root.dataset.position === 'bottom-start' ? 'bottom-start' : 'bottom-end';
    const restaurantId = root.dataset.restaurantId ?? '0';
    const storageKey = `bistro_chat_token_${restaurantId}_${window.location.host}`;

    const posClass =
        position === 'bottom-start'
            ? 'left-4 sm:left-6 bottom-24'
            : 'right-4 sm:right-6 bottom-24';
    const alignCls = position === 'bottom-start' ? 'items-start' : 'items-end';

    root.innerHTML = '';
    root.className = `fixed z-[90] ${posClass} flex flex-col ${alignCls} gap-2 pointer-events-none`;

    const panel = document.createElement('section');
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-modal', 'true');
    panel.setAttribute('aria-labelledby', 'bistro-chat-title');
    panel.className =
        'pointer-events-auto flex max-h-[min(70vh,520px)] w-[min(100vw-2rem,380px)] flex-col overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-lg opacity-0 translate-y-2 transition-all duration-200 hidden';
    panel.id = 'bistro-chat-panel';

    panel.innerHTML = `
        <header class="flex items-center justify-between border-b border-stone-100 bg-stone-50/95 px-4 py-3">
            <h2 id="bistro-chat-title" class="text-sm font-semibold text-stone-900">Assistant menu</h2>
            <button type="button" class="rounded-lg p-1.5 text-stone-500 hover:bg-stone-200/80 hover:text-stone-800" aria-label="Fermer le chat">
                <span aria-hidden="true">✕</span>
            </button>
        </header>
        <p class="border-b border-amber-100/80 bg-amber-50/90 px-3 py-2 text-xs text-amber-950/90">
            Informations indicatives — pour les allergènes ou urgences, parlez au restaurant.
        </p>
        <div id="bistro-chat-log" class="min-h-0 flex-1 space-y-3 overflow-y-auto px-3 py-3 text-sm text-stone-800" tabindex="-1"></div>
        <form id="bistro-chat-form" class="border-t border-stone-100 p-3">
            <label for="bistro-chat-input" class="sr-only">Votre question</label>
            <div class="flex gap-2">
                <textarea id="bistro-chat-input" rows="2" maxlength="4000" placeholder="Posez une question sur la carte…" class="min-w-0 flex-1 resize-none rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm text-stone-900 shadow-inner outline-none focus:border-[var(--bistro-color-primary)] focus:ring-1 focus:ring-[var(--bistro-color-primary)]"></textarea>
                <button type="submit" class="bistro-btn-primary h-auto min-w-0 shrink-0 self-end rounded-xl px-4 py-2 text-sm">Envoyer</button>
            </div>
            <p id="bistro-chat-error" class="mt-2 hidden text-xs font-medium text-red-700"></p>
        </form>
    `;

    const toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.className =
        'pointer-events-auto flex h-14 w-14 items-center justify-center rounded-full bg-[var(--bistro-color-primary)] text-white shadow-md ring-2 ring-white/70 transition hover:opacity-95 focus:outline-none focus-visible:ring-4 focus-visible:ring-[var(--bistro-color-primary)]';
    toggleBtn.setAttribute('aria-expanded', 'false');
    toggleBtn.setAttribute('aria-controls', 'bistro-chat-panel');
    toggleBtn.setAttribute(
        'aria-label',
        'Ouvrir l’assistant menu',
    );
    toggleBtn.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7" aria-hidden="true"><path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zm-1.125 8.625H6a6 6 0 0112 0h2.625a2.625 2.625 0 012.625 2.625V21a1.5 1.5 0 01-1.5 1.5H3A1.5 1.5 0 011.5 21v-3.75a2.625 2.625 0 012.625-2.625z"/></svg>';

    root.appendChild(panel);
    root.appendChild(toggleBtn);

    const logEl = panel.querySelector('#bistro-chat-log');
    const formEl = panel.querySelector('#bistro-chat-form');
    const inputEl = panel.querySelector('#bistro-chat-input');
    const errEl = panel.querySelector('#bistro-chat-error');
    const closeBtn = panel.querySelector('header button');

    let open = false;
    let sessionToken = localStorage.getItem(storageKey) ?? '';

    function setOpen(v) {
        open = v;
        toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            panel.classList.remove('hidden', 'opacity-0', 'translate-y-2');
            requestAnimationFrame(() => inputEl?.focus());
        } else {
            panel.classList.add('hidden', 'opacity-0', 'translate-y-2');
        }
    }

    function showErr(msg) {
        if (!errEl) {
            return;
        }
        errEl.textContent = msg;
        errEl.classList.remove('hidden');
    }

    function clearErr() {
        errEl?.classList.add('hidden');
    }

    function appendBubble(role, text) {
        const wrap = document.createElement('div');
        wrap.className =
            role === 'user' ? 'rounded-xl bg-stone-100 px-3 py-2 text-stone-900' : 'rounded-xl bg-white px-3 py-2 text-stone-800 ring-1 ring-stone-100';
        const who = document.createElement('p');
        who.className = 'mb-1 text-xs font-semibold text-stone-500';
        who.textContent = role === 'user' ? 'Vous' : 'Assistant';
        const body = document.createElement('p');
        body.className = 'whitespace-pre-wrap';
        body.textContent = text;
        wrap.appendChild(who);
        wrap.appendChild(body);
        logEl?.appendChild(wrap);
        logEl?.scrollTo(0, logEl.scrollHeight);
    }

    toggleBtn.addEventListener('click', () => setOpen(!open));
    closeBtn?.addEventListener('click', () => setOpen(false));

    formEl?.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErr();
        const text = (inputEl?.value ?? '').trim();
        if (!text) {
            return;
        }
        appendBubble('user', text);
        inputEl.value = '';
        inputEl.disabled = true;
        const submitBtn = formEl.querySelector('button[type="submit"]');
        if (submitBtn instanceof HTMLButtonElement) {
            submitBtn.disabled = true;
        }

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    message: text,
                    session_token: sessionToken || null,
                }),
            });

            if (!res.ok) {
                let serverMessage = null;
                try {
                    const errJson = await res.json();
                    serverMessage = errJson?.message ?? null;
                } catch {
                    // no-op
                }

                const msg =
                    serverMessage ??
                    (res.status === 429
                        ? 'Trop de requêtes. Patientez un instant.'
                        : res.status === 503
                          ? 'Service temporairement indisponible.'
                          : 'Impossible d’envoyer le message.');
                showErr(typeof msg === 'string' ? msg : 'Erreur.');
                return;
            }

            const data = await res.json();
            sessionToken = data.session_token ?? sessionToken;
            if (sessionToken) {
                localStorage.setItem(storageKey, sessionToken);
            }
            appendBubble('assistant', data.reply ?? '');
        } catch (err) {
            showErr('Impossible d’envoyer le message.');
        } finally {
            inputEl.disabled = false;
            if (submitBtn instanceof HTMLButtonElement) {
                submitBtn.disabled = false;
            }
            inputEl?.focus();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && open) {
            setOpen(false);
        }
    });
}
