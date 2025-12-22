import { useMemo } from 'react';
import { Edit2 } from 'lucide-react';
import { getImageUrl, shuffle } from '../../lib/utils';

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

interface QuestionDisplayProps {
    question: Question;
    startEdit: (q: Question) => void;
}

export const QuestionDisplay = ({ question, startEdit }: QuestionDisplayProps) => {
    // Shuffle options only when question.options changes
    const displayedOptions = useMemo(() => {
        if (!question.options || question.options.length === 0) return [];
        // Clone before shuffling to avoid mutating original
        return shuffle([...question.options]);
    }, [question.options]);

    return (
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

            {question.image_url && (
                <div className="mb-4">
                    <div className="h-48 bg-muted/20 rounded-lg border border-border flex items-center justify-center overflow-hidden">
                        <img
                            src={getImageUrl(question.image_url)}
                            alt="Question"
                            className="h-full w-full object-contain"
                        />
                    </div>
                </div>
            )}

            {displayedOptions.length > 0 && (
                <div className="mb-3">
                    <p className="text-sm font-medium text-foreground mb-2">Options (Randomized):</p>
                    <div className="grid grid-cols-2 gap-2">
                        {displayedOptions.map((option, idx) => (
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

            {question.explanation && (
                <p className="text-sm text-muted-foreground italic">
                    {question.explanation}
                </p>
            )}

            {/* All Database Fields Section */}
            <div className="mt-4 pt-4 border-t border-border">
                <p className="text-sm font-medium text-foreground mb-3">All Database Fields:</p>
                <div className="grid grid-cols-2 gap-3 text-xs">
                    <div className="space-y-2">
                        <div>
                            <span className="font-medium text-muted-foreground">ID:</span>
                            <span className="ml-2 text-foreground">{question.id}</span>
                        </div>
                        <div>
                            <span className="font-medium text-muted-foreground">Type:</span>
                            <span className="ml-2 text-foreground">{question.question_type}</span>
                        </div>
                        <div>
                            <span className="font-medium text-muted-foreground">Category:</span>
                            <span className="ml-2 text-foreground">{question.category}</span>
                        </div>
                        {question.subcategory && (
                            <div>
                                <span className="font-medium text-muted-foreground">Subcategory:</span>
                                <span className="ml-2 text-foreground">{question.subcategory}</span>
                            </div>
                        )}
                        <div>
                            <span className="font-medium text-muted-foreground">Difficulty:</span>
                            <span className="ml-2 text-foreground capitalize">{question.difficulty}</span>
                        </div>
                        <div>
                            <span className="font-medium text-muted-foreground">Points:</span>
                            <span className="ml-2 text-foreground">{question.points}</span>
                        </div>
                        <div>
                            <span className="font-medium text-muted-foreground">Active:</span>
                            <span className={`ml-2 ${question.is_active ? 'text-green-500' : 'text-yellow-500'}`}>
                                {question.is_active ? 'Yes' : 'No'}
                            </span>
                        </div>
                        {question.status && (
                            <div>
                                <span className="font-medium text-muted-foreground">Status:</span>
                                <span className="ml-2 text-foreground">{question.status}</span>
                            </div>
                        )}
                    </div>
                    <div className="space-y-2">
                        {question.image_url && (
                            <div>
                                <span className="font-medium text-muted-foreground">Image URL:</span>
                                <span className="ml-2 text-foreground break-all">{question.image_url}</span>
                            </div>
                        )}
                        {question.media_id && (
                            <div>
                                <span className="font-medium text-muted-foreground">Media ID:</span>
                                <span className="ml-2 text-foreground">{question.media_id}</span>
                            </div>
                        )}
                        {question.hint && (
                            <div>
                                <span className="font-medium text-muted-foreground">Hint:</span>
                                <span className="ml-2 text-foreground">{question.hint}</span>
                            </div>
                        )}
                        {question.tags && question.tags.length > 0 && (
                            <div>
                                <span className="font-medium text-muted-foreground">Tags:</span>
                                <span className="ml-2 text-foreground">{question.tags.join(', ')}</span>
                            </div>
                        )}
                        {question.ai_generated !== undefined && (
                            <div>
                                <span className="font-medium text-muted-foreground">AI Generated:</span>
                                <span className="ml-2 text-foreground">{question.ai_generated ? 'Yes' : 'No'}</span>
                            </div>
                        )}
                        {question.is_demo !== undefined && (
                            <div>
                                <span className="font-medium text-muted-foreground">Demo:</span>
                                <span className="ml-2 text-foreground">{question.is_demo ? 'Yes' : 'No'}</span>
                            </div>
                        )}
                        {question.created_at && (
                            <div>
                                <span className="font-medium text-muted-foreground">Created:</span>
                                <span className="ml-2 text-foreground">{new Date(question.created_at).toLocaleString()}</span>
                            </div>
                        )}
                        {question.updated_at && (
                            <div>
                                <span className="font-medium text-muted-foreground">Updated:</span>
                                <span className="ml-2 text-foreground">{new Date(question.updated_at).toLocaleString()}</span>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};
