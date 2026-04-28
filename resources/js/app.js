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
});
