import { z } from 'zod';

export const questionSchema = z.object({
    question_text: z.string().min(3, 'Question text must be at least 3 characters'),
    question_type: z.enum(['text_mcq', 'image_identify_logo', 'image_identify_person', 'true_false', 'short_answer']),
    category: z.string().min(1, 'Category is required'),
    difficulty: z.enum(['easy', 'medium', 'hard']),
    points: z.number().min(1, 'Points must be at least 1'),
    status: z.enum(['active', 'inactive', 'draft']),
    options: z.array(z.string()).default([]),
    correct_answer: z.string().min(1, 'Correct answer is required'),
    explanation: z.string().optional(),
    image_url: z.string().optional().refine(
        (val) => !val || val === '' || val.startsWith('/') || val.startsWith('http://') || val.startsWith('https://'),
        'Image URL must be a valid URL or path starting with /'
    ),
}).superRefine((data, ctx) => {
    if (data.status === 'active' && (data.question_type === 'image_identify_logo' || data.question_type === 'image_identify_person')) {
        if (!data.image_url) {
            ctx.addIssue({
                code: z.ZodIssueCode.custom,
                message: "Image is required for active image identification questions",
                path: ["image_url"]
            });
        }
    }
});

export type QuestionFormData = z.infer<typeof questionSchema>;

export const aiGenerationSchema = z.object({
    topic: z.string().min(3, 'Topic is required'),
    count: z.number().min(1).max(50),
    difficulty: z.enum(['easy', 'medium', 'hard']),
    type: z.enum(['text_mcq', 'image_identify_logo', 'image_identify_person', 'true_false', 'short_answer']),
});

export type AIGenerationFormData = z.infer<typeof aiGenerationSchema>;
