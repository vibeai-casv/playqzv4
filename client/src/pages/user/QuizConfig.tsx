import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { z } from 'zod';
import { zodResolver } from '@hookform/resolvers/zod';
import {
    CheckCircle,
    XCircle,
    Loader2,
    Clock,
    BarChart2,
    BookOpen,
    Bot,
    Cpu,
    Brain,
    Users,
    PlayCircle,
    AlertCircle,
    Shield,
    Zap
} from 'lucide-react';
import { useQuiz } from '../../hooks/useQuiz';
import api from '../../lib/api';

import { cn } from '../../lib/utils';

// ----- Validation Schema -----
const quizConfigSchema = z.object({
    numQuestions: z.enum(['5', '10', '20', '50']),
    difficulty: z.enum(['Easy', 'Medium', 'Hard', 'Mixed']),
    categories: z
        .array(
            z.enum([
                'Latest Developments',
                'AI Safety',
                'Robotics',
                'Quantum Computing',
                'Generative AI',
                'Personalities',
                'Brands',
            ])
        )
        .min(1, { message: 'Select at least one category' }),
});

type QuizConfig = z.infer<typeof quizConfigSchema>;

// ----- Helper Types -----
type CategoryName =
    | 'Latest Developments'
    | 'AI Safety'
    | 'Robotics'
    | 'Quantum Computing'
    | 'Generative AI'
    | 'Personalities'
    | 'Brands';

interface CategoryInfo {
    name: CategoryName;
    icon: React.ReactNode;
    available: number; // number of questions available in DB
    gradient: string;
}

const CATEGORY_DATA: CategoryInfo[] = [
    { name: 'Latest Developments', icon: <BarChart2 className="w-5 h-5" />, available: 0, gradient: 'from-blue-500 to-cyan-500' },
    { name: 'AI Safety', icon: <Shield className="w-5 h-5" />, available: 0, gradient: 'from-emerald-500 to-teal-500' },
    { name: 'Robotics', icon: <Bot className="w-5 h-5" />, available: 0, gradient: 'from-orange-500 to-amber-500' },
    { name: 'Quantum Computing', icon: <Cpu className="w-5 h-5" />, available: 0, gradient: 'from-violet-500 to-purple-500' },
    { name: 'Generative AI', icon: <Brain className="w-5 h-5" />, available: 0, gradient: 'from-pink-500 to-rose-500' },
    { name: 'Personalities', icon: <Users className="w-5 h-5" />, available: 0, gradient: 'from-indigo-500 to-blue-500' },
    { name: 'Brands', icon: <BookOpen className="w-5 h-5" />, available: 0, gradient: 'from-yellow-500 to-orange-500' },
];

