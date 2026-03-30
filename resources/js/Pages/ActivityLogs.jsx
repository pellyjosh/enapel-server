import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function ActivityLogs({ logs }) {
    return (
        <>
            <Head title="Activity Logs" />

            <div className="py-8 px-4 sm:px-6 lg:px-8">
                <div className="max-w-7xl mx-auto">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div>
                            <h1 className="text-4xl font-black text-gray-900 tracking-tight">Activity Logs</h1>
                            <p className="text-gray-500 font-medium mt-1">Track actions and system events across all terminals.</p>
                        </div>
                        <div>
                            <a 
                                href={route('activity-logs.download')} 
                                className="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-2xl font-black hover:scale-105 transition-transform shadow-xl"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="Vertical-align: middle; 4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download CSV Report
                            </a>
                        </div>
                    </div>

                    <div className="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">User</th>
                                        <th className="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Action</th>
                                        <th className="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Module</th>
                                        <th className="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Description</th>
                                        <th className="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">IP Address</th>
                                        <th className="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {logs.data.map((log) => (
                                        <tr key={log.id} className="hover:bg-gray-50 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                                                        {log.user?.name?.charAt(0) || '?'}
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="text-sm font-bold text-gray-900">{log.user?.name || 'Unknown'}</div>
                                                        <div className="text-xs text-gray-500">{log.user?.email || ''}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className="px-3 py-1 text-xs font-black uppercase tracking-wider rounded-full bg-blue-100 text-blue-800">
                                                    {log.action}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600">
                                                {log.module || '-'}
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                                {log.description}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                                                {log.ip_address}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {new Date(log.created_at).toLocaleString()}
                                            </td>
                                        </tr>
                                    ))}
                                    {logs.data.length === 0 && (
                                        <tr>
                                            <td colSpan="6" className="px-6 py-12 text-center text-gray-500">
                                                No activity logs found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {logs.links && logs.links.length > 3 && (
                            <div className="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
                                <div className="flex-1 flex justify-between sm:hidden">
                                    {/* Mobile pagination simplified */}
                                </div>
                                <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p className="text-sm text-gray-700">
                                            Showing <span className="font-bold">{logs.from}</span> to <span className="font-bold">{logs.to}</span> of <span className="font-bold">{logs.total}</span> results
                                        </p>
                                    </div>
                                    <div>
                                        <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                            {logs.links.map((link, i) => (
                                                <Link
                                                    key={i}
                                                    href={link.url}
                                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                                    className={`relative inline-flex items-center px-4 py-2 border text-sm font-bold ${
                                                        link.active 
                                                            ? 'z-10 bg-blue-600 border-blue-600 text-white' 
                                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                                    } ${i === 0 ? 'rounded-l-xl' : ''} ${i === logs.links.length - 1 ? 'rounded-r-xl' : ''}`}
                                                />
                                            ))}
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

ActivityLogs.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
