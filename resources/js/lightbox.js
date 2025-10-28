// Lightbox simple pour afficher les images en plein écran avec navigation
class SimpleLightbox {
    constructor() {
        this.currentIndex = 0;
        this.images = [];
        this.isOpen = false;
        this.createLightbox();
        this.attachEventListeners();
    }

    createLightbox() {
        const lightboxHTML = `
            <div id="lightbox-overlay" class="lightbox-overlay" style="display: none;">
                <button id="lightbox-close" class="lightbox-close" aria-label="Fermer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>

                <button id="lightbox-prev" class="lightbox-nav lightbox-prev" aria-label="Précédent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <button id="lightbox-next" class="lightbox-nav lightbox-next" aria-label="Suivant">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>

                <div id="lightbox-content" class="lightbox-content">
                    <img id="lightbox-image" src="" alt="" class="lightbox-image" />
                    <video id="lightbox-video" controls class="lightbox-video" style="display: none;"></video>
                </div>

                <div id="lightbox-counter" class="lightbox-counter"></div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', lightboxHTML);
    }

    attachEventListeners() {
        // Fermeture
        document.getElementById('lightbox-close').addEventListener('click', () => this.close());
        document.getElementById('lightbox-overlay').addEventListener('click', (e) => {
            if (e.target.id === 'lightbox-overlay') {
                this.close();
            }
        });

        // Navigation
        document.getElementById('lightbox-prev').addEventListener('click', () => this.prev());
        document.getElementById('lightbox-next').addEventListener('click', () => this.next());

        // Clavier
        document.addEventListener('keydown', (e) => {
            if (!this.isOpen) return;

            if (e.key === 'Escape') this.close();
            if (e.key === 'ArrowLeft') this.prev();
            if (e.key === 'ArrowRight') this.next();
        });
    }

    init() {
        // Récupérer toutes les images cliquables
        const mediaElements = document.querySelectorAll('[data-lightbox]');
        this.images = Array.from(mediaElements).map(el => ({
            src: el.dataset.lightbox,
            type: el.dataset.lightboxType || 'image',
            alt: el.alt || ''
        }));

        // Ajouter les événements de clic
        mediaElements.forEach((el, index) => {
            el.style.cursor = 'pointer';
            el.addEventListener('click', (e) => {
                e.preventDefault();
                this.open(index);
            });
        });
    }

    open(index) {
        this.currentIndex = index;
        this.isOpen = true;
        document.getElementById('lightbox-overlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        this.updateContent();
    }

    close() {
        this.isOpen = false;
        document.getElementById('lightbox-overlay').style.display = 'none';
        document.body.style.overflow = '';

        // Arrêter la vidéo si c'est une vidéo
        const video = document.getElementById('lightbox-video');
        video.pause();
        video.currentTime = 0;
    }

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.updateContent();
    }

    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.updateContent();
    }

    updateContent() {
        const current = this.images[this.currentIndex];
        const img = document.getElementById('lightbox-image');
        const video = document.getElementById('lightbox-video');
        const counter = document.getElementById('lightbox-counter');
        const prevBtn = document.getElementById('lightbox-prev');
        const nextBtn = document.getElementById('lightbox-next');

        // Mettre à jour le compteur
        counter.textContent = `${this.currentIndex + 1} / ${this.images.length}`;

        // Afficher/masquer les boutons de navigation
        if (this.images.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }

        // Afficher le contenu approprié
        if (current.type === 'video') {
            img.style.display = 'none';
            video.style.display = 'block';
            video.src = current.src;
        } else {
            video.style.display = 'none';
            img.style.display = 'block';
            img.src = current.src;
            img.alt = current.alt;
        }
    }
}

// Initialiser la lightbox au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    const lightbox = new SimpleLightbox();
    lightbox.init();
});
