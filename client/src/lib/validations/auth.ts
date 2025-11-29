import { z } from 'zod';

export const loginSchema = z.object({
    identifier: z.string().min(1, "Email or Mobile Number is required"),
    password: z.string().min(1, "Password is required"),
    rememberMe: z.boolean().optional(),
});

export const signupSchema = z.object({
    full_name: z.string().min(2, 'Name must be at least 2 characters'),
    mobile_number: z.string().regex(/^[6-9]\d{9}$/, 'Invalid Indian mobile number'),
    email: z.string().email('Invalid email address'),
    password: z.string().min(6, 'Password must be at least 6 characters'),
    category: z.enum(['student', 'educator', 'other']),
    district: z.string().min(2, 'District is required'),
    institution_name: z.string().min(2, 'Institution name is required'),
    course_of_study: z.string().optional(),
    class_level: z.string().optional(),
    terms_accepted: z.boolean().refine(val => val === true, {
        message: 'You must accept the AI disclaimer',
    }),
}).superRefine((data, ctx) => {
    if (data.category === 'student') {
        if (!data.course_of_study || data.course_of_study.length < 2) {
            ctx.addIssue({
                code: z.ZodIssueCode.custom,
                message: "Course of study is required for students",
                path: ["course_of_study"],
            });
        }
        if (!data.class_level || data.class_level.length < 1) {
            ctx.addIssue({
                code: z.ZodIssueCode.custom,
                message: "Class level is required for students",
                path: ["class_level"],
            });
        }
    }
});

export type LoginFormData = z.infer<typeof loginSchema>;
export type SignupFormData = z.infer<typeof signupSchema>;
