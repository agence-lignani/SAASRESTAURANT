<div
    class="pronote-loading-bar"
    aria-hidden="true"
    data-pronote-loading-bar
>
    <div class="pronote-loading-bar__track">
        <div class="pronote-loading-bar__fill"></div>
        <div class="pronote-loading-bar__gloss"></div>
    </div>
</div>

<script>
    (() => {
        if (window.__pronoteBackofficeLoaderInitialized) {
            return;
        }

        window.__pronoteBackofficeLoaderInitialized = true;

        const root = document.documentElement;
        const delay = 120;
        let timeout = null;
        let fallbackTimeout = null;
        let activeRequests = 0;

        const show = () => {
            clearTimeout(timeout);

            timeout = setTimeout(() => {
                root.classList.add('pronote-loading-bar-visible');
                root.setAttribute('aria-busy', 'true');
            }, delay);
        };

        const hide = () => {
            clearTimeout(timeout);
            clearTimeout(fallbackTimeout);

            if (activeRequests > 0) {
                return;
            }

            root.classList.remove('pronote-loading-bar-visible');
            root.removeAttribute('aria-busy');
        };

        const begin = () => {
            activeRequests += 1;
            show();
            clearTimeout(fallbackTimeout);
            fallbackTimeout = setTimeout(() => {
                activeRequests = 0;
                hide();
            }, 8000);
        };

        const finish = () => {
            activeRequests = Math.max(0, activeRequests - 1);
            window.setTimeout(hide, 140);
        };

        document.addEventListener('livewire:navigate', begin);
        document.addEventListener('livewire:navigated', () => {
            activeRequests = 0;
            hide();
        });

        document.addEventListener('submit', (event) => {
            if (! event.defaultPrevented && event.target.closest('form')) {
                begin();
            }
        }, true);

        document.addEventListener('click', (event) => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            const anchor = event.target.closest('a[href]');

            if (! anchor || anchor.target || anchor.hasAttribute('download')) {
                return;
            }

            const url = new URL(anchor.href, window.location.href);

            if (
                url.origin !== window.location.origin
                || ! url.pathname.startsWith('/admin')
                || (url.pathname === window.location.pathname && url.search === window.location.search)
            ) {
                return;
            }

            begin();
        }, true);

        document.addEventListener('livewire:init', () => {
            if (! window.Livewire?.hook) {
                return;
            }

            window.Livewire.hook('request', ({ respond, succeed, fail }) => {
                begin();

                respond?.(finish);
                succeed?.(finish);
                fail?.(finish);
            });
        });

        window.addEventListener('pageshow', () => {
            activeRequests = 0;
            hide();
        });
    })();
</script>
