import { useState, useEffect } from 'react';
import { Image as ImageIcon, Loader2, RefreshCw, Edit2, Save, X } from 'lucide-react';
import { toast } from 'sonner';
import api from '../../lib/api';
import { getImageUrl } from '../../lib/utils';

interface ImageQuestion {
    id: string;
    question_text: string;
    question_type: 'image_identify_person';
    image_url: string | null;
    options: string[] | null;
    correct_answer: string;
    difficulty: string;
    status: string;
    category: string;
    points: number;
    created_at: string;
    updated_at: string;
}

export function ImageQuestions() {
    const [questions, setQuestions] = useState<ImageQuestion[]>([]);
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState<any>(null);
    const [editingId, setEditingId] = useState<string | null>(null);
    const [editForm, setEditForm] = useState<Partial<ImageQuestion>>({});

    useEffect(() => {
        loadQuestions();
    }, []);

    const loadQuestions = async () => {
        setLoading(true);
        try {
            const response = await api.get('/questions/list_image_questions.php', {
                params: { type: 'personality', limit: 100 }
            });
            setQuestions(response.data.questions);
            setStats(response.data.stats);
        } catch (error: any) {
            console.error('Error loading image questions:', error);
            toast.error('Failed to load questions');
        } finally {
            setLoading(false);
        }
    };

    const startEdit = (question: ImageQuestion) => {
        setEditingId(question.id);
        setEditForm({
            question_text: question.question_text,
            options: question.options,
            correct_answer: question.correct_answer,
            difficulty: question.difficulty,
            category: question.category,
            points: question.points,
        });
    };

    const cancelEdit = () => {
        setEditingId(null);
        setEditForm({});
    };

    const saveEdit = async (id: string) => {
        try {
            await api.put(`/questions/update.php?id=${id}`, editForm);
            toast.success('Question updated successfully!');
            setEditingId(null);
            setEditForm({});
            loadQuestions();
        } catch (error: any) {
            toast.error(error.message || 'Failed to update question');
        }
    };

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            {/* Header */}
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <ImageIcon className="w-8 h-8 text-indigo-600" />
                        Personality Questions
                    </h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Manage personality identification questions with images
                    </p>
                </div>
                <button
                    onClick={loadQuestions}
                    disabled={loading}
                    className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50"
                >
                    <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
                    Refresh
                </button>
            </div>

            {/* Stats */}
            {stats && (
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div className="flex gap-6 text-sm">
                        <div>
                            <span className="text-gray-500 dark:text-gray-400">Total Questions:</span>
                            <span className="ml-2 font-semibold text-gray-900 dark:text-white">{stats.personality_count}</span>
                        </div>
                        <div>
                            <span className="text-gray-500 dark:text-gray-400">With Images:</span>
                            <span className="ml-2 font-semibold text-gray-900 dark:text-white">{stats.with_image_count}</span>
                        </div>
                    </div>
                </div>
            )}

            {/* Questions List */}
            {loading ? (
                <div className="flex items-center justify-center py-12">
                    <Loader2 className="w-8 h-8 animate-spin text-indigo-600" />
                </div>
            ) : questions.length === 0 ? (
                <div className="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <p className="text-gray-500 dark:text-gray-400">No personality questions found</p>
                </div>
            ) : (
                <div className="space-y-4">
                    {questions.map((question) => (
                        <div
                            key={question.id}
                            className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow"
                        >
                            {editingId === question.id ? (
                                /* Edit Mode */
                                <div className="space-y-4">
                                    {/* Question Text */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                            Question Text
                                        </label>
                                        <textarea
                                            value={editForm.question_text || ''}
                                            onChange={(e) =>
                                                setEditForm({ ...editForm, question_text: e.target.value })
                                            }
                                            rows={3}
                                            className="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-600"
                                        />
                                    </div>

                                    {/* Options */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                            Options
                                        </label>
                                        {editForm.options?.map((option, idx) => (
                                            <input
                                                key={idx}
                                                type="text"
                                                value={option}
                                                onChange={(e) => {
                                                    const newOptions = [...(editForm.options || [])];
                                                    newOptions[idx] = e.target.value;
                                                    setEditForm({ ...editForm, options: newOptions });
                                                }}
                                                className="w-full px-4 py-2 mb-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-600"
                                                placeholder={`Option ${idx + 1}`}
                                            />
                                        ))}
                                    </div>

                                    {/* Metadata */}
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                                Correct Answer
                                            </label>
                                            <input
                                                type="text"
                                                value={editForm.correct_answer || ''}
                                                onChange={(e) =>
                                                    setEditForm({ ...editForm, correct_answer: e.target.value })
                                                }
                                                className="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-600"
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                                Difficulty
                                            </label>
                                            <select
                                                value={editForm.difficulty || 'medium'}
                                                onChange={(e) =>
                                                    setEditForm({ ...editForm, difficulty: e.target.value })
                                                }
                                                className="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-600"
                                            >
                                                <option value="easy">Easy</option>
                                                <option value="medium">Medium</option>
                                                <option value="hard">Hard</option>
                                            </select>
                                        </div>
                                    </div>

                                    {/* Action Buttons */}
                                    <div className="flex gap-2 justify-end">
                                        <button
                                            onClick={cancelEdit}
                                            className="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors flex items-center gap-2"
                                        >
                                            <X className="w-4 h-4" />
                                            Cancel
                                        </button>
                                        <button
                                            onClick={() => saveEdit(question.id)}
                                            className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2"
                                        >
                                            <Save className="w-4 h-4" />
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                            ) : (
                                /* View Mode */
                                <div>
                                    <div className="flex gap-6">
                                        {/* Image Preview */}
                                        {question.image_url ? (
                                            <div className="flex-shrink-0">
                                                <div className="w-48 h-48 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex items-center justify-center border-2 border-gray-200 dark:border-gray-600">
                                                    <img
                                                        src={getImageUrl(question.image_url)}
                                                        alt={question.question_text}
                                                        className="max-w-full max-h-full object-contain"
                                                        onError={(e) => {
                                                            e.currentTarget.src = '';
                                                            e.currentTarget.style.display = 'none';
                                                            const parent = e.currentTarget.parentElement;
                                                            if (parent) {
                                                                parent.innerHTML = '<div class="text-red-500 text-sm">Image not found</div>';
                                                            }
                                                        }}
                                                    />
                                                </div>
                                                <div className="mt-2 space-y-1">
                                                    <p className="text-xs text-gray-500 dark:text-gray-400 break-all">
                                                        <span className="font-medium">DB URL:</span> {question.image_url}
                                                    </p>
                                                    <p className="text-xs text-gray-500 dark:text-gray-400 break-all">
                                                        <span className="font-medium">Full:</span> {getImageUrl(question.image_url)}
                                                    </p>
                                                </div>
                                            </div>
                                        ) : (
                                            <div className="flex-shrink-0 w-48 h-48 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600">
                                                <p className="text-gray-400 dark:text-gray-500 text-sm">No image</p>
                                            </div>
                                        )}

                                        {/* Question Details */}
                                        <div className="flex-1">
                                            <div className="flex items-start justify-between mb-4">
                                                <div className="flex-1">
                                                    <div className="flex items-center gap-2 mb-2">
                                                        <span className="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded text-xs font-medium">
                                                            ID: {question.id}
                                                        </span>
                                                        <span className={`px-2 py-1 rounded text-xs font-medium ${question.status === 'active'
                                                            ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
                                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                                            }`}>
                                                            {question.status}
                                                        </span>
                                                        <span className="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium capitalize">
                                                            {question.difficulty}
                                                        </span>
                                                        <span className="text-xs text-gray-500 dark:text-gray-400">
                                                            {question.points} points
                                                        </span>
                                                    </div>
                                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                                        {question.question_text}
                                                    </h3>
                                                </div>
                                                <button
                                                    onClick={() => startEdit(question)}
                                                    className="px-3 py-1.5 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors flex items-center gap-2 text-sm"
                                                >
                                                    <Edit2 className="w-4 h-4" />
                                                    Edit
                                                </button>
                                            </div>

                                            {/* Options */}
                                            {question.options && question.options.length > 0 && (
                                                <div className="mb-3">
                                                    <p className="text-sm font-medium text-gray-900 dark:text-white mb-2">Options:</p>
                                                    <div className="grid grid-cols-2 gap-2">
                                                        {question.options.map((option, idx) => (
                                                            <div
                                                                key={idx}
                                                                className={`px-3 py-2 rounded-lg text-sm ${option === question.correct_answer
                                                                    ? 'bg-green-500/10 text-green-600 dark:text-green-400 border border-green-500/30 font-medium'
                                                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                                                    }`}
                                                            >
                                                                {option}
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            )}

                                            {/* Metadata */}
                                            <div className="flex gap-4 text-xs text-gray-500 dark:text-gray-400 pt-3 border-t border-gray-200 dark:border-gray-700">
                                                <div>
                                                    <span className="font-medium">Category:</span> {question.category}
                                                </div>
                                                <div>
                                                    <span className="font-medium">Created:</span> {new Date(question.created_at).toLocaleDateString()}
                                                </div>
                                                <div>
                                                    <span className="font-medium">Updated:</span> {new Date(question.updated_at).toLocaleDateString()}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
