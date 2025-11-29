import { create } from 'zustand';
import api from '../lib/api';
import { User, LoginCredentials, SignupData } from '../types';

interface AuthState {
    user: User | null;
    isLoading: boolean;
    isAuthenticated: boolean;
    isAdmin: boolean;
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
                        isAdmin: user.role === 'admin'
                    });
                } catch (error) {
                    console.error('Failed to fetch user profile', error);
                    localStorage.removeItem('auth_token');
                    set({ user: null, isAuthenticated: false, isAdmin: false });
                }
            } else {
                set({ user: null, isAuthenticated: false, isAdmin: false });
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
                isAdmin: user.role === 'admin'
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
                isAdmin: user.role === 'admin'
            });
        } catch (error) {
            console.error('Signup failed:', error);
            throw error;
        }
    },

    signOut: async () => {
        localStorage.removeItem('auth_token');
        set({ user: null, isAuthenticated: false, isAdmin: false });
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
                isAdmin: user.role === 'admin'
            });
        } catch (error) {
            console.error('Failed to refresh profile', error);
        }
    },
}));

