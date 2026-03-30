import React from 'react';

export default function CommerceStats({ metrics }) {
    if (!metrics) return null;

    return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 h-full">
            <div className="bg-orange-50 p-6 rounded-3xl border border-orange-100">
                <p className="text-orange-700 text-xs font-bold uppercase tracking-widest">Inventory Alerts</p>
                <h2 className="text-3xl font-black text-orange-900 mt-2">{metrics.low_stock_items}</h2>
                <p className="text-orange-600/70 text-sm mt-1">Items below min stock</p>
            </div>
            <div className="bg-emerald-50 p-6 rounded-3xl border border-emerald-100">
                <p className="text-emerald-700 text-xs font-bold uppercase tracking-widest">Total Inventory</p>
                <h2 className="text-3xl font-black text-emerald-900 mt-2">{metrics.total_items}</h2>
                <p className="text-emerald-600/70 text-sm mt-1">Managed products</p>
            </div>
        </div>
    );
}
