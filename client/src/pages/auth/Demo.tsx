import { useEffect, useState, useCallback, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import { XCircle, Loader2, X, ChevronLeft, ChevronRight, CheckCircle, AlertTriangle, Trophy, ArrowRight } from 'lucide-react';
import { cn, shuffle } from '../../lib/utils';
import { Image } from '../../components/ui/Image';
import api from '../../lib/api';
import type { Question } from '../../types';
import { toast } from 'sonner';

// Simple modal component
function Modal({ open, onClose, children }: { open: boolean; onClose: () => void; children: React.ReactNode }) {
    if (!open) return null;
    return (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 animate-in fade-in duration-200" role="dialog" aria-modal="true">
            <div className="bg-slate-900 border border-white/10 rounded-2xl p-6 max-w-lg w-full relative shadow-2xl shadow-black/50 animate-in zoom-in-95 duration-200">
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 text-slate-500 hover:text-white transition-colors"
                    aria-label="Close modal"
                >
                    <X className="w-5 h-5" />
                </button>
                {children}
            </div>
        </div>
    );
}

export const Demo: React.FC = () => {
    const navigate = useNavigate();

    // Local State
    const [questions, setQuestions] = useState<Question[]>([]);
    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
    const [responses, setResponses] = useState<Record<string, string>>({});
    const [isLoading, setIsLoading] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isFinished, setIsFinished] = useState(false);
    const [score, setScore] = useState(0);

    // UI State
    const [showExitModal, setShowExitModal] = useState(false);
    const [showSubmitModal, setShowSubmitModal] = useState(false);
    const [showImageModal, setShowImageModal] = useState(false);
    const [imageSrc, setImageSrc] = useState('');
    const [isNavigating, setIsNavigating] = useState(false);

    // Fetch Demo Questions
    useEffect(() => {
        const fetchQuestions = async () => {
            try {
                const { data } = await api.get('/quiz/demo.php');
                const shuffledQuestions = (data.questions as Question[]).map(q => ({
                    ...q,
                    options: q.options ? shuffle(q.options) : q.options
                }));
                setQuestions(shuffledQuestions);
            } catch (error) {
                console.error('Failed to load demo quiz:', error);
                toast.error('Failed to load demo quiz. Please try again.');
            } finally {
                setIsLoading(false);
            }
        };
        fetchQuestions();
    }, []);

    const currentQuestion = questions[currentQuestionIndex];
    const totalQuestions = questions.length;

    // Keyboard shortcuts
    const handleKeyDown = useCallback(
        (e: KeyboardEvent) => {
            if (!currentQuestion || isFinished) return;
            const key = parseInt(e.key, 10);
            if (key >= 1 && key <= 4) {
                const option = currentQuestion.options?.[key - 1];
                if (option) {
                    handleSelectOption(option);
                }
            }
        },
        [currentQuestion, isFinished]
    );

    useEffect(() => {
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [handleKeyDown]);

    // Handle answer selection
    const handleSelectOption = useCallback((option: string) => {
        if (isNavigating || isFinished) return;

        setResponses(prev => ({
            ...prev,
            [currentQuestion.id]: option
        }));

        // Auto-advance
        if (currentQuestionIndex < totalQuestions - 1) {
            setIsNavigating(true);
            setTimeout(() => {
                setCurrentQuestionIndex(prev => prev + 1);
                setIsNavigating(false);
            }, 800);
        }
    }, [currentQuestion, currentQuestionIndex, totalQuestions, isNavigating, isFinished]);

    // Navigation
    const nextQuestion = () => {
        if (currentQuestionIndex < totalQuestions - 1) {
            setCurrentQuestionIndex(prev => prev + 1);
        }
    };

    const previousQuestion = () => {
        if (currentQuestionIndex > 0) {
            setCurrentQuestionIndex(prev => prev - 1);
        }
    };

    // Submit Logic
    const handleSubmitQuiz = async () => {
        setIsSubmitting(true);

        // Calculate score locally
        let correctCount = 0;
        questions.forEach(q => {
            if (responses[q.id] === q.correct_answer) {
                correctCount++;
            }
        });

        // Simulate network delay for effect
        await new Promise(resolve => setTimeout(resolve, 1000));

        setScore(correctCount);
        setIsFinished(true);
        setIsSubmitting(false);
        setShowSubmitModal(false);
    };

    const openImage = (src: string) => {
        setImageSrc(src);
        setShowImageModal(true);
    };

    // Focus management
    const firstOptionRef = useRef<HTMLButtonElement>(null);
    useEffect(() => {
        if (!isFinished) {
            firstOptionRef.current?.focus();
        }
    }, [currentQuestionIndex, isFinished]);

    if (isLoading) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <Loader2 className="w-8 h-8 animate-spin text-indigo-500" />
            </div>
        );
    }

    if (isFinished) {
        return (
            <div className="max-w-4xl mx-auto p-6 space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                <div className="text-center space-y-4">
                    <div className="w-24 h-24 bg-indigo-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <Trophy className="w-12 h-12 text-indigo-500" />
                    </div>
                    <h1 className="text-4xl font-bold text-white">Demo Completed!</h1>
                    <p className="text-xl text-slate-400">
                        You scored <span className="text-indigo-400 font-bold">{score}</span> out of <span className="text-white font-bold">{totalQuestions}</span>
                    </p>
                </div>

                {/* Review Section */}
                <div className="space-y-6">
                    <h2 className="text-2xl font-bold text-white">Review Answers</h2>
                    {questions.map((q, idx) => {
                        const userAnswer = responses[q.id];
                        const isCorrect = userAnswer === q.correct_answer;
                        const isSkipped = !userAnswer;

                        return (
                            <div key={q.id} className="bg-slate-900/50 border border-white/10 rounded-2xl p-6 space-y-4">
                                <div className="flex items-start gap-4">
                                    <div className={cn(
                                        "w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold shrink-0",
                                        isCorrect ? "bg-green-500/20 text-green-500" :
                                            isSkipped ? "bg-slate-500/20 text-slate-500" : "bg-red-500/20 text-red-500"
                                    )}>
                                        {idx + 1}
                                    </div>
                                    <div className="space-y-4 w-full">
                                        <h3 className="text-lg font-medium text-white">{q.question_text}</h3>

                                        {q.image_url && (
                                            <Image
                                                src={q.image_url}
                                                alt="Question"
                                                className="max-h-48 rounded-lg border border-white/10"
                                            />
                                        )}

                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            {q.options?.map((opt, optIdx) => {
                                                const isSelected = userAnswer === opt;
                                                const isCorrectOption = opt === q.correct_answer;

                                                let styleClass = "bg-white/5 border-transparent text-slate-400";
                                                if (isCorrectOption) {
                                                    styleClass = "bg-green-500/10 border-green-500/50 text-green-400";
                                                } else if (isSelected && !isCorrectOption) {
                                                    styleClass = "bg-red-500/10 border-red-500/50 text-red-400";
                                                }

                                                return (
                                                    <div key={optIdx} className={cn(
                                                        "p-3 rounded-lg border text-sm flex items-center justify-between",
                                                        styleClass
                                                    )}>
                                                        <span>{opt}</span>
                                                        {isCorrectOption && <CheckCircle className="w-4 h-4 text-green-500" />}
                                                        {isSelected && !isCorrectOption && <XCircle className="w-4 h-4 text-red-500" />}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>

                <div className="bg-gradient-to-br from-indigo-900/50 to-slate-900/50 border border-indigo-500/20 rounded-2xl p-8 text-center space-y-6 mt-12">
                    <h3 className="text-2xl font-bold text-white">Ready to create your own quizzes?</h3>
                    <p className="text-slate-400 leading-relaxed max-w-2xl mx-auto">
                        Sign up now to access the full question bank, create custom quizzes, track your progress, and more!
                    </p>

                    <div className="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                        <button
                            onClick={() => navigate('/signup')}
                            className="btn-vibeai flex items-center justify-center gap-2 px-8 py-3 text-lg shadow-lg shadow-indigo-500/20"
                        >
                            Create Free Account <ArrowRight className="w-5 h-5" />
                        </button>
                        <button
                            onClick={() => navigate('/login')}
                            className="px-8 py-3 rounded-xl bg-slate-800 text-white hover:bg-slate-700 font-medium transition-colors"
                        >
                            Back to Login
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    if (!currentQuestion) return null;

    const selectedAnswer = responses[currentQuestion.id];
    const progress = ((currentQuestionIndex + 1) / totalQuestions) * 100;

    return (
        <div className="max-w-5xl mx-auto p-4 md:p-6 space-y-6">
            {/* Header */}
            <header className="bg-slate-900/50 backdrop-blur-sm border border-white/5 p-4 rounded-2xl flex items-center justify-between sticky top-4 z-30 shadow-lg shadow-black/20">
                <div className="flex items-center space-x-4">
                    <div className="flex flex-col">
                        <span className="text-xs text-slate-400 uppercase tracking-wider font-bold">Demo Quiz</span>
                        <span className="text-xl font-bold text-white">
                            <span className="text-indigo-400">{currentQuestionIndex + 1}</span>
                            <span className="text-slate-600 mx-1">/</span>
                            {totalQuestions}
                        </span>
                    </div>
                    <div className="hidden md:block h-8 w-px bg-white/10" />
                    <div className="hidden md:block w-32 h-2 bg-slate-800 rounded-full overflow-hidden">
                        <div
                            className="h-full bg-indigo-500 rounded-full transition-all duration-500 ease-out"
                            style={{ width: `${progress}%` }}
                        />
                    </div>
                </div>

                <div className="flex items-center space-x-4">
                    <div className="px-3 py-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-sm font-medium">
                        Demo Mode
                    </div>
                    <button
                        onClick={() => setShowExitModal(true)}
                        className="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors"
                        title="Exit Quiz"
                    >
                        <XCircle className="w-6 h-6" />
                    </button>
                </div>
            </header>

            {/* Question Card */}
            <section className="bg-slate-900/50 backdrop-blur-sm rounded-3xl border border-white/5 p-6 md:p-10 shadow-xl min-h-[400px] flex flex-col">
                <div className="flex-1">
                    <h2 className="text-2xl md:text-3xl font-bold text-white leading-tight mb-8">
                        {currentQuestion.question_text}
                    </h2>

                    {currentQuestion.image_url && (
                        <div className="mb-8 relative group w-full max-w-2xl mx-auto rounded-2xl overflow-hidden border border-white/10 bg-black/20 cursor-zoom-in" onClick={() => openImage(currentQuestion.image_url!)}>
                            <div className="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                <span className="opacity-0 group-hover:opacity-100 text-white font-medium px-4 py-2 bg-black/50 rounded-full backdrop-blur-sm transition-opacity">Click to zoom</span>
                            </div>
                            <Image
                                src={currentQuestion.image_url}
                                alt="Question illustration"
                                className="w-full h-auto max-h-96 object-contain"
                            />
                        </div>
                    )}

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {currentQuestion.options?.map((opt, idx) => {
                            const isSelected = selectedAnswer === opt;
                            return (
                                <button
                                    key={opt}
                                    ref={idx === 0 ? firstOptionRef : undefined}
                                    onClick={() => handleSelectOption(opt)}
                                    className={cn(
                                        'group relative p-5 rounded-xl text-left transition-all duration-200 border-2 outline-none',
                                        isSelected
                                            ? 'bg-indigo-600/20 border-indigo-500 shadow-[0_0_15px_-3px_rgba(99,102,241,0.4)]'
                                            : 'bg-white/5 border-transparent hover:bg-white/10 hover:border-white/10'
                                    )}
                                >
                                    <div className="flex items-start">
                                        <div className={cn(
                                            "flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold mr-4 transition-colors",
                                            isSelected ? "bg-indigo-500 text-white" : "bg-white/10 text-slate-400 group-hover:bg-white/20 group-hover:text-white"
                                        )}>
                                            {String.fromCharCode(65 + idx)}
                                        </div>
                                        <span className={cn(
                                            "text-lg font-medium transition-colors",
                                            isSelected ? "text-white" : "text-slate-300 group-hover:text-white"
                                        )}>
                                            {opt}
                                        </span>
                                    </div>
                                    {isSelected && (
                                        <div className="absolute top-5 right-5">
                                            <CheckCircle className="w-5 h-5 text-indigo-400" />
                                        </div>
                                    )}
                                </button>
                            );
                        })}
                    </div>
                </div>

                {/* Navigation Footer */}
                <div className="mt-10 pt-6 border-t border-white/5 flex items-center justify-between">
                    <button
                        onClick={previousQuestion}
                        disabled={currentQuestionIndex === 0}
                        className={cn(
                            'flex items-center px-6 py-3 rounded-xl font-medium transition-all',
                            currentQuestionIndex === 0
                                ? 'text-slate-600 cursor-not-allowed'
                                : 'text-slate-300 hover:text-white hover:bg-white/5'
                        )}
                    >
                        <ChevronLeft className="w-5 h-5 mr-2" /> Previous
                    </button>

                    {currentQuestionIndex < totalQuestions - 1 ? (
                        <button
                            onClick={nextQuestion}
                            className="flex items-center px-8 py-3 rounded-xl bg-white text-indigo-950 font-bold hover:bg-indigo-50 transition-all shadow-lg hover:shadow-white/10 transform hover:-translate-y-0.5"
                        >
                            Next <ChevronRight className="w-5 h-5 ml-2" />
                        </button>
                    ) : (
                        <button
                            onClick={() => setShowSubmitModal(true)}
                            className="flex items-center px-8 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-500/25 transform hover:-translate-y-0.5"
                        >
                            Submit Quiz <CheckCircle className="w-5 h-5 ml-2" />
                        </button>
                    )}
                </div>
            </section>

            {/* Exit Confirmation Modal */}
            <Modal open={showExitModal} onClose={() => setShowExitModal(false)}>
                <div className="text-center">
                    <div className="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <AlertTriangle className="w-8 h-8 text-red-500" />
                    </div>
                    <h3 className="text-xl font-bold text-white mb-2">Exit Demo?</h3>
                    <p className="text-slate-400 mb-6">
                        Are you sure you want to leave? Your progress will not be saved.
                    </p>
                    <div className="flex gap-3 justify-center">
                        <button
                            onClick={() => setShowExitModal(false)}
                            className="px-6 py-2.5 rounded-xl bg-slate-800 text-white hover:bg-slate-700 font-medium transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            onClick={() => navigate('/login')}
                            className="px-6 py-2.5 rounded-xl bg-red-600 text-white hover:bg-red-500 font-medium transition-colors shadow-lg shadow-red-600/20"
                        >
                            Exit
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Submit Confirmation Modal */}
            <Modal open={showSubmitModal} onClose={() => setShowSubmitModal(false)}>
                <div className="text-center">
                    <div className="w-16 h-16 bg-indigo-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <CheckCircle className="w-8 h-8 text-indigo-500" />
                    </div>
                    <h3 className="text-xl font-bold text-white mb-2">Submit Demo?</h3>
                    <p className="text-slate-400 mb-6">
                        Ready to see your score?
                    </p>
                    <div className="flex gap-3 justify-center">
                        <button
                            onClick={() => setShowSubmitModal(false)}
                            className="px-6 py-2.5 rounded-xl bg-slate-800 text-white hover:bg-slate-700 font-medium transition-colors"
                        >
                            Review
                        </button>
                        <button
                            onClick={handleSubmitQuiz}
                            disabled={isSubmitting}
                            className="px-6 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-500 font-medium transition-colors shadow-lg shadow-indigo-600/20 flex items-center"
                        >
                            {isSubmitting ? <Loader2 className="w-4 h-4 mr-2 animate-spin" /> : null}
                            See Results
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Image Zoom Modal */}
            <Modal open={showImageModal} onClose={() => setShowImageModal(false)}>
                {imageSrc && (
                    <img src={imageSrc} alt="Zoomed" className="max-w-full max-h-[80vh] object-contain mx-auto rounded-lg" />
                )}
            </Modal>
        </div>
    );
};

