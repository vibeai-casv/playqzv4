import { useState, useEffect } from 'react';
import { Edit2, Save, X, Filter, Loader2 } from 'lucide-react';
import { fetchAPI } from '../../lib/api';
import { getImageUrl } from '../../lib/utils';


interface Question {
    id: string;
    question_text: string;
    question_type: string;
    options: string[];
    correct_answer: string;
    explanation: string;
    category: string;
    difficulty: string;
    points: number;
    is_active: number;
    image_url?: string;
}

export function BulkEditQuestions() {
    const [questions, setQuestions] = useState<Question[]>([]);
    const [questionTypes, setQuestionTypes] = useState<string[]>([]);
    const [selectedType, setSelectedType] = useState<string>('');
    const [loading, setLoading] = useState(false);
    const [editingId, setEditingId] = useState<string | null>(null);
    const [editForm, setEditForm] = useState<Partial<Question>>({});

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
        });
    };

    const cancelEdit = () => {
        setEditingId(null);
        setEditForm({});
    };

    const saveEdit = async (id: string) => {
        try {
            await fetchAPI(`/questions/update.php?id=${id}`, {
                method: 'PUT',
                body: JSON.stringify(editForm),
            });

            alert('Question updated successfully!');
            setEditingId(null);
            setEditForm({});
            fetchQuestions();
        } catch (error: any) {
            alert(error.message || 'Failed to update question');
        }
    };

    const getTypeLabel = (type: string) => {
        const labels: Record<string, string> = {
            'text_mcq': 'Multiple Choice',
            'image_identify_logo': 'Logo Identification',
            'image_identify_person': 'Personality Identification',
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
            </div>

            {/* Filter Section */}
            <div className="bg-card border border-border rounded-xl p-6">
                <div className="flex items-center gap-4">
                    <Filter className="w-5 h-5 text-muted-foreground" />
                    <div className="flex-1">
                        <label className="block text-sm font-medium text-foreground mb-2">
                            Question Type
                        </label>
                        <select
                            value={selectedType}
                            onChange={(e) => setSelectedType(e.target.value)}
                            className="w-full px-4 py-2 bg-background border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            {questionTypes.map((type) => (
                                <option key={type} value={type}>
                                    {getTypeLabel(type)}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div className="text-sm text-muted-foreground">
                        {questions.length} question{questions.length !== 1 ? 's' : ''} found
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
                    {questions.map((question) => (
                        <div
                            key={question.id}
                            className="bg-card border border-border rounded-xl p-6 hover:border-primary/50 transition-colors"
                        >
                            {editingId === question.id ? (
                                /* Edit Mode */
                                <div className="space-y-4">
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

                                    {/* Options (for MCQ type) */}
                                    {(question.question_type === 'text_mcq' ||
                                        question.question_type === 'image_identify_logo' ||
                                        question.question_type === 'image_identify_person') && (
                                            <div>
                                                <label className="block text-sm font-medium text-foreground mb-2">
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
                                <div>
                                    <div className="flex items-start justify-between mb-4">
                                        <div className="flex-1">
                                            <h3 className="text-lg font-semibold text-foreground mb-2">
                                                {question.question_text}
                                            </h3>
                                            <div className="flex items-center gap-4 text-sm text-muted-foreground">
                                                <span className="px-2 py-1 bg-primary/10 text-primary rounded">
                                                    {question.category}
                                                </span>
                                                <span className="capitalize">{question.difficulty}</span>
                                                <span>{question.points} points</span>
                                                <span
                                                    className={
                                                        question.is_active
                                                            ? 'text-green-500'
                                                            : 'text-yellow-500'
                                                    }
                                                >
                                                    {question.is_active ? 'Active' : 'Inactive'}
                                                </span>
                                            </div>
                                        </div>
                                        <button
                                            onClick={() => startEdit(question)}
                                            className="px-3 py-1.5 border border-border rounded-lg hover:bg-muted/50 transition-colors flex items-center gap-2 text-sm"
                                        >
                                            <Edit2 className="w-4 h-4" />
                                            Edit
                                        </button>
                                    </div>

                                    {question.options && question.options.length > 0 && (
                                        <div className="mb-3">
                                            <p className="text-sm font-medium text-foreground mb-2">Options:</p>
                                            <div className="grid grid-cols-2 gap-2">
                                                {question.options.map((option, idx) => (
                                                    <div
                                                        key={idx}
                                                        className={`px-3 py-2 rounded-lg text-sm ${option === question.correct_answer
                                                            ? 'bg-green-500/10 text-green-500 border border-green-500/30'
                                                            : 'bg-muted/50 text-muted-foreground'
                                                            }`}
                                                    >
                                                        {option}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

{/* Image Display */ }
{
    question.image_url && (
        <div className="mb-4 p-4 bg-muted/30 rounded-lg">
            <p className="text-sm font-medium text-foreground mb-2">Image:</p>
            <div className="flex gap-4 items-start">
                <div className="w-32 h-32 bg-background border border-border rounded-lg overflow-hidden flex items-center justify-center">
                    <img
                        src={getImageUrl(question.image_url)}
                        alt="Question image"
                        className="max-w-full max-h-full object-contain"
                        onError={(e) => {
                            e.currentTarget.src = '';
                            e.currentTarget.style.display = 'none';
                            const parent = e.currentTarget.parentElement;
                            if (parent) {
                                parent.innerHTML = '<div class="text-red-500 text-xs">Image not found</div>';
                            }
                        }}
                    />
                </div>
                <div className="flex-1">
                    <p className="text-xs text-muted-foreground break-all">
                        <span className="font-medium">URL:</span> {question.image_url}
                    </p>
                    <p className="text-xs text-muted-foreground mt-1">
                        <span className="font-medium">Full Path:</span> {getImageUrl(question.image_url)}
                    </p>
                </div>
            </div>
        </div>
    )
}


                                    {question.explanation && (
                                        <p className="text-sm text-muted-foreground italic">
                                            {question.explanation}
                                        </p>
                                    )}
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
