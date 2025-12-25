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

// Safe UUID generator that works in non-secure contexts
export function generateUuid(): string {
    if (typeof crypto !== 'undefined' && crypto.randomUUID) {
        return crypto.randomUUID();
    }
    // Fallback for non-secure contexts (http)
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

export function getImageUrl(path: string | undefined | null): string {
    if (!path) return '';

    // If already a full URL or data URI, return as-is
    if (path.startsWith('http') || path.startsWith('data:')) return path;

    // Ensure path starts with /
    const cleanPath = path.startsWith('/') ? path : `/${path}`;

    if (typeof window !== 'undefined') {
        const currentPath = window.location.pathname;
        const hostname = window.location.hostname;

        // Helper to ensure path includes /uploads/ accurately
        const formatPath = (prefix: string = '') => {
            const pathWithUploads = cleanPath.startsWith('/uploads/') ? cleanPath : `/uploads${cleanPath}`;
            return `${prefix}${pathWithUploads}`;
        };

        // 1. Nested Development / Subdirectory deployments
        if (currentPath.startsWith('/playqzv4/aiq4/')) return formatPath('/playqzv4/aiq4');
        if (currentPath.startsWith('/aiq4/')) return formatPath('/aiq4');
        if (currentPath.startsWith('/aiq3/')) return formatPath('/aiq3');
        if (currentPath.startsWith('/playqzv4/')) return formatPath('/playqzv4');

        // 2. Local XAMPP projects hostname
        if (hostname === 'projects' && currentPath.includes('/playqzv4/')) {
            return formatPath('/playqzv4');
        }

        // 3. Absolute Root Deployment (Production Root or localhost root)
        // If it starts with /uploads/ already, just return it as a root-relative path
        if (cleanPath.startsWith('/uploads/')) {
            return cleanPath;
        }

        // 4. Default: prepend /uploads if missing
        return `/uploads${cleanPath}`;
    }

    return cleanPath.startsWith('/uploads/') ? cleanPath : `/uploads${cleanPath}`;
}
