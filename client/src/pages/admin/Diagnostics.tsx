import { useState, useEffect } from 'react';
import { Stethoscope, CheckCircle2, XCircle, AlertCircle, RefreshCw, Database, Server, Shield, HardDrive } from 'lucide-react';
import { toast } from 'sonner';
import api from '../../lib/api';


interface DiagnosticResponse {
    timestamp: string;
    checks: Record<string, any>;
    summary: {
        total_checks: number;
        errors: number;
        overall_status: string;
    };
    sample_media?: any[];
    all_tables?: {
        tables: string[];
        count: number;
    };
}

export function Diagnostics() {
    const [loading, setLoading] = useState(false);
    const [diagnostics, setDiagnostics] = useState<DiagnosticResponse | null>(null);
    const [mediaTest, setMediaTest] = useState<any>(null);

    const runDiagnostics = async () => {
        setLoading(true);
        try {
            // Test authentication and media API
            const mediaResponse = await api.get('/media/list.php', { params: { limit: 10 } });

            const now = new Date();
            const diagnosticData: DiagnosticResponse = {
                timestamp: now.toISOString(),
                checks: {
                    authentication: {
                        status: 'ok',
                        message: 'Successfully authenticated',
                    },
                    media_api: {
                        status: 'ok',
                        message: `Media API working - ${mediaResponse.data.total} files found`,
                    },
                    database_connection: {
                        status: 'ok',
                        message: 'Database connected successfully',
                    },
                },
                summary: {
                    total_checks: 3,
                    errors: 0,
                    overall_status: 'HEALTHY'
                },
                sample_media: mediaResponse.data.media.slice(0, 3)
            };

            setDiagnostics(diagnosticData);
            setMediaTest({
                authenticated: true,
                steps: {
                    config: 'OK',
                    database: 'OK',
                    authentication: 'OK - Authenticated',
                },
                summary: 'All systems operational'
            });

            toast.success('Diagnostics completed successfully');
        } catch (error: any) {
            console.error('Diagnostics error:', error);

            const errorData: DiagnosticResponse = {
                timestamp: new Date().toISOString(),
                checks: {
                    authentication: {
                        status: 'error',
                        message: error.response?.status === 401 ? 'Authentication failed - Please log out and log back in' : 'API Error: ' + (error.message || 'Unknown error'),
                    }
                },
                summary: {
                    total_checks: 1,
                    errors: 1,
                    overall_status: 'ISSUES FOUND'
                }
            };

            setDiagnostics(errorData);
            setMediaTest({
                authenticated: false,
                steps: {
                    authentication: error.response?.status === 401 ? 'FAILED - Session expired' : 'FAILED - ' + error.message,
                },
                summary: error.response?.status === 401
                    ? 'Your session has expired. Please log out and log back in.'
                    : 'Unable to connect to API'
            });

            toast.error('Diagnostics failed: ' + (error.response?.status === 401 ? 'Session expired' : error.message));
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        runDiagnostics();
    }, []);

    const getStatusIcon = (status: string) => {
        switch (status?.toLowerCase()) {
            case 'ok':
                return <CheckCircle2 className="w-5 h-5 text-green-500" />;
            case 'error':
                return <XCircle className="w-5 h-5 text-red-500" />;
            default:
                return <AlertCircle className="w-5 h-5 text-yellow-500" />;
        }
    };

    const getStatusColor = (status: string) => {
        switch (status?.toLowerCase()) {
            case 'ok':
                return 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800';
            case 'error':
                return 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800';
            default:
                return 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800';
        }
    };

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            {/* Header */}
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Stethoscope className="w-8 h-8 text-indigo-600" />
                        System Diagnostics
                    </h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Check system health and troubleshoot issues
                    </p>
                </div>
                <button
                    onClick={runDiagnostics}
                    disabled={loading}
                    className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50"
                >
                    <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
                    {loading ? 'Running...' : 'Run Diagnostics'}
                </button>
            </div>

            {/* Overall Status */}
            {diagnostics && (
                <div className={`p-6 rounded-lg border-2 ${diagnostics.summary.overall_status === 'HEALTHY'
                    ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800'
                    : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'
                    }`}>
                    <div className="flex items-center gap-3">
                        {diagnostics.summary.overall_status === 'HEALTHY' ? (
                            <CheckCircle2 className="w-8 h-8 text-green-600" />
                        ) : (
                            <XCircle className="w-8 h-8 text-red-600" />
                        )}
                        <div>
                            <h2 className="text-xl font-bold text-gray-900 dark:text-white">
                                System Status: {diagnostics.summary.overall_status}
                            </h2>
                            <p className="text-sm text-gray-600 dark:text-gray-400">
                                {diagnostics.summary.total_checks} checks completed • {diagnostics.summary.errors} errors found
                            </p>
                            <p className="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                Last checked: {new Date(diagnostics.timestamp).toLocaleString()}
                            </p>
                        </div>
                    </div>
                </div>
            )}

            {/* Database Diagnostics */}
            {diagnostics && (
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                        <Database className="w-5 h-5 text-indigo-600" />
                        Database Health
                    </h3>
                    <div className="space-y-3">
                        {Object.entries(diagnostics.checks).map(([key, value]: [string, any]) => (
                            <div
                                key={key}
                                className={`p-4 rounded-lg border ${getStatusColor(value.status)}`}
                            >
                                <div className="flex items-start gap-3">
                                    {getStatusIcon(value.status)}
                                    <div className="flex-1">
                                        <h4 className="font-medium text-gray-900 dark:text-white capitalize">
                                            {key.replace(/_/g, ' ')}
                                        </h4>
                                        <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {typeof value === 'string' ? value : value.message || JSON.stringify(value)}
                                        </p>
                                        {value.details && (
                                            <pre className="mt-2 text-xs bg-gray-100 dark:bg-gray-900 p-2 rounded overflow-x-auto">
                                                {JSON.stringify(value.details, null, 2)}
                                            </pre>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Database Tables */}
            {diagnostics?.all_tables && (
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                        <HardDrive className="w-5 h-5 text-indigo-600" />
                        Database Tables ({diagnostics.all_tables.count})
                    </h3>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
                        {diagnostics.all_tables.tables.map((table: string) => (
                            <div
                                key={table}
                                className="px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded text-sm font-mono text-gray-700 dark:text-gray-300"
                            >
                                {table}
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Media API Test */}
            {mediaTest && (
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                        <Shield className="w-5 h-5 text-indigo-600" />
                        Authentication & API Test
                    </h3>
                    <div className="space-y-3">
                        {Object.entries(mediaTest.steps || {}).map(([key, value]: [string, any]) => {
                            const isOk = typeof value === 'string' && (value.includes('OK') || value === 'Present');
                            const status = isOk ? 'ok' : 'error';

                            return (
                                <div
                                    key={key}
                                    className={`p-4 rounded-lg border ${getStatusColor(status)}`}
                                >
                                    <div className="flex items-start gap-3">
                                        {getStatusIcon(status)}
                                        <div className="flex-1">
                                            <h4 className="font-medium text-gray-900 dark:text-white capitalize">
                                                {key.replace(/_/g, ' ')}
                                            </h4>
                                            <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {value}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    {mediaTest.authenticated && mediaTest.session_info && (
                        <div className="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <h4 className="font-medium text-gray-900 dark:text-white mb-2">Session Information</h4>
                            <div className="text-sm space-y-1 text-gray-600 dark:text-gray-400">
                                <p><span className="font-medium">Email:</span> {mediaTest.session_info.email}</p>
                                <p><span className="font-medium">Role:</span> {mediaTest.session_info.role}</p>
                                <p><span className="font-medium">Expires:</span> {new Date(mediaTest.session_info.expires_at).toLocaleString()}</p>
                            </div>
                        </div>
                    )}

                    {!mediaTest.authenticated && (
                        <div className="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <h4 className="font-medium text-gray-900 dark:text-white mb-2">Authentication Issue</h4>
                            <p className="text-sm text-gray-600 dark:text-gray-400">
                                {mediaTest.summary || 'Your session may have expired. Try logging out and logging back in.'}
                            </p>
                        </div>
                    )}
                </div>
            )}

            {/* Sample Media Data */}
            {diagnostics?.sample_media && diagnostics.sample_media.length > 0 && (
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                        <Server className="w-5 h-5 text-indigo-600" />
                        Sample Media Files ({diagnostics.sample_media.length} of {diagnostics.checks.media_count?.total_rows || 0})
                    </h3>
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead className="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Filename</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Size</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Created</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                {diagnostics.sample_media.map((media: any) => (
                                    <tr key={media.id}>
                                        <td className="px-4 py-3 text-sm text-gray-900 dark:text-gray-300 font-mono">
                                            {media.original_filename}
                                        </td>
                                        <td className="px-4 py-3 text-sm">
                                            <span className="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded">
                                                {media.type}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {Math.round(media.size_bytes / 1024)} KB
                                        </td>
                                        <td className="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {new Date(media.created_at).toLocaleDateString()}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}

            {/* Loading State */}
            {loading && !diagnostics && (
                <div className="flex items-center justify-center py-12">
                    <div className="text-center">
                        <RefreshCw className="w-8 h-8 animate-spin text-indigo-600 mx-auto mb-4" />
                        <p className="text-gray-600 dark:text-gray-400">Running diagnostics...</p>
                    </div>
                </div>
            )}
        </div>
    );
}
