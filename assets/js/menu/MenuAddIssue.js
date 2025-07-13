import {FlashBag} from "../form/flashbag.js";
// Dans MenuAddIssue.js (extrait simplifié)
import Stepper from "../step/Stepper.js";
import StepLogin from "../step/StepLogin.js";
import StepAddress from "../step/StepAddress.js";
import StepIssueType from "../step/StepIssueType.js";
import StepDescription from "../step/StepDescription.js";
import StepPhoto from "../step/StepPhoto.js";

class MenuAddIssue {
    constructor(api) {
        this.api = api;
        // Construire le DOM de ton menu (ou récupérer avec querySelector)
        this.container = document.createElement("div");
        this.container.id = "menuIssue";
        this.container.className = "fixed z-1 bottom-0 w-full md:max-w-lg -translate-x-1/2 left-1/2 bg-redimportant text-white rounded-t-3xl p-6 rounded-white border hidden";

        this.form = document.createElement('form');
        this.form.id = "reportForm";
        this.form.className = "space-y-6"

        // Conteneur des étapes
        this.stepsContainer = document.createElement('div');
        this.stepsContainer.classList.add("steps-container");

        const steps = [
            new StepLogin(api),
            new StepAddress(),
            new StepIssueType(api),
            new StepDescription(),
            new StepPhoto(),
        ];

        // Boutons de navigation
        this.prevBtn = document.createElement('button');
        this.nextBtn = document.createElement('button');
        this.prevBtn.textContent = "< Précédent";
        this.prevBtn.classList.add('btn', 'btn-red');
        this.nextBtn.textContent = "Suivant >";
        this.nextBtn.classList.add('btn', 'btn-red');

        this.stepper = new Stepper(steps, this.stepsContainer, this.prevBtn, this.nextBtn, () => this.submit());

        this.form.appendChild(this.stepsContainer);
        this.container.appendChild(this.form);

        // <!-- Navigation -->
        this.divNavigation = document.createElement('div');
        this.divNavigation.className = "flex justify-between mt-6";
        this.divNavigation.appendChild(this.prevBtn);
        this.divNavigation.appendChild(this.nextBtn);
        this.container.appendChild(this.divNavigation);

        this.flashbag = new FlashBag();
    }

    async submit() {
        const data = {
            state: "submitted",
            category: this.form.categoryLabel.value,
            city: this.form.city.value,
            address: this.form.streetNumber.value + ' ' + this.form.streetName.value,
            location: `${this.form.latitude.value},${this.form.longitude.value}`,
            description: this.form.description.value,
            firstname: this.form.firstname.value,
            lastname: this.form.lastname.value,
            email: this.form.email.value,
            phone: this.form.phone.value,
            photos: [],
        };

        try {
            await this.api.submitIssue(data);
            this.stepper.reset();
            this.flashbag.success('Merci pour votre contribution !');
        } catch (e) {
            console.error(e);
        }
    }

    getDOM() {
        return this.container;
    }

    show() {
        this.container.classList.remove("hidden");
    }

    hide() {
        this.container.classList.add("hidden");
    }

    onComplete() {
        // Soumission ou message final
        this.stepper.reset();
        // this.hide();
    }
}

export default MenuAddIssue;
