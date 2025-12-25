import { useState } from 'react';
import { Upload, FileJson, AlertCircle, CheckCircle2, XCircle } from 'lucide-react';
import { toast } from 'sonner';
import api from '../../lib/api';
import axios from 'axios'; // Keep axios for isAxiosError check

interface ImportResult {
    imported: number;
    skipped: number;
    errors: string[];
    total_processed: number;
}

interface JSONImporterProps {
    onImportComplete: () => void;
    onCancel: () => void;
}

export function JSONImporter({ onImportComplete, onCancel }: JSONImporterProps) {
    const [file, setFile] = useState<File | null>(null);
    const [skipDuplicates, setSkipDuplicates] = useState(true);
    const [isImporting, setIsImporting] = useState(false);
    const [result, setResult] = useState<ImportResult | null>(null);

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const selectedFile = e.target.files?.[0];
        if (selectedFile) {
            if (selectedFile.type !== 'application/json') {
                toast.error('Please select a JSON file');
                return;
            }
            setFile(selectedFile);
            setResult(null);
        }
    };

    const handleImport = async () => {
        if (!file) {
            toast.error('Please select a file');
            return;
        }

        setIsImporting(true);
        setResult(null);

        try {
            // Read file content
            const fileContent = await file.text();

            // 1. Clean the content (strip markdown code blocks if present)
            let cleanedContent = fileContent.trim();
            if (cleanedContent.startsWith('```')) {
                cleanedContent = cleanedContent.replace(/^```(?:json)?/, '').replace(/```$/, '').trim();
            }

            // 2. Parse JSON
            let data;
            try {
                data = JSON.parse(cleanedContent);
            } catch (e: any) {
                // Try one more time stripping common hidden characters
                const reCleaned = cleanedContent.replace(/[\u200B-\u200D\uFEFF]/g, '');
                data = JSON.parse(reCleaned);
            }

            // 3. Extract questions array
            let questionsArray: any[] = [];
            if (Array.isArray(data)) {
                questionsArray = data;
            } else if (data && typeof data === 'object' && Array.isArray(data.questions)) {
                questionsArray = data.questions;
            } else {
                toast.error('JSON must be an array of questions or an object with a "questions" array');
                setIsImporting(false);
                return;
            }

            // 4. Send to API
            const response = await api.post(
                '/questions/import.php',
                {
                    questions: questionsArray,
                    skipDuplicates
                }
            );

            setResult(response.data);

            if (response.data.imported > 0) {
                toast.success(`Successfully imported ${response.data.imported} questions!`);
                if (response.data.skipped > 0) {
                    toast.info(`Skipped ${response.data.skipped} duplicate questions`);
                }
            } else {
                toast.warning('No questions were imported');
            }

            if (response.data.errors.length > 0) {
                console.error('Import errors:', response.data.errors);
            }

        } catch (error) {
            console.error('Import error:', error);

            let errorMessage = 'Failed to import questions';

            if (axios.isAxiosError(error)) {
                if (error.response) {
                    // Server responded with error
                    const data = error.response.data;

                    if (data.error) {
                        errorMessage = data.error;
                    }

                    // Show partial results if any
                    if (data.imported > 0 || data.skipped > 0) {
                        setResult({
                            imported: data.imported || 0,
                            skipped: data.skipped || 0,
                            errors: data.errors || [],
                            total_processed: data.total_processed || 0
                        });

                        if (data.imported > 0) {
                            toast.success(`Partially imported ${data.imported} questions`);
                        }
                    }

                    console.error('Server error:', data);
                } else if (error.request) {
                    // Request made but no response
                    errorMessage = 'No response from server. Check your connection.';
                    console.error('No response:', error.request);
                } else {
                    // Error setting up request
                    errorMessage = error.message;
                }
            } else if (error instanceof Error) {
                errorMessage = error.message;
            }

            toast.error(errorMessage);
        } finally {
            setIsImporting(false);
        }
    };

    const handleClose = () => {
        if (result && result.imported > 0) {
            onImportComplete();
        } else {
            onCancel();
        }
    };

    return (
        <div className="space-y-6">
            {/* File Upload */}
            <div className="space-y-4">
                <div className="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                    <FileJson className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <label className="cursor-pointer">
                        <span className="text-indigo-600 hover:text-indigo-700 font-medium">
                            Choose a JSON file
                        </span>
                        <input
                            type="file"
                            accept=".json"
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

                {/* Options */}
                <div className="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="skipDuplicates"
                        checked={skipDuplicates}
                        onChange={(e) => setSkipDuplicates(e.target.checked)}
                        className="rounded border-gray-300"
                    />
                    <label htmlFor="skipDuplicates" className="text-sm text-gray-700 dark:text-gray-300">
                        Skip duplicate questions (based on question text)
                    </label>
                </div>

                {/* Expected Format */}
                <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 className="font-medium text-blue-900 dark:text-blue-100 mb-2 flex items-center gap-2">
                        <AlertCircle className="w-4 h-4" />
                        Expected JSON Format
                    </h4>
                    <pre className="text-xs bg-white dark:bg-gray-800 p-3 rounded overflow-x-auto">
                        {`[
  {
    "type": "text_mcq",
    "category": "Fundamentals",
    "difficulty": "easy",
    "question": "What is AI?",
    "options": ["Option 1", "Option 2", "Option 3", "Option 4"],
    "correct_answer": "Option 1",
    "image_url": "optional",
    "explanation": "optional"
  }
]`}
                    </pre>
                </div>
            </div>

            {/* Import Result */}
            {result && (
                <div className="space-y-3">
                    <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-2">
                        <div className="flex items-center gap-2 text-green-600 dark:text-green-400">
                            <CheckCircle2 className="w-5 h-5" />
                            <span className="font-medium">
                                Imported: {result.imported} questions
                            </span>
                        </div>
                        {result.skipped > 0 && (
                            <div className="flex items-center gap-2 text-yellow-600 dark:text-yellow-400">
                                <AlertCircle className="w-5 h-5" />
                                <span className="font-medium">
                                    Skipped: {result.skipped} duplicates
                                </span>
                            </div>
                        )}
                        {result.errors.length > 0 && (
                            <div className="flex items-start gap-2 text-red-600 dark:text-red-400">
                                <XCircle className="w-5 h-5 mt-0.5" />
                                <div className="flex-1">
                                    <span className="font-medium block mb-1">
                                        Errors: {result.errors.length}
                                    </span>
                                    <div className="text-sm space-y-1 max-h-40 overflow-y-auto">
                                        {result.errors.map((error, i) => (
                                            <div key={i} className="text-xs">• {error}</div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}
                        <div className="text-sm text-gray-600 dark:text-gray-400 pt-2 border-t border-gray-200 dark:border-gray-600">
                            Total processed: {result.total_processed}
                        </div>
                    </div>
                </div>
            )}

            {/* Actions */}
            <div className="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    onClick={handleClose}
                    className="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                >
                    {result && result.imported > 0 ? 'Close' : 'Cancel'}
                </button>
                <button
                    onClick={handleImport}
                    disabled={!file || isImporting}
                    className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Upload className="w-4 h-4" />
                    {isImporting ? 'Importing...' : 'Import Questions'}
                </button>
            </div>
        </div>
    );
}
