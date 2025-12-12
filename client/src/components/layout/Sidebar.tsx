import { Link, useLocation } from 'react-router-dom';
import {
    LayoutDashboard, PlayCircle, History, User, LogOut, X,
    Users, FileQuestion, Image as ImageIcon, Settings, Activity, Stethoscope, Download
} from 'lucide-react';
import { useAuth } from '../../hooks/useAuth';
import { cn } from '../../lib/utils';
import { ThemeToggle } from '../theme/ThemeToggle';

interface SidebarProps {
    isOpen: boolean;
    onClose: () => void;
}

export function Sidebar({ isOpen, onClose }: SidebarProps) {
    const location = useLocation();
    const { logout, isAdmin, user } = useAuth();

    const userNavigation = [
        { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
        { name: 'Take Quiz', href: '/quiz-config', icon: PlayCircle },
        { name: 'History', href: '/history', icon: History },
        { name: 'Profile', href: '/profile', icon: User },
    ];

    const adminNavigation = [
        { name: 'Overview', href: '/admin', icon: LayoutDashboard },
        { name: 'Users', href: '/admin/users', icon: Users },
        { name: 'Questions', href: '/admin/questions', icon: FileQuestion },
        { name: 'Import/Export', href: '/admin/import-export', icon: Download },
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
                    "fixed inset-y-0 left-0 z-50 w-64 bg-card border-r border-border transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:flex lg:flex-col shadow-2xl",
                    isOpen ? "translate-x-0" : "-translate-x-full"
                )}
            >
                {/* Logo Area */}
                <div className="flex items-center justify-between h-20 px-6 border-b border-border relative overflow-hidden">
                    <div className="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-transparent opacity-50" />
                    <Link to="/dashboard" className="flex items-center space-x-3 relative z-10">
                        <div className="w-12 h-12 relative group flex items-center justify-center">
                            <div className="absolute inset-0 bg-indigo-500 rounded-full blur-lg opacity-20 group-hover:opacity-40 transition-opacity" />
                            <img src={`${import.meta.env.BASE_URL}aiq4.png`} alt="Logo" className="w-full h-full object-contain relative z-10" />
                        </div>
                        <div>
                            <span className="font-bold text-lg text-foreground tracking-tight block">AI Quizzer</span>
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
                                        ? "text-primary-foreground bg-primary/10 border border-primary/20 shadow-[0_0_20px_-5px_rgba(var(--primary),0.3)]"
                                        : "text-muted-foreground hover:text-foreground hover:bg-muted/50 border border-transparent"
                                )}
                                onClick={() => onClose()}
                            >
                                {active && (
                                    <div className="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-500 rounded-r-full shadow-[0_0_10px_rgba(79,70,229,0.5)]" />
                                )}
                                <item.icon
                                    className={cn(
                                        "w-5 h-5 mr-3 transition-colors",
                                        active ? "text-primary" : "text-muted-foreground group-hover:text-foreground"
                                    )}
                                />
                                {item.name}
                            </Link>
                        );
                    })}
                </nav>

                {/* User / Logout Section */}
                <div className="p-4 border-t border-border bg-muted/10">
                    <div className="mb-4">
                        <ThemeToggle />
                    </div>
                    {user && (
                        <div className="flex items-center gap-3 mb-4 px-2">
                            <div className="w-10 h-10 bg-indigo-500/20 rounded-full flex items-center justify-center border border-indigo-500/30 text-indigo-400 font-bold shrink-0">
                                {user.name?.charAt(0).toUpperCase()}
                            </div>
                            <div className="flex-1 min-w-0">
                                <p className="text-sm font-medium text-foreground truncate">{user.name}</p>
                                <p className="text-xs text-muted-foreground truncate capitalize flex items-center gap-1">
                                    {user.role}
                                    {isAdmin && <span className="w-1.5 h-1.5 rounded-full bg-indigo-500" />}
                                </p>
                            </div>
                        </div>
                    )}

                    <button
                        onClick={() => {
                            logout();
                            onClose();
                        }}
                        className="flex items-center w-full px-4 py-3.5 text-sm font-medium text-muted-foreground rounded-xl hover:bg-destructive/10 hover:text-destructive hover:border-destructive/20 border border-transparent transition-all group"
                    >
                        <LogOut className="w-5 h-5 mr-3 group-hover:text-red-400 transition-colors" />
                        Sign Out
                    </button>
                </div>
            </div >
        </>
    );
}
