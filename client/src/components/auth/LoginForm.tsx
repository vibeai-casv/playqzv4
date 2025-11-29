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
                <h1 className="text-2xl font-bold tracking-tight text-white">
                    Welcome Back
                </h1>
                <p className="text-slate-400 text-sm">
                    Sign in to access your personalized AI learning path
                </p>
            </div>

            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">

                <div className="space-y-4">
                    <div>
                        <input
                            {...form.register('identifier')}
                            placeholder="Email or Mobile Number"
                            className="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-slate-700 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all"
                        />
                        {form.formState.errors.identifier && (
                            <p className="text-xs text-red-400 mt-1 ml-1">{form.formState.errors.identifier.message}</p>
                        )}
                    </div>
                    <div>
                        <input
                            {...form.register('password')}
                            type="password"
                            placeholder="Password"
                            className="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-slate-700 text-white placeholder:text-slate-500 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all"
                        />
                        {form.formState.errors.password && (
                            <p className="text-xs text-red-400 mt-1 ml-1">{form.formState.errors.password.message}</p>
                        )}
                    </div>
                </div>

                <div className="flex items-center justify-between">
                    <label className="flex items-center gap-2 cursor-pointer group">
                        <input
                            type="checkbox"
                            {...form.register('rememberMe')}
                            className="rounded border-slate-600 bg-slate-800 text-indigo-500 focus:ring-indigo-500/50 focus:ring-offset-0"
                        />
                        <span className="text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Remember me</span>
                    </label>
                    <a href="#" className="text-sm text-indigo-400 hover:text-indigo-300 hover:underline transition-colors">Forgot password?</a>
                </div>

                <button
                    type="submit"
                    disabled={loading}
                    className="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white py-3.5 rounded-xl font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:scale-[1.02] active:scale-[0.98]"
                >
                    {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : (
                        <>
                            Sign In <ArrowRight className="w-4 h-4" />
                        </>
                    )}
                </button>

            </form>
        </div>
    );
};
