import StepLoginOrSignIn from "../stepMyAccount/StepLoginOrSignIn.js";
import {FlashBag} from "../form/flashbag.js";
import LoaderManager from "../LoaderManager.js";

class MenuMyAccount {
    constructor(api, comeBackToPrincipalMenuCallback) {
        this.api = api;
        this.comeBackToPrincipalMenuCallback = comeBackToPrincipalMenuCallback;

        // Conteneur des étapes
        this.stepsContainer = document.createElement('div');
        this.stepsContainer.classList.add("steps-container");

        this.element = document.createElement("div");
        this.element.id = "menu";
        this.element.className = "fixed z-10 bottom-0 w-full md:max-w-lg -translate-x-1/2 left-1/2 bg-sky-600 rounded-t-3xl px-4 sm:px-6 md:px-8 py-4 shadow-lg text-white space-y-3 opacity-95";

        this.element.innerHTML = `
            <button class="w-full font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Modifier mes infos personnelles</button>
            <button class="w-full font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Changer de mot de passe</button>
            <button id="btn-connexion" class="w-full font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Connexion / Inscription</button>
            <button id="btn-deconnexion" class="w-full font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Déconnexion</button>
        `;

        this.btnConnexion = this.element.querySelector("#btn-connexion");
        this.btnDeconnexion = this.element.querySelector("#btn-deconnexion");
        this.stepLoginOrSignIn = new StepLoginOrSignIn(this.api, () => this.comeBackToPrincipalMenuCallback());
        this.initOrRefreshConnection();
        this.flashbag = new FlashBag();
        this.loader = new LoaderManager();

        this.btnDeconnexion.addEventListener('click', () => {
            this.api.logout();
            this.flashbag.success('Déconnexion réussie');
            this.comeBackToPrincipalMenuCallback();
        });
        this.btnConnexion.addEventListener('click', () => this.stepLoginOrSignIn.show());
    }

    initOrRefreshConnection() {
        this.stepLoginOrSignIn.hide();
        if (this.api.isLogged()) {
            this.btnConnexion.classList.add("hidden");
            this.btnDeconnexion.classList.remove("hidden");
        } else {
            this.btnDeconnexion.classList.add("hidden");
            this.btnConnexion.classList.remove("hidden");
            this.stepsContainer.appendChild(this.stepLoginOrSignIn.getDOM());
            this.element.appendChild(this.stepsContainer);
        }
    }

    show() {
        this.initOrRefreshConnection();
        this.element.classList.remove("hidden");
    }

    hide() {
        this.element.classList.add("hidden");
    }

    getDOM() {
        return this.element;
    }
}

export default MenuMyAccount;
