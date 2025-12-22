import { create } from 'zustand';
import api from '../lib/api';
import { User, LoginCredentials, SignupData } from '../types';

interface AuthState {
    user: User | null;
    isLoading: boolean;
    isAuthenticated: boolean;
    isAdmin: boolean;
    isSuperAdmin: boolean;
    initialize: () => Promise<void>;
    signIn: (credentials: LoginCredentials & { rememberMe?: boolean }) => Promise<void>;
    signUp: (data: SignupData) => Promise<void>;
    signOut: () => Promise<void>;
    logout: () => Promise<void>;
    refreshProfile: () => Promise<void>;
}

export const useAuth = create<AuthState>((set, get) => ({
    user: null,
    isLoading: true,
    isAuthenticated: false,
    isAdmin: false,
    isSuperAdmin: false,

    initialize: async () => {
        try {
            set({ isLoading: true });
            // Check both storage mechanisms, preferring localStorage
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

            if (token) {
                // Ensure consistency - if found in one, make available to app logic
                if (localStorage.getItem('auth_token')) {
                    // It's persistent
                } else {
                    // It's session only
                }

                try {
                    const response = await api.get('/auth/me.php');
                    const user = response.data.user;
                    const userData = { ...user, role: user?.role || 'user' };
                    set({
                        user: userData,
                        isAuthenticated: true,
                        isAdmin: userData.role === 'admin' || userData.role === 'super_admin',
                        isSuperAdmin: userData.role === 'super_admin'
                    });
                } catch (error) {
                    console.error('Failed to fetch user profile', error);
                    localStorage.removeItem('auth_token');
                    sessionStorage.removeItem('auth_token');
                    set({ user: null, isAuthenticated: false, isAdmin: false, isSuperAdmin: false });
                }
            } else {
                set({ user: null, isAuthenticated: false, isAdmin: false, isSuperAdmin: false });
            }
        } catch (error) {
            console.error('Auth initialization error:', error);
        } finally {
            set({ isLoading: false });
        }
    },

    signIn: async (credentials: LoginCredentials & { rememberMe?: boolean }) => {
        try {
            const response = await api.post('/auth/login.php', credentials);
            const { token, user } = response.data;

            if (!user || typeof user !== 'object') {
                throw new Error('Invalid user data received from server');
            }

            // Ensure role property exists, default to 'user' if missing
            const userData = {
                ...user,
                role: user.role || 'user'
            };

            // Store token based on preference
            if (credentials.rememberMe) {
                localStorage.setItem('auth_token', token);
                sessionStorage.setItem('auth_token', token); // Keep both in sync for fallback
            } else {
                sessionStorage.setItem('auth_token', token);
            }

            set({
                user: userData,
                isAuthenticated: true,
                isAdmin: userData.role === 'admin' || userData.role === 'super_admin',
                isSuperAdmin: userData.role === 'super_admin'
            });
        } catch (error) {
            console.error('Login failed:', error);
            throw error;
        }
    },

    signUp: async (data: SignupData) => {
        try {
            const response = await api.post('/auth/signup.php', data);
            const { token, user } = response.data;
            const userData = { ...user, role: user?.role || 'user' };

            sessionStorage.setItem('auth_token', token);
            set({
                user: userData,
                isAuthenticated: true,
                isAdmin: userData.role === 'admin' || userData.role === 'super_admin',
                isSuperAdmin: userData.role === 'super_admin'
            });
        } catch (error) {
            console.error('Signup failed:', error);
            throw error;
        }
    },

    signOut: async () => {
        try {
            await api.post('/auth/logout.php');
        } catch (error) {
            console.error('Logout API call failed:', error);
        } finally {
            sessionStorage.removeItem('auth_token');
            localStorage.removeItem('auth_token'); // Clear both
            sessionStorage.clear();
            set({ user: null, isAuthenticated: false, isAdmin: false, isSuperAdmin: false });
        }
    },

    logout: async () => {
        await get().signOut();
    },

    refreshProfile: async () => {
        try {
            const response = await api.get('/auth/me.php');
            const user = response.data.user;
            const userData = { ...user, role: user?.role || 'user' };
            set({
                user: userData,
                isAuthenticated: true,
                isAdmin: userData.role === 'admin' || userData.role === 'super_admin',
                isSuperAdmin: userData.role === 'super_admin'
            });
        } catch (error) {
            console.error('Failed to refresh profile', error);
        }
    },
}));

