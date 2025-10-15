import maplibregl from '../../vendor/maplibre-gl/maplibre-gl.index.js';
// import Point from './point.js'

export class MapManager {
    constructor(containerId, api) {
        this.map = new maplibregl.Map({
            container: containerId,
            style: '/map/style.json',
            center: [-0.61, 44.85],
            zoom: 12,
        });
        this.api = api;
        this.newMarker = null;
        this.issuesData = [];
        this.displayMarkersOfOther = true;

        this.map.addControl(new maplibregl.NavigationControl());
        this.map.on("load", () => {
            const splash = document.getElementById("splash");
            splash?.classList.add("opacity-0");
            splash?.remove();
            this.initIssuesLayer()
        });
    }

    // 1. Initialisation du layer à vide
    initIssuesLayer() {
        this.map.addSource('issues', {
            type: 'geojson',
            data: {
                type: 'FeatureCollection',
                features: []
            }
        });

        this.map.addLayer({
            id: 'issues-layer',
            type: 'circle',
            source: 'issues',
            paint: {
                'circle-radius': 6,
                'circle-color': '#ff0000'
            }
        });

        // Popup au clic sur un point
        this.map.on('click', 'issues-layer', (e) => {
            const feature = e.features[0];
            const issue = JSON.parse(feature.properties.issue);

            const popupContent = `
            <div style="max-width: 300px;">
                <h3>${issue.category?.libelle ?? 'Sans catégorie'}</h3>
                <p><strong>État:</strong> ${issue.state}</p>
                <p><strong>Description:</strong> ${issue.description ?? 'Non précisé'}</p>
                <p><strong>Adresse:</strong> ${issue.address ?? ''}, ${issue.city ?? ''}</p>
                <p><strong>Créateur:</strong> ${issue.creator?.firstname ?? ''} ${issue.creator?.lastname ?? ''}</p>
                ${issue.photos?.length ? `<div><strong>Photos:</strong><br>
                    ${issue.photos.map(p => `<img src="${p.filename}" alt="photo issue" style="max-width:100%;margin-top:5px;">`).join('')}
                </div>` : ''}
            </div>
        `;

            new maplibregl.Popup({ offset: 25 })
                .setLngLat(e.lngLat)
                .setHTML(popupContent)
                .addTo(this.map);
        });

        // Changement du curseur
        this.map.on('mouseenter', 'issues-layer', () => this.map.getCanvas().style.cursor = 'pointer');
        this.map.on('mouseleave', 'issues-layer', () => this.map.getCanvas().style.cursor = '');
    }

    // 3. Fonction pour ajouter une issue dans le layer
    addIssue(issue) {
        const coords = issue.location?.split(',').map(Number);
        if (!coords || coords.length !== 2 || coords.some(isNaN)) return;

        // Ajouter l'issue au tableau interne
        this.issuesData.push({
            type: 'Feature',
            geometry: {
                type: 'Point',
                coordinates: [coords[1], coords[0]] // [lng, lat]
            },
            properties: {
                issue: JSON.stringify(issue),
                emailCrypted: issue.emailCrypted,
                creatorKey: issue.creator
                    ? `${issue.creator.firstname ?? ''} ${issue.creator.lastname ?? ''}`.trim()
                    : null
            }
        });

        // Mettre à jour la source
        this.map.getSource('issues').setData({
            type: 'FeatureCollection',
            features: this.issuesData
        });
    }

    dropMarker(lat, lng, form) {
        let that = this;
        if (this.newMarker)  {
            this.newMarker.remove();
        }
        this.newMarker = new maplibregl.Marker({draggable: true, color: "#d40740",}).setLngLat([lng, lat]).addTo(this.map);
        this.map.flyTo({ center: [lng, lat], zoom: 16 });

        function onDragEnd() {
            const lngLat = that.newMarker.getLngLat();
            form.latitude.value = lngLat.lat;
            form.longitude.value = lngLat.lng;
        }
        this.newMarker.on('dragend', onDragEnd);
    }

    acceptNewIssue(issue) {
        this.addIssue(issue);
        this.newMarker.remove();
    }

    toggleMarkerOfOther() {
        this.displayMarkersOfOther = !this.displayMarkersOfOther;
        if (this.displayMarkersOfOther) {
            this.map.setFilter('issues-layer', null);
        } else {
            this.map.setFilter('issues-layer', ['==', ['get', 'emailCrypted'], this.api.emailUserCrypted]);
        }
    }
}
