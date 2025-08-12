import { decodeJwt } from 'jose';

export class ApiClient {
    constructor(baseUrl = '/api') {
        this.baseUrl = baseUrl;
        this.refreshToken = null;
        this.refreshTimer = null;
    }

    async login(email, password) {
        const response = await fetch(`${this.baseUrl}/login_check`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: email, password }),
        });

        if (!response.ok) throw new Error('Identifiants invalides');

        const data = await response.json();
        this.setTokens(data.token, data.refresh_token);
        return data;
    }

    async signIn(firstname, lastname, email, phone, password) {
        const response = await fetch(`${this.baseUrl}/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, firstname, lastname, phone, password }),
        });

        const data = await response.json();
        if (data.error) {
            throw new Error(data.error);
        }
        return data;
    }

    async logout() {
        this.accessToken = null;
        this.refreshToken = null;
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        if (this.refreshTimer) clearTimeout(this.refreshTimer);
        this.refreshTimer = null;
    }

    setTokens(access, refresh) {
        this.accessToken = access;
        this.refreshToken = refresh;
        localStorage.setItem('access_token', access);
        localStorage.setItem('refresh_token', refresh);
        this.scheduleRefresh();
    }

    loadTokens() {
        this.accessToken = localStorage.getItem('access_token');
        this.refreshToken = localStorage.getItem('refresh_token');
    }

    async refreshAccessToken() {
        const response = await fetch('/api/token/refresh', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ refresh_token: this.refreshToken }),
        });

        if (!response.ok) {
            await this.logout();
            throw new Error('Refresh token invalide');
        }

        const data = await response.json();
        await this.setTokens(data.token, data.refresh_token || this.refreshToken);
    }

    isLogged() {
        return null != this.accessToken;
    }

    async fetchWithAuth(url, options = {}, data = {}) {
        this.loadTokens();

        if (!this.accessToken) throw new Error('Pas de token disponible');

        // Vérifie si le token a expiré
        if (this.isTokenExpired(this.accessToken)) {
            try {
                await this.refreshAccessToken();
            } catch (e) {
                throw new Error('Session expirée');
            }
        }

        // Ajout du token dans les headers
        options = options || {};
        options.method = 'POST';
        options.headers = options.headers || {};
        options.headers['Authorization'] = 'Bearer ' + this.accessToken;
        options.headers['Content-Type'] = 'application/ld+json';
        options.body = JSON.stringify(data)

        let response = await fetch(url, options);

        // Si pour une autre raison on reçoit un 401 (par exemple refresh token révoqué)
        if (response.status === 401) {
            await this.logout();
            throw new Error('Non autorisé');
        }

        return response;
    }

    isTokenExpired (token) {
        try {
            const payload = decodeJwt(this.accessToken);
            const now = Math.floor(Date.now() / 1000);
            return payload.exp < now;
        } catch (e) {
            return true; // Si le token est invalide ou non décodable
        }
    }

    scheduleRefresh() {
        if (!this.accessToken) return;

        try {
            const payload = decodeJwt(this.accessToken);
            const now = Math.floor(Date.now() / 1000);
            const exp = payload.exp;

            // On programme le refresh 60 secondes avant expiration
            const delay = (exp - now - 60) * 1000;

            if (delay <= 0) {
                console.warn('Token presque expiré, on rafraîchit immédiatement');
                this.refreshAccessToken();
                return;
            }

            // Annule l'ancien timer s'il existe
            if (this.refreshTimer) clearTimeout(this.refreshTimer);

            // Programme le nouveau timer
            this.refreshTimer = setTimeout(async () => {
                try {
                    console.log('[AuthService] Rafraîchissement programmé du token');
                    await this.refreshAccessToken();
                } catch (e) {
                    console.error('Erreur lors du rafraîchissement automatique du token');
                    this.logout();
                }
            }, delay);

            console.log(`[AuthService] Token rafraîchira dans ${Math.round(delay / 1000)}s`);
        } catch (e) {
            console.error('Erreur de parsing du token :', e);
        }
    }

    async fetchIssues() {
        const res = await fetch(`${this.baseUrl}/issues`, {
            headers: { 'Accept': 'application/ld+json' }
        });
        if (!res.ok) throw new Error('Erreur lors du chargement des issues');
        const data = await res.json();
        return data.member || [];
    }

    async submitIssue(data) {
        let res = null;

        if (this.accessToken) {
            res = await this.fetchWithAuth(`${this.baseUrl}/issues`, null, data)
        } else {
            res = await fetch(`${this.baseUrl}/issues`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/ld+json',
                },
                body: JSON.stringify(data)
            });
        }
        if (!res.ok) throw new Error('Erreur lors de l’envoi');
        return res.json();
    }

    async fetchCategories() {
        const res = await fetch(`${this.baseUrl}/issue_categories`, {
            headers: { 'Accept': 'application/ld+json' }
        });
        if (!res.ok) throw new Error('Erreur lors du chargement des catégories');
        const data = await res.json();
        return data.member || [];
    }
}
