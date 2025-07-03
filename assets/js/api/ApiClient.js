export class ApiClient {
    constructor(baseUrl = '/api') {
        this.baseUrl = baseUrl;
    }

    async login(email, password) {
        const res = await fetch(`${this.baseUrl}/login_check`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: email, password })
        });
        if (!res.ok) throw new Error('Identifiants invalides');
        return res.json();
    }

    async fetchIssues() {
        const res = await fetch(`${this.baseUrl}/issues`, {
            headers: { 'Accept': 'application/ld+json' }
        });
        if (!res.ok) throw new Error('Erreur lors du chargement des issues');
        const data = await res.json();
        return data.member || [];
    }

    async submitIssue(data, token) {
        const res = await fetch(`${this.baseUrl}/issues`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/ld+json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data)
        });
        if (!res.ok) throw new Error('Erreur lors de l’envoi');
        return res.json();
    }
}
