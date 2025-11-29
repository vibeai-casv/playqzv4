import React, { useEffect } from 'react';
import { AuthLayout } from '../../components/auth/AuthLayout';
import { LoginForm } from '../../components/auth/LoginForm';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';

export const Login: React.FC = () => {
    const { isAuthenticated, isLoading } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isLoading && isAuthenticated) {
            navigate('/dashboard', { replace: true });
        }
    }, [isAuthenticated, isLoading, navigate]);

    if (isLoading && isAuthenticated) return null;

    return (
        <AuthLayout>
            <LoginForm />
            <div className="mt-6 text-center text-sm text-slate-400">
                Don't have an account?{' '}
                <Link to="/signup" className="font-medium text-indigo-400 hover:text-indigo-300 hover:underline transition-all">
                    Create one now
                </Link>
            </div>
        </AuthLayout>
    );
};
