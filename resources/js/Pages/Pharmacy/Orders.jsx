import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Orders({ orders }) {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Pharmacy Procurement" />

            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Refill Orders</h1>
                <p className="text-gray-500 font-medium mt-1">Track incoming drug shipments and procurement status.</p>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Order Ref</th>
                                <th className="p-6">Medication</th>
                                <th className="p-6">Supplier</th>
                                <th className="p-6">Quantity</th>
                                <th className="p-6">Amount</th>
                                <th className="p-6">Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {orders.map(o => (
                                <tr key={o.id} className="hover:bg-indigo-50/30 transition-colors">
                                    <td className="p-6 font-mono text-xs font-black text-indigo-600">ORD-{o.id.toString().padStart(4, '0')}</td>
                                    <td className="p-6 text-sm font-bold text-gray-900">{o.inventory?.name || 'N/A'}</td>
                                    <td className="p-6 text-sm font-medium text-gray-600">{o.supplier?.supplier_name || 'N/A'}</td>
                                    <td className="p-6 text-sm font-black text-gray-600">{o.quantity} units</td>
                                    <td className="p-6 text-sm font-black text-gray-900">₦{Number(o.total_price).toLocaleString()}</td>
                                    <td className="p-6 text-sm">
                                        <span className="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Received</span>
                                    </td>
                                </tr>
                            ))}
                            {orders.length === 0 && (
                                <tr>
                                    <td colSpan="6" className="p-20 text-center text-gray-400 font-medium">No order history found.</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}

Orders.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
