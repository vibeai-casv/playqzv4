import { useEffect, useState, useCallback, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import { XCircle, Loader2, Clock, X, ChevronLeft, ChevronRight, CheckCircle, AlertTriangle } from 'lucide-react';
import { useQuizStore } from '../../stores/quizStore';
import { cn } from '../../lib/utils';
import { Image } from '../../components/ui/Image';

// Simple modal component for confirmations and image zoom
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

export function TakeQuiz() {
    const navigate = useNavigate();
    const {
        questions,
        currentQuestionIndex,
        responses,
        nextQuestion,
        previousQuestion,
        submitAnswer,
        submitQuiz,
        timeRemaining,
        setTimeRemaining,
        isLoading,
    } = useQuizStore();

    const [showExitModal, setShowExitModal] = useState(false);
    const [showSubmitModal, setShowSubmitModal] = useState(false);
    const [showImageModal, setShowImageModal] = useState(false);
    const [imageSrc, setImageSrc] = useState('');

    const currentQuestion = questions[currentQuestionIndex];
    const totalQuestions = questions.length;

    // Timer countdown (if timeRemaining is set)
    useEffect(() => {
        if (timeRemaining <= 0) return;
        const timer = setInterval(() => {
            setTimeRemaining((t) => Math.max(t - 1, 0));
        }, 1000);
        return () => clearInterval(timer);
    }, [timeRemaining, setTimeRemaining]);

    // Warn before leaving the page (auto‑save is handled by Zustand store)
    useEffect(() => {
        const handler = (e: BeforeUnloadEvent) => {
            e.preventDefault();
            e.returnValue = '';
        };
        window.addEventListener('beforeunload', handler);
        return () => window.removeEventListener('beforeunload', handler);
    }, []);

    // Keyboard shortcuts for options (1‑4)
    const handleKeyDown = useCallback(
        (e: KeyboardEvent) => {
            if (!currentQuestion) return;
            const key = parseInt(e.key, 10);
            if (key >= 1 && key <= 4) {
                const option = currentQuestion.options?.[key - 1];
                if (option) {
                    const start = performance.now();
                    submitAnswer(currentQuestion.id, option, Math.round(performance.now() - start) / 1000);
                }
            }
        },
        [currentQuestion, submitAnswer]
    );

    useEffect(() => {
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [handleKeyDown]);

    const [isNavigating, setIsNavigating] = useState(false);

    // Handle answer click
    const onSelectOption = useCallback((option: string) => {
        if (isNavigating) return;

        const start = performance.now();
        submitAnswer(currentQuestion.id, option, Math.round(performance.now() - start) / 1000);

        // Auto-advance if not the last question
        if (currentQuestionIndex < totalQuestions - 1) {
            setIsNavigating(true);
            setTimeout(() => {
                nextQuestion();
                setIsNavigating(false);
            }, 800);
        }
    }, [currentQuestion, submitAnswer, currentQuestionIndex, totalQuestions, nextQuestion, isNavigating]);

    const openImage = (src: string) => {
        setImageSrc(src);
        setShowImageModal(true);
    };

    // Submit quiz flow
    const handleSubmitQuiz = async () => {
        try {
            await submitQuiz();
            navigate('/quiz-results');
        } catch (err) {
            console.error(err);
        }
    };

    // Focus management – focus first option when question changes
    const firstOptionRef = useRef<HTMLButtonElement>(null);
    useEffect(() => {
        firstOptionRef.current?.focus();
    }, [currentQuestionIndex]);

    if (!currentQuestion) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="text-center">
                    <AlertTriangle className="w-12 h-12 text-amber-500 mx-auto mb-4" />
                    <p className="text-slate-400">No quiz loaded. Please start a quiz from the configuration page.</p>
                    <button
                        onClick={() => navigate('/quiz-config')}
                        className="mt-4 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition-colors"
                    >
                        Go to Config
                    </button>
                </div>
            </div>
        );
    }

    const selectedAnswer = responses.get(currentQuestion.id)?.user_answer;
    const progress = ((currentQuestionIndex + 1) / totalQuestions) * 100;

    return (
        <div className="max-w-5xl mx-auto p-4 md:p-6 space-y-6">
            {/* Header */}
            <header className="bg-slate-900/50 backdrop-blur-sm border border-white/5 p-4 rounded-2xl flex items-center justify-between sticky top-4 z-30 shadow-lg shadow-black/20">
                <div className="flex items-center space-x-4">
                    <div className="flex flex-col">
                        <span className="text-xs text-slate-400 uppercase tracking-wider font-bold">Question</span>
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
                    {timeRemaining > 0 && (
                        <div className={cn(
                            "flex items-center px-3 py-1.5 rounded-lg border transition-colors",
                            timeRemaining < 60 ? "bg-red-500/10 border-red-500/30 text-red-400" : "bg-slate-800 border-white/5 text-slate-300"
                        )}>
                            <Clock className={cn("w-4 h-4 mr-2", timeRemaining < 60 && "animate-pulse")} />
                            <span className="font-mono font-bold">
                                {Math.floor(timeRemaining / 60)}:{String(timeRemaining % 60).padStart(2, '0')}
                            </span>
                        </div>
                    )}
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
                                className="max-w-[250px] max-h-[250px] object-contain mx-auto"
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
                                    onClick={() => onSelectOption(opt)}
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
                    <h3 className="text-xl font-bold text-white mb-2">Leave Quiz?</h3>
                    <p className="text-slate-400 mb-6">
                        Your progress will be saved, but you will lose the current attempt if you close the tab.
                    </p>
                    <div className="flex gap-3 justify-center">
                        <button
                            onClick={() => setShowExitModal(false)}
                            className="px-6 py-2.5 rounded-xl bg-slate-800 text-white hover:bg-slate-700 font-medium transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            onClick={() => navigate('/dashboard')}
                            className="px-6 py-2.5 rounded-xl bg-red-600 text-white hover:bg-red-500 font-medium transition-colors shadow-lg shadow-red-600/20"
                        >
                            Leave Quiz
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
                    <h3 className="text-xl font-bold text-white mb-2">Submit Quiz?</h3>
                    <p className="text-slate-400 mb-6">
                        Are you sure you want to submit? You will not be able to change answers after submission.
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
                            disabled={isLoading}
                            className="px-6 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-500 font-medium transition-colors shadow-lg shadow-indigo-600/20 flex items-center"
                        >
                            {isLoading ? <Loader2 className="w-4 h-4 mr-2 animate-spin" /> : null}
                            Confirm Submit
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
}
