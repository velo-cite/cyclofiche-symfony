import maplibregl from '../../vendor/maplibre-gl/maplibre-gl.index.js';
// import Point from './point.js'

export class MapManager {
    constructor(containerId) {
        this.map = new maplibregl.Map({
            container: containerId,
            style: 'map/style.json',
            center: [-0.61, 44.85],
            zoom: 12,
        });

        this.map.addControl(new maplibregl.NavigationControl());

        this.map.on("load", () => {
            const splash = document.getElementById("splash");
            splash?.classList.add("opacity-0");
            splash?.remove();
        });
        this.newMarker = null;
    }

    addIssue(issue) {
        const coords = issue.location?.split(',').map(Number);
        if (!coords || coords.length !== 2 || coords.some(isNaN)) return;

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
            </div>`;

        const popup = new maplibregl.Popup({ offset: 25 }).setHTML(popupContent);
        new maplibregl.Marker().setLngLat([coords[1], coords[0]]).setPopup(popup).addTo(this.map);
    }

    dropMarker(lat, lng, form) {
        let that = this;
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
}
