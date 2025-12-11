import React, { useState, useCallback } from 'react';
import { Upload, Image as ImageIcon, Check } from 'lucide-react';
import { useAdmin } from '../hooks/useAdmin';

interface MediaItem {
    id: string;
    url: string;
    filename: string;
    original_filename: string;
    type: string;
    mime_type: string;
    size_bytes: number;
    description?: string;
    metadata?: {
        width?: number;
        height?: number;
    };
    created_at: string;
}

interface LogoUploaderProps {
    onSelect: (media: MediaItem) => void;
    selectedMediaId?: string;
    mediaType?: 'logo' | 'personality' | 'question_image';
}

export default function LogoUploader({ onSelect, selectedMediaId, mediaType = 'logo' }: LogoUploaderProps) {
    const { uploadMedia, fetchMedia } = useAdmin();
    const [uploading, setUploading] = useState(false);
    const [mediaLibrary, setMediaLibrary] = useState<MediaItem[]>([]);
    const [showLibrary, setShowLibrary] = useState(false);
    const [loading, setLoading] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');
    const [dragActive, setDragActive] = useState(false);

    // Load media library
    const loadMediaLibrary = useCallback(async () => {
        setLoading(true);
        try {
            const response = await fetchMedia({ type: mediaType, limit: 100 });
            if (response.media) {
                setMediaLibrary(response.media);
            }
        } catch (error) {
            console.error('Failed to load media library:', error);
        } finally {
            setLoading(false);
        }
    }, [mediaType, fetchMedia]);

    // Handle file upload
    const handleUpload = async (file: File) => {
        if (!file) return;

        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        if (!validTypes.includes(file.type)) {
            alert('Invalid file type. Please upload JPG, PNG, GIF, WebP, or SVG');
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File too large. Maximum size is 5MB');
            return;
        }

        setUploading(true);
        try {
            const response = await uploadMedia(file, mediaType);

            if (response && response.media) {
                onSelect(response.media);
                setMediaLibrary(prev => [response.media, ...prev]);
                alert('Logo uploaded successfully!');
            }
        } catch (error: any) {
            console.error('Upload failed:', error);
            alert(error.message || 'Failed to upload logo');
        } finally {
            setUploading(false);
        }
    };

    // Handle drag and drop
    const handleDrag = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.type === 'dragenter' || e.type === 'dragover') {
            setDragActive(true);
        } else if (e.type === 'dragleave') {
            setDragActive(false);
        }
    };

    const handleDrop = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);

        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleUpload(e.dataTransfer.files[0]);
        }
    };

    // Handle file input
    const handleFileInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            handleUpload(e.target.files[0]);
        }
    };

    // Filter media library
    const filteredMedia = mediaLibrary.filter(item =>
        item.original_filename.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (item.description?.toLowerCase().includes(searchTerm.toLowerCase()))
    );

    return (
        <div className="space-y-4">
            {/* Upload Area */}
            <div
                className={`border-2 border-dashed rounded-lg p-8 text-center transition-colors ${dragActive ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'
                    }`}
                onDragEnter={handleDrag}
                onDragLeave={handleDrag}
                onDragOver={handleDrag}
                onDrop={handleDrop}
            >
                <input
                    type="file"
                    id="logo-upload"
                    className="hidden"
                    accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml"
                    onChange={handleFileInput}
                    disabled={uploading}
                />

                <label htmlFor="logo-upload" className="cursor-pointer">
                    <Upload className="w-12 h-12 mx-auto mb-4 text-gray-400" />
                    <p className="text-lg font-medium text-gray-700 mb-2">
                        {uploading ? 'Uploading...' : 'Upload Logo Image'}
                    </p>
                    <p className="text-sm text-gray-500">
                        Drag and drop or click to browse
                    </p>
                    <p className="text-xs text-gray-400 mt-2">
                        JPG, PNG, GIF, WebP, SVG (max 5MB)
                    </p>
                </label>
            </div>

            {/* Browse Library Button */}
            <button
                type="button"
                onClick={() => {
                    setShowLibrary(!showLibrary);
                    if (!showLibrary && mediaLibrary.length === 0) {
                        loadMediaLibrary();
                    }
                }}
                className="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center justify-center gap-2"
            >
                <ImageIcon className="w-4 h-4" />
                {showLibrary ? 'Hide' : 'Browse'} Media Library
            </button>

            {/* Media Library */}
            {showLibrary && (
                <div className="border rounded-lg p-4 bg-gray-50">
                    <div className="mb-4">
                        <input
                            type="text"
                            placeholder="Search logos..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>

                    {loading ? (
                        <div className="text-center py-8 text-gray-500">Loading...</div>
                    ) : filteredMedia.length === 0 ? (
                        <div className="text-center py-8 text-gray-500">
                            No logos found. Upload your first logo above!
                        </div>
                    ) : (
                        <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4 max-h-96 overflow-y-auto">
                            {filteredMedia.map((item) => (
                                <button
                                    key={item.id}
                                    type="button"
                                    onClick={() => {
                                        onSelect(item);
                                        setShowLibrary(false);
                                    }}
                                    className={`relative aspect-square rounded-lg overflow-hidden border-2 transition-all hover:scale-105 ${selectedMediaId === item.id
                                        ? 'border-blue-500 ring-2 ring-blue-200'
                                        : 'border-gray-200 hover:border-gray-300'
                                        }`}
                                >
                                    <img
                                        src={item.url}
                                        alt={item.original_filename}
                                        className="w-full h-full object-contain bg-white p-2"
                                    />
                                    {selectedMediaId === item.id && (
                                        <div className="absolute top-1 right-1 bg-blue-500 text-white rounded-full p-1">
                                            <Check className="w-3 h-3" />
                                        </div>
                                    )}
                                    <div className="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 truncate">
                                        {item.original_filename}
                                    </div>
                                </button>
                            ))}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}
