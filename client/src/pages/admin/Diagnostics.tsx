import { useState } from 'react';
import {
    Activity, RefreshCw, Database, Server,
    FileJson, Lock, CheckCircle, XCircle,
    AlertCircle, Loader2, ExternalLink
} from 'lucide-react';
import { toast } from 'sonner';
import axios from 'axios';

interface DiagnosticResult {
    [key: string]: any;
}

export function Diagnostics() {
    const [results, setResults] = useState<Record<string, DiagnosticResult>>({});
    const [loading, setLoading] = useState<Record<string, boolean>>({});

    const diagnosticTests = [
        {
            id: 'import',
            name: 'Import Diagnostic',
            description: 'Tests JSON import functionality and dependencies',
            endpoint: '/fix/import-diagnostic.php',
            icon: FileJson,
            color: 'text-blue-600'
        },
        {
            id: 'database',
            name: 'Database Connection',
            description: 'Verifies database connectivity and table structure',
            endpoint: '/fix/test-database.php',
            icon: Database,
            color: 'text-green-600'
        },
        {
            id: 'server',
            name: 'Server Environment',
            description: 'Checks PHP version, extensions, and settings',
            endpoint: '/fix/test-server-env.php',
            icon: Server,
            color: 'text-purple-600'
        },
        {
            id: 'session',
            name: 'Session & Auth',
            description: 'Tests authentication and session management',
            endpoint: '/fix/test-session.php',
            icon: Lock,
            color: 'text-orange-600'
        },
        {
            id: 'api',
            name: 'API Endpoints',
            description: 'Tests all API endpoints for accessibility',
            endpoint: '/fix/test-api-endpoints.php',
            icon: Activity,
            color: 'text-cyan-600'
        }
    ];

    const runTest = async (test: typeof diagnosticTests[0]) => {
        setLoading(prev => ({ ...prev, [test.id]: true }));

        try {
            const response = await axios.get(
                `${import.meta.env.VITE_API_URL.replace('/api', '')}${test.endpoint}`,
                { withCredentials: true }
            );

            setResults(prev => ({ ...prev, [test.id]: response.data }));
            toast.success(`${test.name} completed`);
        } catch (error) {
            console.error(`${test.name} error:`, error);
            toast.error(`${test.name} failed`);
            setResults(prev => ({
                ...prev,
                [test.id]: { error: 'Test failed to run' }
            }));
        } finally {
            setLoading(prev => ({ ...prev, [test.id]: false }));
        }
    };

    const runAllTests = async () => {
        for (const test of diagnosticTests) {
            await runTest(test);
            // Small delay between tests
            await new Promise(resolve => setTimeout(resolve, 500));
        }
    };

    const getStatusIcon = (result: DiagnosticResult) => {
        if (!result) return null;

        // Check various success indicators
        const isHealthy =
            result.ready_for_import === true ||
            result.overall_status === 'HEALTHY' ||
            result.connection === 'SUCCESS' ||
            result.logged_in === true ||
            (result.summary && result.summary.missing === 0);

        const hasError = result.error || result.ready_for_import === false;

        if (isHealthy && !hasError) {
            return <CheckCircle className="w-5 h-5 text-green-600" />;
        } else if (hasError) {
            return <XCircle className="w-5 h-5 text-red-600" />;
        } else {
            return <AlertCircle className="w-5 h-5 text-yellow-600" />;
        }
    };

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            {/* Header */}
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
                        System Diagnostics
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400 mt-1">
                        Run comprehensive tests to verify system health
                    </p>
                </div>
                <button
                    onClick={runAllTests}
                    disabled={Object.values(loading).some(Boolean)}
                    className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
                >
                    <RefreshCw className={`w-4 h-4 ${Object.values(loading).some(Boolean) ? 'animate-spin' : ''}`} />
                    Run All Tests
                </button>
            </div>

            {/* Test Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {diagnosticTests.map(test => {
                    const Icon = test.icon;
                    const result = results[test.id];
                    const isLoading = loading[test.id];

                    return (
                        <div
                            key={test.id}
                            className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4"
                        >
                            {/* Test Header */}
                            <div className="flex items-start justify-between">
                                <div className="flex items-center gap-3">
                                    <Icon className={`w-6 h-6 ${test.color}`} />
                                    <div>
                                        <h3 className="font-semibold text-gray-900 dark:text-white">
                                            {test.name}
                                        </h3>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">
                                            {test.description}
                                        </p>
                                    </div>
                                </div>
                                {result && getStatusIcon(result)}
                            </div>

                            {/* Action Button */}
                            <button
                                onClick={() => runTest(test)}
                                disabled={isLoading}
                                className="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50"
                            >
                                {isLoading ? (
                                    <>
                                        <Loader2 className="w-4 h-4 animate-spin" />
                                        Running...
                                    </>
                                ) : (
                                    <>
                                        <Activity className="w-4 h-4" />
                                        Run Test
                                    </>
                                )}
                            </button>

                            {/* Result Summary */}
                            {result && (
                                <div className="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div className="space-y-2 text-sm">
                                        {/* Key metrics */}
                                        {result.ready_for_import !== undefined && (
                                            <div className="flex justify-between">
                                                <span className="text-gray-600 dark:text-gray-400">Ready:</span>
                                                <span className={result.ready_for_import ? 'text-green-600' : 'text-red-600'}>
                                                    {result.ready_for_import ? 'Yes' : 'No'}
                                                </span>
                                            </div>
                                        )}
                                        {result.overall_status && (
                                            <div className="flex justify-between">
                                                <span className="text-gray-600 dark:text-gray-400">Status:</span>
                                                <span className={result.overall_status === 'HEALTHY' ? 'text-green-600' : 'text-yellow-600'}>
                                                    {result.overall_status}
                                                </span>
                                            </div>
                                        )}
                                        {result.logged_in !== undefined && (
                                            <div className="flex justify-between">
                                                <span className="text-gray-600 dark:text-gray-400">Logged In:</span>
                                                <span className={result.logged_in ? 'text-green-600' : 'text-red-600'}>
                                                    {result.logged_in ? 'Yes' : 'No'}
                                                </span>
                                            </div>
                                        )}
                                        {result.is_admin !== undefined && (
                                            <div className="flex justify-between">
                                                <span className="text-gray-600 dark:text-gray-400">Is Admin:</span>
                                                <span className={result.is_admin ? 'text-green-600' : 'text-red-600'}>
                                                    {result.is_admin ? 'Yes' : 'No'}
                                                </span>
                                            </div>
                                        )}
                                        {result.error && (
                                            <div className="text-red-600 text-xs mt-2">
                                                Error: {result.error}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>
                    );
                })}
            </div>

            {/* Detailed Results */}
            {Object.keys(results).length > 0 && (
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Detailed Results
                    </h2>

                    <div className="space-y-4">
                        {Object.entries(results).map(([testId, result]) => {
                            const test = diagnosticTests.find(t => t.id === testId);
                            if (!test) return null;

                            return (
                                <details key={testId} className="group">
                                    <summary className="cursor-pointer flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <div className="flex items-center gap-3">
                                            <test.icon className={`w-5 h-5 ${test.color}`} />
                                            <span className="font-medium text-gray-900 dark:text-white">
                                                {test.name}
                                            </span>
                                        </div>
                                        {getStatusIcon(result)}
                                    </summary>

                                    <div className="mt-2 p-4 bg-gray-900 rounded-lg overflow-x-auto">
                                        <pre className="text-xs text-green-400 font-mono">
                                            {JSON.stringify(result, null, 2)}
                                        </pre>
                                    </div>
                                </details>
                            );
                        })}
                    </div>
                </div>
            )}

            {/* External Link */}
            <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div className="flex items-start gap-3">
                    <ExternalLink className="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                    <div>
                        <h3 className="font-medium text-blue-900 dark:text-blue-100">
                            External Diagnostic Dashboard
                        </h3>
                        <p className="text-sm text-blue-700 dark:text-blue-300 mt-1">
                            You can also access the standalone diagnostic dashboard at:
                        </p>
                        <a
                            href={`${import.meta.env.VITE_API_URL.replace('/api', '')}/fix/`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block"
                        >
                            {import.meta.env.VITE_API_URL.replace('/api', '')}/fix/
                        </a>
                    </div>
                </div>
            </div>
        </div>
    );
}
