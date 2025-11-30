import { useState, useEffect, useMemo } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';
import { useUserStats } from '../../hooks/useUserStats';
import { Play, TrendingUp, Award, Clock, Calendar, ArrowRight, ChevronRight, User, History, AlertCircle, Activity } from 'lucide-react';
import { XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, AreaChart, Area } from 'recharts';
import { cn } from '../../lib/utils';
import { AnimatedCounter } from '../../components/ui/AnimatedCounter';
import { Skeleton } from '../../components/ui/Skeleton';
import { ErrorBoundary } from '../../components/ErrorBoundary';

interface StatCardProps {
    title: string;
    value: number;
    prefix?: string;
    suffix?: string;
    icon: React.ElementType;
    gradient: string;
    delay: number;
    isLoading?: boolean;
}

const StatCard = ({ title, value, prefix, suffix, icon: Icon, gradient, delay, isLoading }: StatCardProps) => (
    <div
        className="relative group overflow-hidden rounded-2xl bg-slate-900/50 border border-white/5 p-6 hover:border-white/10 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4"
        style={{ animationDelay: `${delay}ms` }}
    >
        <div className={`absolute inset-0 bg-gradient-to-br ${gradient} opacity-5 group-hover:opacity-10 transition-opacity`} />

        <div className="relative flex items-center justify-between">
            <div>
                <p className="text-sm font-medium text-slate-400">{title}</p>
                <h3 className="text-3xl font-bold text-white mt-2 tracking-tight">
                    {isLoading ? (
                        <Skeleton className="h-8 w-24 bg-slate-800" />
                    ) : (
                        <AnimatedCounter value={value} prefix={prefix} suffix={suffix} />
                    )}
                </h3>
            </div>
            <div className={`p-3 rounded-xl bg-white/5 border border-white/5 group-hover:scale-110 transition-transform duration-300`}>
                <Icon className="w-6 h-6 text-white" />
            </div>
        </div>
    </div>
);

