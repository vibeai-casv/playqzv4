import React, { useEffect, useState } from 'react';
import { useAdmin } from '../../hooks/useAdmin';
import { Users, FileQuestion, Activity, TrendingUp, Calendar, PieChart as PieChartIcon, BarChart as BarChartIcon } from 'lucide-react';
import {
    LineChart,
    Line,
    BarChart,
    Bar,
    PieChart,
    Pie,
    Cell,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer,
} from 'recharts';
import { Skeleton } from '../../components/ui/Skeleton';
import { cn } from '../../lib/utils';

const COLORS = ['#00d4ff', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#06b6d4'];

export function AdminDashboard() {
    const { fetchAnalytics, fetchDailyStats, fetchCategoryStats, fetchRegistrationStats } = useAdmin();
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState<{ totalUsers: number; activeUsers: number; totalAttempts: number } | null>(null);
    const [dailyStats, setDailyStats] = useState<{ date: string; total_attempts: number }[]>([]);
    const [categoryStats, setCategoryStats] = useState<{ category: string; total_questions: number; accuracy_rate: number }[]>([]);
    const [registrationStats, setRegistrationStats] = useState<{ date: string; count: number }[]>([]);

    useEffect(() => {
        const loadData = async () => {
            setLoading(true);
            try {
                const [analyticsData, dailyData, categoryData, regData] = await Promise.all([
                    fetchAnalytics(),
                    fetchDailyStats(),
                    fetchCategoryStats(),
                    fetchRegistrationStats(),
                ]);

                setStats(analyticsData);
                setDailyStats(dailyData || []);
                setCategoryStats(categoryData || []);
                setRegistrationStats(regData || []);
            } catch (error) {
                console.error('Failed to load dashboard data', error);
            } finally {
                setLoading(false);
            }
        };

        loadData();
    }, []);

    if (loading) {
        return (
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                    {[1, 2, 3, 4].map((i) => (
                        <Skeleton key={i} className="h-32 rounded-2xl bg-slate-800" />
                    ))}
                </div>
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <Skeleton className="h-80 rounded-2xl bg-slate-800" />
                    <Skeleton className="h-80 rounded-2xl bg-slate-800" />
                </div>
            </div>
        );
    }

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-3xl font-bold text-white tracking-tight">Admin Dashboard</h1>
                    <p className="text-slate-300 mt-1">System overview and analytics</p>
                </div>
                <div className="flex items-center space-x-2 text-sm text-slate-300 bg-slate-900/50 px-4 py-2 rounded-lg border border-white/5">
                    <Calendar className="w-4 h-4" />
                    <span>Last 30 Days</span>
                </div>
            </div>

            {/* Key Metrics */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatCard
                    title="Total Users"
                    value={stats?.totalUsers || 0}
                    icon={Users}
                    trend="+12%"
                    trendUp={true}
                    gradient="from-primary to-cyan-400"
                />
                <StatCard
                    title="Active Users"
                    value={stats?.activeUsers || 0}
                    icon={Activity}
                    trend="+5%"
                    trendUp={true}
                    gradient="from-emerald-500 to-teal-500"
                />
                <StatCard
                    title="Total Questions"
                    value={categoryStats.reduce((acc, curr) => acc + (curr.total_questions || 0), 0)}
                    icon={FileQuestion}
                    trend="+8%"
                    trendUp={true}
                    gradient="from-cyan-500 to-blue-500"
                />
                <StatCard
                    title="Total Attempts"
                    value={stats?.totalAttempts || 0}
                    icon={TrendingUp}
                    trend="+24%"
                    trendUp={true}
                    gradient="from-orange-500 to-amber-500"
                />
            </div>

            {/* Charts Row 1 */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* User Growth */}
                <div className="bg-slate-900/50 backdrop-blur-sm p-6 rounded-3xl shadow-sm border border-white/5">
                    <h3 className="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <Users className="w-5 h-5 text-primary" /> User Registration Trend
                    </h3>
                    <div className="h-80">
                        <ResponsiveContainer width="100%" height="100%">
                            <LineChart data={registrationStats}>
                                <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.05)" />
                                <XAxis
                                    dataKey="date"
                                    tick={{ fill: '#94a3b8', fontSize: 12 }}
                                    tickFormatter={(val) => new Date(val).toLocaleDateString(undefined, { month: 'short', day: 'numeric' })}
                                    axisLine={false}
                                    tickLine={false}
                                    dy={10}
                                />
                                <YAxis tick={{ fill: '#94a3b8', fontSize: 12 }} axisLine={false} tickLine={false} />
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
                                <Line
                                    type="monotone"
                                    dataKey="count"
                                    stroke="#00d4ff"
                                    strokeWidth={3}
                                    dot={{ fill: '#00d4ff', strokeWidth: 2, r: 4, stroke: '#1e293b' }}
                                    activeDot={{ r: 6, strokeWidth: 0 }}
                                />
                            </LineChart>
                        </ResponsiveContainer>
                    </div>
                </div>

                {/* Quiz Attempts */}
                <div className="bg-slate-900/50 backdrop-blur-sm p-6 rounded-3xl shadow-sm border border-white/5">
                    <h3 className="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <BarChartIcon className="w-5 h-5 text-purple-400" /> Quiz Attempts (Daily)
                    </h3>
                    <div className="h-80">
                        <ResponsiveContainer width="100%" height="100%">
                            <BarChart data={dailyStats}>
                                <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.05)" />
                                <XAxis
                                    dataKey="date"
                                    tick={{ fill: '#94a3b8', fontSize: 12 }}
                                    tickFormatter={(val) => new Date(val).toLocaleDateString(undefined, { month: 'short', day: 'numeric' })}
                                    axisLine={false}
                                    tickLine={false}
                                    dy={10}
                                />
                                <YAxis tick={{ fill: '#94a3b8', fontSize: 12 }} axisLine={false} tickLine={false} />
                                <Tooltip
                                    contentStyle={{
                                        backgroundColor: '#1e293b',
                                        borderColor: 'rgba(255,255,255,0.1)',
                                        borderRadius: '12px',
                                        color: '#fff',
                                        boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.5)'
                                    }}
                                    cursor={{ fill: 'rgba(255,255,255,0.05)' }}
                                    itemStyle={{ color: '#fff' }}
                                />
                                <Bar dataKey="total_attempts" fill="#8b5cf6" radius={[4, 4, 0, 0]} />
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                </div>
            </div>

            {/* Charts Row 2 */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Category Distribution */}
                <div className="bg-slate-900/50 backdrop-blur-sm p-6 rounded-3xl shadow-sm border border-white/5">
                    <h3 className="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <PieChartIcon className="w-5 h-5 text-emerald-400" /> Questions by Category
                    </h3>
                    <div className="h-80">
                        <ResponsiveContainer width="100%" height="100%">
                            <PieChart>
                                <Pie
                                    data={categoryStats}
                                    cx="50%"
                                    cy="50%"
                                    labelLine={false}
                                    label={({ name, percent }) => `${name} ${((percent || 0) * 100).toFixed(0)}%`}
                                    outerRadius={100}
                                    fill="#8884d8"
                                    dataKey="total_questions"
                                    nameKey="category"
                                >
                                    {categoryStats.map((_entry, index) => (
                                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} stroke="rgba(0,0,0,0.2)" />
                                    ))}
                                </Pie>
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
                            </PieChart>
                        </ResponsiveContainer>
                    </div>
                </div>

                {/* Category Performance */}
                <div className="bg-slate-900/50 backdrop-blur-sm p-6 rounded-3xl shadow-sm border border-white/5">
                    <h3 className="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <Activity className="w-5 h-5 text-orange-400" /> Accuracy by Category
                    </h3>
                    <div className="h-80">
                        <ResponsiveContainer width="100%" height="100%">
                            <BarChart data={categoryStats} layout="vertical">
                                <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.05)" />
                                <XAxis type="number" domain={[0, 100]} tick={{ fill: '#94a3b8', fontSize: 12 }} axisLine={false} tickLine={false} />
                                <YAxis dataKey="category" type="category" width={120} tick={{ fill: '#94a3b8', fontSize: 12 }} axisLine={false} tickLine={false} />
                                <Tooltip
                                    contentStyle={{
                                        backgroundColor: '#1e293b',
                                        borderColor: 'rgba(255,255,255,0.1)',
                                        borderRadius: '12px',
                                        color: '#fff',
                                        boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.5)'
                                    }}
                                    cursor={{ fill: 'rgba(255,255,255,0.05)' }}
                                    formatter={(value: number) => [`${value.toFixed(1)}%`, 'Accuracy']}
                                    itemStyle={{ color: '#fff' }}
                                />
                                <Bar dataKey="accuracy_rate" fill="#10b981" radius={[0, 4, 4, 0]} />
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                </div>
            </div>
        </div>
    );
}

function StatCard({ title, value, icon: Icon, trend, trendUp, gradient }: { title: string; value: string | number; icon: React.ElementType; trend: string; trendUp: boolean; gradient: string }) {
    return (
        <div className="relative group overflow-hidden rounded-2xl bg-slate-900/50 border border-white/5 p-6 hover:border-white/10 transition-all duration-300">
            <div className={`absolute inset-0 bg-gradient-to-br ${gradient} opacity-5 group-hover:opacity-10 transition-opacity`} />

            <div className="relative flex justify-between items-start">
                <div>
                    <p className="text-sm font-medium text-slate-300">{title}</p>
                    <h3 className="text-3xl font-bold text-white mt-2 tracking-tight">{value}</h3>
                </div>
                <div className="p-3 bg-white/5 rounded-xl border border-white/5 group-hover:scale-110 transition-transform duration-300">
                    <Icon className="w-6 h-6 text-white" />
                </div>
            </div>
            <div className="relative mt-4 flex items-center text-sm">
                <span className={cn("font-bold", trendUp ? "text-emerald-400" : "text-red-400")}>
                    {trend}
                </span>
                <span className="text-slate-400 ml-2">vs last month</span>
            </div>
        </div>
    );
}
