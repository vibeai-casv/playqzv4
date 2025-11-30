import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { toast } from 'sonner';
import { Loader2, ArrowRight, CheckCircle2 } from 'lucide-react';
import { signupSchema, type SignupFormData } from '../../lib/validations/auth';

import { AIDisclaimerModal } from './AIDisclaimerModal';
import { useAuth } from '../../hooks/useAuth';

export const SignupForm: React.FC = () => {
    const [loading, setLoading] = useState(false);
    const [showDisclaimer, setShowDisclaimer] = useState(false);
    const { signUp } = useAuth();

    const form = useForm<SignupFormData>({
        resolver: zodResolver(signupSchema),
        defaultValues: {
            category: 'student',
            terms_accepted: undefined,
            mobile_number: '',
            email: '',
            password: '',
        },
    });



    const onSubmit = async (data: SignupFormData) => {
        try {
            setLoading(true);

            await signUp({
                email: data.email,
                password: data.password,
                name: data.full_name,
                phone: data.mobile_number,
                category: data.category,
                district: data.district,
                institution_name: data.institution_name,
                course_of_study: data.course_of_study,
                class_level: data.class_level,
            });

            toast.success('Account created successfully!');
            // navigate('/dashboard'); // Handled by auth state change usually, or explicit redirect
        } catch (error: unknown) {
            const message = error instanceof Error ? error.message : 'Failed to sign up';
            toast.error(message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            <AIDisclaimerModal
                isOpen={showDisclaimer}
                onAccept={() => {
                    form.setValue('terms_accepted', true);
                    setShowDisclaimer(false);
                    toast.success('Terms accepted');
                }}
                onDecline={() => setShowDisclaimer(false)}
            />

            <div className="w-full space-y-6">
                <div className="text-center space-y-2">
                    <h1 className="text-2xl font-bold tracking-tight text-foreground">
                        Create Account
                    </h1>
                    <p className="text-muted-foreground text-sm">
                        Join the AI-powered learning revolution
                    </p>
                </div>

                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">

                    <div className="space-y-4">
                        {/* Full Name */}
                        <div>
                            <input
                                {...form.register('full_name')}
                                placeholder="Full Name"
                                className="input-vibeai"
                            />
                            {form.formState.errors.full_name && (
                                <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.full_name.message}</p>
                            )}
                        </div>

                        {/* Email */}
                        <div>
                            <input
                                {...form.register('email')}
                                type="email"
                                placeholder="Email Address"
                                className="input-vibeai"
                            />
                            {form.formState.errors.email && (
                                <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.email.message}</p>
                            )}
                        </div>

                        {/* Mobile */}
                        <div>
                            <div className="relative">
                                <span className="absolute left-4 top-3.5 text-slate-400">+91</span>
                                <input
                                    {...form.register('mobile_number')}
                                    placeholder="Mobile Number"
                                    className="w-full pl-12 pr-4 py-3 rounded-lg bg-input border border-border text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                />
                            </div>
                            {form.formState.errors.mobile_number && (
                                <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.mobile_number.message}</p>
                            )}
                        </div>

                        {/* Password */}
                        <div>
                            <input
                                {...form.register('password')}
                                type="password"
                                placeholder="Password (min 6 chars)"
                                className="input-vibeai"
                            />
                            {form.formState.errors.password && (
                                <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.password.message}</p>
                            )}
                        </div>

                        {/* Category & District Row */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <select
                                    {...form.register('category')}
                                    className="input-vibeai appearance-none"
                                >
                                    <option value="student" className="bg-card">Student</option>
                                    <option value="educator" className="bg-card">Educator</option>
                                    <option value="other" className="bg-card">Other</option>
                                </select>
                            </div>
                            <div>
                                <input
                                    {...form.register('district')}
                                    placeholder="District"
                                    className="input-vibeai"
                                />
                                {form.formState.errors.district && (
                                    <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.district.message}</p>
                                )}
                            </div>
                        </div>

                        {/* Institution */}
                        <div>
                            <input
                                {...form.register('institution_name')}
                                placeholder="Institution Name"
                                className="input-vibeai"
                            />
                            {form.formState.errors.institution_name && (
                                <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.institution_name.message}</p>
                            )}
                        </div>

                        {/* Course & Class Row (Only for Students) */}
                        {form.watch('category') === 'student' && (
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <input
                                        {...form.register('course_of_study')}
                                        placeholder="Course"
                                        className="input-vibeai"
                                    />
                                    {form.formState.errors.course_of_study && (
                                        <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.course_of_study.message}</p>
                                    )}
                                </div>
                                <div>
                                    <input
                                        {...form.register('class_level')}
                                        placeholder="Class/Year"
                                        className="input-vibeai"
                                    />
                                    {form.formState.errors.class_level && (
                                        <p className="text-xs text-destructive mt-1 ml-1">{form.formState.errors.class_level.message}</p>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Terms Checkbox */}
                        <div className="flex items-start gap-3 pt-2">
                            <div className="relative flex items-center mt-1">
                                <input
                                    type="checkbox"
                                    {...form.register('terms_accepted')}
                                    className="peer sr-only"
                                    id="terms"
                                />
                                <div
                                    onClick={() => setShowDisclaimer(true)}
                                    className={`w-5 h-5 border-2 rounded cursor-pointer transition-colors ${form.watch('terms_accepted')
                                        ? 'bg-primary border-primary'
                                        : 'border-border bg-input'
                                        }`}
                                ></div>
                                {form.watch('terms_accepted') && (
                                    <CheckCircle2 className="absolute w-3.5 h-3.5 text-white left-0.5 top-0.5 pointer-events-none" />
                                )}
                            </div>
                            <label htmlFor="terms" className="text-sm text-muted-foreground cursor-pointer select-none">
                                I agree to the <span className="text-primary hover:text-primary/80 hover:underline" onClick={() => setShowDisclaimer(true)}>AI Content Disclaimer</span> and Terms of Service
                            </label>
                        </div>
                        {form.formState.errors.terms_accepted && (
                            <p className="text-xs text-destructive ml-1">{form.formState.errors.terms_accepted.message}</p>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={loading}
                        className="btn-vibeai w-full flex items-center justify-center gap-2"
                    >
                        {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : (
                            <>
                                Create Account <ArrowRight className="w-4 h-4" />
                            </>
                        )}
                    </button>


                </form>
            </div>
        </>
    );
};


