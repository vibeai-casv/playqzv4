import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';
import { LogOut } from 'lucide-react';

export function Header() {
    const { user, isAuthenticated, isAdmin, logout } = useAuth();
    const navigate = useNavigate();

    const handleLogout = async () => {
        await logout();
        navigate('/login');
    };

    return (
        <header className="bg-white dark:bg-zinc-900 shadow-sm border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-50">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-center h-16">
                    {/* Logo */}
                    <Link to="/" className="flex items-center space-x-2">
                        <div className="w-8 h-8 bg-primary rounded-lg flex items-center justify-center shadow-lg shadow-primary/20">
                            <span className="text-primary-foreground font-bold text-xl">Q</span>
                        </div>
                        <span className="font-bold text-xl text-zinc-900 dark:text-white">AI Quizzer</span>
                    </Link>

                    {/* Navigation */}
                    {isAuthenticated && (
                        <nav className="hidden md:flex items-center space-x-6">
                            <Link
                                to="/dashboard"
                                className="text-sm font-medium text-muted-foreground hover:text-primary transition-colors"
                            >
                                Dashboard
                            </Link>
                            <Link
                                to="/take-quiz"
                                className="text-sm font-medium text-muted-foreground hover:text-primary transition-colors"
                            >
                                Take Quiz
                            </Link>
                            <Link
                                to="/history"
                                className="text-sm font-medium text-muted-foreground hover:text-primary transition-colors"
                            >
                                History
                            </Link>
                            {isAdmin && (
                                <Link
                                    to="/admin"
                                    className="text-sm font-medium text-muted-foreground hover:text-primary transition-colors"
                                >
                                    Admin
                                </Link>
                            )}
                        </nav>
                    )}

                    {/* User Menu */}
                    <div className="flex items-center space-x-4">
                        {isAuthenticated ? (
                            <div className="flex items-center gap-4">
                                <Link
                                    to="/profile"
                                    className="flex items-center gap-2 group"
                                    aria-label="User profile"
                                >
                                    <div className="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center border border-primary/20 transition-colors group-hover:border-primary/40">
                                        <span className="text-primary font-medium text-sm">
                                            {user?.name?.charAt(0).toUpperCase()}
                                        </span>
                                    </div>
                                    <div className="hidden md:flex flex-col items-start">
                                        <div className="flex items-center gap-2">
                                            <span className="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                                                {user?.name}
                                            </span>
                                            {isAdmin && (
                                                <span className="text-[10px] font-bold text-primary bg-primary/10 px-1.5 py-0.5 rounded border border-primary/20">
                                                    ADMIN
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                </Link>

                                <div className="h-6 w-px bg-zinc-200 dark:bg-zinc-700 mx-1"></div>

                                <button
                                    onClick={handleLogout}
                                    className="flex items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-all"
                                    title="Logout"
                                >
                                    <LogOut className="w-4 h-4" />
                                    <span className="hidden sm:inline">Logout</span>
                                </button>
                            </div>
                        ) : (
                            <>
                                <Link
                                    to="/login"
                                    className="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors"
                                >
                                    Login
                                </Link>
                                <Link
                                    to="/signup"
                                    className="px-4 py-2 text-sm font-medium text-primary-foreground bg-primary rounded-lg hover:bg-primary/90 shadow-sm shadow-primary/20 transition-all"
                                >
                                    Sign Up
                                </Link>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
}
