import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { toast } from 'sonner';
import { Loader2, ArrowRight } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { loginSchema, type LoginFormData } from '../../lib/validations/auth';

import { useAuth } from '../../hooks/useAuth';

export const LoginForm: React.FC = () => {
    const [loading, setLoading] = useState(false);
    const { signIn } = useAuth();
    const navigate = useNavigate();

    const form = useForm<LoginFormData>({
        resolver: zodResolver(loginSchema),
        defaultValues: {
            rememberMe: false,
            identifier: '',
            password: '',
        },
    });

    const onSubmit = async (data: LoginFormData) => {
        try {
            setLoading(true);

            await signIn({
                email: data.identifier,
                password: data.password
            });

            toast.success('Welcome back!');
            navigate('/dashboard');
        } catch (error: unknown) {
            const message = error instanceof Error ? error.message : 'Failed to sign in';
            toast.error(message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="w-full space-y-6">
            <div className="text-center space-y-2">
                <h1 className="text-2xl font-bold tracking-tight text-foreground">
                    Welcome Back
                </h1>
                <p className="text-muted-foreground text-sm">
                    Sign in to access your personalized AI learning path
                </p>
            </div>

            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">

                <div className="space-y-4">
                    <div>
                        <input
                            {...form.register('identifier')}
                            placeholder="Email or Mobile Number"
                            className="input-vibeai"
                        />
                        {form.formState.errors.identifier && (
                            <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.identifier.message}</p>
                        )}
                    </div>
                    <div>
                        <input
                            {...form.register('password')}
                            type="password"
                            placeholder="Password"
                            className="input-vibeai"
                        />
                        {form.formState.errors.password && (
                            <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.password.message}</p>
                        )}
                    </div>
                </div>

                <div className="flex items-center justify-between">
                    <label className="flex items-center gap-2 cursor-pointer group">
                        <input
                            type="checkbox"
                            {...form.register('rememberMe')}
                            className="rounded border-border bg-input text-primary focus:ring-primary/50 focus:ring-offset-0"
                        />
                        <span className="text-sm text-muted-foreground group-hover:text-foreground transition-colors">Remember me</span>
                    </label>
                    <a href="#" className="text-sm text-primary hover:text-primary/80 hover:underline transition-colors">Forgot password?</a>
                </div>

                <div className="flex w-full gap-2">
                    <button
                        type="button"
                        onClick={() => navigate('/demo')}
                        className="btn-vibeai w-1/2 flex items-center justify-center gap-2"
                    >
                        Demo
                    </button>
                    <button
                        type="submit"
                        disabled={loading}
                        className="btn-vibeai w-1/2 flex items-center justify-center gap-2"
                    >
                        {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : (
                            <>
                                Sign In <ArrowRight className="w-4 h-4" />
                            </>
                        )}
                    </button>
                </div>

            </form>
        </div>
    );
};
