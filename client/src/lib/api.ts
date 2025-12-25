import axios from 'axios';

// You can change this to your PHP server URL
// Function to detect correct API URL based on environment
const getApiUrl = () => {
    if (typeof window !== 'undefined') {
        const currentPath = window.location.pathname;
        const hostname = window.location.hostname;
        if (currentPath.startsWith('/playqzv4/aiq4/')) {
            // Development Nested: /playqzv4/aiq4/
            return '/playqzv4/aiq4/api';
        } else if (currentPath.startsWith('/aiq4/')) {
            // Production Sub: /aiq4/
            return '/aiq4/api';
        } else if (currentPath.startsWith('/aiq3/')) {
            // Legacy Production: /aiq3/ subdirectory
            return '/aiq3/api';
        } else if (hostname === 'projects' && currentPath.includes('/playqzv4/')) {
            // Check if we are in aiq4 even on projects hostname
            if (currentPath.includes('/aiq4/')) return '/playqzv4/aiq4/api';
            return '/playqzv4/api';
        } else if (currentPath.startsWith('/playqzv4/')) {
            // Local Build Test
            return '/playqzv4/api';
        } else if (hostname === 'aiquiz.vibeai.cv' || window.location.pathname === '/') {
            // Production Root: https://aiquiz.vibeai.cv/
            return '/api';
        } else if (hostname === 'localhost' || hostname === '127.0.0.1') {
            // Development: Use XAMPP virtual host
            return import.meta.env.VITE_API_URL || 'http://projects/playqzv4/api';
        } else {
            // Fallback: try to detect base from current URL
            const base = window.location.pathname.split('/').slice(0, 2).join('/');
            return base ? `${base}/api` : '/api';
        }
    }
    return import.meta.env.VITE_API_URL || '/api';
};

const API_URL = getApiUrl();

const api = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'application/json',
    },
});

// Add a request interceptor to include the token
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Add a response interceptor to handle auth errors and unwrap data
api.interceptors.response.use(
    (response) => {
        // If it follows the standard format, unwrap it
        if (response.data && typeof response.data === 'object' && 'success' in response.data) {
            if (response.data.success) {
                return { ...response, data: response.data.data };
            } else {
                // If success is false, treat it as an error
                const error = new Error(response.data.error || 'API Error');
                (error as any).response = response;
                return Promise.reject(error);
            }
        }
        return response;
    },
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
        if (currentPath.startsWith('/playqzv4/aiq4/')) {
            // Development Nested: /playqzv4/aiq4/
            API_URL = '/playqzv4/aiq4/api';
        } else if (currentPath.startsWith('/aiq4/')) {
            // Production Sub: /aiq4/
            API_URL = '/aiq4/api';
        } else if (currentPath.startsWith('/aiq3/')) {
            // Legacy Production: /aiq3/ subdirectory
            API_URL = '/aiq3/api';
        } else if (window.location.hostname === 'aiquiz.vibeai.cv' || window.location.pathname === '/') {
            // Production Root
            API_URL = '/api';
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
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
    }

    const result = await response.json();

    // Unwrap if it follows the standard format
    if (result && typeof result === 'object' && 'success' in result) {
        if (!result.success) {
            throw new Error(result.error || 'API Error');
        }
        return result.data;
    }

    return result;
}

export default api;
