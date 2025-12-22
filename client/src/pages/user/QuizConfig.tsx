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
    Zap,
    Image as ImageIcon,
    FileText,
    CheckSquare
} from 'lucide-react';
import { useQuiz } from '../../hooks/useQuiz';
import api from '../../lib/api';

import { cn } from '../../lib/utils';

// ----- Validation Schema -----
const quizConfigSchema = z.object({
    numQuestions: z.enum(['3', '5', '10', '20', '50']),
    difficulty: z.enum(['Easy', 'Medium', 'Hard', 'Mixed']),
    categories: z.array(z.string()), // Optional: empty means "Any"
    types: z.array(z.string()).optional(),
});

const QUESTION_TYPES = [
    { id: 'text_mcq', label: 'Multiple Choice', icon: FileText },
    { id: 'image_identify_person', label: 'Identify Person', icon: Users },
    { id: 'image_identify_logo', label: 'Identify Logo', icon: ImageIcon },
    { id: 'true_false', label: 'True / False', icon: CheckSquare },
];

type QuizConfig = z.infer<typeof quizConfigSchema>;

// ... (CategoryInfo interface and STYLE_MAP kept as is, skipping for brevity in replacement if possible, but replace_file_content needs contiguous block)
// I will target specific blocks to minimize context.

// Block 1: Schema
// Block 2: Logic inside component

// I'll do Block 1 (Schema) first.
// Wait, I can't do multiple blocks in one ONE-SHOT unless using multi_replace.
// I'll use multi_replace.

interface CategoryInfo {
    name: string;
    icon: React.ReactNode;
    available: number;
    gradient: string;
}

