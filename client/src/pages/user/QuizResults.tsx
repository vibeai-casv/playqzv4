import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { CheckCircle, XCircle, Loader2, ArrowLeft, RefreshCw, Share2, X, Trophy, AlertCircle, BarChart2 } from 'lucide-react';
import { useQuizStore } from '../../stores/quizStore';
import { cn } from '../../lib/utils';
import { toast } from 'sonner';
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, Cell } from 'recharts';
import confetti from 'canvas-confetti';

// Simple modal for share link
function ShareModal({ open, onClose, link }: { open: boolean; onClose: () => void; link: string }) {
    if (!open) return null;
    return (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 animate-in fade-in duration-200" role="dialog" aria-modal="true">
            <div className="bg-slate-900 border border-white/10 rounded-2xl p-6 max-w-sm w-full relative shadow-2xl shadow-black/50 animate-in zoom-in-95 duration-200">
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 text-slate-500 hover:text-white transition-colors"
                >
                    <X className="w-5 h-5" />
                </button>
                <h3 className="text-xl font-bold mb-4 text-white">Share Results</h3>
                <div className="relative mb-6">
                    <input
                        readOnly
                        value={link}
                        className="w-full p-3 pr-12 border border-white/10 rounded-xl bg-black/20 text-slate-300 focus:outline-none focus:border-indigo-500 transition-colors"
                        onClick={(e) => (e.target as HTMLInputElement).select()}
                    />
                </div>
                <button
                    onClick={() => {
                        navigator.clipboard.writeText(link);
                        toast.success('Copied to clipboard!');
                    }}
                    className="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-600/20"
                >
                    Copy to Clipboard
                </button>
            </div>
        </div>
    );
}

