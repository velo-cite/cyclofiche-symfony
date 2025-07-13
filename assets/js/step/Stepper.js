import {FlashBag} from "../form/flashbag.js";
class Stepper {
    /**
     * @param {Array} steps - tableau d'instances des étapes (objets avec méthodes show, hide, validate, reset, getDOM)
     * @param {HTMLElement} container - élément DOM parent où injecter les étapes
     * @param {HTMLElement} prevBtn - bouton "Précédent"
     * @param {HTMLElement} nextBtn - bouton "Suivant"
     * @param {callback} onComplete - callback appelée lorsque le stepper arrive à la fin
     */
    constructor(steps, container, prevBtn, nextBtn, onComplete = () => {}) {
        this.steps = steps;
        this.container = container;
        this.prevBtn = prevBtn;
        this.nextBtn = nextBtn;
        this.currentStepIndex = 0;
        this.onComplete = onComplete;
        this.flashbag = new FlashBag();

        this._init();
    }

    _calculFirstStep(steps) {
        for (let i = 0; i < steps.length; i++){
            if (typeof steps[i].shouldBeSkip === "function") {
                if (!steps[i].shouldBeSkip()) {
                    this.currentStepIndex = i;
                    return;
                }
            } else {
                this.currentStepIndex = i;
                return;
            }
        }
    }

    _init() {
        // Injecter toutes les étapes dans le container, cachées sauf la première
        this.container.innerHTML = "";

        this._calculFirstStep(this.steps);

        this.steps.forEach((step, i) => {
            const dom = step.getDOM();
            if (i !== this.currentStepIndex) dom.classList.add("hidden");
            this.container.appendChild(dom);
        });

        this._updateButtons();

        this.prevBtn.addEventListener("click", () => this.prevStep());
        this.nextBtn.addEventListener("click", () => this.nextStep());
    }

    _updateButtons() {
        this.prevBtn.classList.toggle("hidden", this.currentStepIndex === 0);
        this.nextBtn.textContent = this.currentStepIndex === this.steps.length - 1 ? "Terminer" : "Suivant >";
    }

    nextStep() {
        const currentStep = this.steps[this.currentStepIndex];

        // Valider l'étape actuelle
        if (!currentStep.validate()) {
            this.flashbag.error("Merci de remplir correctement cette étape avant de continuer.");
            return;
        }

        currentStep.hide();

        if (this.currentStepIndex < this.steps.length - 1) {
            this.currentStepIndex++;
            if (typeof this.steps[this.currentStepIndex].shouldBeSkip === "function") {
                if (this.steps[this.currentStepIndex].shouldBeSkip()) {
                    this.nextStep();
                }
            }
            this.steps[this.currentStepIndex].show();
            this._updateButtons();
        } else {
            // Terminé
            this._onComplete();
        }
    }

    prevStep() {
        if (this.currentStepIndex === 0) return;

        this.steps[this.currentStepIndex].hide();
        this.currentStepIndex--;
        this.steps[this.currentStepIndex].show();
        this._updateButtons();
    }

    reset() {
        this.steps.forEach(step => {
            step.reset();
            step.hide();
        });
        this._calculFirstStep(this.steps);
        this.steps[this.currentStepIndex].show();
        this._updateButtons();
    }

    _onComplete() {
        this.onComplete();
    }
}

export default Stepper;