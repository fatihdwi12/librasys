const TOKEN_KEY = "librasys_token";

const Auth = {
    getToken: () => localStorage.getItem(TOKEN_KEY),
    setToken: (token) => localStorage.setItem(TOKEN_KEY, token),
    clear: () => localStorage.removeItem(TOKEN_KEY),
};

function requireAuth() {
    if (!Auth.getToken()) {
        window.location.href = "/login";
    }
}

async function apiFetch(path, options = {}) {
    const { skipAuthRedirect = false, ...fetchOptions } = options;

    const headers = {
        Accept: "application/json",
        "Content-Type": "application/json",
        ...(fetchOptions.headers || {}),
    };

    const token = Auth.getToken();
    if (token) {
        headers["Authorization"] = `Bearer ${token}`;
    }

    const response = await fetch(`/api${path}`, { ...fetchOptions, headers });

    if (response.status === 401 && !skipAuthRedirect) {
        Auth.clear();
        window.location.href = "/login";
        throw new Error("Unauthenticated");
    }

    const body = await response.json().catch(() => ({}));

    if (!response.ok) {
        const error = new Error(body.message || "Request failed");
        error.status = response.status;
        error.errors = body.errors || null;
        throw error;
    }

    return body;
}

async function logout() {
    try {
        await apiFetch("/logout", { method: "POST", skipAuthRedirect: true });
    } catch (error) {}
    Auth.clear();
    window.location.href = "/login";
}

function showAlert(message, type = "danger") {
    const box = document.getElementById("alert");
    if (!box) return;

    box.innerHTML = "";
    const div = document.createElement("div");
    div.className = `alert alert-${type}`;
    div.textContent = message;
    box.appendChild(div);
}

function formatErrors(error) {
    if (!error.errors) return error.message;
    return Object.values(error.errors).flat().join(" ");
}
