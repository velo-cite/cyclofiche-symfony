import {FlashBag} from "../form/flashbag.js";
import LoaderManager from "../LoaderManager.js";

class StepLogin {
    /**
     * @param {ApiClient} api
     */
    constructor(api, callbackOnSuccess) {
        this.api = api;
        this.callbackOnSuccess = callbackOnSuccess;
        this.element = document.createElement("div");
        this.element.classList.add("step");

        this.element.innerHTML = `
      <h2 class="text-xl font-semibold mb-4">Connexion</h2>
      <p class="mb-4">Souhaitez-vous vous connecter ou saisir vos informations personnelles ?</p>

      <div id="loginBloc" class="mb-4">
        <button type="button" id="loginBtn" class="btn w-full bg-green-600 hover:bg-green-700">Se connecter</button>

        <div id="loginForm" class="mt-6 hidden space-y-3">
          <input name="loginEmail" type="email" placeholder="Email" class="input" />
          <input name="loginPassword" type="password" placeholder="Password" class="input" />
          <button type="button" id="validateLogin" class="btn w-full bg-blue-500 hover:bg-blue-600">Valider</button>
        </div>
      </div>

      <hr class="mb-4">

      <div class="space-y-4">
        <button type="button" id="manualEntryBtn" class="btn w-full">Saisir mes informations personnelles</button>
      </div>

      <div id="manualInfoForm" class="mt-6 hidden space-y-3">
        <input name="email" type="email" placeholder="Email" class="input" />
        <input name="phone" type="tel" placeholder="Téléphone" class="input" />
        <input name="firstname" type="text" placeholder="Prénom" class="input" />
        <input name="lastname" type="text" placeholder="Nom" class="input" />
        <button type="button" id="validateManualInfoForm" class="btn w-full bg-blue-500 hover:bg-blue-600">Valider</button>
      </div>
    `;

        this.loginBtn = this.element.querySelector("#loginBtn");
        this.manualEntryBtn = this.element.querySelector("#manualEntryBtn");
        this.loginForm = this.element.querySelector("#loginForm");
        this.manualForm = this.element.querySelector("#manualInfoForm");
        this.validateLogin = this.element.querySelector("#validateLogin");

        this._bindEvents();
        this.flashbag = new FlashBag();
        this.loader = new LoaderManager("Connexion en cours...");
    }

    _bindEvents() {
        const that = this;
        this.validateLogin?.addEventListener("click", async () => {
            const email = this.loginForm.querySelector("[name='loginEmail']").value.trim();
            const password = this.loginForm.querySelector("[name='loginPassword']").value.trim();

            that.loader.show();
            this.api.login(email, password)
                .then(function () {
                    that.loader.hide();
                    that.flashbag.success('Connexion réussie');
                    that.callbackOnSuccess();
                })
                .catch(function (reason) {
                    that.loader.hide();
                    that.flashbag.error(reason);
                })
            ;
        });

        this.loginBtn?.addEventListener("click", () => {
            this.loginBtn.classList.add("hidden");
            this.loginForm.classList.remove("hidden");
            this.manualEntryBtn.classList.remove("hidden");
            this.manualForm.classList.add("hidden");
        });

        this.manualEntryBtn?.addEventListener("click", () => {
            this.loginBtn.classList.remove("hidden");
            this.loginForm.classList.add("hidden");
            this.manualEntryBtn.classList.add("hidden");
            this.manualForm.classList.remove("hidden");
        });
    }

    shouldBeSkip() {
        return this.api.isLogged();
    }

    validate() {
        if (!this.loginForm.classList.contains("hidden")) {
            const email = this.loginForm.querySelector("[name='loginEmail']").value.trim();
            const password = this.loginForm.querySelector("[name='loginPassword']").value.trim();
            return email !== "" && password !== "";
        }

        if (!this.manualForm.classList.contains("hidden")) {
            const requiredFields = ["email", "phone", "firstname", "lastname"];
            return requiredFields.every(name =>
                this.manualForm.querySelector(`[name='${name}']`).value.trim() !== ""
            );
        }

        return false;
    }

    reset() {
        this.element.querySelectorAll("input").forEach(input => {
            if (input.type === "checkbox") input.checked = false;
            else input.value = "";
        });
        this.loginForm.classList.add("hidden");
        this.manualForm.classList.add("hidden");
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

export default StepLogin;
