<style>
    @keyframes pending-bell-pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgb(239 68 68 / 0.45); }
        50% { transform: scale(1.06); box-shadow: 0 0 0 7px rgb(239 68 68 / 0.10); }
    }

    .fi-topbar-database-notifications-btn.pending-alert,
    .fi-sidebar-database-notifications-btn.pending-alert {
        background: #dc2626 !important;
        color: #fff !important;
        animation: pending-bell-pulse 1.2s ease-in-out infinite;
        border-radius: 0.75rem;
    }
</style>

<script>
    (() => {
        let audioContext = null;
        let audioUnlocked = false;
        let shouldPlayOnArrival = false;
        const playedKey = 'backoffice_pending_sound_played_once';

        const ensureAudioContext = async () => {
            if (!audioContext) {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }

            if (audioContext.state === 'suspended') {
                await audioContext.resume();
            }
        };

        const unlockAudio = async () => {
            try {
                await ensureAudioContext();
                audioUnlocked = true;
                if (shouldPlayOnArrival && sessionStorage.getItem(playedKey) !== '1') {
                    playNotificationSound();
                    sessionStorage.setItem(playedKey, '1');
                    shouldPlayOnArrival = false;
                }
            } catch (error) {
                // Ignore unlock errors.
            }
        };

        window.addEventListener('click', unlockAudio, { once: true });
        window.addEventListener('pointerdown', unlockAudio, { once: true });
        window.addEventListener('keydown', unlockAudio, { once: true });

        const getBadgeCount = () => {
            const topbarBadge = document.querySelector('.fi-topbar-database-notifications-btn .fi-badge');
            const sidebarBadge = document.querySelector('.fi-sidebar-database-notifications-btn .fi-badge');
            const text = (topbarBadge?.textContent || sidebarBadge?.textContent || '').trim();
            const count = Number.parseInt(text, 10);

            return Number.isFinite(count) ? count : 0;
        };

        const bellButtons = () => [
            ...document.querySelectorAll('.fi-topbar-database-notifications-btn, .fi-sidebar-database-notifications-btn'),
        ];

        const setBellState = (hasPending) => {
            bellButtons().forEach((button) => {
                button.classList.toggle('pending-alert', hasPending);
            });
        };

        const playNotificationSound = async () => {
            try {
                await ensureAudioContext();
                if (!audioContext || (!audioUnlocked && audioContext.state !== 'running')) {
                    return;
                }

                const start = audioContext.currentTime;
                const pulse = (frequency, from, to) => {
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(frequency, start + from);
                    gain.gain.setValueAtTime(0.0001, start + from);
                    gain.gain.exponentialRampToValueAtTime(0.12, start + from + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, start + to);
                    osc.connect(gain);
                    gain.connect(audioContext.destination);
                    osc.start(start + from);
                    osc.stop(start + to);
                };

                pulse(880, 0, 0.22);
                pulse(1175, 0.24, 0.48);
            } catch (error) {
                // Ignore audio errors silently (browser policies, etc.).
            }
        };

        let lastCount = 0;
        const refresh = () => {
            const count = getBadgeCount();
            setBellState(count > 0);
            if (count > 0 && lastCount === 0 && sessionStorage.getItem(playedKey) !== '1') {
                shouldPlayOnArrival = true;
                if (audioUnlocked) {
                    playNotificationSound();
                    sessionStorage.setItem(playedKey, '1');
                    shouldPlayOnArrival = false;
                }
            }

            lastCount = count;
        };

        const observer = new MutationObserver(() => refresh());
        observer.observe(document.body, { childList: true, subtree: true, characterData: true });

        window.addEventListener('load', () => {
            refresh();
            setInterval(refresh, 3000);
        });
    })();
</script>
