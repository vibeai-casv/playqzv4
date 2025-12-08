import { useEffect, useRef, useCallback } from 'react';
import { useAuth } from '../hooks/useAuth';
import { useNavigate } from 'react-router-dom';
import { toast } from 'sonner';

const IDLE_TIMEOUT = 10 * 60 * 1000; // 10 minutes

export function SessionManager() {
    const { isAuthenticated, logout } = useAuth();
    const navigate = useNavigate();
    const timeoutRef = useRef<NodeJS.Timeout | null>(null);
    const lastActivityRef = useRef<number>(Date.now());

    const handleLogout = useCallback(async () => {
        if (isAuthenticated) {
            await logout();
            navigate('/login');
            toast.info('Session expired due to inactivity');
        }
    }, [isAuthenticated, logout, navigate]);

    const resetTimer = useCallback(() => {
        if (timeoutRef.current) {
            clearTimeout(timeoutRef.current);
        }
        if (isAuthenticated) {
            timeoutRef.current = setTimeout(handleLogout, IDLE_TIMEOUT);
        }
    }, [isAuthenticated, handleLogout]);

    const handleActivity = useCallback(() => {
        // Throttle activity updates to once per second
        const now = Date.now();
        if (now - lastActivityRef.current > 1000) {
            lastActivityRef.current = now;
            resetTimer();
        }
    }, [resetTimer]);

    useEffect(() => {
        if (!isAuthenticated) {
            if (timeoutRef.current) {
                clearTimeout(timeoutRef.current);
            }
            return;
        }

        // Initial timer
        resetTimer();

        // Events to listen for
        const events = ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart', 'click'];

        // Add listeners
        events.forEach(event => {
            window.addEventListener(event, handleActivity);
        });

        return () => {
            // Cleanup
            if (timeoutRef.current) {
                clearTimeout(timeoutRef.current);
            }
            events.forEach(event => {
                window.removeEventListener(event, handleActivity);
            });
        };
    }, [isAuthenticated, handleActivity, resetTimer]);

    return null;
}
