import { useState, useEffect } from 'react';
import { Download, Upload, FileJson, Loader2, CheckSquare, Square, Filter, X } from 'lucide-react';
import { useAdmin } from '../../hooks/useAdmin';
import { Question } from '../../types';
import { toast } from 'sonner';
import { cn } from '../../lib/utils';

export function ImportExport() {
    const { fetchQuestions, isLoading } = useAdmin();
    const [activeTab, setActiveTab] = useState<'export' | 'import'>('export');

    // Export state
    const [questions, setQuestions] = useState<Question[]>([]);
    const [selectedQuestions, setSelectedQuestions] = useState<Set<string>>(new Set());
    const [filters, setFilters] = useState({
        type: 'all',
        difficulty: 'all'
    });

    // Import state
    const [importFile, setImportFile] = useState<File | null>(null);
    const [previewQuestions, setPreviewQuestions] = useState<Question[]>([]);
    const [importing, setImporting] = useState(false);

    useEffect(() => {
        if (activeTab === 'export') {
            loadQuestions();
        }
    }, [activeTab, filters]);

    const loadQuestions = async () => {
        try {
            const params: any = {};
            if (filters.type !== 'all') params.type = filters.type;
            if (filters.difficulty !== 'all') params.difficulty = filters.difficulty;

            const { questions: data } = await fetchQuestions(params);
            setQuestions(data);
        } catch (error) {
            console.error(error);
            toast.error('Failed to load questions');
        }
    };

    const toggleQuestion = (id: string) => {
        const newSelected = new Set(selectedQuestions);
        if (newSelected.has(id)) {
            newSelected.delete(id);
        } else {
            newSelected.add(id);
        }
        setSelectedQuestions(newSelected);
    };

    const selectAll = () => {
        if (selectedQuestions.size === questions.length) {
            setSelectedQuestions(new Set());
        } else {
            setSelectedQuestions(new Set(questions.map(q => q.id)));
        }
    };

    const handleExport = () => {
        if (selectedQuestions.size === 0) {
            toast.error('Please select at least one question to export');
            return;
        }

        const exportData = questions
            .filter(q => selectedQuestions.has(q.id))
            .map(({ created_at, updated_at, ...rest }) => rest);

        const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `questions_export_${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        toast.success(`Exported ${selectedQuestions.size} questions`);
    };

    const handleFileSelect = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        if (!file.name.endsWith('.json')) {
            toast.error('Please select a JSON file');
            return;
        }

        setImportFile(file);

        try {
            const text = await file.text();
            const data = JSON.parse(text);

            if (!Array.isArray(data)) {
                toast.error('Invalid JSON format. Expected an array of questions');
                return;
            }

            setPreviewQuestions(data);
            toast.success(`Loaded ${data.length} questions from file`);
        } catch (error) {
            console.error(error);
            toast.error('Failed to parse JSON file');
        }
    };

    const handleImport = async () => {
        if (previewQuestions.length === 0) {
            toast.error('No questions to import');
            return;
        }

        setImporting(true);
        try {
            // Import questions via API
            const response = await fetch('/api/questions/import.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify({ questions: previewQuestions })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Import failed');
            }

            // Show detailed results
            const { imported, skipped, errors, summary } = result;

            if (imported > 0) {
                toast.success(
                    `Import complete! ${summary}`,
                    { duration: 5000 }
                );
            } else {
                toast.error('No questions were imported');
            }

            // Show errors if any
            if (errors && errors.length > 0) {
                console.log('Import errors:', errors);
                const errorMsg = errors.slice(0, 5).join('\n');
                const more = errors.length > 5 ? `\n...and ${errors.length - 5} more` : '';
                toast.warning(
                    `${skipped} question(s) skipped:\n${errorMsg}${more}`,
                    { duration: 8000 }
                );
            }

            // Reset
            setImportFile(null);
            setPreviewQuestions([]);

            // Refresh questions list if on export tab
            if (activeTab === 'export') {
                loadQuestions();
            }
        } catch (error) {
            console.error(error);
            toast.error(error instanceof Error ? error.message : 'Failed to import questions');
        } finally {
            setImporting(false);
        }
    };

    return (
        <div className="max-w-7xl mx-auto p-6 space-y-6">
            <div>
                <h1 className="text-3xl font-bold text-gray-900 dark:text-white">Import / Export Questions</h1>
                <p className="mt-2 text-gray-600 dark:text-gray-400">
                    Export questions to JSON or import questions from a JSON file
                </p>
            </div>

            {/* Tabs */}
            <div className="bg-gray-100 dark:bg-gray-800 p-1 rounded-xl inline-flex">
                <button
                    onClick={() => setActiveTab('export')}
                    className={cn(
                        "px-6 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2",
                        activeTab === 'export'
                            ? "bg-white dark:bg-gray-700 shadow-lg text-indigo-600 dark:text-indigo-400"
                            : "text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
                    )}
                >
                    <Download className="w-4 h-4" />
                    Export Questions
                </button>
                <button
                    onClick={() => setActiveTab('import')}
                    className={cn(
                        "px-6 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2",
                        activeTab === 'import'
                            ? "bg-white dark:bg-gray-700 shadow-lg text-indigo-600 dark:text-indigo-400"
                            : "text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
                    )}
                >
                    <Upload className="w-4 h-4" />
                    Import Questions
                </button>
            </div>

            {/* Export Tab */}
            {activeTab === 'export' && (
                <div className="space-y-6">
                    {/* Filters */}
                    <div className="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                        <div className="flex items-center gap-2 mb-4">
                            <Filter className="w-5 h-5 text-gray-600 dark:text-gray-400" />
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Filters</h2>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Question Type
                                </label>
                                <select
                                    value={filters.type}
                                    onChange={(e) => setFilters({ ...filters, type: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                >
                                    <option value="all">All Types</option>
                                    <option value="text_mcq">Multiple Choice</option>
                                    <option value="image_identify_logo">Logo Identification</option>
                                    <option value="image_identify_person">Person Identification</option>
                                    <option value="true_false">True/False</option>
                                    <option value="short_answer">Short Answer</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Difficulty Level
                                </label>
                                <select
                                    value={filters.difficulty}
                                    onChange={(e) => setFilters({ ...filters, difficulty: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                >
                                    <option value="all">All Difficulties</option>
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Questions List */}
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                        <div className="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div>
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                    Select Questions ({selectedQuestions.size} of {questions.length})
                                </h2>
                                <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Choose questions to export
                                </p>
                            </div>
                            <div className="flex gap-3">
                                <button
                                    onClick={selectAll}
                                    className="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-2"
                                >
                                    {selectedQuestions.size === questions.length ? (
                                        <>
                                            <CheckSquare className="w-4 h-4" />
                                            Deselect All
                                        </>
                                    ) : (
                                        <>
                                            <Square className="w-4 h-4" />
                                            Select All
                                        </>
                                    )}
                                </button>
                                <button
                                    onClick={handleExport}
                                    disabled={selectedQuestions.size === 0}
                                    className="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 font-medium"
                                >
                                    <Download className="w-4 h-4" />
                                    Export Selected
                                </button>
                            </div>
                        </div>

                        <div className="p-6">
                            {isLoading ? (
                                <div className="flex justify-center py-12">
                                    <Loader2 className="w-8 h-8 animate-spin text-indigo-600" />
                                </div>
                            ) : questions.length === 0 ? (
                                <div className="text-center py-12">
                                    <FileJson className="w-16 h-16 mx-auto text-gray-400 mb-4" />
                                    <p className="text-gray-600 dark:text-gray-400">No questions found with current filters</p>
                                </div>
                            ) : (
                                <div className="space-y-2 max-h-[500px] overflow-y-auto">
                                    {questions.map((q) => (
                                        <div
                                            key={q.id}
                                            onClick={() => toggleQuestion(q.id)}
                                            className={cn(
                                                "p-4 rounded-lg border cursor-pointer transition-all",
                                                selectedQuestions.has(q.id)
                                                    ? "bg-indigo-50 dark:bg-indigo-900/20 border-indigo-500"
                                                    : "bg-gray-50 dark:bg-gray-700/50 border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500"
                                            )}
                                        >
                                            <div className="flex items-start gap-3">
                                                <div className="mt-1">
                                                    {selectedQuestions.has(q.id) ? (
                                                        <CheckSquare className="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                                                    ) : (
                                                        <Square className="w-5 h-5 text-gray-400" />
                                                    )}
                                                </div>
                                                <div className="flex-1">
                                                    <p className="font-medium text-gray-900 dark:text-white">{q.question_text}</p>
                                                    <div className="flex gap-2 mt-2">
                                                        <span className="text-xs px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                                            {q.question_type.replace(/_/g, ' ')}
                                                        </span>
                                                        <span className="text-xs px-2 py-1 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 capitalize">
                                                            {q.difficulty}
                                                        </span>
                                                        <span className="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                            {q.points} pts
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}

            {/* Import Tab */}
            {activeTab === 'import' && (
                <div className="space-y-6">
                    {/* File Upload */}
                    <div className="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Select JSON File</h2>
                        <div className="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                            <Upload className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                            <input
                                type="file"
                                accept=".json"
                                onChange={handleFileSelect}
                                className="hidden"
                                id="import-file"
                            />
                            <label
                                htmlFor="import-file"
                                className="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer font-medium"
                            >
                                Choose JSON File
                            </label>
                            {importFile && (
                                <p className="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    Selected: <span className="font-medium">{importFile.name}</span>
                                </p>
                            )}
                        </div>
                    </div>

                    {/* Preview Questions */}
                    {previewQuestions.length > 0 && (
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div className="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <div>
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                        Preview ({previewQuestions.length} questions)
                                    </h2>
                                    <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Review questions before importing
                                    </p>
                                </div>
                                <div className="flex gap-3">
                                    <button
                                        onClick={() => {
                                            setPreviewQuestions([]);
                                            setImportFile(null);
                                        }}
                                        className="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white flex items-center gap-2"
                                    >
                                        <X className="w-4 h-4" />
                                        Cancel
                                    </button>
                                    <button
                                        onClick={handleImport}
                                        disabled={importing}
                                        className="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 flex items-center gap-2 font-medium"
                                    >
                                        {importing ? (
                                            <>
                                                <Loader2 className="w-4 h-4 animate-spin" />
                                                Importing...
                                            </>
                                        ) : (
                                            <>
                                                <Upload className="w-4 h-4" />
                                                Import All
                                            </>
                                        )}
                                    </button>
                                </div>
                            </div>

                            <div className="p-6 space-y-3 max-h-[500px] overflow-y-auto">
                                {previewQuestions.map((q, idx) => (
                                    <div
                                        key={idx}
                                        className="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600"
                                    >
                                        <p className="font-medium text-gray-900 dark:text-white">{q.question_text}</p>
                                        <div className="flex gap-2 mt-2">
                                            <span className="text-xs px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                                {q.question_type?.replace(/_/g, ' ')}
                                            </span>
                                            <span className="text-xs px-2 py-1 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 capitalize">
                                                {q.difficulty}
                                            </span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}
