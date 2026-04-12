import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head, useForm, router } from '@inertiajs/react';
import { formatDistanceToNow } from 'date-fns';

export default function Terminals({ terminals }) {
    
    const toggleStatus = (id) => {
        router.post(`/global/settings/terminals/${id}/toggle`, {}, {
            preserveScroll: true
        });
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Terminal Management" />

            {/* Header Section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight">
                        Terminal Management
                    </h1>
                    <p className="text-gray-500 font-medium mt-1">
                        Monitor and control all connected POS and reception devices
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <button className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2 text-sm">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        Register New Terminal
                    </button>
                </div>
            </div>

            {/* Terminal List */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {terminals.map(terminal => {
                    const isActive = terminal.status === 'active';
                    
                    return (
                        <div key={terminal.id} className="bg-white border border-gray-100 rounded-3xl p-6 shadow-xl shadow-gray-200/40 hover:shadow-2xl hover:shadow-gray-300/50 transition-all flex flex-col justify-between">
                            <div>
                                <div className="flex items-center justify-between mb-4">
                                    <div className={`w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-inner ${isActive ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100'}`}>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"/><rect x="9" y="9" width="6" height="6"/></svg>
                                    </div>
                                    <span className={`px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-full border ${isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200'}`}>
                                        {terminal.status}
                                    </span>
                                </div>
                                <h3 className="text-xl font-black text-gray-900 mb-1">{terminal.name || `Terminal #${terminal.id}`}</h3>
                                <div className="flex items-center gap-2 text-gray-500 font-medium text-sm">
                                    <svg className="w-4 h-4 shrink-0 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    <span className="truncate">{terminal.identifier || 'Unknown ID'}</span>
                                </div>
                            </div>
                            
                            <div className="mt-6 pt-6 border-t border-gray-100">
                                <div className="grid grid-cols-2 gap-4 mb-6">
                                    <div>
                                        <p className="text-gray-400 text-xs font-bold uppercase mb-1 tracking-widest">Sales</p>
                                        <p className="font-black text-gray-900">{terminal.sales_count || 0}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-400 text-xs font-bold uppercase mb-1 tracking-widest">Receipts</p>
                                        <p className="font-black text-gray-900">{terminal.receipts_count || 0}</p>
                                    </div>
                                </div>
                                
                                <button 
                                    onClick={() => toggleStatus(terminal.id)}
                                    className={`w-full py-3 rounded-xl font-bold text-sm transition-colors ${isActive ? 'bg-red-50 hover:bg-red-100 text-red-600' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600'}`}
                                >
                                    {isActive ? 'Disable Terminal' : 'Enable Terminal'}
                                </button>
                            </div>
                        </div>
                    );
                })}

                {terminals.length === 0 && (
                    <div className="col-span-full">
                        <TablePlaceholder 
                            title="No terminals found"
                            description="There are currently no connected terminals in the system. New terminals will appear here once they check in or are manually registered."
                            icon="🖥️"
                        />
                    </div>
                )}
            </div>
        </div>
    );
}

Terminals.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
