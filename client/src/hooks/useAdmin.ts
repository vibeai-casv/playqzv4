import { useState, useCallback } from 'react';
import api from '../lib/api';
import { Question, MediaFile, AnalyticsData } from '../types';

export function useAdmin() {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    // User Management
    const fetchUsers = async (filters?: any) => {
        setIsLoading(true);
        setError(null);
        try {
            const params = { ...filters };
            const response = await api.get('/admin/users.php', { params });
            return { users: response.data.users, total: response.data.total };
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const fetchActivityLogs = async (filters?: {
        type?: string;
        userId?: string;
        startDate?: Date;
        endDate?: Date;
        search?: string;
        limit?: number;
        offset?: number;
    }) => {
        setIsLoading(true);
        setError(null);
        try {
            const params: any = { ...filters };
            if (filters?.startDate) params.startDate = filters.startDate.toISOString();
            if (filters?.endDate) params.endDate = filters.endDate.toISOString();

            const response = await api.get('/analytics/logs.php', { params });
            return { logs: response.data.logs, total: response.data.total };
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const fetchUserActivity = async (userId: string) => {
        setIsLoading(true);
        setError(null);
        try {
            const response = await api.get('/admin/user_activity.php', { params: { userId } });
            return {
                logins: response.data.logins,
                attempts: response.data.attempts,
                activity: response.data.activity
            };
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const toggleUserStatus = async (userId: string, disabled: boolean, reason?: string) => {
        setIsLoading(true);
        setError(null);
        try {
            await api.post('/admin/toggle_user.php', { userId, disabled, reason });
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const updateUserRole = async (userId: string, role: 'user' | 'admin') => {
        setIsLoading(true);
        setError(null);
        try {
            await api.post('/admin/update_role.php', { user_id: userId, role });
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    // Question Management
    const fetchQuestions = useCallback(async (filters?: {
        category?: string;
        difficulty?: string;
        type?: string;
        status?: 'active' | 'inactive' | 'draft';
        ai_generated?: boolean;
        search?: string;
        limit?: number;
        offset?: number;
        sortBy?: string;
        sortOrder?: 'asc' | 'desc';
    }) => {
        setIsLoading(true);
        setError(null);
        try {
            const response = await api.get('/questions/list.php', { params: { ...filters, _t: Date.now() } });
            return { questions: response.data.data as Question[], total: response.data.total };
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    }, []);

    const createQuestion = async (question: Partial<Question>) => {
        setIsLoading(true);
        setError(null);
        try {
            const response = await api.post('/questions/create.php', question);
            return response.data as Question;
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const updateQuestion = async (id: string, updates: Partial<Question>) => {
        setIsLoading(true);
        setError(null);
        try {
            const response = await api.post('/questions/update.php', { id, ...updates });
            return response.data.data as Question;
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const deleteQuestion = async (id: string) => {
        setIsLoading(true);
        setError(null);
        try {
            await api.post('/questions/delete.php', { id });
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const generateQuestions = async (params: { topic: string; count: number; difficulty: string; type: string }) => {
        setIsLoading(true);
        setError(null);
        try {
            const response = await api.post('/admin/generate_questions.php', params);
            return response.data;
        } catch (err: any) {
            const message = err.response?.data?.error || (err instanceof Error ? err.message : 'An error occurred');
            setError(message);
            throw new Error(message);
        } finally {
            setIsLoading(false);
        }
    };

    // Media Management
    const fetchMedia = async (filters?: {
        type?: string;
        limit?: number;
        offset?: number;
    }) => {
        setIsLoading(true);
        setError(null);
        try {
            const response = await api.get('/media/list.php', { params: filters });
            return { media: response.data.media as MediaFile[], total: response.data.total };
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const uploadMedia = async (file: File, type: string, description?: string) => {
        setIsLoading(true);
        setError(null);
        try {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', type);
            if (description) formData.append('description', description);

            const response = await api.post('/media/upload.php', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            return response.data.media;
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const deleteMedia = async (id: string) => {
        setIsLoading(true);
        setError(null);
        try {
            await api.post('/media/delete.php', { id });
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    // Analytics
    const fetchAnalytics = async (startDate?: Date, endDate?: Date) => {
        setIsLoading(true);
        setError(null);
        try {
            const params: any = {};
            if (startDate) params.startDate = startDate.toISOString();
            if (endDate) params.endDate = endDate.toISOString();

            const response = await api.get('/admin/analytics.php', { params });
            return response.data as AnalyticsData;
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const fetchDailyStats = async () => { return []; };
    const fetchCategoryStats = async () => { return []; };
    const fetchRegistrationStats = async () => { return []; };

    const fetchMetadata = async () => {
        setIsLoading(true);
        setError(null);
        try {
            const response = await api.get('/metadata/list.php');
            return response.data;
        } catch (err: unknown) {
            const message = err instanceof Error ? err.message : 'An error occurred';
            setError(message);
            // Don't throw, just return defaults if fails
            return { categories: [], types: [], difficulties: [], tags: [] };
        } finally {
            setIsLoading(false);
        }
    };


    return {
        isLoading,
        error,

        // User Management
        fetchUsers,
        fetchUserActivity,
        fetchActivityLogs,
        toggleUserStatus,
        updateUserRole,

        // Question Management
        fetchQuestions,
        createQuestion,
        updateQuestion,
        deleteQuestion,
        generateQuestions,

        // Media Management
        fetchMedia,
        uploadMedia,
        deleteMedia,

        // Analytics
        fetchAnalytics,
        fetchDailyStats,
        fetchCategoryStats,
        fetchRegistrationStats,
        fetchMetadata,
    };
}