export function QuizConfig() {
    const {
        register,
        handleSubmit,
        watch,
        formState: { errors, isValid },
        setValue,
    } = useForm<QuizConfig>({
        resolver: zodResolver(quizConfigSchema),
        mode: 'onChange',
        defaultValues: {
            numQuestions: '10',
            difficulty: 'Mixed',
            categories: [],
        },
    });

    const [categoryInfo, setCategoryInfo] = useState<CategoryInfo[]>(CATEGORY_DATA);
    const [loadingCounts, setLoadingCounts] = useState(false);
    const [startLoading, setStartLoading] = useState(false);
    const [startError, setStartError] = useState<string | null>(null);

    const navigate = useNavigate();
    const { generateQuiz } = useQuiz();

    // Load available question counts per category on mount
    useEffect(() => {
        const fetchCounts = async () => {
            setLoadingCounts(true);
            try {
                const response = await api.get('/questions/stats.php');
                const counts = response.data.stats;
                setCategoryInfo((prev) =>
                    prev.map((c) => ({ ...c, available: counts[c.name] ?? 0 }))
                );
            } catch (e) {
                console.error('Failed to fetch category counts', e);
            } finally {
                setLoadingCounts(false);
            }
        };
        fetchCounts();
    }, []);

    // Load saved config from localStorage
    useEffect(() => {
        const saved = localStorage.getItem('quizConfig');
        if (saved) {
            try {
                const parsed = JSON.parse(saved) as QuizConfig;
                setValue('numQuestions', parsed.numQuestions);
                setValue('difficulty', parsed.difficulty);
                setValue('categories', parsed.categories);
            } catch { }
        }
    }, [setValue]);

    const onSubmit = async (data: QuizConfig) => {
        setStartError(null);
        setStartLoading(true);
        localStorage.setItem('quizConfig', JSON.stringify(data));
        try {
            const config = {
                numQuestions: Number(data.numQuestions),
                difficulty: data.difficulty.toLowerCase() as 'easy' | 'medium' | 'hard' | 'mixed',
                categories: data.categories,
                timeLimit: Number(data.numQuestions) * 30,
                includeExplanations: true,
            };
            await generateQuiz(config);
            navigate('/take-quiz');
        } catch (e) {
            setStartError(e instanceof Error ? e.message : 'Failed to generate quiz');
        } finally {
            setStartLoading(false);
        }
    };

    const watched = watch();
    const selectedCategories = watched.categories as CategoryName[];
    const insufficient = selectedCategories.some((cat) => {
        const info = categoryInfo.find((c) => c.name === cat);
        return info && info.available < Number(watched.numQuestions);
    });

    const estimatedTime = Number(watched.numQuestions) * 30; // seconds

    return (
        <div className="max-w-5xl mx-auto p-6 space-y-8">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold text-white tracking-tight">Configure Quiz</h1>
                    <p className="text-slate-400 mt-2">Customize your assessment parameters</p>
                </div>
                <div className="hidden md:block">
                    <div className="p-3 bg-indigo-500/10 rounded-xl border border-indigo-500/20">
                        <Zap className="w-6 h-6 text-indigo-400" />
                    </div>
                </div>
            </div>

            <form onSubmit={handleSubmit(onSubmit)} className="space-y-8">
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Left Column: Settings */}
                    <div className="lg:col-span-2 space-y-8">
                        {/* Number of Questions */}
                        <section className="bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 md:p-8">
                            <h2 className="text-xl font-bold text-white mb-6 flex items-center">
                                <span className="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center mr-3 text-sm font-bold">1</span>
                                Number of Questions
                            </h2>
                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                {['5', '10', '20', '50'].map((val) => (
                                    <label
                                        key={val}
                                        className={cn(
                                            'relative flex flex-col items-center justify-center p-4 rounded-2xl cursor-pointer transition-all duration-200 border-2',
                                            watched.numQuestions === val
                                                ? 'bg-blue-500/10 border-blue-500 shadow-[0_0_20px_-5px_rgba(59,130,246,0.5)]'
                                                : 'bg-white/5 border-transparent hover:bg-white/10 hover:border-white/10'
                                        )}
                                    >
                                        <input
                                            type="radio"
                                            value={val}
                                            {...register('numQuestions')}
                                            className="hidden"
                                        />
                                        <span className={cn(
                                            "text-2xl font-bold mb-1",
                                            watched.numQuestions === val ? "text-blue-400" : "text-white"
                                        )}>{val}</span>
                                        <span className="text-xs text-slate-400 uppercase tracking-wider font-medium">Questions</span>
                                    </label>
                                ))}
                            </div>
                            {errors.numQuestions && (
                                <p className="mt-2 text-sm text-red-400">{errors.numQuestions.message}</p>
                            )}
                        </section>

                        {/* Difficulty */}
                        <section className="bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 md:p-8">
                            <h2 className="text-xl font-bold text-white mb-6 flex items-center">
                                <span className="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center mr-3 text-sm font-bold">2</span>
                                Difficulty Level
                            </h2>
                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                {['Easy', 'Medium', 'Hard', 'Mixed'].map((diff) => (
                                    <label
                                        key={diff}
                                        className={cn(
                                            'relative flex items-center justify-center p-4 rounded-2xl cursor-pointer transition-all duration-200 border-2',
                                            watched.difficulty === diff
                                                ? 'bg-purple-500/10 border-purple-500 shadow-[0_0_20px_-5px_rgba(168,85,247,0.5)]'
                                                : 'bg-white/5 border-transparent hover:bg-white/10 hover:border-white/10'
                                        )}
                                    >
                                        <input type="radio" value={diff} {...register('difficulty')} className="hidden" />
                                        <span className={cn(
                                            "font-bold",
                                            watched.difficulty === diff ? "text-purple-400" : "text-slate-300"
                                        )}>{diff}</span>
                                    </label>
                                ))}
                            </div>
                            {errors.difficulty && (
                                <p className="mt-2 text-sm text-red-400">{errors.difficulty.message}</p>
                            )}
                        </section>

                        {/* Categories */}
                        <section className="bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 md:p-8">
                            <h2 className="text-xl font-bold text-white mb-6 flex items-center">
                                <span className="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center mr-3 text-sm font-bold">3</span>
                                Topics
                            </h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {categoryInfo.map((cat) => {
                                    const isSelected = selectedCategories.includes(cat.name);
                                    return (
                                        <label
                                            key={cat.name}
                                            className={cn(
                                                'group relative flex items-center p-4 rounded-2xl cursor-pointer transition-all duration-200 border-2 overflow-hidden',
                                                isSelected
                                                    ? 'bg-emerald-500/10 border-emerald-500 shadow-[0_0_20px_-5px_rgba(16,185,129,0.3)]'
                                                    : 'bg-white/5 border-transparent hover:bg-white/10 hover:border-white/10'
                                            )}
                                        >
                                            <div className={cn(
                                                "absolute inset-0 opacity-0 transition-opacity duration-300",
                                                isSelected ? "opacity-100" : "group-hover:opacity-10"
                                            )}>
                                                <div className={`absolute inset-0 bg-gradient-to-r ${cat.gradient} opacity-5`} />
                                            </div>

                                            <input
                                                type="checkbox"
                                                value={cat.name}
                                                {...register('categories')}
                                                className="hidden"
                                            />
                                            <div className={cn(
                                                "p-3 rounded-xl mr-4 transition-colors",
                                                isSelected ? "bg-emerald-500/20 text-emerald-400" : "bg-white/5 text-slate-400 group-hover:text-white"
                                            )}>
                                                {cat.icon}
                                            </div>
                                            <div className="flex-1 relative z-10">
                                                <p className={cn(
                                                    "font-bold transition-colors",
                                                    isSelected ? "text-white" : "text-slate-300 group-hover:text-white"
                                                )}>{cat.name}</p>
                                                <p className="text-xs text-slate-500 mt-1">
                                                    {loadingCounts ? 'Loading...' : `${cat.available} questions`}
                                                </p>
                                            </div>
                                            {isSelected && (
                                                <CheckCircle className="w-6 h-6 text-emerald-500 animate-in zoom-in duration-200" />
                                            )}
                                        </label>
                                    );
                                })}
                            </div>
                            {errors.categories && (
                                <p className="mt-2 text-sm text-red-400">{errors.categories.message}</p>
                            )}
                        </section>
                    </div>

                    {/* Right Column: Preview & Action */}
                    <div className="lg:col-span-1">
                        <div className="sticky top-8 space-y-6">
                            <div className="bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 md:p-8">
                                <h2 className="text-xl font-bold text-white mb-6">Summary</h2>

                                <div className="space-y-4 mb-8">
                                    <div className="flex justify-between items-center py-3 border-b border-white/5">
                                        <span className="text-slate-400">Questions</span>
                                        <span className="font-bold text-white">{watched.numQuestions}</span>
                                    </div>
                                    <div className="flex justify-between items-center py-3 border-b border-white/5">
                                        <span className="text-slate-400">Difficulty</span>
                                        <span className="font-bold text-white">{watched.difficulty}</span>
                                    </div>
                                    <div className="py-3 border-b border-white/5">
                                        <span className="text-slate-400 block mb-2">Topics</span>
                                        <div className="flex flex-wrap gap-2">
                                            {selectedCategories.length > 0 ? (
                                                selectedCategories.map(cat => (
                                                    <span key={cat} className="px-2 py-1 rounded-lg bg-white/5 text-xs text-slate-300 border border-white/5">
                                                        {cat}
                                                    </span>
                                                ))
                                            ) : (
                                                <span className="text-slate-600 text-sm italic">None selected</span>
                                            )}
                                        </div>
                                    </div>
                                    <div className="flex justify-between items-center py-3">
                                        <span className="text-slate-400 flex items-center">
                                            <Clock className="w-4 h-4 mr-2" /> Est. Time
                                        </span>
                                        <span className="font-bold text-indigo-400">
                                            {Math.round(estimatedTime / 60)} min {estimatedTime % 60}s
                                        </span>
                                    </div>
                                </div>

                                {insufficient && (
                                    <div className="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 flex items-start">
                                        <XCircle className="w-5 h-5 text-red-400 mr-3 shrink-0 mt-0.5" />
                                        <p className="text-sm text-red-400">
                                            Some selected categories do not have enough questions for the chosen amount.
                                        </p>
                                    </div>
                                )}

                                <button
                                    type="submit"
                                    disabled={!isValid || insufficient || startLoading}
                                    className={cn(
                                        'w-full flex items-center justify-center px-8 py-4 text-base font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-1',
                                        !isValid || insufficient || startLoading
                                            ? 'bg-slate-800 text-slate-500 cursor-not-allowed'
                                            : 'bg-indigo-600 text-white hover:bg-indigo-500 hover:shadow-indigo-500/25'
                                    )}
                                >
                                    {startLoading ? (
                                        <Loader2 className="w-5 h-5 mr-2 animate-spin" />
                                    ) : (
                                        <PlayCircle className="w-5 h-5 mr-2" />
                                    )}
                                    {startLoading ? 'Generating...' : 'Start Quiz'}
                                </button>

                                {startError && (
                                    <p className="mt-4 text-sm text-red-400 flex items-center justify-center">
                                        <AlertCircle className="w-4 h-4 mr-2" /> {startError}
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    );
}
