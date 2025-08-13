import StepLoginOrSignIn from "../stepMyAccount/StepLoginOrSignIn.js";
import {FlashBag} from "../form/flashbag.js";
import LoaderManager from "../LoaderManager.js";
import StepResetPassword from "../stepMyAccount/StepResetPassword.js";

class MenuMyAccount {
    constructor(api, comeBackToPrincipalMenuCallback) {
        this.api = api;
        this.comeBackToPrincipalMenuCallback = comeBackToPrincipalMenuCallback;

        // Conteneur des étapes
        this.stepsContainer = document.createElement('div');
        this.stepsContainer.classList.add("steps-container");

        this.element = document.createElement("div");
        this.element.className = "fixed z-10 bottom-0 w-full md:max-w-lg -translate-x-1/2 left-1/2 bg-sky-600 rounded-t-3xl px-4 sm:px-6 md:px-8 py-4 shadow-lg text-white opacity-95";

        this.menu = document.createElement("div");
        this.menu.id = "menu";
        this.menu.className = "space-y-3";
        this.menu.innerHTML = `
            <button class="w-full font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Modifier mes infos personnelles</button>
            <button class="w-full font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Changer de mot de passe</button>
            <button id="btn-connexion" class="w-full font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Connexion / Inscription</button>
            <button id="btn-deconnexion" class="w-full font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Déconnexion</button>
        `;
        this.element.appendChild(this.menu)

        this.btnConnexion = this.element.querySelector("#btn-connexion");
        this.btnDeconnexion = this.element.querySelector("#btn-deconnexion");
        this.stepResetPassword = new StepResetPassword(this.api, () => {});
        this.element.appendChild(this.stepResetPassword.getDOM());
        this.stepLoginOrSignIn = new StepLoginOrSignIn(this.api, this.stepResetPassword, () => {
            this.comeBackToPrincipalMenuCallback();
            this.reset();
        });

        this.initOrRefreshConnection();
        this.flashbag = new FlashBag();
        this.loader = new LoaderManager();

        this.btnDeconnexion.addEventListener('click', () => {
            this.api.logout();
            this.flashbag.success('Déconnexion réussie');
            this.comeBackToPrincipalMenuCallback();
            this.reset();
        });
        this.btnConnexion.addEventListener('click', () => {
            this.hideMenu();
            this.stepLoginOrSignIn.show();
        });
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

    showMenu() {
        this.menu.classList.remove("hidden");
    }

    hideMenu() {
        this.menu.classList.add("hidden");
    }

    reset() {
        this.showMenu();
        this.initOrRefreshConnection();
    }
}

export default MenuMyAccount;
