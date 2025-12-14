import { useState } from 'react';
import { Package, AlertCircle, CheckCircle2, XCircle } from 'lucide-react';
import { toast } from 'sonner';
import api from '../../lib/api';
import axios from 'axios';

interface ImportStats {
    questions_imported: number;
    questions_skipped: number;
    media_imported: number;
    errors: string[];
}

interface BundleImporterProps {
    onImportComplete: () => void;
    onCancel: () => void;
}

export function BundleImporter({ onImportComplete, onCancel }: BundleImporterProps) {
    const [file, setFile] = useState<File | null>(null);
    const [isImporting, setIsImporting] = useState(false);
    const [stats, setStats] = useState<ImportStats | null>(null);

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const selectedFile = e.target.files?.[0];
        if (selectedFile) {
            if (!selectedFile.name.endsWith('.zip')) {
                toast.error('Please select a ZIP file');
                return;
            }
            setFile(selectedFile);
            setStats(null);
        }
    };

    const handleImport = async () => {
        if (!file) {
            toast.error('Please select a file');
            return;
        }

        setIsImporting(true);
        setStats(null);

        // Diagnostic logging
        console.log('=== BUNDLE IMPORT STARTED ===');
        console.log('File:', {
            name: file.name,
            size: file.size,
            type: file.type,
            lastModified: new Date(file.lastModified).toISOString()
        });

        try {
            console.log('Step 1: Creating FormData...');
            const formData = new FormData();
            formData.append('bundle', file);
            console.log('✅ FormData created');

            // Log API URL being used
            const apiBaseURL = (api.defaults.baseURL || '') + '/bundle/import.php';
            console.log('Step 2: Sending POST request to:', apiBaseURL);
            console.log('Request headers will include:', {
                'Content-Type': 'multipart/form-data',
                'Authorization': sessionStorage.getItem('auth_token') ? '✓ Present' : '✗ Missing'
            });

            toast.info('Uploading bundle...', { duration: 2000 });

            const response = await api.post('/bundle/import.php', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });

            console.log('Step 3: Response received');
            console.log('Response status:', response.status);
            console.log('Response data:', response.data);

            setStats(response.data.stats);

            // Log import results
            console.log('Import Stats:', {
                questions_imported: response.data.stats.questions_imported,
                questions_skipped: response.data.stats.questions_skipped,
                media_imported: response.data.stats.media_imported,
                errors_count: response.data.stats.errors.length
            });

            if (response.data.stats.questions_imported > 0) {
                toast.success(`Successfully imported ${response.data.stats.questions_imported} question(s)!`);
                console.log('✅ Import successful');
            } else {
                toast.warning('No new questions were imported (records may already exist)');
                console.log('⚠️ No questions imported (possibly duplicates)');
            }

            if (response.data.stats.errors.length > 0) {
                console.error('Import errors:', response.data.stats.errors);
            }

            console.log('=== BUNDLE IMPORT COMPLETED ===');

        } catch (error) {
            console.error('=== BUNDLE IMPORT FAILED ===');
            console.error('Error details:', error);

            let errorMessage = 'Failed to import bundle';

            if (axios.isAxiosError(error)) {
                console.error('Axios Error:', {
                    message: error.message,
                    code: error.code,
                    status: error.response?.status,
                    statusText: error.response?.statusText,
                    responseData: error.response?.data,
                    requestURL: error.config?.url,
                    requestMethod: error.config?.method
                });

                if (error.response?.data?.error) {
                    errorMessage = error.response.data.error;
                } else if (error.response?.status === 404) {
                    errorMessage = 'Import endpoint not found - check if /bundle/import.php exists';
                } else if (error.response?.status === 413) {
                    errorMessage = 'File too large - check server upload limits';
                } else if (error.response?.status === 500) {
                    errorMessage = 'Server error - check server logs';
                } else if (error.code === 'ERR_NETWORK') {
                    errorMessage = 'Network error - check connection';
                }
            } else if (error instanceof Error) {
                console.error('Generic Error:', error.message);
                errorMessage = error.message;
            }

            toast.error(errorMessage, { duration: 5000 });
            console.log('Error message shown to user:', errorMessage);

        } finally {
            setIsImporting(false);
            console.log('=== IMPORT PROCESS ENDED ===');
        }
    };

    const handleClose = () => {
        if (stats && stats.questions_imported > 0) {
            onImportComplete();
        } else {
            onCancel();
        }
    };

    return (
        <div className="space-y-6">
            <div className="space-y-4">
                <div className="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                    <Package className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <label className="cursor-pointer">
                        <span className="text-indigo-600 hover:text-indigo-700 font-medium">
                            Choose Question Bundle (ZIP)
                        </span>
                        <input
                            type="file"
                            accept=".zip"
                            onChange={handleFileChange}
                            className="hidden"
                        />
                    </label>
                    {file && (
                        <p className="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Selected: {file.name}
                        </p>
                    )}
                </div>

                <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-sm text-blue-900 dark:text-blue-100">
                    <p className="flex items-center gap-2 mb-1">
                        <AlertCircle className="w-4 h-4" />
                        <strong>Importing Bundles</strong>
                    </p>
                    <p className="opacity-90 ml-6">
                        Bundles contain questions and their associated images. Existing questions (by ID) will be skipped to prevent duplicates.
                    </p>
                </div>
            </div>

            {stats && (
                <div className="space-y-3">
                    <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-2">
                        <div className="flex items-center gap-2 text-green-600 dark:text-green-400">
                            <CheckCircle2 className="w-5 h-5" />
                            <span className="font-medium">
                                Questions Imported: {stats.questions_imported}
                            </span>
                        </div>
                        <div className="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                            <CheckCircle2 className="w-5 h-5" />
                            <span className="font-medium">
                                Images Imported: {stats.media_imported}
                            </span>
                        </div>
                        {stats.questions_skipped > 0 && (
                            <div className="flex items-center gap-2 text-yellow-600 dark:text-yellow-400">
                                <AlertCircle className="w-5 h-5" />
                                <span className="font-medium">
                                    Skipped (Duplicates): {stats.questions_skipped}
                                </span>
                            </div>
                        )}
                        {stats.errors.length > 0 && (
                            <div className="flex items-start gap-2 text-red-600 dark:text-red-400 pt-2 border-t border-gray-200 dark:border-gray-600 mt-2">
                                <XCircle className="w-5 h-5 mt-0.5" />
                                <div className="flex-1">
                                    <span className="font-medium block mb-1">
                                        Errors: {stats.errors.length}
                                    </span>
                                    <div className="text-sm space-y-1 max-h-40 overflow-y-auto">
                                        {stats.errors.map((error, i) => (
                                            <div key={i} className="text-xs">• {error}</div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            )}

            <div className="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    onClick={handleClose}
                    className="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                >
                    {stats ? 'Close' : 'Cancel'}
                </button>
                <button
                    onClick={handleImport}
                    disabled={!file || isImporting}
                    className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Package className="w-4 h-4" />
                    {isImporting ? 'Importing...' : 'Import Bundle'}
                </button>
            </div>
        </div>
    );
}
