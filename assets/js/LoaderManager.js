class LoaderManager {
    constructor(message = "Chargement...") {
        this.message = message;
        this.loaderElement = null;
    }

    show() {
        if (this.loaderElement) return;

        this.loaderElement = document.createElement("div");
        this.loaderElement.className = `
            fixed inset-0 z-50 flex items-center justify-center bg-velocite/30 backdrop-blur
        `;

        this.loaderElement.innerHTML = `
            <div class="flex flex-col items-center space-y-4 text-gray-700">
                <div class="w-8 h-8 border-4 border-gray-300 border-t-gray-700 rounded-full animate-spin"></div>
                <p class="text-lg font-medium">${this.message}</p>
            </div>
        `;

        document.body.appendChild(this.loaderElement);
        document.body.classList.add("overflow-hidden");
    }

    hide() {
        if (this.loaderElement) {
            this.loaderElement.remove();
            this.loaderElement = null;
            document.body.classList.remove("overflow-hidden");
        }
    }
}

export default LoaderManager;