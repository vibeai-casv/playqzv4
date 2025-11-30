import { Moon, Sun, Laptop, Palette } from "lucide-react";
import { useTheme } from "./ThemeProvider";
import { useState, useRef, useEffect } from "react";

export function ThemeToggle() {
    const { theme, setTheme, colorTheme, setColorTheme } = useTheme();
    const [showColors, setShowColors] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
                setShowColors(false);
            }
        };

        document.addEventListener("mousedown", handleClickOutside);
        return () => document.removeEventListener("mousedown", handleClickOutside);
    }, []);

    const colors = [
        { name: "blue", color: "bg-[#00c2ff]" },
        { name: "green", color: "bg-[#10b981]" },
        { name: "purple", color: "bg-[#8b5cf6]" },
        { name: "orange", color: "bg-[#f97316]" },
        { name: "red", color: "bg-[#ef4444]" },
        { name: "slate", color: "bg-[#64748b]" },
    ] as const;

    return (
        <div className="relative" ref={containerRef}>
            <div className="flex items-center gap-1 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                <button
                    onClick={() => setTheme("light")}
                    className={`p-1.5 rounded-md transition-all duration-200 ${theme === "light"
                        ? "bg-white dark:bg-zinc-600 text-indigo-600 dark:text-indigo-400 shadow-sm"
                        : "text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-700/50"
                        }`}
                    title="Light Mode"
                >
                    <Sun className="w-4 h-4" />
                </button>
                <button
                    onClick={() => setTheme("system")}
                    className={`p-1.5 rounded-md transition-all duration-200 ${theme === "system"
                        ? "bg-white dark:bg-zinc-600 text-indigo-600 dark:text-indigo-400 shadow-sm"
                        : "text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-700/50"
                        }`}
                    title="System Theme"
                >
                    <Laptop className="w-4 h-4" />
                </button>
                <button
                    onClick={() => setTheme("dark")}
                    className={`p-1.5 rounded-md transition-all duration-200 ${theme === "dark"
                        ? "bg-white dark:bg-zinc-600 text-indigo-600 dark:text-indigo-400 shadow-sm"
                        : "text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-700/50"
                        }`}
                    title="Dark Mode"
                >
                    <Moon className="w-4 h-4" />
                </button>

                <div className="w-px h-4 bg-zinc-300 dark:bg-zinc-600 mx-0.5" />

                <button
                    onClick={() => setShowColors(!showColors)}
                    className={`p-1.5 rounded-md transition-all duration-200 ${showColors
                        ? "bg-white dark:bg-zinc-600 text-indigo-600 dark:text-indigo-400 shadow-sm"
                        : "text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-700/50"
                        }`}
                    title="Select Color Theme"
                >
                    <Palette className="w-4 h-4" />
                </button>
            </div>

            {showColors && (
                <div className="absolute right-0 bottom-full mb-2 p-2 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-xl grid grid-cols-3 gap-2 z-50 min-w-[120px]">
                    {colors.map((c) => (
                        <button
                            key={c.name}
                            onClick={() => {
                                setColorTheme(c.name);
                                setShowColors(false);
                            }}
                            className={`w-8 h-8 rounded-full flex items-center justify-center transition-transform hover:scale-110 ${c.color
                                } ${colorTheme === c.name ? "ring-2 ring-offset-2 ring-offset-white dark:ring-offset-zinc-900 ring-zinc-900 dark:ring-white" : ""}`}
                            title={c.name.charAt(0).toUpperCase() + c.name.slice(1)}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}
