import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function PharmacyDashboard({ metrics }) {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Pharmacy Dashboard" />

            {/* Header Section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight">
                        Pharmacy Dashboard
                    </h1>
                    <p className="text-gray-500 font-medium mt-1">
                        Manage prescriptions, point of sale, and drug inventory.
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <button className="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-500/30 transition-all flex items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        New Drug Entry
                    </button>
                    <button className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        Open POS
                    </button>
                </div>
            </div>

            {/* Alert Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="bg-gradient-to-br from-red-500 to-rose-600 p-6 rounded-3xl text-white shadow-xl shadow-red-500/20 relative overflow-hidden group">
                    <div className="relative z-10">
                        <div className="bg-white/20 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 backdrop-blur-md">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <h3 className="text-3xl font-black mb-1">{metrics.expiring_soon}</h3>
                        <p className="font-medium text-red-100 mb-6 font-bold tracking-tight uppercase text-xs">Expiring Items</p>
                        <Link href="/pharmacy/catalog" className="text-white text-xs font-black bg-white/20 px-4 py-2 rounded-xl group-hover:bg-white/30 transition-colors uppercase tracking-widest">
                            Manage Batch
                        </Link>
                    </div>
                </div>

                <div className="bg-gradient-to-br from-orange-500 to-amber-500 p-6 rounded-3xl text-white shadow-xl shadow-orange-500/20 relative overflow-hidden group">
                    <div className="relative z-10">
                        <div className="bg-white/20 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 backdrop-blur-md">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        </div>
                        <h3 className="text-3xl font-black mb-1">{metrics.low_stock}</h3>
                        <p className="font-medium text-orange-100 mb-6 font-bold tracking-tight uppercase text-xs">Low Stock Alerts</p>
                        <Link href="/pharmacy/catalog" className="text-white text-xs font-black bg-white/20 px-4 py-2 rounded-xl group-hover:bg-white/30 transition-colors uppercase tracking-widest">
                            Order Stock
                        </Link>
                    </div>
                </div>

                <div className="bg-white border border-gray-100 p-6 rounded-3xl shadow-xl shadow-gray-200/40 relative overflow-hidden group">
                    <div className="relative z-10">
                        <div className="bg-blue-50 text-indigo-600 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 border border-indigo-100">
                             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                        </div>
                        <h3 className="text-3xl font-black text-gray-900 mb-1">{metrics.pending_prescriptions}</h3>
                        <p className="font-medium text-gray-500 mb-6 font-bold tracking-tight uppercase text-xs">Active Prescriptions</p>
                        <Link href="/pharmacy/prescriptions" className="text-indigo-600 text-xs font-black bg-indigo-50 px-4 py-2 rounded-xl hover:bg-indigo-100 transition-colors uppercase tracking-widest">
                            Dispense Now &rarr;
                        </Link>
                    </div>
                </div>

                <div className="bg-white border border-gray-100 p-6 rounded-3xl shadow-xl shadow-gray-200/40 relative overflow-hidden group">
                    <div className="relative z-10">
                        <div className="bg-emerald-50 text-emerald-600 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 border border-emerald-100">
                             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        </div>
                        <h3 className="text-3xl font-black text-gray-900 mb-1">{metrics.total_drugs}</h3>
                        <p className="font-medium text-gray-500 mb-6 font-bold tracking-tight uppercase text-xs">Total Drugs Cataloged</p>
                        <Link href="/pharmacy/catalog" className="text-emerald-600 text-xs font-black bg-emerald-50 px-4 py-2 rounded-xl hover:bg-emerald-100 transition-colors uppercase tracking-widest">
                            View Catalog &rarr;
                        </Link>
                    </div>
                </div>
            </div>
            
            <div className="bg-gray-50 border border-gray-200 rounded-3xl p-8 text-center mt-12">
                <div className="w-16 h-16 bg-white text-blue-600 shadow-sm rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <span className="text-2xl">💊</span>
                </div>
                <h3 className="text-xl font-black text-gray-900 mb-2">Build your Drug Catalog</h3>
                <p className="text-gray-500 max-w-md mx-auto mb-6">Start entering your inventory by categorizing drugs, tracking batches, and monitoring stock levels automatically.</p>
                <Link href="/pharmacy/catalog" className="text-sm font-bold bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-xl transition-colors inline-block">
                    Add First Drug
                </Link>
            </div>
        </div>
    );
}

PharmacyDashboard.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
