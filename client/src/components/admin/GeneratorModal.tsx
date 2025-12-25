import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Sparkles, Loader2, X, Brain, Target, Layers, Info, CheckCircle2 } from 'lucide-react';
import { toast } from 'sonner';
import { useAdmin } from '../../hooks/useAdmin';
import { aiGenerationSchema, type AIGenerationFormData } from '../../lib/validations/admin';
import { cn } from '../../lib/utils';

interface GeneratorModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
}

export function GeneratorModal({ isOpen, onClose, onSuccess }: GeneratorModalProps) {
    const { generateQuestions, isLoading } = useAdmin();

    const form = useForm<AIGenerationFormData>({
        resolver: zodResolver(aiGenerationSchema),
        defaultValues: {
            topic: '',
            count: 5,
            difficulty: 'medium',
            type: 'text_mcq',
        }
    });

    const onSubmit = async (data: AIGenerationFormData) => {
        try {
            const result = await generateQuestions(data);
            if (result.success) {
                toast.success(`Successfully generated ${result.count} questions!`, {
                    description: "They have been added to your bank as drafts.",
                    icon: <CheckCircle2 className="w-5 h-5 text-green-500" />
                });
                onSuccess();
                onClose();
            } else {
                throw new Error(result.error || 'Failed to generate questions');
            }
        } catch (error: any) {
            console.error(error);
            toast.error(error.message || 'Generation failed. Please check your API key and try again.');
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
            <div className="bg-white dark:bg-gray-950 w-full max-w-2xl rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden animate-in zoom-in-95 duration-300">
                {/* Header */}
                <div className="relative p-6 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-950/20 dark:to-indigo-950/20">
                    <div className="flex items-center gap-3">
                        <div className="p-2.5 bg-purple-600 rounded-xl shadow-lg shadow-purple-500/30">
                            <Sparkles className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 className="text-2xl font-bold text-gray-900 dark:text-white">AI Question Generator</h2>
                            <p className="text-sm text-gray-500 dark:text-gray-400">Powered by Gemini 1.5 Flash</p>
                        </div>
                    </div>
                    <button
                        onClick={onClose}
                        className="absolute top-6 right-6 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800 rounded-full transition-all"
                    >
                        <X className="w-5 h-5" />
                    </button>
                </div>

                <form onSubmit={form.handleSubmit(onSubmit)} className="p-8 space-y-8">
                    {/* Topic Field */}
                    <div className="space-y-3">
                        <label className="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <Brain className="w-4 h-4 text-purple-500" />
                            What is the main topic?
                        </label>
                        <div className="relative">
                            <input
                                {...form.register('topic')}
                                className={cn(
                                    "w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all",
                                    form.formState.errors.topic && "border-red-500 focus:ring-red-500"
                                )}
                                placeholder="e.g. Quantum Physics, React Hooks, Great Wall of China..."
                            />
                            <div className="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2 text-xs text-gray-400 font-medium">
                                <Info className="w-3 h-3" />
                                Be specific
                            </div>
                        </div>
                        {form.formState.errors.topic && (
                            <p className="text-xs font-medium text-red-500 pl-1 animate-in slide-in-from-top-1">{form.formState.errors.topic.message}</p>
                        )}
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {/* Type Field */}
                        <div className="space-y-3">
                            <label className="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <Layers className="w-4 h-4 text-blue-500" />
                                Question Type
                            </label>
                            <select
                                {...form.register('type')}
                                className="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all appearance-none cursor-pointer"
                            >
                                <option value="text_mcq">Text-based MCQ</option>
                                <option value="image_identify_logo">Logo Identification</option>
                                <option value="personality">Personality ID</option>
                                <option value="true_false">True / False</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>

                        {/* Difficulty Field */}
                        <div className="space-y-3">
                            <label className="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <Target className="w-4 h-4 text-red-500" />
                                Difficulty Level
                            </label>
                            <div className="flex p-1 bg-gray-100 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                                {['easy', 'medium', 'hard'].map((level) => (
                                    <button
                                        key={level}
                                        type="button"
                                        onClick={() => form.setValue('difficulty', level as any)}
                                        className={cn(
                                            "flex-1 py-1.5 px-3 text-xs font-bold rounded-lg capitalize transition-all",
                                            form.watch('difficulty') === level
                                                ? "bg-white dark:bg-gray-800 text-purple-600 dark:text-purple-400 shadow-sm"
                                                : "text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                                        )}
                                    >
                                        {level}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="space-y-3">
                        <div className="flex justify-between items-center">
                            <label className="text-sm font-semibold text-gray-700 dark:text-gray-300">How many questions?</label>
                            <span className="text-lg font-bold text-purple-600 dark:text-purple-400">{form.watch('count')}</span>
                        </div>
                        <input
                            type="range"
                            {...form.register('count', { valueAsNumber: true })}
                            min={1}
                            max={20}
                            className="w-full h-2 bg-gray-200 dark:bg-gray-800 rounded-lg appearance-none cursor-pointer accent-purple-600"
                        />
                        <div className="flex justify-between text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                            <span>1 Question</span>
                            <span>20 Questions</span>
                        </div>
                    </div>

                    {/* Footer / Actions */}
                    <div className="flex items-center justify-between pt-4 gap-4">
                        <div className="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 italic">
                            <Sparkles className="w-3 h-3 text-yellow-500 animate-pulse" />
                            Note: Questions will be saved as drafts for your review.
                        </div>
                        <div className="flex gap-3">
                            <button
                                type="button"
                                onClick={onClose}
                                className="px-6 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={isLoading}
                                className="relative flex items-center justify-center gap-2 px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl shadow-lg shadow-purple-500/25 font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed group overflow-hidden"
                            >
                                {isLoading ? (
                                    <>
                                        <Loader2 className="w-5 h-5 animate-spin" />
                                        Generating...
                                    </>
                                ) : (
                                    <>
                                        <div className="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300" />
                                        <Sparkles className="w-5 h-5 group-hover:rotate-12 transition-transform" />
                                        <span>Create Questions</span>
                                    </>
                                )}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
}
