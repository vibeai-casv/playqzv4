import { Link, useLocation } from 'react-router-dom';
import {
    LayoutDashboard, PlayCircle, History, User, LogOut, X,
    Users, FileQuestion, Image as ImageIcon, Settings, Activity, Stethoscope
} from 'lucide-react';
import { useAuth } from '../../hooks/useAuth';
import { cn } from '../../lib/utils';

interface SidebarProps {
    isOpen: boolean;
    onClose: () => void;
}

export function Sidebar({ isOpen, onClose }: SidebarProps) {
    const location = useLocation();
    const { logout, isAdmin } = useAuth();

    const userNavigation = [
        { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
        { name: 'Take Quiz', href: '/take-quiz', icon: PlayCircle },
        { name: 'History', href: '/history', icon: History },
        { name: 'Profile', href: '/profile', icon: User },
    ];

    const adminNavigation = [
        { name: 'Overview', href: '/admin', icon: LayoutDashboard },
        { name: 'Users', href: '/admin/users', icon: Users },
        { name: 'Questions', href: '/admin/questions', icon: FileQuestion },
        { name: 'Media Library', href: '/admin/media', icon: ImageIcon },
        { name: 'Activity Logs', href: '/admin/activity', icon: Activity },
        { name: 'System Tools', href: '/admin/system', icon: Settings },
        { name: 'Diagnostics', href: '/admin/diagnostics', icon: Stethoscope },
    ];

    const navigation = isAdmin ? adminNavigation : userNavigation;

    const isActive = (path: string) => location.pathname === path;

    return (
        <>
            {/* Mobile backdrop */}
            <div
                className={cn(
                    "fixed inset-0 z-40 bg-black/80 backdrop-blur-sm transition-opacity lg:hidden",
                    isOpen ? "opacity-100" : "opacity-0 pointer-events-none"
                )}
                onClick={onClose}
            />

            {/* Sidebar */}
            <div
                className={cn(
                    "fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 border-r border-white/10 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:flex lg:flex-col shadow-2xl shadow-black",
                    isOpen ? "translate-x-0" : "-translate-x-full"
                )}
            >
                {/* Logo Area */}
                <div className="flex items-center justify-between h-20 px-6 border-b border-white/5 relative overflow-hidden">
                    <div className="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-transparent opacity-50" />
                    <Link to="/dashboard" className="flex items-center space-x-3 relative z-10">
                        <div className="w-10 h-10 relative group">
                            <div className="absolute inset-0 bg-indigo-500 rounded-xl blur-lg opacity-40 group-hover:opacity-60 transition-opacity" />
                            <div className="relative w-full h-full bg-slate-900 rounded-xl border border-white/10 flex items-center justify-center overflow-hidden">
                                <img src="/aiqmpm.png" alt="Logo" className="w-8 h-8 object-contain" />
                            </div>
                        </div>
                        <div>
                            <span className="font-bold text-lg text-white tracking-tight block">AI Quiz</span>
                            <span className="text-[10px] text-indigo-400 uppercase tracking-wider font-medium">Platform</span>
                        </div>
                    </Link>
                    <button
                        onClick={onClose}
                        className="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 lg:hidden transition-colors"
                        aria-label="Close sidebar"
                    >
                        <X className="w-6 h-6" />
                    </button>
                </div>

                {/* Navigation */}
                <nav className="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
                    {navigation.map((item) => {
                        const active = isActive(item.href);
                        return (
                            <Link
                                key={item.name}
                                to={item.href}
                                className={cn(
                                    "flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-200 group relative overflow-hidden",
                                    active
                                        ? "text-white bg-indigo-600/10 border border-indigo-500/20 shadow-[0_0_20px_-5px_rgba(79,70,229,0.3)]"
                                        : "text-slate-400 hover:text-white hover:bg-white/5 border border-transparent"
                                )}
                                onClick={() => onClose()}
                            >
                                {active && (
                                    <div className="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-500 rounded-r-full shadow-[0_0_10px_rgba(79,70,229,0.5)]" />
                                )}
                                <item.icon
                                    className={cn(
                                        "w-5 h-5 mr-3 transition-colors",
                                        active ? "text-indigo-400" : "text-slate-500 group-hover:text-slate-300"
                                    )}
                                />
                                {item.name}
                            </Link>
                        );
                    })}
                </nav>

                {/* User / Logout Section */}
                <div className="p-4 border-t border-white/5 bg-slate-900/50">
                    <button
                        onClick={() => {
                            logout();
                            onClose();
                        }}
                        className="flex items-center w-full px-4 py-3.5 text-sm font-medium text-slate-400 rounded-xl hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/20 border border-transparent transition-all group"
                    >
                        <LogOut className="w-5 h-5 mr-3 group-hover:text-red-400 transition-colors" />
                        Sign Out
                    </button>
                </div>
            </div>
        </>
    );
}