function DashboardContent() {
    const { user } = useAuth();
    const { stats, recentAttempts, loading, fetchStats } = useUserStats();
    const [greeting, setGreeting] = useState('');

    useEffect(() => {
        const hour = new Date().getHours();
        if (hour < 12) setGreeting('Good morning');
        else if (hour < 18) setGreeting('Good afternoon');
        else setGreeting('Good evening');

        fetchStats();
    }, [fetchStats]);

    // Derive chart data from recent attempts (last 7 attempts for simplicity)
    const performanceData = useMemo(() => {
        return [...recentAttempts]
            .reverse() // Oldest first for chart
            .filter(a => a.status === 'completed')
            .map(a => ({
                date: new Date(a.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }),
                score: a.score || 0
            }));
    }, [recentAttempts]);

    if (loading && !stats) {
        return (
            <div className="space-y-8 p-6">
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <Skeleton className="h-10 w-64 mb-2 bg-slate-800" />
                        <Skeleton className="h-5 w-96 bg-slate-800" />
                    </div>
                    <Skeleton className="h-12 w-40 rounded-lg bg-slate-800" />
                </div>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {[1, 2, 3].map((i) => (
                        <Skeleton key={i} className="h-32 rounded-2xl bg-slate-800" />
                    ))}
                </div>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <Skeleton className="lg:col-span-2 h-96 rounded-2xl bg-slate-800" />
                    <Skeleton className="h-96 rounded-2xl bg-slate-800" />
                </div>
            </div>
        );
    }

    return (
        <div className="space-y-8">
            {/* Welcome Section */}
            <div className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary via-cyan-500 to-blue-500 p-8 md:p-12 shadow-2xl shadow-primary/20">
                <div className="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse" />
                <div className="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-black/10 rounded-full blur-2xl" />

                <div className="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <h1 className="text-3xl md:text-4xl font-bold text-white tracking-tight flex flex-wrap items-center gap-3">
                            {greeting}, {user?.name || 'User'}! 🚀
                            {user?.role === 'admin' && (
                                <span className="text-sm font-bold bg-white/20 text-white px-3 py-1 rounded-full border border-white/30 backdrop-blur-sm">
                                    [ ADMIN ]
                                </span>
                            )}
                        </h1>
                        <p className="text-white/90 mt-2 text-lg whitespace-nowrap overflow-hidden text-ellipsis max-w-full">
                            Ready to expand your knowledge? Your AI learning assistant is prepped and waiting.
                        </p>
                    </div>
                    <Link
                        to="/quiz-config"
                        className="btn-vibeai-outline bg-white/10 backdrop-blur-sm border-white hover:bg-white hover:text-primary inline-flex items-center justify-center group"
                    >
                        <Play className="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform" />
                        Start New Quiz
                    </Link>
                </div>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <StatCard
                    title="Total Quizzes"
                    value={stats?.total_attempts || 0}
                    icon={Award}
                    gradient="from-primary to-cyan-400"
                    delay={0}
                />
                <StatCard
                    title="Average Score"
                    value={Math.round(stats?.average_score || 0)}
                    suffix="%"
                    icon={TrendingUp}
                    gradient="from-emerald-500 to-teal-500"
                    delay={100}
                />
                <StatCard
                    title="Completion Rate"
                    value={Math.round(stats?.completion_rate || 0)}
                    suffix="%"
                    icon={Clock}
                    gradient="from-cyan-500 to-blue-500"
                    delay={200}
                />
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Performance Chart */}
                <div className="lg:col-span-2 bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 md:p-8">
                    <div className="flex items-center justify-between mb-8">
                        <div>
                            <h2 className="text-xl font-bold text-white">Performance Trend</h2>
                            <p className="text-sm text-slate-400">Your score progression over time</p>
                        </div>
                        <div className="p-2 bg-white/5 rounded-lg">
                            <TrendingUp className="w-5 h-5 text-primary" />
                        </div>
                    </div>
                    <div className="h-80 w-full">
                        {performanceData.length > 0 ? (
                            <ResponsiveContainer width="100%" height="100%">
                                <AreaChart data={performanceData}>
                                    <defs>
                                        <linearGradient id="colorScore" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="5%" stopColor="#6366f1" stopOpacity={0.3} />
                                            <stop offset="95%" stopColor="#6366f1" stopOpacity={0} />
                                        </linearGradient>
                                    </defs>
                                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="rgba(255,255,255,0.05)" />
                                    <XAxis
                                        dataKey="date"
                                        axisLine={false}
                                        tickLine={false}
                                        tick={{ fill: '#94a3b8', fontSize: 12 }}
                                        dy={10}
                                    />
                                    <YAxis
                                        axisLine={false}
                                        tickLine={false}
                                        tick={{ fill: '#94a3b8', fontSize: 12 }}
                                    />
                                    <Tooltip
                                        contentStyle={{
                                            backgroundColor: '#1e293b',
                                            borderColor: 'rgba(255,255,255,0.1)',
                                            borderRadius: '12px',
                                            color: '#fff',
                                            boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.5)'
                                        }}
                                        itemStyle={{ color: '#fff' }}
                                    />
                                    <Area
                                        type="monotone"
                                        dataKey="score"
                                        stroke="#6366f1"
                                        strokeWidth={3}
                                        fillOpacity={1}
                                        fill="url(#colorScore)"
                                    />
                                </AreaChart>
                            </ResponsiveContainer>
                        ) : (
                            <div className="flex flex-col items-center justify-center h-full text-slate-500">
                                <Activity className="w-12 h-12 mb-4 opacity-20" />
                                <p>No performance data available yet</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Recent Activity / Quick Actions */}
                <div className="bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 md:p-8 flex flex-col">
                    <h2 className="text-xl font-bold text-white mb-6">Quick Actions</h2>
                    <div className="space-y-4 flex-1">
                        <Link to="/profile" className="block p-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-primary/30 transition-all group">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center">
                                    <div className="w-12 h-12 rounded-xl bg-primary/20 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                        <User className="w-6 h-6" />
                                    </div>
                                    <div className="ml-4">
                                        <p className="font-bold text-white group-hover:text-primary transition-colors">Update Profile</p>
                                        <p className="text-xs text-slate-400">Manage your account</p>
                                    </div>
                                </div>
                                <ChevronRight className="w-5 h-5 text-slate-500 group-hover:text-white group-hover:translate-x-1 transition-all" />
                            </div>
                        </Link>

                        <Link to="/history" className="block p-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-emerald-500/30 transition-all group">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center">
                                    <div className="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                                        <History className="w-6 h-6" />
                                    </div>
                                    <div className="ml-4">
                                        <p className="font-bold text-white group-hover:text-emerald-400 transition-colors">Review History</p>
                                        <p className="text-xs text-slate-400">Analyze past results</p>
                                    </div>
                                </div>
                                <ChevronRight className="w-5 h-5 text-slate-500 group-hover:text-white group-hover:translate-x-1 transition-all" />
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            {/* Recent History Table */}
            <div className="bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 overflow-hidden">
                <div className="p-6 md:p-8 border-b border-white/5 flex items-center justify-between">
                    <div>
                        <h2 className="text-xl font-bold text-white">Recent Quizzes</h2>
                        <p className="text-sm text-slate-400 mt-1">Your latest assessment sessions</p>
                    </div>
                    <Link to="/history" className="text-sm font-medium text-primary hover:text-primary/80 flex items-center transition-colors">
                        View All <ArrowRight className="w-4 h-4 ml-1" />
                    </Link>
                </div>

                {recentAttempts.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-slate-400">
                            <thead className="bg-white/5 text-xs uppercase text-slate-300 font-semibold tracking-wider">
                                <tr>
                                    <th className="px-6 py-4">Topic</th>
                                    <th className="px-6 py-4">Date</th>
                                    <th className="px-6 py-4">Score</th>
                                    <th className="px-6 py-4">Time</th>
                                    <th className="px-6 py-4 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-white/5">
                                {recentAttempts.map((quiz) => (
                                    <tr key={quiz.id} className="hover:bg-white/5 transition-colors">
                                        <td className="px-6 py-4 font-medium text-white">
                                            {quiz.config?.categories?.join(', ') || 'General Quiz'}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center">
                                                <Calendar className="w-4 h-4 mr-2 text-slate-500" />
                                                {new Date(quiz.created_at).toLocaleDateString()}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            {quiz.status === 'completed' ? (
                                                <span className={cn(
                                                    "px-3 py-1 rounded-full text-xs font-bold",
                                                    (quiz.score || 0) >= 80 ? "bg-emerald-500/20 text-emerald-400 border border-emerald-500/20" :
                                                        (quiz.score || 0) >= 60 ? "bg-yellow-500/20 text-yellow-400 border border-yellow-500/20" :
                                                            "bg-red-500/20 text-red-400 border border-red-500/20"
                                                )}>
                                                    {quiz.score}%
                                                </span>
                                            ) : (
                                                <span className="text-slate-600">-</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 font-mono text-slate-300">
                                            {quiz.time_spent_seconds ? `${Math.floor(quiz.time_spent_seconds / 60)}m ${quiz.time_spent_seconds % 60}s` : '-'}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <span className={cn(
                                                "px-3 py-1 rounded-full text-xs font-bold capitalize",
                                                quiz.status === 'completed' ? "bg-blue-500/20 text-blue-400 border border-blue-500/20" : "bg-slate-700 text-slate-300"
                                            )}>
                                                {quiz.status.replace('_', ' ')}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="flex flex-col items-center justify-center py-16 text-center">
                        <div className="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mb-4 border border-white/10">
                            <AlertCircle className="w-8 h-8 text-slate-500" />
                        </div>
                        <h3 className="text-lg font-bold text-white">No quizzes taken yet</h3>
                        <p className="text-slate-400 mt-2 max-w-sm">
                            You haven't taken any quizzes yet. Start your first quiz to see your history here!
                        </p>
                        <Link
                            to="/quiz-config"
                            className="btn-vibeai mt-6 inline-flex items-center"
                        >
                            Take a Quiz
                        </Link>
                    </div>
                )}
            </div>
        </div>
    );
}

export function Dashboard() {
    return (
        <ErrorBoundary>
            <DashboardContent />
        </ErrorBoundary>
    );
}
