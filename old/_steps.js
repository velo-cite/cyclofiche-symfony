let steps, currentStep = 0;

export function initSteps() {
    steps = document.querySelectorAll('.step');
    showStep(currentStep);
}

export function getCurrentStep() {
    return currentStep;
}

export function showStep(i) {
    currentStep = i;
    steps.forEach((step, index) => step.classList.toggle('hidden', index !== i));
    document.getElementById('prevBtn').classList.toggle('hidden', i === 0);
    document.getElementById('nextBtn').textContent = (i === steps.length - 2) ? 'Envoyer' : 'Suivant >';
}

export function nextStep() {
    currentStep++;
    showStep(currentStep);
}

export function prevStep() {
    currentStep = Math.max(0, currentStep - 1);
    showStep(currentStep);
}

export function currentStepIsFormEnd() {
    return getCurrentStep() === steps.length - 2;
}