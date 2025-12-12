import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function formatDate(date: string | Date): string {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

export function formatTime(seconds: number): string {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

export function formatScore(score: number): string {
    return `${score.toFixed(1)}%`;
}

export function getInitials(name: string): string {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

export function truncateText(text: string, maxLength: number): string {
    if (text.length <= maxLength) return text;
    return text.slice(0, maxLength) + '...';
}

export function debounce<T extends (...args: unknown[]) => unknown>(
    func: T,
    wait: number
): (...args: Parameters<T>) => void {
    let timeout: ReturnType<typeof setTimeout>;
    return (...args: Parameters<T>) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

export function shuffle<T>(array: T[]): T[] {
    const newArray = [...array];
    for (let i = newArray.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
    }
    return newArray;
}

export function getImageUrl(path: string | undefined | null): string {
    if (!path) return '';
    if (path.startsWith('http') || path.startsWith('data:')) return path;

    // Get base URL from API URL
    const apiUrl = import.meta.env.VITE_API_URL || '';
    let baseUrl = '';

    try {
        if (apiUrl) {
            const url = new URL(apiUrl);
            const pathname = url.pathname;

            // If API is at .../api, assume uploads are at .../uploads (sibling)
            // Example: http://host/app/api -> http://host/app
            if (pathname.endsWith('/api') || pathname.endsWith('/api/')) {
                const cleanPath = pathname.endsWith('/') ? pathname.slice(0, -1) : pathname;
                baseUrl = url.origin + cleanPath.slice(0, -4); // Remove /api
            } else {
                baseUrl = url.origin + (pathname === '/' ? '' : pathname);
            }

            // Remove trailing slash
            if (baseUrl.endsWith('/')) baseUrl = baseUrl.slice(0, -1);
        }
    } catch (e) {
        console.error('Error parsing API URL', e);
    }

    // Ensure path starts with /
    const cleanPath = path.startsWith('/') ? path : `/${path}`;

    // If we have a base URL, use it. Otherwise rely on relative path (proxy)
    // But if proxy is misconfigured, this won't help. 
    // However, if we are in production, API URL should be set correctly.

    return baseUrl ? `${baseUrl}${cleanPath}` : cleanPath;
}
