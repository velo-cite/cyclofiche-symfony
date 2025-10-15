import singletonFlashBag from "../form/flashbag.js";
import LoaderManager from "../LoaderManager.js";

class StepResetPassword {
    /**
     * @param {ApiClient} api
     * @param {Function} callbackOnSuccess
     */
    constructor(api, callbackOnSuccess) {
        this.api = api;
        this.callbackOnSuccess = callbackOnSuccess;

        this.element = document.createElement("div");
        this.element.classList.add("step");

        this.element.innerHTML = `
            <h2 class="text-xl font-semibold mb-4">Réinitialisation du mot de passe</h2>

            <form id="resetRequestForm" class="space-y-3">
                <input name="email" type="email" placeholder="Votre email" class="input" />
                <button type="button" id="sendResetLink" class="btn w-full bg-blue-500 hover:bg-blue-600">Envoyer le lien</button>
            </form>

<!--            <div id="resetConfirmForm" class="space-y-3 hidden">-->
<!--                <input name="token" type="text" placeholder="Token reçu par email" class="input" />-->
<!--                <input name="password" type="password" placeholder="Nouveau mot de passe" class="input" />-->
<!--                <input name="passwordRepeated" type="password" placeholder="Répétez le mot de passe" class="input" />-->
<!--                <button type="button" id="confirmReset" class="btn w-full bg-green-600 hover:bg-green-700">Confirmer</button>-->
<!--            </div>-->
        `;

        this.resetRequestForm = this.element.querySelector("#resetRequestForm");
        // this.resetConfirmForm = this.element.querySelector("#resetConfirmForm");
        this.sendResetLinkBtn = this.element.querySelector("#sendResetLink");
        // this.confirmResetBtn = this.element.querySelector("#confirmReset");

        this.flashbag = singletonFlashBag.getInstance();
        this.loader = new LoaderManager("Traitement en cours...");

        this._bindEvents();
        this.hide();
    }

    _bindEvents() {
        this.sendResetLinkBtn.addEventListener("click", async () => {
            const email = this.resetRequestForm.querySelector("[name='email']").value.trim();
            if (!email) {
                this.flashbag.error("Veuillez saisir un email");
                return;
            }

            this.loader.show();
            try {
                const data = await this.api.requestPasswordReset(email);
                this.flashbag.success(data.message || "Si l'email existe, un lien a été envoyé");
                this.callbackOnSuccess();
                this.reset();
                // this.resetConfirmForm.classList.remove("hidden");
            } catch (err) {
                this.flashbag.error(err.message || "Erreur lors de l'envoi du lien");
            } finally {
                this.loader.hide();
            }
        });

        // this.confirmResetBtn.addEventListener("click", async () => {
        //     const token = this.resetConfirmForm.querySelector("[name='token']").value.trim();
        //     const password = this.resetConfirmForm.querySelector("[name='password']").value.trim();
        //     const passwordRepeated = this.resetConfirmForm.querySelector("[name='passwordRepeated']").value.trim();
        //
        //     if (!token || !password || !passwordRepeated) {
        //         this.flashbag.error("Tous les champs sont obligatoires");
        //         return;
        //     }
        //     if (password.length < 7) {
        //         this.flashbag.error("Mot de passe trop court");
        //         return;
        //     }
        //     if (password !== passwordRepeated) {
        //         this.flashbag.error("Les mots de passe ne correspondent pas");
        //         return;
        //     }
        //
        //     this.loader.show();
        //     try {
        //         const data = await this.api.confirmPasswordReset(token, password);
        //         this.flashbag.success(data.message || "Mot de passe modifié avec succès");
        //         this.callbackOnSuccess();
        //     } catch (err) {
        //         this.flashbag.error(err.message || "Erreur lors de la réinitialisation");
        //     } finally {
        //         this.loader.hide();
        //     }
        // });
    }

    getDOM() {
        return this.element;
    }

    show() {
        this.element.classList.remove("hidden");
    }

    hide() {
        this.element.classList.add("hidden");
    }

    reset() {
        this.resetRequestForm.reset();
    }
}

export default StepResetPassword;
