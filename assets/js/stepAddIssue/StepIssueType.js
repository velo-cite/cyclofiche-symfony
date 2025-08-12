class StepIssueType {
    constructor(api) {
        this.api = api;
        this.element = document.createElement("div");
        this.element.classList.add("step");

        this.element.innerHTML = `
          <label class="text-xl font-semibold mb-4" for="categoryLabel">Type de signalement</label>
          <select name="categoryLabel" id="categoryLabel" required class="input">
            <option value="">-- Choisissez un type --</option>
          </select>
        `;

        this.select = this.element.querySelector("#categoryLabel");
        this.loadCategories();
    }

    loadCategories() {
        const select = this.select;
        this.api.fetchCategories()
            .then(function (categories) {
                categories.forEach(function (category) {
                    const option = document.createElement('option');
                    option.value = category['@id'];
                    option.textContent = category['libelle'];
                    select.appendChild(option);
                })
            })
    }

    validate() {
        return this.select.value !== "";
    }

    reset() {
        this.select.value = "";
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

export default StepIssueType;
