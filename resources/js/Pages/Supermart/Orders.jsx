import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import { Head } from '@inertiajs/react';


export default function Orders({ orders = { data: [], links: [] } }) {
    const ordersData = orders.data || [];
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Supermart Procurement" />

            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight text-blue-900">Retail Procurement</h1>
                <p className="text-gray-500 font-medium mt-1">Track purchase orders for FMCG and retail inventory.</p>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Order #</th>
                                <th className="p-6">Product</th>
                                <th className="p-6">Supplier</th>
                                <th className="p-6">Qty</th>
                                <th className="p-6">Cost</th>
                                <th className="p-6">Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {ordersData.map(o => (
                                <tr key={o.id} className="hover:bg-blue-50 transition-colors">
                                    <td className="p-6 font-mono text-xs font-black text-blue-600">PO-{o.id}</td>
                                    <td className="p-6">
                                        <div className="flex items-center gap-2">
                                            <p className="text-sm font-bold text-gray-900">{o.inventory?.name || 'Retail Item'}</p>
                                            {o.inventory?.sku && (
                                                <span className="text-[8px] bg-gray-900 text-white px-1 py-0.5 rounded font-mono uppercase shrink-0">
                                                    {o.inventory.sku}
                                                </span>
                                            )}
                                        </div>
                                    </td>
                                    <td className="p-6 text-sm font-medium text-gray-400">{o.supplier?.supplier_name || 'Vendor'}</td>
                                    <td className="p-6 font-black text-gray-900">{o.quantity}</td>
                                    <td className="p-6 font-black text-blue-900">₦{Number(o.total_price).toLocaleString()}</td>
                                    <td className="p-6">
                                        <span className="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Fulfilled</span>
                                    </td>
                                </tr>
                            ))}
                            {ordersData.length === 0 && (
                                <tr>
                                    <td colSpan="6" className="p-20 text-center text-gray-400 font-medium italic">No retail procurement history.</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination links={orders.links} />
        </div>
    );
}

Orders.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
