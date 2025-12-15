import axios from 'axios';

// You can change this to your PHP server URL
const API_URL = import.meta.env.VITE_API_URL || 'http://projects/playqzv4/api';

const api = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'application/json',
    },
});

// Add a request interceptor to include the token
api.interceptors.request.use(
    (config) => {
        const token = sessionStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Add a response interceptor to handle auth errors
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            // Clear token and redirect to login if needed
            // localStorage.removeItem('auth_token');
            // window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

// Helper function for fetch-based API calls
export async function fetchAPI(endpoint: string, options: RequestInit = {}) {
    // Always detect from current location (ignore VITE_API_URL for subdirectory deployments)
    let API_URL;

    if (typeof window !== 'undefined') {
        // Detect from current location
        const currentPath = window.location.pathname;
        if (currentPath.startsWith('/aiq3/')) {
            // Production: We're in /aiq3/ subdirectory
            API_URL = '/aiq3/api';
        } else if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            // Development: Use XAMPP virtual host
            API_URL = 'http://projects/playqzv4/api';
        } else {
            // Fallback: try to detect base from current URL
            const base = window.location.pathname.split('/').slice(0, 2).join('/');
            API_URL = base ? `${base}/api` : '/api';
        }
    } else {
        // Server-side rendering fallback
        API_URL = '/api';
    }

    const token = sessionStorage.getItem('auth_token');

    const headers: Record<string, string> = {
        'Content-Type': 'application/json',
        ...(options.headers as Record<string, string> || {}),
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch(`${API_URL}${endpoint}`, {
        ...options,
        headers,
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
}

export default api;
