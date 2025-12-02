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
    signIn: (credentials: LoginCredentials) => Promise<void>;
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
            const token = localStorage.getItem('auth_token');

            if (token) {
                try {
                    const response = await api.get('/auth/me.php');
                    const user = response.data.user;
                    set({
                        user,
                        isAuthenticated: true,
                        isAdmin: user.role === 'admin' || user.role === 'super_admin',
                        isSuperAdmin: user.role === 'super_admin'
                    });
                } catch (error) {
                    console.error('Failed to fetch user profile', error);
                    localStorage.removeItem('auth_token');
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

    signIn: async (credentials: LoginCredentials) => {
        try {
            const response = await api.post('/auth/login.php', credentials);
            const { token, user } = response.data;

            localStorage.setItem('auth_token', token);
            set({
                user,
                isAuthenticated: true,
                isAdmin: user.role === 'admin' || user.role === 'super_admin',
                isSuperAdmin: user.role === 'super_admin'
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

            localStorage.setItem('auth_token', token);
            set({
                user,
                isAuthenticated: true,
                isAdmin: user.role === 'admin' || user.role === 'super_admin',
                isSuperAdmin: user.role === 'super_admin'
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
            localStorage.removeItem('auth_token');
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
            set({
                user,
                isAuthenticated: true,
                isAdmin: user.role === 'admin' || user.role === 'super_admin',
                isSuperAdmin: user.role === 'super_admin'
            });
        } catch (error) {
            console.error('Failed to refresh profile', error);
        }
    },
}));

