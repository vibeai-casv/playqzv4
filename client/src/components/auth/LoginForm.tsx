import React, { useState } from 'react';
import axios from 'axios';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { toast } from 'sonner';
import { Loader2, ArrowRight } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { loginSchema, type LoginFormData } from '../../lib/validations/auth';

import { useAuth } from '../../hooks/useAuth';

export const LoginForm: React.FC = () => {
    const [loading, setLoading] = useState(false);
    const [errorDetail, setErrorDetail] = useState<{
        message: string;
        url?: string;
        baseUrl?: string;
        method?: string;
        status?: number;
        code?: string;
        data?: any;
    } | null>(null);
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
            setErrorDetail(null);

            await signIn({
                email: data.identifier,
                password: data.password,
                rememberMe: data.rememberMe
            });

            toast.success('Welcome back!');
            navigate('/dashboard');
        } catch (error: any) {
            console.error('Login error detail:', error);

            let message = 'Failed to sign in';
            let diagnostic: any = { message };

            if (axios.isAxiosError(error)) {
                diagnostic = {
                    message: error.message,
                    code: error.code,
                    url: error.config?.url,
                    baseUrl: error.config?.baseURL,
                    method: error.config?.method?.toUpperCase(),
                    status: error.response?.status,
                    data: error.response?.data
                };
                message = error.message === 'Network Error'
                    ? `Network Error (Request to ${error.config?.baseURL}${error.config?.url} failed)`
                    : error.message;
            } else if (error instanceof Error) {
                diagnostic = { message: error.message };
                message = error.message;
            }

            setErrorDetail(diagnostic);
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

            {errorDetail && (
                <div className="mt-4 p-4 rounded-lg bg-destructive/10 border border-destructive/20 text-xs font-mono overflow-auto max-h-48 space-y-2">
                    <p className="font-bold text-destructive">Diagnostic Information:</p>
                    <div className="grid grid-cols-[80px_1fr] gap-x-2 gap-y-1 text-muted-foreground">
                        <span>Message:</span> <span className="text-foreground">{errorDetail.message}</span>
                        {errorDetail.code && <><span>Code:</span> <span className="text-foreground">{errorDetail.code}</span></>}
                        {errorDetail.method && <><span>Method:</span> <span className="text-foreground">{errorDetail.method}</span></>}
                        {errorDetail.baseUrl && <><span>Base URL:</span> <span className="text-foreground">{errorDetail.baseUrl}</span></>}
                        {errorDetail.url && <><span>Endpoint:</span> <span className="text-foreground">{errorDetail.url}</span></>}
                        {errorDetail.status && <><span>Status:</span> <span className="text-foreground">{errorDetail.status}</span></>}
                    </div>
                    {errorDetail.data && (
                        <div className="mt-2 pt-2 border-t border-destructive/10">
                            <p className="mb-1 text-destructive/70 italic">Response Data:</p>
                            <pre className="p-2 bg-black/20 rounded whitespace-pre-wrap">
                                {JSON.stringify(errorDetail.data, null, 2)}
                            </pre>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};
