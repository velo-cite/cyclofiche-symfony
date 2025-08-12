class StepDescription {
    constructor(api) {
        this.api = api;
        this.element = document.createElement("div");
        this.element.classList.add("step");

        this.element.innerHTML = `
            <h2 class="text-xl font-semibold mb-4">Photo (optionnel)</h2>
            <input name="photo" type="file" accept="image/*" class="input">
        `;
    }

    validate() {
        return true;
    }

    reset() {
        // this.select.value = "";
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

export default StepDescription;
