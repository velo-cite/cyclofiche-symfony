class MenuPrincipal {
    constructor() {
        this.element = document.createElement("div");
        this.element.id = "menu";
        this.element.className = "fixed z-10 bottom-0 w-full md:max-w-lg -translate-x-1/2 left-1/2 bg-velocite rounded-t-3xl px-4 sm:px-6 md:px-8 py-4 shadow-lg text-white space-y-3 opacity-95";

        this.element.innerHTML = `
      <button id="btn-signaler-un-probleme" class="w-full bg-white text-sky-600 font-semibold py-2 rounded-lg hover:bg-gray-100">
        Signaler un problème
      </button>

      <div class="flex items-center justify-between">
        <label class="flex items-center justify-center py-2 space-x-2 w-full rounded-lg rounded-white border">
          <span>Afficher tous les signalements</span>
          <input id="display-signalement" type="checkbox" value="" class="sr-only peer" checked>
          <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-600"></div>
        </label>
      </div>

      <div class="flex justify-between">
        <button id="btn-my-account" class="font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Mon compte</button>
        <button class="font-medium py-2 px-4 rounded-lg rounded-white border hover:bg-gray-100 hover:text-velocite">Tutoriel</button>
      </div>
    `;

        this.buttonReport = this.element.querySelector("#btn-signaler-un-probleme");
        this.displaySignalementBtn = this.element.querySelector("#display-signalement");
        this.myAccountBtn = this.element.querySelector("#btn-my-account");
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

    onReportClick(callback) {
        this.buttonReport.addEventListener("click", callback);
    }

    myAccountClick(callback) {
        this.myAccountBtn.addEventListener("click", callback);
    }

    toggleDisplaySignalementBtnClick(callback) {
        this.displaySignalementBtn.addEventListener("click", callback);
    }
}

export default MenuPrincipal;