export function QuizResults() {
    const navigate = useNavigate();
    const {
        questions,
        responses,
        currentAttempt,
        isLoading,
        resetQuiz,
    } = useQuizStore();

    const [filter, setFilter] = useState<'all' | 'correct' | 'incorrect'>('all');
    const [showShare, setShowShare] = useState(false);

    // Compute basic stats
    const total = questions.length;
    const correct = Array.from(responses.values()).filter((r) => r.is_correct).length;
    const percentage = total ? Math.round((correct / total) * 100) : 0;
    const passed = percentage >= 50;

    // Confetti on high score
    useEffect(() => {
        if (percentage > 80) {
            const duration = 3000;
            const end = Date.now() + duration;

            const frame = () => {
                confetti({
                    particleCount: 2,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: ['#6366f1', '#8b5cf6', '#ec4899']
                });
                confetti({
                    particleCount: 2,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: ['#6366f1', '#8b5cf6', '#ec4899']
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            };
            frame();
        }
    }, [percentage]);

    // Category breakdown data for recharts
    const categoryData = React.useMemo(() => {
        const map: Record<string, { correct: number; total: number }> = {};
        questions.forEach((q) => {
            const cat = q.category || 'Other';
            if (!map[cat]) map[cat] = { correct: 0, total: 0 };
            map[cat].total += 1;
            const resp = responses.get(q.id);
            if (resp?.is_correct) map[cat].correct += 1;
        });
        return Object.entries(map).map(([name, val]) => ({ name, ...val }));
    }, [questions, responses]);

    const filteredQuestions = questions.filter((q) => {
        const resp = responses.get(q.id);
        if (filter === 'correct') return resp?.is_correct;
        if (filter === 'incorrect') return resp && !resp.is_correct;
        return true;
    });

    const handleRetake = async () => {
        resetQuiz();
        navigate('/quiz-config');
    };

    if (isLoading || !currentAttempt) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <Loader2 className="w-8 h-8 animate-spin text-indigo-500" />
            </div>
        );
    }

    const completedAt = new Date(currentAttempt.completed_at || Date.now()).toLocaleString();
    const shareLink = `${window.location.origin}/quiz-results/${currentAttempt.id}`;

    return (
        <div className="max-w-5xl mx-auto p-4 md:p-6 space-y-8">
            {/* Score Header */}
            <section className="relative overflow-hidden rounded-3xl bg-slate-900 border border-white/10 p-8 md:p-12 text-center shadow-2xl">
                <div className="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-pink-500/10" />
                <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500" />

                <div className="relative z-10">
                    <div className={cn(
                        "w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6 shadow-[0_0_30px_-5px_rgba(0,0,0,0.3)]",
                        passed ? "bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" : "bg-red-500/10 text-red-400 border border-red-500/20"
                    )}>
                        {passed ? <Trophy className="w-10 h-10" /> : <AlertCircle className="w-10 h-10" />}
                    </div>

                    <h1 className="text-5xl md:text-6xl font-black text-white mb-4 tracking-tight">
                        {percentage}%
                    </h1>
                    <p className="text-xl text-slate-300 mb-2">
                        You answered <span className="text-white font-bold">{correct}</span> out of <span className="text-white font-bold">{total}</span> questions correctly
                    </p>
                    <p className="text-sm text-slate-500 uppercase tracking-widest font-medium">
                        Completed on {completedAt}
                    </p>
                </div>
            </section>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Category Breakdown */}
                <section className="lg:col-span-1 bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 flex flex-col">
                    <h2 className="text-xl font-bold text-white mb-6 flex items-center">
                        <BarChart2 className="w-5 h-5 mr-3 text-indigo-400" />
                        Performance
                    </h2>
                    <div className="flex-1 min-h-[300px]">
                        <ResponsiveContainer width="100%" height="100%">
                            <BarChart data={categoryData} layout="vertical" margin={{ top: 0, right: 30, left: 40, bottom: 0 }}>
                                <XAxis type="number" domain={[0, 'dataMax']} hide />
                                <YAxis
                                    dataKey="name"
                                    type="category"
                                    width={100}
                                    tick={{ fill: '#94a3b8', fontSize: 12 }}
                                    axisLine={false}
                                    tickLine={false}
                                />
                                <Tooltip
                                    contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '0.75rem', color: '#f8fafc' }}
                                    itemStyle={{ color: '#e2e8f0' }}
                                    cursor={{ fill: 'rgba(255,255,255,0.05)' }}
                                />
                                <Bar dataKey="total" stackId="a" fill="#1e293b" radius={[0, 4, 4, 0]} barSize={20} />
                                <Bar dataKey="correct" stackId="a" fill="#6366f1" radius={[0, 4, 4, 0]} barSize={20}>
                                    {categoryData.map((entry, index) => (
                                        <Cell key={`cell-${index}`} fill={entry.correct / entry.total > 0.7 ? '#10b981' : entry.correct / entry.total > 0.4 ? '#f59e0b' : '#ef4444'} />
                                    ))}
                                </Bar>
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                </section>

                {/* Question Review */}
                <section className="lg:col-span-2 bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6">
                    <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                        <h2 className="text-xl font-bold text-white">Question Review</h2>
                        <div className="flex bg-slate-800/50 p-1 rounded-xl">
                            {(['all', 'correct', 'incorrect'] as const).map((f) => (
                                <button
                                    key={f}
                                    onClick={() => setFilter(f)}
                                    className={cn(
                                        'px-4 py-1.5 rounded-lg text-sm font-medium transition-all capitalize',
                                        filter === f
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25'
                                            : 'text-slate-400 hover:text-white hover:bg-white/5'
                                    )}
                                >
                                    {f}
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        {filteredQuestions.map((q) => {
                            const resp = responses.get(q.id);
                            const isCorrect = resp?.is_correct;
                            return (
                                <div key={q.id} className="group border border-white/5 rounded-2xl p-5 bg-white/5 hover:bg-white/10 transition-colors">
                                    <div className="flex items-start gap-4">
                                        <div className={cn(
                                            "mt-1 w-6 h-6 rounded-full flex items-center justify-center shrink-0",
                                            isCorrect ? "bg-emerald-500/20 text-emerald-400" : "bg-red-500/20 text-red-400"
                                        )}>
                                            {isCorrect ? <CheckCircle className="w-4 h-4" /> : <XCircle className="w-4 h-4" />}
                                        </div>
                                        <div className="flex-1">
                                            <p className="font-medium text-slate-200 mb-3">{q.question_text}</p>

                                            {q.image_url && (
                                                <div className="mb-4">
                                                    <img src={q.image_url} alt="Question" className="max-h-40 rounded-lg border border-white/10" />
                                                </div>
                                            )}

                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                                <div className={cn(
                                                    "p-3 rounded-xl border",
                                                    isCorrect
                                                        ? "bg-emerald-500/10 border-emerald-500/20 text-emerald-300"
                                                        : "bg-red-500/10 border-red-500/20 text-red-300"
                                                )}>
                                                    <span className="block text-xs opacity-70 mb-1 uppercase tracking-wider font-bold">Your Answer</span>
                                                    {resp?.user_answer || '—'}
                                                </div>
                                                <div className="p-3 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-300">
                                                    <span className="block text-xs opacity-70 mb-1 uppercase tracking-wider font-bold">Correct Answer</span>
                                                    {q.correct_answer}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </section>
            </div>

            {/* Action Buttons */}
            <section className="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-white/5">
                <button
                    onClick={() => navigate('/dashboard')}
                    className="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition-colors font-medium"
                >
                    <ArrowLeft className="w-4 h-4" /> Back to Dashboard
                </button>
                <div className="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                    <button
                        onClick={handleRetake}
                        className="flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white hover:bg-indigo-500 rounded-xl font-bold transition-all shadow-lg shadow-indigo-600/20"
                    >
                        <RefreshCw className="w-4 h-4" /> Retake Quiz
                    </button>
                    <button
                        onClick={() => setShowShare(true)}
                        className="flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 text-white hover:bg-emerald-500 rounded-xl font-bold transition-all shadow-lg shadow-emerald-600/20"
                    >
                        <Share2 className="w-4 h-4" /> Share Results
                    </button>
                </div>
            </section>

            {/* Share Modal */}
            <ShareModal open={showShare} onClose={() => setShowShare(false)} link={shareLink} />
        </div>
    );
}
