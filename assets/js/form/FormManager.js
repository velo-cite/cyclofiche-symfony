import { getSelectedTags } from './formUtils.js';
import {FlashBag} from './flashbag.js';

export class FormManager {
    constructor(formEl, mapManager, apiClient) {
        this.form = formEl;
        this.map = mapManager;
        this.api = apiClient;
        this.steps = [...document.querySelectorAll(".step")];
        this.currentStep = 0;
        this.connectionStep = 0;
        this.flashbag = new FlashBag();
    }

    init() {
        this.loadCategories();
        this.bindUI();
        this.showStep(this.currentStep);
    }

    bindUI() {
        document.getElementById("btn-signaler-un-probleme").addEventListener("click", () => {
            document.getElementById("menu").classList.add("hidden");
            document.getElementById("menuIssue").classList.remove("hidden");
            this.showStep(this.currentStep);
        });

        function showLoginForm() {
            document.getElementById("loginForm").classList.remove("hidden");
            document.getElementById("loginBtn").classList.add("hidden");
        }

        function showPersonalDataForm() {
            document.getElementById("manualInfoForm").classList.remove("hidden");
            document.getElementById("manualEntryBtn").classList.add("hidden");
        }

        function resetConnectionForm() {
            document.getElementById("loginForm").classList.add("hidden");
            document.getElementById("manualInfoForm").classList.add("hidden");
            document.getElementById("loginBtn").classList.remove("hidden");
            document.getElementById("manualEntryBtn").classList.remove("hidden");
        }

        document.getElementById("loginBtn").addEventListener("click", () => {
            resetConnectionForm();
            showLoginForm();
        });

        document.getElementById("manualEntryBtn").addEventListener("click", () => {
            resetConnectionForm();
            showPersonalDataForm();
        });

        document.getElementById("validateLogin").addEventListener("click", () => this.login());

        document.getElementById("nextBtn").addEventListener("click", () => {
            this.currentStep === this.steps.length - 2 ? this.submit() : this.showStep(++this.currentStep);
        });

        document.getElementById("prevBtn").addEventListener("click", () => {
            this.showStep(--this.currentStep);
        });

        document.getElementById("useGps").addEventListener("change", e => {
            const manualAddress = document.getElementById("manualAddress");
            manualAddress.classList.toggle("hidden", e.target.checked);

            if (e.target.checked) {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        this.form.latitude.value = pos.coords.latitude;
                        this.form.longitude.value = pos.coords.longitude;
                        this.map.dropMarker(pos.coords.latitude, pos.coords.longitude, this.form);
                    },
                    () => alert("Impossible d’accéder à votre position.")
                );
            }
        });
    }

    showStep(i) {
        if (i === this.connectionStep && localStorage.getItem('jwt')) {
            this.currentStep++;
            i++;
        }
        this.steps.forEach((el, idx) => el.classList.toggle("hidden", idx !== i));
        document.getElementById("prevBtn").classList.toggle("hidden", i === 0);
        document.getElementById("nextBtn").textContent = (i === this.steps.length - 2) ? "Envoyer" : "Suivant >";
    }

    async login() {
        const email = this.form.loginEmail.value;
        const password = this.form.loginPassword.value;
        try {
            const { token } = await this.api.login(email, password);
            localStorage.setItem("jwt", token);
            this.showStep(++this.currentStep);
        } catch (e) {
            alert(e.message);
        }
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
            this.currentStep = 0;
            this.flashbag.success("Signalement confirmé, merci beaucoup !");
            this.showStep(this.currentStep);
        } catch (e) {
            this.flashbag.error(e.message);
        }
    }

    loadCategories() {
        let categorySelect = document.getElementById("categoryLabel");
        this.api.fetchCategories()
        .then(function (categories) {
            categories.forEach(function (category) {
                const option = document.createElement('option');
                option.value = category['@id'];
                option.textContent = category['libelle'];
                categorySelect.appendChild(option);
            })
        })
    }
}
