import MenuPrincipal from "./menu/MenuPrincipal.js";
import MenuAddIssue from "./menu/MenuAddIssue.js";
import MenuMyAccount from "./menu/MenuMyAccount.js";

export class AppController {
    constructor(api, mapManager) {
        this.menuPrincipal = new MenuPrincipal();
        this.menuAddIssue = new MenuAddIssue(api);
        this.menuMyAccount = new MenuMyAccount(api, () => this.backToPrincipalMenu());

        this.menues = [this.menuPrincipal, this.menuAddIssue, this.menuMyAccount];

        this.addDomOfMenus();

        this.menuPrincipal.onReportClick(() => {
            this.hideMenus();
            this.menuAddIssue.show();
        });
        this.menuPrincipal.myAccountClick(() => {
            this.hideMenus();
            this.menuMyAccount.show();
        });
        this.menuPrincipal.toggleDisplaySignalementBtnClick(() => {
            window.dispatchEvent(new CustomEvent("toggleMarkerOfOther"));
        });

        window.addEventListener("issueAdded", (e) => {
            this.hideMenus();
            this.menuPrincipal.show();
            mapManager.acceptNewIssue(e.detail);
        });
    }

    addDomOfMenus() {
        this.menues.forEach(menu => document.body.appendChild(menu.getDOM()));
        this.hideMenus();
        this.menuPrincipal.show();
    }

    hideMenus() {
        this.menues.forEach(menu => menu.hide());
    }

    backToPrincipalMenu() {
        this.hideMenus();
        this.menuPrincipal.show();
    }
}