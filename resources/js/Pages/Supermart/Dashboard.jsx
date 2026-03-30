import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function SupermartDashboard({ metrics }) {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Retail Dashboard" />

            {/* Header Section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight">
                        Retail Dashboard
                    </h1>
                    <p className="text-gray-500 font-medium mt-1">
                        High-volume sales tracking, stock levels, and supply chain.
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <button className="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-500/30 transition-all flex items-center gap-2 text-sm md:text-base">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        Add Product
                    </button>
                    <button className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2 text-sm md:text-base">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        Launch POS
                    </button>
                </div>
            </div>

            {/* Alert Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="bg-gradient-to-br from-indigo-500 to-blue-600 p-6 rounded-3xl text-white shadow-xl shadow-blue-500/20 relative overflow-hidden group">
                    <div className="relative z-10">
                        <div className="bg-white/20 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 backdrop-blur-md">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        </div>
                        <h3 className="text-3xl font-black mb-1">{metrics.total_sales}</h3>
                        <p className="font-medium text-blue-100 mb-6 font-bold tracking-tight uppercase text-xs">Total Sales Transactions</p>
                        
                        <button className="text-blue-600 text-xs font-black bg-white px-4 py-2 rounded-xl group-hover:scale-105 transition-all uppercase tracking-widest">
                            View Reports
                        </button>
                    </div>
                </div>

                <div className="bg-gradient-to-br from-orange-500 to-amber-500 p-6 rounded-3xl text-white shadow-xl shadow-orange-500/20 relative overflow-hidden group">
                    <div className="relative z-10">
                        <div className="bg-white/20 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 backdrop-blur-md">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        </div>
                        <h3 className="text-3xl font-black mb-1">{metrics.low_stock}</h3>
                        <p className="font-medium text-orange-100 mb-6 font-bold tracking-tight uppercase text-xs">Low Stock Alerts</p>
                        
                        <Link href="/supermart/catalog" className="text-white text-xs font-black bg-white/20 px-4 py-2 rounded-xl group-hover:bg-white/30 transition-colors uppercase tracking-widest">
                            Inventory
                        </Link>
                    </div>
                </div>

                <div className="bg-white border border-gray-100 p-6 rounded-3xl shadow-xl shadow-gray-200/40 relative overflow-hidden group">
                    <div className="relative z-10">
                        <div className="bg-rose-50 text-rose-600 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 border border-rose-100">
                             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <h3 className="text-3xl font-black text-gray-900 mb-1">{metrics.out_of_stock}</h3>
                        <p className="font-medium text-gray-500 mb-6 font-bold tracking-tight uppercase text-xs">Out of Stock Items</p>
                        
                        <Link href="/supermart/catalog" className="text-rose-600 text-xs font-black bg-rose-50 px-4 py-2 rounded-xl hover:bg-rose-100 transition-colors uppercase tracking-widest">
                            Restock &rarr;
                        </Link>
                    </div>
                </div>

                <div className="bg-white border border-gray-100 p-6 rounded-3xl shadow-xl shadow-gray-200/40 relative overflow-hidden group">
                    <div className="relative z-10">
                        <div className="bg-emerald-50 text-emerald-600 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 border border-emerald-100">
                             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        </div>
                        <h3 className="text-3xl font-black text-gray-900 mb-1">{metrics.total_products}</h3>
                        <p className="font-medium text-gray-500 mb-6 font-bold tracking-tight uppercase text-xs">Total Store Items</p>
                        
                        <Link href="/supermart/catalog" className="text-emerald-600 text-xs font-black bg-emerald-50 px-4 py-2 rounded-xl hover:bg-emerald-100 transition-colors uppercase tracking-widest">
                             Catalog &rarr;
                        </Link>
                    </div>
                </div>
            </div>
            
        </div>
    );
}

SupermartDashboard.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
