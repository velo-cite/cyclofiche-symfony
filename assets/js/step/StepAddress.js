import LoaderManager from "../LoaderManager.js";

class StepAddress {
    constructor() {
        this.element = document.createElement("div");
        this.element.classList.add("step");

        this.element.innerHTML = `
      <h2 class="text-xl font-semibold mb-4">Lieu du signalement</h2>

      <label class="flex items-center mb-4">
        <input type="checkbox" id="useGps" class="sr-only peer">
        Utiliser ma position GPS actuelle
        <div class="ml-2 relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
      </label>

      <button type="button" id="manualBtn" class="btn bg-gray-500 hover:bg-gray-600 mb-4">Saisir l'adresse manuellement</button>

      <div id="manualAddress" class="hidden space-y-2">
        <input name="streetNumber" placeholder="Numéro" class="input">
        <input name="streetName" placeholder="Rue" class="input">
        <input name="city" placeholder="Ville" class="input">
      </div>

      <input type="hidden" name="latitude" id="latitude">
      <input type="hidden" name="longitude" id="longitude">
    `;

        this.useGps = this.element.querySelector("#useGps");
        this.manualBtn = this.element.querySelector("#manualBtn");
        this.manualAddress = this.element.querySelector("#manualAddress");

        this.loader = new LoaderManager("Récupération de votre position GPS...");

        this._bindEvents();
    }

    _bindEvents() {
        this.useGps.addEventListener("change", () => {
            if (this.useGps.checked) {
                this.manualAddress.classList.add("hidden");
                this._trySetGps();
            }
        });

        this.manualBtn.addEventListener("click", () => {
            this.manualAddress.classList.toggle("hidden");
            this.useGps.checked = false;
        });
    }

    _trySetGps() {
        if (!navigator.geolocation) {
            return;
        }
        this.loader.show();

        navigator.geolocation.getCurrentPosition((position) => {
            this.loader.hide();
            this.element.querySelector("input[name='latitude']").value = position.coords.latitude;
            this.element.querySelector("input[name='longitude']").value = position.coords.longitude;

            const event = new CustomEvent("addMarker", {
                detail: {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    form: this.element.closest("form"),
                }
            });
            window.dispatchEvent(event); // ou document.dispatchEvent(event)
        }, (error) => {
            this.loader.hide();
            console.error('Erreur GPS :', error);
        });
    }

    validate() {
        if (this.useGps.checked) {
            const lat = this.element.querySelector("input[name='latitude']").value;
            const lng = this.element.querySelector("input[name='longitude']").value;
            return lat && lng;
        }

        const fields = ["streetNumber", "streetName", "city"];
        return fields.every(name =>
            this.element.querySelector(`[name='${name}']`).value.trim() !== ""
        );
    }

    reset() {
        this.element.querySelectorAll("input").forEach(input => {
            if (input.type === "checkbox") input.checked = false;
            else input.value = "";
        });
        this.manualAddress.classList.add("hidden");
    }

    show() {
        this.element.classList.remove("hidden");
    }

    hide() {
        this.element.classList.add("hidden");
    }

    getDOM() {
        return this.element;
    }
}

export default StepAddress;
