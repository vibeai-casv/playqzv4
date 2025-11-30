import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';

import api from '../../lib/api';
import { useAuth } from '../../hooks/useAuth';
import { toast } from 'sonner';
import { Loader2, Save, User, Building, Phone, BookOpen, FileText } from 'lucide-react';
import { cn } from '../../lib/utils';

const profileSchema = z.object({
    name: z.string().min(2, 'Name must be at least 2 characters'),
    phone: z.string().optional().or(z.literal('')),
    institution: z.string().optional().or(z.literal('')),
    category: z.enum(['student', 'professional', 'educator', 'hobbyist']),
    bio: z.string().max(500, 'Bio must be less than 500 characters').optional().or(z.literal('')),
});

type ProfileFormData = z.infer<typeof profileSchema>;

export function Profile() {
    const { user, refreshProfile } = useAuth();
    const [loading, setLoading] = useState(false);
    const [initialLoading, setInitialLoading] = useState(true);

    const {
        register,
        handleSubmit,
        setValue,
        formState: { errors }
    } = useForm<ProfileFormData>({
        resolver: zodResolver(profileSchema),
        defaultValues: {
            category: 'student'
        }
    });

    useEffect(() => {
        if (user) {
            setValue('name', user.name);
            setValue('phone', user.phone || '');
            setValue('institution', user.institution || '');
            setValue('category', (user.category as any) || 'student');
            setValue('bio', user.bio || '');
            setInitialLoading(false);
        }
    }, [user, setValue]);

    const onSubmit = async (data: ProfileFormData) => {
        if (!user) return;
        setLoading(true);
        try {
            await api.post('/profile/update.php', {
                name: data.name,
                phone: data.phone || null,
                institution: data.institution || null,
                category: data.category,
                bio: data.bio || null,
            });

            await refreshProfile();
            toast.success('Profile updated successfully');
        } catch (error) {
            console.error('Error updating profile:', error);
            toast.error('Failed to update profile');
        } finally {
            setLoading(false);
        }
    };

    if (initialLoading) {
        return (
            <div className="flex items-center justify-center min-h-[60vh]">
                <Loader2 className="w-8 h-8 animate-spin text-primary-600" />
            </div>
        );
    }

    return (
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 className="text-3xl font-bold text-white mb-8">Profile Settings</h1>

            <div className="bg-slate-900/50 backdrop-blur-sm rounded-xl shadow-xl border border-white/10 overflow-hidden">
                <div className="p-6 sm:p-8">
                    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
                        {/* Name */}
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-slate-300 mb-1">
                                Full Name
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <User className="h-5 w-5 text-slate-500" />
                                </div>
                                <input
                                    {...register('name')}
                                    type="text"
                                    className={cn(
                                        "block w-full pl-10 pr-3 py-2 bg-black/20 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white placeholder-slate-500 transition-colors",
                                        errors.name ? "border-red-500/50" : "border-white/10"
                                    )}
                                    placeholder="Your full name"
                                />
                            </div>
                            {errors.name && (
                                <p className="mt-1 text-sm text-red-400">{errors.name.message}</p>
                            )}
                        </div>

                        {/* Phone */}
                        <div>
                            <label htmlFor="phone" className="block text-sm font-medium text-slate-300 mb-1">
                                Phone Number
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Phone className="h-5 w-5 text-slate-500" />
                                </div>
                                <input
                                    {...register('phone')}
                                    type="tel"
                                    className="block w-full pl-10 pr-3 py-2 bg-black/20 border border-white/10 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white placeholder-slate-500 transition-colors"
                                    placeholder="+91 9876543210"
                                />
                            </div>
                            {errors.phone && (
                                <p className="mt-1 text-sm text-red-400">{errors.phone.message}</p>
                            )}
                        </div>

                        {/* Institution */}
                        <div>
                            <label htmlFor="institution" className="block text-sm font-medium text-slate-300 mb-1">
                                Institution / Organization
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Building className="h-5 w-5 text-slate-500" />
                                </div>
                                <input
                                    {...register('institution')}
                                    type="text"
                                    className="block w-full pl-10 pr-3 py-2 bg-black/20 border border-white/10 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white placeholder-slate-500 transition-colors"
                                    placeholder="University or Company name"
                                />
                            </div>
                        </div>

                        {/* Category */}
                        <div>
                            <label htmlFor="category" className="block text-sm font-medium text-slate-300 mb-1">
                                Category
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <BookOpen className="h-5 w-5 text-slate-500" />
                                </div>
                                <select
                                    {...register('category')}
                                    className="block w-full pl-10 pr-3 py-2 bg-black/20 border border-white/10 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white transition-colors appearance-none"
                                >
                                    <option value="student" className="bg-slate-900">Student</option>
                                    <option value="professional" className="bg-slate-900">Professional</option>
                                    <option value="educator" className="bg-slate-900">Educator</option>
                                    <option value="hobbyist" className="bg-slate-900">Hobbyist</option>
                                </select>
                            </div>
                        </div>

                        {/* Bio */}
                        <div>
                            <label htmlFor="bio" className="block text-sm font-medium text-slate-300 mb-1">
                                Bio
                            </label>
                            <div className="relative">
                                <div className="absolute top-3 left-3 pointer-events-none">
                                    <FileText className="h-5 w-5 text-slate-500" />
                                </div>
                                <textarea
                                    {...register('bio')}
                                    rows={4}
                                    className="block w-full pl-10 pr-3 py-2 bg-black/20 border border-white/10 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white placeholder-slate-500 transition-colors resize-none"
                                    placeholder="Tell us a bit about yourself..."
                                />
                            </div>
                            {errors.bio && (
                                <p className="mt-1 text-sm text-red-400">{errors.bio.message}</p>
                            )}
                        </div>

                        {/* Submit Button */}
                        <div className="pt-4 border-t border-white/10 flex justify-end">
                            <button
                                type="submit"
                                disabled={loading}
                                className="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-lg shadow-indigo-500/20 text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            >
                                {loading ? (
                                    <>
                                        <Loader2 className="w-5 h-5 mr-2 animate-spin" />
                                        Saving...
                                    </>
                                ) : (
                                    <>
                                        <Save className="w-5 h-5 mr-2" />
                                        Save Changes
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
