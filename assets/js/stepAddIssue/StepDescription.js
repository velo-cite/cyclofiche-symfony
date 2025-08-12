class StepDescription {
    constructor(api) {
        this.api = api;
        this.element = document.createElement("div");
        this.element.classList.add("step");

        this.element.innerHTML = `
            <label for="description" class="text-xl font-semibold mb-4">Description</label>
            <p class="mb-2 text-sm">Sélectionnez un ou plusieurs tags pour qualifier le problème</p>
            <textarea class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" name="description" id="description"></textarea>
        `;
        this.textarea = this.element.querySelector("#description");

    }

    validate() {
        return this.textarea.value !== "";
    }

    reset() {
        this.textarea.value = "";
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
