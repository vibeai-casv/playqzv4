import React from 'react';


interface AuthLayoutProps {
    children: React.ReactNode;
}

export const AuthLayout: React.FC<AuthLayoutProps> = ({ children }) => {
    return (
        <div className="min-h-[calc(100vh-4rem)] flex items-center justify-center p-4 sm:p-8 relative overflow-hidden bg-slate-950">
            {/* AI/Cyber Background */}
            <div className="absolute inset-0 overflow-hidden pointer-events-none">
                <div className="absolute -top-[30%] -left-[10%] w-[70%] h-[70%] rounded-full bg-indigo-500/20 blur-[120px] animate-pulse" />
                <div className="absolute top-[20%] -right-[10%] w-[60%] h-[60%] rounded-full bg-cyan-500/20 blur-[120px] animate-pulse delay-700" />
                <div className="absolute -bottom-[20%] left-[20%] w-[50%] h-[50%] rounded-full bg-purple-500/20 blur-[120px] animate-pulse delay-1000" />

                {/* Grid Pattern */}
                <div className="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]" />
            </div>

            <div className="w-full max-w-md relative z-10">
                {/* Glassmorphism Card */}
                <div className="bg-slate-900/40 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl p-8 relative overflow-hidden ring-1 ring-white/5">
                    {/* Top Highlight */}
                    <div className="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-50" />

                    {/* Logo Area */}
                    <div className="flex flex-col items-center mb-8">
                        <div className="w-20 h-20 relative mb-4 p-3 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl border border-white/10 shadow-lg group">
                            <div className="absolute inset-0 bg-indigo-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500 opacity-0 group-hover:opacity-100" />
                            <img
                                src="/aiqmpm.png"
                                alt="PlayQz Logo"
                                className="w-full h-full object-contain relative z-10 drop-shadow-md"
                            />
                        </div>
                        <div className="text-center">
                            <h2 className="text-2xl font-bold text-white tracking-tight">
                                AI Quiz Platform
                            </h2>
                            <p className="text-sm text-indigo-300/80 mt-1 font-medium">
                                CAS Vattamkulam
                            </p>
                        </div>
                    </div>

                    <div className="relative">
                        {children}
                    </div>
                </div>

                {/* Bottom Text */}
                <div className="text-center mt-6 text-slate-500 text-xs font-medium tracking-wide uppercase">
                    Powered by Advanced AI &bull; Secure &bull; Intelligent
                </div>
            </div>
        </div>
    );
};
