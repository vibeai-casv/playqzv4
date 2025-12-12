import{j as e}from"./index-C57Ivx-l.js";import{r as c}from"./vendor-D8AvkUoV.js";import{ah as m,o as p,T as x,h as r,ai as g}from"./ui-8QwkGQs4.js";const h=async i=>(i("Seeder is currently disabled during migration to MySQL."),{successCount:0,failCount:0,skippedCount:0});function N(){const[i,o]=c.useState(!1),[l,n]=c.useState([]),a=s=>{n(t=>[...t,`[${new Date().toLocaleTimeString()}] ${s}`])},u=async()=>{if(confirm("This will insert questions from the local JSON files into the database. Continue?")){o(!0),n([]),a("Starting seed process...");try{const s=await h(t=>a(t));a(`Completed! Success: ${s.successCount}, Skipped: ${s.skippedCount}, Failed: ${s.failCount}`),r.success(`Seeding complete! Added ${s.successCount} questions.`)}catch(s){a(`Error: ${s.message}`),r.error("Seeding failed")}finally{o(!1)}}},d=`
-- 1. Create generate_quiz RPC
CREATE OR REPLACE FUNCTION public.generate_quiz(
    p_num_questions INTEGER,
    p_difficulty TEXT,
    p_categories TEXT[],
    p_time_limit INTEGER
)
RETURNS TABLE(question_ids UUID[]) AS $$
DECLARE
    v_question_ids UUID[];
BEGIN
    SELECT ARRAY_AGG(id)
    INTO v_question_ids
    FROM (
        SELECT id
        FROM public.questions
        WHERE 
            is_active = true AND
            (p_difficulty = 'Mixed' OR difficulty::TEXT = LOWER(p_difficulty)) AND
            (array_length(p_categories, 1) IS NULL OR category = ANY(p_categories))
        ORDER BY random()
        LIMIT p_num_questions
    ) sub;

    RETURN QUERY SELECT v_question_ids;
END;
$$ LANGUAGE plpgsql;

-- 2. Fix Permissions (Recursion)
CREATE OR REPLACE FUNCTION public.is_admin()
RETURNS BOOLEAN AS $$
BEGIN
  RETURN EXISTS (
    SELECT 1
    FROM public.profiles
    WHERE id = auth.uid() AND role = 'admin'
  );
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Apply policies...
-- (See full migration file for policies)
`;return e.jsxs("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8",children:[e.jsx("h1",{className:"text-3xl font-bold text-gray-900 mb-8",children:"System Tools"}),e.jsxs("div",{className:"grid grid-cols-1 lg:grid-cols-2 gap-8",children:[e.jsxs("div",{className:"bg-white rounded-xl shadow-sm border border-gray-100 p-6",children:[e.jsxs("div",{className:"flex items-center gap-3 mb-4",children:[e.jsx("div",{className:"p-2 bg-blue-100 rounded-lg",children:e.jsx(m,{className:"w-6 h-6 text-blue-600"})}),e.jsx("h2",{className:"text-xl font-semibold text-gray-900",children:"Seed Question Bank"})]}),e.jsx("p",{className:"text-gray-600 mb-6",children:"Import questions from the local JSON files (`qbank/`) into the Supabase database. Duplicates will be skipped automatically."}),e.jsx("button",{onClick:u,disabled:i,className:"w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50",children:i?e.jsx(e.Fragment,{children:"Processing..."}):e.jsxs(e.Fragment,{children:[e.jsx(p,{className:"w-4 h-4 mr-2"}),"Start Seeding"]})}),l.length>0&&e.jsx("div",{className:"mt-6 bg-gray-900 rounded-lg p-4 font-mono text-xs text-green-400 h-48 overflow-y-auto",children:l.map((s,t)=>e.jsx("div",{children:s},t))})]}),e.jsxs("div",{className:"bg-white rounded-xl shadow-sm border border-gray-100 p-6",children:[e.jsxs("div",{className:"flex items-center gap-3 mb-4",children:[e.jsx("div",{className:"p-2 bg-yellow-100 rounded-lg",children:e.jsx(x,{className:"w-6 h-6 text-yellow-600"})}),e.jsx("h2",{className:"text-xl font-semibold text-gray-900",children:"Database Setup Required"})]}),e.jsx("p",{className:"text-gray-600 mb-4",children:'Some database functions (RPCs) cannot be created from this dashboard. If "Start Quiz" is failing, please run the following SQL in your Supabase SQL Editor:'}),e.jsxs("div",{className:"relative",children:[e.jsx("pre",{className:"bg-gray-50 rounded-lg p-4 text-xs text-gray-700 overflow-x-auto h-64 border border-gray-200",children:d}),e.jsx("button",{onClick:()=>{navigator.clipboard.writeText(d),r.success("SQL copied to clipboard")},className:"absolute top-2 right-2 p-2 bg-white rounded shadow hover:bg-gray-50 text-xs font-medium text-gray-600 border border-gray-200",children:"Copy SQL"})]}),e.jsxs("div",{className:"mt-4 flex items-start gap-2 text-sm text-gray-500",children:[e.jsx(g,{className:"w-4 h-4 mt-0.5 flex-shrink-0"}),e.jsx("p",{children:"Go to Supabase Dashboard → SQL Editor → New Query → Paste & Run."})]})]})]})]})}export{N as SystemTools};
