import { useState, useEffect } from 'react';
import { Save, X, Filter, Loader2, Upload, Image as ImageIcon, CheckSquare, Square, Download, Package } from 'lucide-react';
import { toast } from 'sonner';
import { fetchAPI } from '../../lib/api';
import { getImageUrl } from '../../lib/utils';
import { QuestionDisplay } from '../../components/admin/QuestionDisplay';
import { Modal } from '../../components/ui/Modal';
import { BundleImporter } from '../../components/admin/BundleImporter';
import { cn } from '../../lib/utils';
import api from '../../lib/api';


interface Question {
    id: string;
    question_text: string;
    question_type: string;
    options: string[];
    correct_answer: string;
    explanation: string;
    category: string;
    subcategory?: string;
    difficulty: string;
    points: number;
    is_active: number;
    image_url?: string;
    media_id?: string;
    hint?: string;
    tags?: string[];
    status?: string;
    ai_generated?: boolean;
    is_demo?: boolean;
    created_at?: string;
    updated_at?: string;
}

export function BulkEditQuestions() {
    const [questions, setQuestions] = useState<Question[]>([]);
    const [questionTypes, setQuestionTypes] = useState<string[]>([]);
    const [selectedType, setSelectedType] = useState<string>('');
    const [loading, setLoading] = useState(false);
    const [editingId, setEditingId] = useState<string | null>(null);
    const [editForm, setEditForm] = useState<Partial<Question>>({});
    const [selectedIds, setSelectedIds] = useState<string[]>([]);
    const [isImportModalOpen, setIsImportModalOpen] = useState(false);
    const [isExporting, setIsExporting] = useState(false);

    // Fetch unique question types
    useEffect(() => {
        fetchQuestionTypes();
    }, []);

    // Fetch questions when type changes
    useEffect(() => {
        if (selectedType) {
            fetchQuestions();
        }
    }, [selectedType]);

    const fetchQuestionTypes = async () => {
        try {
            const data = await fetchAPI('/questions/types.php');

            // If API returns types, use them
            if (data.types && data.types.length > 0) {
                setQuestionTypes(data.types);
                setSelectedType(data.types[0]);
            } else {
                // Fallback to known types if database is empty
                const fallbackTypes = [
                    'text_mcq',
                    'image_identify_logo',
                    'image_identify_person',
                    'true_false',
                    'short_answer'
                ];
                setQuestionTypes(fallbackTypes);
                setSelectedType(fallbackTypes[0]);
                console.warn('No question types from database, using fallback types');
            }
        } catch (error) {
            console.error('Failed to load question types', error);

            // Fallback to hardcoded types on error
            const fallbackTypes = [
                'text_mcq',
                'image_identify_logo',
                'image_identify_person',
                'true_false',
                'short_answer'
            ];
            setQuestionTypes(fallbackTypes);
            setSelectedType(fallbackTypes[0]);
        }
    };

    const fetchQuestions = async () => {
        setLoading(true);
        try {
            const data = await fetchAPI(`/questions/list.php?type=${selectedType}&limit=1000`);
            setQuestions(data.data || []);
        } catch (error) {
            console.error('Failed to load questions', error);
        } finally {
            setLoading(false);
        }
    };

    const handleFileUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', 'question_image');

        // Pass token in body as fallback for Apache header stripping
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (token) {
            formData.append('token', token);
        }

        try {
            const response = await api.post('/media/public_upload.php', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            const data = response.data;

            if (data.success) {
                setEditForm(prev => ({ ...prev, image_url: data.media.url }));
                toast.success('Image uploaded successfully');
            } else {
                alert('Upload failed: ' + (data.error || 'Unknown error'));
            }
        } catch (error: any) {
            console.error('Upload error:', error);
            const errorMsg = error.response?.data?.error || error.message || 'Unknown error';
            alert(`Upload failed: ${errorMsg}`);
        }
    };

    const startEdit = (question: Question) => {
        setEditingId(question.id);
        setEditForm({
            question_text: question.question_text,
            options: question.options,
            correct_answer: question.correct_answer,
            explanation: question.explanation,
            category: question.category,
            difficulty: question.difficulty,
            points: question.points,
            image_url: question.image_url,
        });
    };

    const cancelEdit = () => {
        setEditingId(null);
        setEditForm({});
    };

    const saveEdit = async (id: string) => {
        try {
            await api.put(`/questions/public_update.php?id=${id}`, editForm);

            toast.success('Question updated successfully!');
            setEditingId(null);
            setEditForm({});
            fetchQuestions();
        } catch (error: any) {
            toast.error(error.message || 'Failed to update question');
        }
    };

    const toggleSelect = (id: string) => {
        setSelectedIds(prev =>
            prev.includes(id) ? prev.filter((i: string) => i !== id) : [...prev, id]
        );
    };

    const toggleSelectAll = () => {
        if (selectedIds.length === questions.length) {
            setSelectedIds([]);
        } else {
            setSelectedIds(questions.map((q: Question) => q.id));
        }
    };

    const exportSelectedBundle = async () => {
        if (selectedIds.length === 0) {
            toast.error('Please select questions to export');
            return;
        }

        setIsExporting(true);
        try {
            const response = await api.post('/bundle/export.php', {
                question_ids: selectedIds
            }, {
                responseType: 'blob'
            });

            const blob = response.data;
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `bundle_${selectedType}_${new Date().getTime()}.zip`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            toast.success(`Successfully exported ${selectedIds.length} questions as a bundle`);
        } catch (error: any) {
            toast.error('Failed to export bundle');
            console.error(error);
        } finally {
            setIsExporting(false);
        }
    };

    const getTypeLabel = (type: string) => {
        const labels: Record<string, string> = {
            'text_mcq': 'Multiple Choice',
            'image_identify_logo': 'Logo Identification',
            'image_identify_person': 'Personality Identification',
            'personality': 'Personality Identification',
            'true_false': 'True/False',
            'short_answer': 'Short Answer',
        };
        return labels[type] || type;
    };

    return (
        <div className="p-6 space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold text-foreground">Bulk Edit Questions</h1>
                    <p className="text-muted-foreground mt-1">
                        Edit multiple questions filtered by type
                    </p>
                </div>
                <div className="flex gap-3">
                    <button
                        onClick={() => setIsImportModalOpen(true)}
                        className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
                    >
                        <Package className="w-4 h-4" />
                        Import Bundle
                    </button>
                    <button
                        onClick={exportSelectedBundle}
                        disabled={selectedIds.length === 0 || isExporting}
                        className="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm"
                    >
                        {isExporting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Download className="w-4 h-4" />}
                        Export Bundle ({selectedIds.length})
                    </button>
                </div>
            </div>

            <div className="bg-card border border-border rounded-xl p-6">
                <div className="flex items-center gap-6">
                    <div className="flex items-center gap-2">
                        <button
                            onClick={toggleSelectAll}
                            className="p-1 hover:bg-muted rounded transition-colors"
                            title={selectedIds.length === questions.length ? "Deselect All" : "Select All"}
                        >
                            {selectedIds.length === questions.length && questions.length > 0 ? (
                                <CheckSquare className="w-5 h-5 text-primary" />
                            ) : (
                                <Square className="w-5 h-5 text-muted-foreground" />
                            )}
                        </button>
                    </div>

                    <Filter className="w-5 h-5 text-muted-foreground" />
                    <div className="flex-1">
                        <select
                            value={selectedType}
                            onChange={(e) => {
                                setSelectedType(e.target.value);
                                setSelectedIds([]);
                            }}
                            className="w-full px-4 py-2 bg-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            {questionTypes.map((type: string) => (
                                <option key={type} value={type}>
                                    {getTypeLabel(type)}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div className="flex items-center gap-4 text-sm font-medium">
                        <span className="text-muted-foreground">
                            {questions.length} Total
                        </span>
                        {selectedIds.length > 0 && (
                            <span className="text-primary bg-primary/10 px-2 py-1 rounded">
                                {selectedIds.length} Selected
                            </span>
                        )}
                    </div>
                </div>
            </div>

            {/* Questions List */}
            {loading ? (
                <div className="flex items-center justify-center py-12">
                    <Loader2 className="w-8 h-8 animate-spin text-primary" />
                </div>
            ) : questions.length === 0 ? (
                <div className="text-center py-12 bg-card border border-border rounded-xl">
                    <p className="text-muted-foreground">No questions found for this type</p>
                </div>
            ) : (
                <div className="space-y-4">
                    {questions.map((question: Question) => (
                        <div
                            key={question.id}
                            className={cn(
                                "relative group bg-card border rounded-xl transition-all duration-200",
                                selectedIds.includes(question.id)
                                    ? "border-primary ring-1 ring-primary/20 shadow-md"
                                    : "border-border hover:border-primary/40"
                            )}
                        >
                            {/* Selection Checkbox */}
                            <button
                                onClick={() => toggleSelect(question.id)}
                                className={cn(
                                    "absolute top-4 left-4 z-10 p-1.5 rounded-lg transition-all",
                                    selectedIds.includes(question.id)
                                        ? "bg-primary text-primary-foreground shadow-sm"
                                        : "bg-background/80 text-muted-foreground hover:bg-muted border border-border opacity-0 group-hover:opacity-100"
                                )}
                            >
                                {selectedIds.includes(question.id) ? (
                                    <CheckSquare className="w-4 h-4" />
                                ) : (
                                    <Square className="w-4 h-4" />
                                )}
                            </button>

                            <div className={cn("p-6", selectedIds.includes(question.id) ? "" : "")}>
                                {editingId === question.id ? (
                                    /* Edit Mode */
                                    <div className="space-y-4 pt-2">
                                        {/* Question Text */}
                                        <div>
                                            <label className="block text-sm font-medium text-foreground mb-2">
                                                Question Text
                                            </label>
                                            <textarea
                                                value={editForm.question_text || ''}
                                                onChange={(e) =>
                                                    setEditForm({ ...editForm, question_text: e.target.value })
                                                }
                                                rows={3}
                                                className="w-full px-4 py-2 bg-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                            />
                                        </div>

                                        {/* Image Upload */}
                                        <div className="border border-border rounded-lg p-4 bg-muted/20">
                                            <label className="block text-sm font-medium text-foreground mb-2">
                                                Question Image
                                            </label>
                                            <div className="flex items-start gap-4">
                                                {/* Preview */}
                                                <div className="w-32 h-32 bg-background border border-border rounded-lg flex items-center justify-center overflow-hidden">
                                                    {editForm.image_url ? (
                                                        <img
                                                            src={getImageUrl(editForm.image_url)}
                                                            alt="Question"
                                                            className="w-full h-full object-contain"
                                                        />
                                                    ) : (
                                                        <div className="text-muted-foreground flex flex-col items-center">
                                                            <ImageIcon className="w-8 h-8 mb-1" />
                                                            <span className="text-xs">No image</span>
                                                        </div>
                                                    )}
                                                </div>

                                                {/* Controls */}
                                                <div className="flex-1 space-y-3">
                                                    <div>
                                                        <label className="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 cursor-pointer w-fit transition-colors text-sm font-medium">
                                                            <Upload className="w-4 h-4" />
                                                            Upload Image
                                                            <input
                                                                type="file"
                                                                accept="image/*"
                                                                className="hidden"
                                                                onChange={handleFileUpload}
                                                            />
                                                        </label>
                                                        <p className="text-xs text-muted-foreground mt-1">
                                                            JPG, PNG, GIF, WebP (Max 5MB)
                                                        </p>
                                                    </div>

                                                    {editForm.image_url && (
                                                        <div>
                                                            <p className="text-xs font-medium text-foreground mb-1">Current URL:</p>
                                                            <input
                                                                type="text"
                                                                value={editForm.image_url}
                                                                readOnly
                                                                className="w-full text-xs px-2 py-1 bg-background border border-border rounded text-muted-foreground"
                                                            />
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        </div>

                                        {/* Options (for MCQ type) */}
                                        {(question.question_type === 'text_mcq' ||
                                            question.question_type === 'image_identify_logo' ||
                                            question.question_type === 'image_identify_person') && (
                                                <div>
                                                    <label className="block text-sm font-medium text-foreground mb-2">
                                                        Options
                                                    </label>
                                                    {editForm.options?.map((option: string, idx: number) => (
                                                        <input
                                                            key={idx}
                                                            type="text"
                                                            value={option}
                                                            onChange={(e) => {
                                                                const newOptions = [...(editForm.options || [])];
                                                                newOptions[idx] = e.target.value;
                                                                setEditForm({ ...editForm, options: newOptions });
                                                            }}
                                                            className="w-full px-4 py-2 mb-2 bg-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                                            placeholder={`Option ${idx + 1}`}
                                                        />
                                                    ))}
                                                </div>
                                            )}

                                        {/* Correct Answer */}
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-foreground mb-2">
                                                    Correct Answer
                                                </label>
                                                <input
                                                    type="text"
                                                    value={editForm.correct_answer || ''}
                                                    onChange={(e) =>
                                                        setEditForm({ ...editForm, correct_answer: e.target.value })
                                                    }
                                                    className="w-full px-4 py-2 bg-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-foreground mb-2">
                                                    Points
                                                </label>
                                                <input
                                                    type="number"
                                                    value={editForm.points || 10}
                                                    onChange={(e) =>
                                                        setEditForm({ ...editForm, points: parseInt(e.target.value) })
                                                    }
                                                    className="w-full px-4 py-2 bg-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                                />
                                            </div>
                                        </div>

                                        {/* Category and Difficulty */}
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-foreground mb-2">
                                                    Category
                                                </label>
                                                <input
                                                    type="text"
                                                    value={editForm.category || ''}
                                                    onChange={(e) =>
                                                        setEditForm({ ...editForm, category: e.target.value })
                                                    }
                                                    className="w-full px-4 py-2 bg-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-foreground mb-2">
                                                    Difficulty
                                                </label>
                                                <select
                                                    value={editForm.difficulty || 'medium'}
                                                    onChange={(e) =>
                                                        setEditForm({ ...editForm, difficulty: e.target.value })
                                                    }
                                                    className="w-full px-4 py-2 bg-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                                >
                                                    <option value="easy">Easy</option>
                                                    <option value="medium">Medium</option>
                                                    <option value="hard">Hard</option>
                                                </select>
                                            </div>
                                        </div>

                                        {/* Explanation */}
                                        <div>
                                            <label className="block text-sm font-medium text-foreground mb-2">
                                                Explanation
                                            </label>
                                            <textarea
                                                value={editForm.explanation || ''}
                                                onChange={(e) =>
                                                    setEditForm({ ...editForm, explanation: e.target.value })
                                                }
                                                rows={2}
                                                className="w-full px-4 py-2 bg-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                            />
                                        </div>

                                        {/* Action Buttons */}
                                        <div className="flex gap-2 justify-end">
                                            <button
                                                onClick={cancelEdit}
                                                className="px-4 py-2 border border-border rounded-lg hover:bg-muted/50 transition-colors flex items-center gap-2"
                                            >
                                                <X className="w-4 h-4" />
                                                Cancel
                                            </button>
                                            <button
                                                onClick={() => saveEdit(question.id)}
                                                className="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2"
                                            >
                                                <Save className="w-4 h-4" />
                                                Save Changes
                                            </button>
                                        </div>
                                    </div>
                                ) : (
                                    /* View Mode */
                                    <QuestionDisplay question={question} startEdit={startEdit} />
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {/* Import Modal */}
            <Modal
                open={isImportModalOpen}
                onClose={() => setIsImportModalOpen(false)}
                title="Import Question Bundle"
                className="max-w-xl"
            >
                <BundleImporter
                    onImportComplete={() => {
                        setIsImportModalOpen(false);
                        fetchQuestions();
                    }}
                    onCancel={() => setIsImportModalOpen(false)}
                />
            </Modal>
        </div>
    );
}
