import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head } from '@inertiajs/react';

export default function Sales({ sales }) {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Pharmacy Sales History" />

            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Pharmacy Sales</h1>
                <p className="text-gray-500 font-medium mt-1">Detailed transaction history for medications and medical supplies.</p>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Transaction ID</th>
                                <th className="p-6">Medication</th>
                                <th className="p-6">Quantity</th>
                                <th className="p-6">Total Price</th>
                                <th className="p-6">Payment Method</th>
                                <th className="p-6">Date</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {sales.map(s => (
                                <tr key={s.id} className="hover:bg-indigo-50/30 transition-colors">
                                    <td className="p-6">
                                        <span className="font-mono text-xs font-black text-indigo-600">#{s.receipt?.receipt_number || s.id}</span>
                                    </td>
                                    <td className="p-6">
                                        <p className="font-bold text-gray-900">{s.product?.name}</p>
                                        <p className="text-[10px] text-gray-400 font-black uppercase tracking-tighter">{s.product?.category}</p>
                                    </td>
                                    <td className="p-6 font-bold text-gray-600">{s.quantity}</td>
                                    <td className="p-6 font-black text-gray-900">₦{Number(s.total_price).toLocaleString()}</td>
                                    <td className="p-6">
                                        <span className="bg-gray-100 px-3 py-1 rounded-full text-[10px] font-black uppercase text-gray-600">
                                            {s.receipt?.payment_method || 'Cash'}
                                        </span>
                                    </td>
                                    <td className="p-6 text-sm text-gray-500">
                                        {new Date(s.created_at).toLocaleString()}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {sales.length === 0 && (
                        <TablePlaceholder 
                            title="No sales records"
                            description="There are no pharmacy transactions recorded yet. Sales history will appear here as medications are dispensed."
                            icon="💊"
                        />
                    )}
                </div>
            </div>
        </div>
    );
}

Sales.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
