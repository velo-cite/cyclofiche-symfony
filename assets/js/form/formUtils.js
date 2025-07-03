export function getSelectedTags(form) {
    return [...form.querySelectorAll("input[name='issues']:checked")].map(el => el.value);
}

// export function buildFormData(form) {
//     return {
//         state: 'submitted',
//         category: '/api/issue_categories/1',
//         city: form.city?.value || '',
//         address: form.address?.value || '',
//         location: form.latitude?.value + ',' + form.longitude?.value || '',
//         description: form.description?.value || '',
//         firstname: form.firstname.value,
//         lastname: form.lastname.value,
//         email: form.email.value,
//         phone: form.phone.value,
//         photos: []
//     };
// }