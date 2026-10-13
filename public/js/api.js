const TOKEN_KEY = "librasys_token";
const ROLE_KEY = "librasys_role";

const Auth = {
    getToken: () => localStorage.getItem(TOKEN_KEY),
    setToken: (token) => localStorage.setItem(TOKEN_KEY, token),
    getRole: () => localStorage.getItem(ROLE_KEY),
    setRole: (role) => localStorage.setItem(ROLE_KEY, role),
    clear: () => {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(ROLE_KEY);
    },
};

function homeFor(role) {
    return role === "admin" ? "/dashboard" : "/member";
}

// Penjaga halaman (kemudahan tampilan saja; keamanan sebenarnya ada di API).
function requireRole(role) {
    const current = Auth.getRole();

    if (!Auth.getToken() || !current) {
        Auth.clear();
        window.location.href = "/login";
        return;
    }

    if (current !== role) {
        window.location.href = homeFor(current);
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
    } catch (error) {
        // Token mungkin sudah kedaluwarsa; tetap bersihkan sesi lokal.
    }
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

function escapeHtml(value) {
    const div = document.createElement("div");
    div.textContent = value ?? "";
    return div.innerHTML;
}

function formatDate(value) {
    if (!value) return "-";
    return new Date(value).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
}
