import { login } from './login.js';
import { submitIssue } from './issues.js';
import { dropAnimatedMarker } from '../map/dropMarker.js';
import { getSelectedTags, buildFormData } from '../assets/js/form/formUtils.js';
import { showStep, nextStep, prevStep, currentStepIsFormEnd } from './steps.js';

export function setupFormHandlers(map) {
    const form = document.getElementById('reportForm');
    const loginBtn = document.getElementById('loginBtn');
    const manualEntryBtn = document.getElementById('manualEntryBtn');
    const validateLogin = document.getElementById('validateLogin');
    const useGps = document.getElementById('useGps');
    const manualAddress = document.getElementById('manualAddress');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const btnAlertIssue = document.getElementById("btn-signaler-un-probleme");

    btnAlertIssue.addEventListener("click", () => {
        menu.classList.add('hidden');
        menuIssue.classList.remove('hidden');
        showStep(0);
    });

    loginBtn.addEventListener('click', () => {
        document.getElementById('loginForm').classList.remove('hidden');
    });

    manualEntryBtn.addEventListener('click', () => {
        document.getElementById('manualInfoForm').classList.remove('hidden');
    });

    validateLogin.addEventListener('click', async () => {
        const email = form.loginEmail.value;
        const password = form.loginPassword.value;
        try {
            const token = await login(email, password);
            localStorage.setItem('jwt', token);
            nextStep();
        } catch (err) {
            alert('Identifiants incorrects');
        }
    });

    useGps.addEventListener('change', () => {
        manualAddress.classList.toggle('hidden', useGps.checked);
        if (useGps.checked) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    form.latitude.value = `${pos.coords.latitude}`;
                    form.longitude.value = `${pos.coords.longitude}`;
                    dropAnimatedMarker(map, pos.coords.longitude, pos.coords.latitude);
                },
                () => alert('Impossible d’accéder à votre position.')
            );
        }
    });

    nextBtn.addEventListener('click', async () => {
        document.querySelectorAll('.step');
        if (currentStepIsFormEnd()) {
            const data = buildFormData(form);
            const token = localStorage.getItem('jwt');
            const success = await submitIssue(data, token);
            if (success) showStep(0);
            else alert('Erreur lors de l’envoi');
        } else {
            nextStep();
        }
    });

    prevBtn.addEventListener('click', () => {
        prevStep();
    });
}