const STYLE_MAP: Record<string, { icon: React.ReactNode; gradient: string }> = {
    'Latest Developments': { icon: <BarChart2 className="w-5 h-5" />, gradient: 'from-blue-500 to-cyan-500' },
    'AI Safety': { icon: <Shield className="w-5 h-5" />, gradient: 'from-emerald-500 to-teal-500' },
    'Robotics': { icon: <Bot className="w-5 h-5" />, gradient: 'from-orange-500 to-amber-500' },
    'Quantum Computing': { icon: <Cpu className="w-5 h-5" />, gradient: 'from-violet-500 to-purple-500' },
    'Generative AI': { icon: <Brain className="w-5 h-5" />, gradient: 'from-pink-500 to-rose-500' },
    'Personalities': { icon: <Users className="w-5 h-5" />, gradient: 'from-indigo-500 to-blue-500' },
    'Brands': { icon: <BookOpen className="w-5 h-5" />, gradient: 'from-yellow-500 to-orange-500' },
};

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
            numQuestions: '5',
            difficulty: 'Mixed',
            categories: [],
            types: [],
        },
    });

    const [categoryInfo, setCategoryInfo] = useState<CategoryInfo[]>([]);
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

                // Map API response to CategoryInfo
                const categories: CategoryInfo[] = Object.entries(counts).map(([name, count]) => {
                    const style = STYLE_MAP[name] || {
                        icon: <BookOpen className="w-5 h-5" />,
                        gradient: 'from-slate-500 to-gray-500'
                    };
                    return {
                        name,
                        available: Number(count),
                        icon: style.icon,
                        gradient: style.gradient
                    };
                });

                // Sort categories: known styles first, then alphabetical
                categories.sort((a, b) => {
                    const aKnown = !!STYLE_MAP[a.name];
                    const bKnown = !!STYLE_MAP[b.name];
                    if (aKnown && !bKnown) return -1;
                    if (!aKnown && bKnown) return 1;
                    return a.name.localeCompare(b.name);
                });

                setCategoryInfo(categories);
            } catch (e) {
                console.error('Failed to fetch category counts', e);
                // Fallback to default categories if API fails
                const defaults = Object.keys(STYLE_MAP).map(name => ({
                    name,
                    available: 0,
                    ...STYLE_MAP[name]
                }));
                setCategoryInfo(defaults);
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

                // Ensure difficulty is Title Case to match schema
                const diff = parsed.difficulty || 'Mixed';
                const normalizedDiff = diff.charAt(0).toUpperCase() + diff.slice(1).toLowerCase();
                setValue('difficulty', normalizedDiff as any);

                // Only set categories that actually exist in the loaded info (if loaded)
                // But since info loads async, we might just set it and let the user adjust
                setValue('categories', parsed.categories);

                if (parsed.types) {
                    setValue('types', parsed.types);
                }
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
                types: data.types,
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
    const selectedCategories = watched.categories as string[];
    const selectedTypes = (watched.types as string[]) || [];

    // Calculate total available questions across ALL selected categories
    const totalAvailable = selectedCategories.length > 0
        ? selectedCategories.reduce((sum, cat) => {
            const info = categoryInfo.find((c) => c.name === cat);
            return sum + (info?.available || 0);
        }, 0)
        : categoryInfo.reduce((sum, c) => sum + c.available, 0);

    const insufficient = totalAvailable < Number(watched.numQuestions);

    const estimatedTime = Number(watched.numQuestions) * 30; // seconds

    const toggleSelectAll = () => {
        if (selectedCategories.length === categoryInfo.length) {
            setValue('categories', []);
        } else {
            setValue('categories', categoryInfo.map(c => c.name));
        }
    };

    const toggleSelectAllTypes = () => {
        if (selectedTypes.length === QUESTION_TYPES.length) {
            setValue('types', []);
        } else {
            setValue('types', QUESTION_TYPES.map(t => t.id));
        }
    };

    return (
        <div className="max-w-5xl mx-auto p-6 space-y-8">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold text-white tracking-tight">Configure Quiz</h1>
                    <p className="text-slate-400 mt-2">Customize your assessment parameters</p>
                </div>
                <div className="hidden md:block">
                    <div className="p-3 bg-primary/10 rounded-xl border border-primary/20">
                        <Zap className="w-6 h-6 text-primary" />
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
                            <div className="grid grid-cols-2 sm:grid-cols-5 gap-2">
                                {['3', '5', '10', '20', '50'].map((val) => (
                                    <label
                                        key={val}
                                        className={cn(
                                            'relative flex flex-col items-center justify-center p-4 rounded-2xl cursor-pointer transition-all duration-200 border-2',
                                            watched.numQuestions === val
                                                ? 'bg-primary/10 border-primary shadow-[0_0_20px_-5px_rgba(0,212,255,0.5)]'
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
                                            watched.numQuestions === val ? "text-primary" : "text-white"
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
                                                ? 'bg-cyan-500/10 border-cyan-500 shadow-[0_0_20px_-5px_rgba(6,182,212,0.5)]'
                                                : 'bg-white/5 border-transparent hover:bg-white/10 hover:border-white/10'
                                        )}
                                    >
                                        <input type="radio" value={diff} {...register('difficulty')} className="hidden" />
                                        <span className={cn(
                                            "font-bold",
                                            watched.difficulty === diff ? "text-cyan-400" : "text-slate-300"
                                        )}>{diff}</span>
                                    </label>
                                ))}
                            </div>
                            {errors.difficulty && (
                                <p className="mt-2 text-sm text-red-400">{errors.difficulty.message}</p>
                            )}
                        </section>

                        {/* Question Types */}
                        <section className="bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 md:p-8">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-xl font-bold text-white flex items-center">
                                    <span className="w-8 h-8 rounded-lg bg-pink-500/20 text-pink-400 flex items-center justify-center mr-3 text-sm font-bold">3</span>
                                    Question Types
                                </h2>
                                <button
                                    type="button"
                                    onClick={toggleSelectAllTypes}
                                    className="text-sm text-primary hover:text-primary/80 font-medium transition-colors"
                                >
                                    {selectedTypes.length === QUESTION_TYPES.length ? 'Deselect All' : 'Select All'}
                                </button>
                            </div>
                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                {QUESTION_TYPES.map((type) => {
                                    const isSelected = selectedTypes.includes(type.id);
                                    return (
                                        <label
                                            key={type.id}
                                            className={cn(
                                                'relative flex flex-col items-center justify-center p-4 rounded-2xl cursor-pointer transition-all duration-200 border-2 text-center',
                                                isSelected
                                                    ? 'bg-pink-500/10 border-pink-500 shadow-[0_0_20px_-5px_rgba(236,72,153,0.5)]'
                                                    : 'bg-white/5 border-transparent hover:bg-white/10 hover:border-white/10'
                                            )}
                                        >
                                            <input
                                                type="checkbox"
                                                value={type.id}
                                                {...register('types')}
                                                className="hidden"
                                            />
                                            <div className={cn(
                                                "mb-2 p-2 rounded-lg",
                                                isSelected ? "bg-pink-500/20 text-pink-400" : "bg-white/10 text-slate-400"
                                            )}>
                                                <type.icon className="w-6 h-6" />
                                            </div>
                                            <span className={cn(
                                                "font-bold text-sm",
                                                isSelected ? "text-pink-400" : "text-slate-300"
                                            )}>{type.label}</span>
                                        </label>
                                    );
                                })}
                            </div>
                            <p className="text-xs text-slate-500 mt-4 text-center">
                                Select specific question types or leave blank to include all.
                            </p>
                        </section>

                        {/* Categories */}
                        <section className="bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 md:p-8">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-xl font-bold text-white flex items-center">
                                    <span className="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center mr-3 text-sm font-bold">4</span>
                                    Topics
                                </h2>
                                <button
                                    type="button"
                                    onClick={toggleSelectAll}
                                    className="text-sm text-primary hover:text-primary/80 font-medium transition-colors"
                                >
                                    {selectedCategories.length === categoryInfo.length && categoryInfo.length > 0 ? 'Deselect All' : 'Select All'}
                                </button>
                            </div>

                            {loadingCounts ? (
                                <div className="flex justify-center py-8">
                                    <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
                                </div>
                            ) : categoryInfo.length === 0 ? (
                                <div className="text-center py-8 text-slate-400">
                                    No topics available.
                                </div>
                            ) : (
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
                                                        {cat.available} questions
                                                    </p>
                                                </div>
                                                {isSelected && (
                                                    <CheckCircle className="w-6 h-6 text-emerald-500 animate-in zoom-in duration-200" />
                                                )}
                                            </label>
                                        );
                                    })}
                                </div>
                            )}
                            <p className="text-xs text-slate-500 mt-4 text-center">
                                Select specific topics or leave blank to include all.
                            </p>
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
                                        <span className="text-slate-400 block mb-2">Types</span>
                                        <div className="flex flex-wrap gap-2">
                                            {selectedTypes.length > 0 ? (
                                                selectedTypes.map(typeId => {
                                                    const type = QUESTION_TYPES.find(t => t.id === typeId);
                                                    return (
                                                        <span key={typeId} className="px-2 py-1 rounded-lg bg-white/5 text-xs text-slate-300 border border-white/5">
                                                            {type?.label || typeId}
                                                        </span>
                                                    );
                                                })
                                            ) : (
                                                <span className="text-slate-600 text-sm italic">All Types</span>
                                            )}
                                        </div>
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
                                                <span className="text-slate-600 text-sm italic">Any Topic</span>
                                            )}
                                        </div>
                                    </div>
                                    <div className="flex justify-between items-center py-3">
                                        <span className="text-slate-400 flex items-center">
                                            <Clock className="w-4 h-4 mr-2" /> Est. Time
                                        </span>
                                        <span className="font-bold text-primary">
                                            {Math.round(estimatedTime / 60)} min {estimatedTime % 60}s
                                        </span>
                                    </div>
                                </div>

                                {insufficient && (
                                    <div className="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 flex items-start">
                                        <XCircle className="w-5 h-5 text-red-400 mr-3 shrink-0 mt-0.5" />
                                        <p className="text-sm text-red-400">
                                            Selected topics have {totalAvailable} questions, but you requested {watched.numQuestions}.
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
                                            : 'btn-vibeai'
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
