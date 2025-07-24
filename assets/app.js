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

const api = new ApiClient();
api.loadTokens(); // Pour réinitialiser après rechargement

document.addEventListener('DOMContentLoaded', async () => {
    const mapManager = new MapManager('map');

    // Charge les markers
    try {
        const issues = await api.fetchIssues();
        issues.forEach(issue => mapManager.addIssue(issue));
    } catch (err) {
        console.error(err);
    }

    const appController = new AppController(api, mapManager);

    window.addEventListener("addMarker", (e) => {
        const { latitude, longitude, form } = e.detail;
        mapManager.dropMarker(latitude, longitude, form);
    });
});