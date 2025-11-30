import React, { useEffect } from 'react';
import { AuthLayout } from '../../components/auth/AuthLayout';
import { SignupForm } from '../../components/auth/SignupForm';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';

export const Signup: React.FC = () => {
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
            <SignupForm />
            <div className="mt-6 text-center text-sm text-muted-foreground">
                Already have an account?{' '}
                <Link to="/login" className="font-medium text-primary hover:text-primary/80 hover:underline transition-all">
                    Sign in instead
                </Link>
            </div>
        </AuthLayout>
    );
};
