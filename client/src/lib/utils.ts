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

    // If already a full URL or data URI, return as-is
    if (path.startsWith('http') || path.startsWith('data:')) return path;

    // For development server at http://localhost/projects/playqzv4/
    // Database stores URLs like: /uploads/personality/filename.png
    // We need to prepend /projects/playqzv4/ to get: /projects/playqzv4/uploads/personality/filename.png

    // Ensure path starts with /
    let cleanPath = path.startsWith('/') ? path : `/${path}`;

    // If path doesn't already include /projects/playqzv4/, add it
    if (!cleanPath.startsWith('/projects/playqzv4/')) {
        cleanPath = `/projects/playqzv4${cleanPath}`;
    }

    return cleanPath;
}
