import './bootstrap';
import { initBistroGalleryLightbox } from './gallery-lightbox';
import { initBistroChatWidget } from './chat-widget';

document.addEventListener('DOMContentLoaded', () => {
    initBistroGalleryLightbox();
    initBistroChatWidget();
});
