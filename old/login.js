export async function login(email, password) {
    const res = await fetch('/api/login_check', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username: email, password })
    });
    if (!res.ok) throw new Error('Identifiants incorrects');
    const { token } = await res.json();
    return token;
}