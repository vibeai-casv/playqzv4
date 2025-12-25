import { create } from 'zustand';
import type { Question, QuizAttempt, QuizResponse, QuizConfig } from '../types';
import api from '../lib/api';
import { generateUuid, shuffle } from '../lib/utils';

interface QuizState {
    currentAttempt: QuizAttempt | null;
    questions: Question[];
    responses: Map<string, QuizResponse>;
    currentQuestionIndex: number;
    timeRemaining: number;
    isLoading: boolean;

    // Actions
    generateQuiz: (config: QuizConfig) => Promise<void>;
    startAttempt: (quizId: string) => Promise<void>;
    submitAnswer: (questionId: string, answer: string, timeSpent: number) => void;
    nextQuestion: () => void;
    previousQuestion: () => void;
    submitQuiz: () => Promise<void>;
    resetQuiz: () => void;
    setTimeRemaining: (time: number | ((prev: number) => number)) => void;
    setQuiz: (attempt: QuizAttempt, questions: Question[]) => void;
}

export const useQuizStore = create<QuizState>((set, get) => ({
    currentAttempt: null,
    questions: [],
    responses: new Map(),
    currentQuestionIndex: 0,
    timeRemaining: 0,
    isLoading: false,

    generateQuiz: async (config) => {
        set({ isLoading: true });
        try {
            const response = await api.post('/quiz/generate.php', config);
            const { attempt, questions } = response.data;
            const shuffledQuestions = (questions as Question[]).map(q => ({
                ...q,
                options: q.options ? shuffle(q.options) : q.options
            }));

            set({
                currentAttempt: attempt as QuizAttempt,
                questions: shuffledQuestions,
                responses: new Map(),
                currentQuestionIndex: 0,
                timeRemaining: config.timeLimit || 0,
                isLoading: false,
            });
        } catch (error) {
            set({ isLoading: false });
            throw error;
        }
    },

    startAttempt: async (attemptId) => {
        set({ isLoading: true });
        try {
            const response = await api.get(`/quiz/get.php?id=${attemptId}`);
            const { attempt, questions } = response.data;
            const shuffledQuestions = (questions as Question[]).map(q => ({
                ...q,
                options: q.options ? shuffle(q.options) : q.options
            }));

            set({
                currentAttempt: attempt as QuizAttempt,
                questions: shuffledQuestions,
                responses: new Map(), // TODO: Fetch existing responses if resuming
                currentQuestionIndex: 0,
                timeRemaining: attempt.time_limit || 0, // Adjust based on elapsed time if needed
                isLoading: false,
            });
        } catch (error) {
            set({ isLoading: false });
            throw error;
        }
    },

    submitAnswer: (questionId, answer, timeSpent) => {
        const { currentAttempt, questions, responses } = get();
        if (!currentAttempt) return;

        const question = questions.find((q) => q.id === questionId);
        if (!question) return;

        const isCorrect = answer === question.correct_answer;

        const response: QuizResponse = {
            id: generateUuid(),
            attempt_id: currentAttempt.id,
            question_id: questionId,
            user_id: currentAttempt.user_id,
            user_answer: answer,
            is_correct: isCorrect,
            time_spent_seconds: timeSpent,
            answered_at: new Date().toISOString(),
            points_awarded: isCorrect ? question.points : 0,
            max_points: question.points,
            question_position: get().currentQuestionIndex + 1,
            skipped: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        const newResponses = new Map(responses);
        newResponses.set(questionId, response);

        set({ responses: newResponses });
    },

    nextQuestion: () => {
        const { currentQuestionIndex, questions } = get();
        if (currentQuestionIndex < questions.length - 1) {
            set({ currentQuestionIndex: currentQuestionIndex + 1 });
        }
    },

    previousQuestion: () => {
        const { currentQuestionIndex } = get();
        if (currentQuestionIndex > 0) {
            set({ currentQuestionIndex: currentQuestionIndex - 1 });
        }
    },

    submitQuiz: async () => {
        const { currentAttempt, responses } = get();
        if (!currentAttempt) throw new Error('No active quiz attempt');

        set({ isLoading: true });
        try {
            const responsesArray = Array.from(responses.values());

            await api.post('/quiz/submit.php', {
                attemptId: currentAttempt.id,
                responses: responsesArray
            });

            set({ isLoading: false });
        } catch (error) {
            set({ isLoading: false });
            throw error;
        }
    },

    resetQuiz: () => {
        set({
            currentAttempt: null,
            questions: [],
            responses: new Map(),
            currentQuestionIndex: 0,
            timeRemaining: 0,
        });
    },

    setTimeRemaining: (time) => set((state) => ({
        timeRemaining: typeof time === 'function' ? time(state.timeRemaining) : time
    })),

    setQuiz: (attempt, questions) => {
        const shuffledQuestions = (questions as Question[]).map(q => ({
            ...q,
            options: q.options ? shuffle(q.options) : q.options
        }));

        set({
            currentAttempt: attempt,
            questions: shuffledQuestions,
            responses: new Map(),
            currentQuestionIndex: 0,
            timeRemaining: attempt.config.timeLimit || 0,
            isLoading: false,
        });
    },
}));

