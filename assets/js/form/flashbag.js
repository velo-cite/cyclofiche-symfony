let instance;

class FlashBag {
    constructor(containerId = 'flashContainer') {
        if (instance) {
            throw new Error("You can only create one instance!");
        }
        instance = this;

        this.container = document.getElementById(containerId);
        this.messages = [];
        this.config = {
            defaultDuration: 5000,
            animationDuration: 300,
            icons: {
                success: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>`,
                error: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>`,
                warning: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>`,
                info: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>`
            },
            classes: {
                success: 'bg-green-50 border-green-200 text-green-800',
                error: 'bg-red-50 border-red-200 text-red-800',
                warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
                info: 'bg-blue-50 border-blue-200 text-blue-800'
            }
        };

        if (!this.container) {
            console.error(`FlashBag: Container with id '${containerId}' not found`);
        }
    }

    getInstance() {
        return this;
    }

    success(message, duration = this.config.defaultDuration) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = this.config.defaultDuration) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = this.config.defaultDuration) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = this.config.defaultDuration) {
        return this.show(message, 'info', duration);
    }

    custom(message, type = 'info', duration = this.config.defaultDuration) {
        return this.show(message, type, duration);
    }

    show(message, type = 'info', duration = this.config.defaultDuration) {
        const flashMessage = {
            id: this.generateId(),
            message: message,
            type: type,
            duration: duration,
            element: null,
            timeout: null,
            progressInterval: null
        };

        this.messages.push(flashMessage);
        this.render(flashMessage);

        return flashMessage.id;
    }

    render(flashMessage) {
        const element = document.createElement('div');
        const typeClasses = this.config.classes[flashMessage.type] || this.config.classes.info;

        element.className = `relative border-l-4 p-4 rounded-b-lg shadow-lg backdrop-blur-sm opacity-0 transform -translate-y-full transition-all duration-300 ease-out ${typeClasses}`;
        element.setAttribute('data-id', flashMessage.id);

        const icon = this.config.icons[flashMessage.type] || this.config.icons.info;

        element.innerHTML = `
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            ${icon}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium leading-5">
                                ${flashMessage.message}
                            </p>
                        </div>
                        <div class="flex-shrink-0 ml-3">
                            <button type="button" class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-150">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    ${flashMessage.duration > 0 ? `<div class="absolute bottom-0 left-0 h-1 bg-current opacity-30 rounded-b transition-all duration-100 ease-linear" style="width: 100%"></div>` : ''}
                `;

        flashMessage.element = element;

        // Ajout des événements
        const closeButton = element.querySelector('button');
        closeButton.addEventListener('click', () => {
            this.remove(flashMessage.id);
        });

        // Ajout au DOM
        this.container.appendChild(element);

        // Animation d'apparition
        requestAnimationFrame(() => {
            element.classList.remove('opacity-0', '-translate-y-full');
            element.classList.add('opacity-100', 'translate-y-0');
        });

        // Barre de progression
        this.startProgress(flashMessage);

        // Auto-suppression
        if (flashMessage.duration > 0) {
            flashMessage.timeout = setTimeout(() => {
                this.remove(flashMessage.id);
            }, flashMessage.duration);
        }
    }

    startProgress(flashMessage) {
        if (flashMessage.duration <= 0) return;

        const progressBar = flashMessage.element.querySelector('.absolute.bottom-0');
        if (!progressBar) return;

        const startTime = Date.now();

        flashMessage.progressInterval = setInterval(() => {
            const elapsed = Date.now() - startTime;
            const remaining = Math.max(0, flashMessage.duration - elapsed);
            const progress = (remaining / flashMessage.duration) * 100;

            progressBar.style.width = `${progress}%`;

            if (remaining <= 0) {
                clearInterval(flashMessage.progressInterval);
            }
        }, 50);
    }

    remove(id) {
        const messageIndex = this.messages.findIndex(msg => msg.id === id);
        if (messageIndex === -1) return;

        const flashMessage = this.messages[messageIndex];

        // Nettoyage des timers
        if (flashMessage.timeout) {
            clearTimeout(flashMessage.timeout);
        }
        if (flashMessage.progressInterval) {
            clearInterval(flashMessage.progressInterval);
        }

        // Animation de disparition
        if (flashMessage.element) {
            flashMessage.element.classList.add('opacity-0', '-translate-y-full');

            setTimeout(() => {
                if (flashMessage.element.parentNode) {
                    flashMessage.element.parentNode.removeChild(flashMessage.element);
                }
            }, this.config.animationDuration);
        }

        // Suppression du tableau
        this.messages.splice(messageIndex, 1);
    }

    removeAll() {
        this.messages.forEach(message => {
            this.remove(message.id);
        });
    }

    generateId() {
        return `flash_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    }

    // Méthodes utilitaires
    getMessages() {
        return this.messages;
    }

    getMessageById(id) {
        return this.messages.find(msg => msg.id === id);
    }

    updateConfig(newConfig) {
        this.config = { ...this.config, ...newConfig };
    }
}

const singletonFlashBag = Object.freeze(new FlashBag());
export default singletonFlashBag;