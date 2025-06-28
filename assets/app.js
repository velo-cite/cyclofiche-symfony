// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import './vendor/maplibre-gl/dist/maplibre-gl.min.css';

import maplibregl from './vendor/maplibre-gl/maplibre-gl.index.js';

document.addEventListener('DOMContentLoaded', () => {
    const map = new maplibregl.Map({
        container: 'map',
        style: 'map/style.json',
        center: [-0.61, 44.85],
        zoom: 12
    });

    map.on("load", () => {
        const splash = document.getElementById("splash");
        splash.classList.add("opacity-0"); // anime l'opacité
        splash.remove();
    });

    map.addControl(new maplibregl.NavigationControl());

    async function fetchIssues() {
        try {
            const response = await fetch('/api/issues', {
                headers: {
                    'Accept': 'application/ld+json'
                }
            });

            if (!response.ok) {
                throw new Error(`Erreur HTTP : ${response.status}`);
            }

            const data = await response.json();
            const issues = data.member || [];
            issues.forEach(issue => {
                // Coords attendues sous forme "lat,lng" => on split et convertit
                const coords = issue.location?.split(',').map(Number);
                if (!coords || coords.length !== 2 || coords.some(isNaN)) {
                    console.warn('Coordonnées invalides pour:', issue);
                    return;
                }

                // Inverser lat/lng → MapLibre attend [lng, lat]
                const lngLat = [coords[1], coords[0]];

                // Construire contenu HTML de la popup
                const popupContent = `
                    <div style="max-width: 300px;">
                      <h3>${issue.category?.libelle ?? 'Sans catégorie'}</h3>
                      <p><strong>État:</strong> ${issue.state}</p>
                      <p><strong>Description:</strong> ${issue.description ?? 'Non précisé'}</p>
                      <p><strong>Adresse:</strong> ${issue.address ?? ''}, ${issue.city ?? ''}</p>
                      <p><strong>Créateur:</strong> ${issue.creator?.firstname ?? ''} ${issue.creator?.lastname ?? ''}</p>
                      ${issue.photos?.length ? `<div><strong>Photos:</strong><br>
                        ${issue.photos.map(photo => `<img src="${photo.filename}" alt="photo issue" style="max-width:100%;margin-top:5px;">`).join('')}
                      </div>` : ''}
                    </div>
                  `;

                // Créer la popup
                const popup = new maplibregl.Popup({ offset: 25 }).setHTML(popupContent);

                new maplibregl.Marker()
                    .setLngLat(issue.location.split(',').reverse())
                    .setPopup(popup)
                    .addTo(map);
            });

        } catch (error) {
            console.error('Erreur lors du chargement des issues :', error);
        }
    }

    // Charger les données dès que la page est prête
    fetchIssues().catch();


    /**
     * Gestion de la saisie d'une nouvelle issue
     */
    const steps = document.querySelectorAll(".step");
    const form = document.getElementById("reportForm");
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");
    const useGps = document.getElementById("useGps");
    const manualAddress = document.getElementById("manualAddress");
    const loginBtn = document.getElementById("loginBtn");
    const manualEntryBtn = document.getElementById("manualEntryBtn");
    const loginForm = document.getElementById("loginForm");
    const manualInfoForm = document.getElementById("manualInfoForm");
    const validateLogin = document.getElementById("validateLogin");

    let currentmarker;

    let currentStep = 0;

    function showStep(i) {
        steps.forEach((step, index) => step.classList.toggle("hidden", index !== i));
        prevBtn.classList.toggle("hidden", i === 0);
        nextBtn.textContent = (i === steps.length - 2) ? "Envoyer" : "Suivant >";
    }

    loginBtn.addEventListener("click", () => {
        loginForm.classList.remove("hidden");
    });

    manualEntryBtn.addEventListener("click", () => {
        manualInfoForm.classList.remove('hidden')
    });

    validateLogin.addEventListener("click", async () => {
        const email = form.loginEmail.value;
        const password = form.loginPassword.value;

        try {
            const res = await fetch("/api/login_check", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({username: email, password: password })
            });

            if (!res.ok) {
                alert("Identifiants incorrects.");
                return;
            }

            const { token } = await res.json();
            localStorage.setItem("jwt", token); // 🔐 Stockage du JWT

            showStep(currentStep ++);
        } catch (err) {
            alert("Erreur réseau ou serveur.");
        }
    });

    useGps.addEventListener("change", () => {
        manualAddress.classList.toggle("hidden", useGps.checked);
    });

    useGps.addEventListener("change", () => {
        navigator.geolocation.getCurrentPosition(
            pos => {
                form.latitude.value = `${pos.coords.latitude}`;
                form.longitude.value = `${pos.coords.longitude}`;

                dropAnimatedMarker(pos.coords.longitude, pos.coords.latitude);
            },
            () => alert("Impossible d’accéder à votre position.")
        );
    });

    nextBtn.addEventListener("click", async () => {
        if (currentStep === steps.length - 2) {
            await submitForm();
        } else {
            currentStep++;
            showStep(currentStep);
        }
    });

    prevBtn.addEventListener("click", () => {
        currentStep = Math.max(0, currentStep - 1);
        showStep(currentStep);
    });

    function getSelectedTags() {
        return [...form.querySelectorAll("input[name='issues']:checked")].map(el => el.value);
    }

    async function submitForm() {
        console.log(form);

        const category = {
            libelle: form.categoryLabel.value,
            issues: getSelectedTags(),
            image: "" // optionnel ou à construire
        };

        const data = {
            state: "submitted",
            category: "/api/issue_categories/1",
            city: form.city?.value || "",
            address: form.address?.value || "",
            location: form.latitude?.value+','+form.longitude?.value || "",
            description: form.description?.value || "",
            firstname: form.firstname.value,
            lastname: form.lastname.value,
            email: form.email.value,
            phone: form.phone.value,
            photos: [] // à gérer si upload actif
        };
        try {
            const res = await fetch("/api/issues", {
                method: "POST",
                headers: { "Content-Type": "application/ld+json" },
                body: JSON.stringify(data)
            });

            if (res.ok) {
                currentStep = 0;
                showStep(currentStep);
            } else {
                alert("Erreur lors de l’envoi");
            }
        } catch (e) {
            alert("Erreur réseau ou serveur");
        }
    }

    showStep(currentStep);


    function dropAnimatedMarker(lng, lat) {
        if (currentmarker) {
            currentmarker.remove();
        }
        const el = document.createElement('div');
        // el.className = 'marker-drop';
        el.style.width = '30px';
        el.style.height = '30px';
        el.style.backgroundImage = 'url(https://docs.mapbox.com/mapbox-gl-js/assets/custom_marker.png)';
        el.style.backgroundSize = 'cover';
        el.style.borderRadius = '50%';

        // currentmarker = new maplibregl.Marker(el)
        currentmarker = new maplibregl.Marker()
            .setLngLat([lng, lat])
            .addTo(map);

        map.flyTo({center: [lng, lat], zoom: 16});
    }
});