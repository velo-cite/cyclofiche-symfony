import MenuPrincipal from "./menu/MenuPrincipal.js";
import MenuAddIssue from "./menu/MenuAddIssue.js";

export class AppController {
    constructor(api) {
        this.menuPrincipal = new MenuPrincipal();
        this.menuAddIssue = new MenuAddIssue(api);

        document.body.appendChild(this.menuPrincipal.getDOM());
        document.body.appendChild(this.menuAddIssue.getDOM());

        this.menuPrincipal.onReportClick(() => {
            this.menuPrincipal.hide();
            this.menuAddIssue.show();
        });

        window.addEventListener("issueAdded", (e) => {
            this.menuPrincipal.show();
            this.menuAddIssue.hide();
        });
    }
}