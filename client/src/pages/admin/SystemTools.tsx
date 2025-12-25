import { useState } from 'react';
import { seedQuestions } from '../../utils/seeder';
import { toast } from 'sonner';
import { Database, Play, AlertTriangle, Terminal, RefreshCw, CheckCircle2 } from 'lucide-react';
import { fetchAPI } from '../../lib/api';
import api from '../../lib/api';

export function SystemTools() {
    const [seeding, setSeeding] = useState(false);
    const [logs, setLogs] = useState<string[]>([]);
    const [schemaUpdating, setSchemaUpdating] = useState(false);
    const [schemaLogs, setSchemaLogs] = useState<string[]>([]);
    const [schemaResult, setSchemaResult] = useState<any>(null);
    const [activating, setActivating] = useState(false);
    const [activateLogs, setActivateLogs] = useState<string[]>([]);

    const addLog = (msg: string) => {
        setLogs(prev => [...prev, `[${new Date().toLocaleTimeString()}] ${msg}`]);
    };

    const handleSeed = async () => {
        if (!confirm('This will insert questions from the local JSON files into the database. Continue?')) return;

        setSeeding(true);
        setLogs([]);
        addLog('Starting seed process...');

        try {
            const result = await seedQuestions((msg) => addLog(msg));
            addLog(`Completed! Success: ${result.successCount}, Skipped: ${result.skippedCount}, Failed: ${result.failCount}`);
            toast.success(`Seeding complete! Added ${result.successCount} questions.`);
        } catch (error: any) {
            addLog(`Error: ${error.message}`);
            toast.error('Seeding failed');
        } finally {
            setSeeding(false);
        }
    };

    const handleSchemaUpdate = async () => {
        if (!confirm('This will update the database schema with any missing fields. This is safe to run multiple times. Continue?')) return;

        setSchemaUpdating(true);
        setSchemaLogs([]);
        setSchemaResult(null);

        setSchemaLogs(prev => [...prev, 'Starting database schema update...']);
        setSchemaLogs(prev => [...prev, 'Starting database schema update...']);

        try {
            const result = await fetchAPI('/admin/update-schema.php', {
                method: 'POST',
            });

            setSchemaResult(result);

            if (result.success) {
                setSchemaLogs(prev => [...prev, '✅ Schema update completed successfully']);
                if (result.changes && result.changes.length > 0) {
                    result.changes.forEach((change: string) => {
                        setSchemaLogs(prev => [...prev, `  • ${change}`]);
                    });
                    toast.success(`Schema updated! ${result.changes.length} change(s) applied.`);
                } else {
                    setSchemaLogs(prev => [...prev, 'ℹ️ No changes needed - schema is up to date']);
                    toast.info('Schema is already up to date');
                }

                if (result.stats) {
                    setSchemaLogs(prev => [...prev, '', 'Database Statistics:']);
                    setSchemaLogs(prev => [...prev, `  - Tables Checked: ${result.stats.tablesChecked}`]);
                    setSchemaLogs(prev => [...prev, `  - Fields Validated: ${result.stats.fieldsChecked}`]);
                    setSchemaLogs(prev => [...prev, `  - Fields Added: ${result.stats.fieldsAdded}`]);
                    setSchemaLogs(prev => [...prev, `  - Indexes Created: ${result.stats.indexesAdded}`]);
                }
            } else {
                setSchemaLogs(prev => [...prev, `❌ Error: ${result.error || 'Unknown error'}`]);
                toast.error('Schema update failed');
            }
        } catch (error: any) {
            // Enhanced error logging
            setSchemaLogs(prev => [...prev, '']);
            setSchemaLogs(prev => [...prev, '❌ Request Failed:']);
            setSchemaLogs(prev => [...prev, `  Error: ${error.message}`]);
            setSchemaLogs(prev => [...prev, `  Type: ${error.constructor.name}`]);

            // Try to provide more context
            if (error.message.includes('404')) {
                setSchemaLogs(prev => [...prev, '']);
                setSchemaLogs(prev => [...prev, '💡 Troubleshooting 404:']);
                setSchemaLogs(prev => [...prev, '  1. Verify file exists on server']);
                setSchemaLogs(prev => [...prev, `  2. Check: ${window.location.origin}/aiq3/api/admin/update-schema.php`]);
                setSchemaLogs(prev => [...prev, '  3. Ensure .htaccess is not blocking the file']);
                setSchemaLogs(prev => [...prev, '  4. Check file permissions (should be 644)']);
            }

            console.error('Schema update error:', error);
            toast.error('Failed to update schema - check console for details');
        } finally {
            setSchemaUpdating(false);
        }
    };

    // Test connection function
    const testConnection = async () => {
        setSchemaLogs([]);
        setSchemaLogs(prev => [...prev, '🔍 Testing API Connection...']);

        setSchemaLogs(prev => [...prev, `Current Path: ${window.location.pathname}`]);

        try {
            setSchemaLogs(prev => [...prev, 'Testing endpoint: /admin/test.php']);

            const response = await api.get('/admin/test.php');
            const data = response.data;

            setSchemaLogs(prev => [...prev, '✅ Connection successful!']);
            setSchemaLogs(prev => [...prev, `Server Time: ${data.timestamp}`]);
            setSchemaLogs(prev => [...prev, `PHP Version: ${data.php_version}`]);
            setSchemaLogs(prev => [...prev, '']);
            setSchemaLogs(prev => [...prev, 'Files in admin directory:']);
            data.files_in_directory?.forEach((file: string) => {
                if (file !== '.' && file !== '..') {
                    setSchemaLogs(prev => [...prev, `  - ${file}`]);
                }
            });

            toast.success('API connection test successful');
        } catch (error: any) {
            setSchemaLogs(prev => [...prev, `❌ Connection failed: ${error.message}`]);
            toast.error('API connection test failed');
        }
    };

    const handleBulkActivate = async (type: string) => {
        if (!confirm(`This will activate all inactive questions of type "${type}". Continue?`)) return;

        setActivating(true);
        setActivateLogs([]);
        try {
            const result = await fetchAPI('/admin/bulk-activate_types.php', {
                method: 'POST',
                body: JSON.stringify({ type }),
            });

            if (result.success) {
                setActivateLogs(prev => [...prev, `✅ ${result.message}`]);
                toast.success(result.message);
            } else {
                setActivateLogs(prev => [...prev, `❌ Error: ${result.error}`]);
                toast.error('Activation failed');
            }
        } catch (error: any) {
            setActivateLogs(prev => [...prev, `❌ Request Failed: ${error.message}`]);
            toast.error('Failed to communicate with API');
        } finally {
            setActivating(false);
        }
    };

    const rpcSql = `
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
`;

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 className="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8">System Tools</h1>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {/* Database Schema Update */}
                <div className="bg-card rounded-xl shadow-sm border border-border p-6">
                    <div className="flex items-center gap-3 mb-4">
                        <div className="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                            <RefreshCw className="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <h2 className="text-xl font-semibold text-foreground">Database Schema Update</h2>
                    </div>
                    <p className="text-muted-foreground mb-6">
                        Update the database schema with any missing fields or indexes.
                        This is safe to run multiple times and will only add what's missing.
                    </p>

                    <div className="space-y-3">
                        <button
                            onClick={testConnection}
                            className="w-full flex items-center justify-center px-4 py-2 border border-border rounded-md shadow-sm text-sm font-medium text-foreground bg-muted hover:bg-muted/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <Terminal className="w-4 h-4 mr-2" />
                            Test API Connection
                        </button>

                        <button
                            onClick={handleSchemaUpdate}
                            disabled={schemaUpdating}
                            className="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {schemaUpdating ? (
                                <>
                                    <RefreshCw className="w-4 h-4 mr-2 animate-spin" />
                                    Updating Schema...
                                </>
                            ) : (
                                <>
                                    <CheckCircle2 className="w-4 h-4 mr-2" />
                                    Run Schema Update
                                </>
                            )}
                        </button>
                    </div>

                    {schemaLogs.length > 0 && (
                        <div className="mt-6 bg-gray-900 dark:bg-gray-950 rounded-lg p-4 font-mono text-xs text-green-400 h-48 overflow-y-auto">
                            {schemaLogs.map((log, i) => (
                                <div key={i}>{log}</div>
                            ))}
                        </div>
                    )}

                    {schemaResult && schemaResult.success && (
                        <div className="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div className="flex items-start gap-2">
                                <CheckCircle2 className="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" />
                                <div className="text-sm text-green-800 dark:text-green-200">
                                    <strong>Success!</strong> Database schema has been updated successfully.
                                </div>
                            </div>
                        </div>
                    )}
                </div>

                {/* Seed Questions */}
                <div className="bg-card rounded-xl shadow-sm border border-border p-6">
                    <div className="flex items-center gap-3 mb-4">
                        <div className="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <Database className="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <h2 className="text-xl font-semibold text-foreground">Seed Question Bank</h2>
                    </div>
                    <p className="text-muted-foreground mb-6">
                        Import questions from the local JSON files (`qbank/`) into the Supabase database.
                        Duplicates will be skipped automatically.
                    </p>

                    <button
                        onClick={handleSeed}
                        disabled={seeding}
                        className="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                    >
                        {seeding ? (
                            <>Processing...</>
                        ) : (
                            <>
                                <Play className="w-4 h-4 mr-2" />
                                Start Seeding
                            </>
                        )}
                    </button>

                    {logs.length > 0 && (
                        <div className="mt-6 bg-gray-900 dark:bg-gray-950 rounded-lg p-4 font-mono text-xs text-green-400 h-48 overflow-y-auto">
                            {logs.map((log, i) => (
                                <div key={i}>{log}</div>
                            ))}
                        </div>
                    )
                    }
                </div>

                {/* Bulk Activate Questions */}
                <div className="bg-card rounded-xl shadow-sm border border-border p-6">
                    <div className="flex items-center gap-3 mb-4">
                        <div className="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <CheckCircle2 className="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <h2 className="text-xl font-semibold text-foreground">Bulk Activate Questions</h2>
                    </div>
                    <p className="text-muted-foreground mb-6">
                        Activate questions that are currently in "Draft" or "Inactive" status.
                    </p>

                    <div className="grid grid-cols-2 gap-3">
                        <button
                            onClick={() => handleBulkActivate('image_identify_person')}
                            disabled={activating}
                            className="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
                        >
                            Activate Person Questions
                        </button>
                        <button
                            onClick={() => handleBulkActivate('image_identify_logo')}
                            disabled={activating}
                            className="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                        >
                            Activate Logo Questions
                        </button>
                        <button
                            onClick={() => handleBulkActivate('text_mcq')}
                            disabled={activating}
                            className="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50"
                        >
                            Activate MCQ Questions
                        </button>
                        <button
                            onClick={() => handleBulkActivate('all')}
                            disabled={activating}
                            className="flex items-center justify-center px-4 py-2 border border-border rounded-md shadow-sm text-xs font-medium text-foreground bg-muted hover:bg-muted/80 disabled:opacity-50"
                        >
                            Activate ALL Questions
                        </button>
                    </div>

                    {activateLogs.length > 0 && (
                        <div className="mt-6 bg-gray-900 dark:bg-gray-950 rounded-lg p-4 font-mono text-xs text-green-400 h-24 overflow-y-auto">
                            {activateLogs.map((log, i) => (
                                <div key={i}>{log}</div>
                            ))}
                        </div>
                    )}
                </div>

            </div>

            {/* Database Setup Info - Full Width */}
            <div className="mt-8 bg-card rounded-xl shadow-sm border border-border p-6">
                <div className="flex items-center gap-3 mb-4">
                    <div className="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <AlertTriangle className="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <h2 className="text-xl font-semibold text-foreground">Database Setup Required</h2>
                </div>
                <p className="text-muted-foreground mb-4">
                    Some database functions (RPCs) cannot be created from this dashboard.
                    If "Start Quiz" is failing, please run the following SQL in your Supabase SQL Editor:
                </p>

                <div className="relative">
                    <pre className="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 text-xs text-gray-700 dark:text-gray-300 overflow-x-auto h-64 border border-border">
                        {rpcSql}
                    </pre>
                    <button
                        onClick={() => {
                            navigator.clipboard.writeText(rpcSql);
                            toast.success('SQL copied to clipboard');
                        }}
                        className="absolute top-2 right-2 p-2 bg-white dark:bg-gray-800 rounded shadow hover:bg-gray-50 dark:hover:bg-gray-700 text-xs font-medium text-gray-600 dark:text-gray-300 border border-border"
                    >
                        Copy SQL
                    </button>
                </div>

                <div className="mt-4 flex items-start gap-2 text-sm text-muted-foreground">
                    <Terminal className="w-4 h-4 mt-0.5 flex-shrink-0" />
                    <p>
                        Go to Supabase Dashboard &rarr; SQL Editor &rarr; New Query &rarr; Paste & Run.
                    </p>
                </div>
            </div>
        </div>
    );
}
