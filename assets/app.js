// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './vendor/maplibre-gl/dist/maplibre-gl.min.css';

import { MapManager } from './js/map/MapManager.js';
import { ApiClient } from './js/api/ApiClient.js';
import { AppController } from './js/AppController.js';
import singletonFlashBag from "./js/form/flashbag.js";

const api = new ApiClient();
api.loadTokens(); // Pour réinitialiser après rechargement
let flashbag = singletonFlashBag.getInstance();

document.addEventListener('DOMContentLoaded', async () => {
    const mapManager = new MapManager('map', api);

    // Charge les markers
    try {
        const issues = await api.fetchIssues();
        mapManager.map.on("load", () => {
            issues.forEach(issue => mapManager.addIssue(issue));
        });
    } catch (err) {
        console.error(err);
    }

    const appController = new AppController(api, mapManager);

    window.addEventListener("addMarker", (e) => {
        const { latitude, longitude, form } = e.detail;
        mapManager.dropMarker(latitude, longitude, form);
    });

    window.addEventListener("toggleMarkerOfOther", (e) => {
        mapManager.toggleMarkerOfOther();
    });

    const page = document.body.dataset.page;

    if (page === 'app-verify-email') {
        handleVerifyEmail();
    }
});

function handleVerifyEmail() {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    const signature = params.get('signature');
    const expires = params.get('expires');
    const token = params.get('token');

    if (!id || !signature || !expires) {
        flashbag.error('Lien invalide.');
        return;
    }

    fetch(`/api/verify/email?expires=${encodeURIComponent(expires)}&id=${encodeURIComponent(id)}&signature=${encodeURIComponent(signature)}&token=${encodeURIComponent(token)}`)
        .then(async response => {
            if (!response.ok) {
                throw new Error((await response.json()).error || 'Erreur de validation.');
            }
            return response.json();
        })
        .then(data => {
            api.setTokens(data.token, data.refresh_token)
            flashbag.success(data.message || 'Email confirmé.');
            history.replaceState({}, '', '/');
        })
        .catch(err => flashbag.error(err.message));
}