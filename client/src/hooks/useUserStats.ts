import { useState, useCallback } from 'react';

import api from '../lib/api';
import { useAuth } from './useAuth';

export interface UserStats {
    total_attempts: number;
    completed_attempts: number;
    average_score: number;
    best_score: number;
    total_time_spent: number;
    completion_rate: number;
}

export interface QuizAttempt {
    id: string;
    score: number;
    total_questions: number;
    correct_answers: number;
    time_spent_seconds: number;
    created_at: string;
    status: 'in_progress' | 'completed' | 'abandoned' | 'expired';
    config: {
        categories: string[];
        difficulty: string;
    };
}

export function useUserStats() {
    const { user } = useAuth();
    const [stats, setStats] = useState<UserStats | null>(null);
    const [recentAttempts, setRecentAttempts] = useState<QuizAttempt[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchStats = useCallback(async () => {
        if (!user) return;
        setLoading(true);
        setError(null);
        try {
            const response = await api.get('/user/stats.php');
            const { stats, recentAttempts } = response.data;

            setStats(stats);
            setRecentAttempts(recentAttempts || []);

        } catch (err: any) {
            console.error('Error fetching user stats:', err);
            setError(err.message);
        } finally {
            setLoading(false);
        }
    }, [user]);

    return {
        stats,
        recentAttempts,
        loading,
        error,
        fetchStats
    };
}
