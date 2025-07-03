export async function submitIssue(data, token) {
    const res = await fetch('/api/issues', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/ld+json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(data)
    });
    return res.ok;
}