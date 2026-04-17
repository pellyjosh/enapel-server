import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import { Head, Link } from '@inertiajs/react';

export default function StockHistory({ product, adjustments = { data: [], links: [] } }) {
    const adjustmentsData = adjustments.data || [];

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title={`${product.name} - Stock History`} />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex items-center gap-4">
                    <Link
                        href={route('supermart.stock')}
                        className="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-500 hover:text-gray-900"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                    </Link>
                    <div>
                        <h1 className="text-4xl font-black text-gray-900 tracking-tight text-blue-900 truncate max-w-md">
                            {product.name} History
                        </h1>
                        <p className="text-gray-500 font-medium mt-1">
                            {product.variation_name ? `${product.variation_name} | ` : ''} 
                            SKU: {product.sku || 'N/A'} | Category: {product.category || 'Uncategorized'}
                        </p>
                    </div>
                </div>
                <div className="flex items-center gap-3 bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100">
                    <div className="flex flex-col">
                        <span className="text-[10px] font-black uppercase tracking-widest text-blue-400">Current Stock</span>
                        <span className="text-2xl font-black text-blue-900">{product.quantity} <span className="text-xs font-bold text-blue-400 uppercase">Units</span></span>
                    </div>
                </div>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden mb-12">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-slate-50 border-b border-gray-100 uppercase text-[9px] font-black tracking-[0.2em] text-slate-400">
                                <th className="p-6">Time</th>
                                <th className="p-6">Adjustment</th>
                                <th className="p-6">Supplier</th>
                                <th className="p-6">Unit Price</th>
                                <th className="p-6">Expiry Set</th>
                                <th className="p-6 text-right">Reference</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {adjustmentsData.map(adj => {
                                const quantity = Number(adj.quantity || 0);
                                const isPositive = quantity > 0;
                                
                                return (
                                    <tr key={adj.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="p-6">
                                            <div className="flex flex-col gap-0.5">
                                                <span className="text-xs font-black text-slate-900">{new Date(adj.created_at).toLocaleDateString()}</span>
                                                <span className="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                                    {new Date(adj.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="p-6">
                                            <div className="flex items-center gap-2">
                                                <span className={`flex items-center justify-center w-6 h-6 rounded-full text-xs font-black ${isPositive ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'}`}>
                                                    {isPositive ? '+' : ''}
                                                </span>
                                                <span className={`text-lg font-black ${isPositive ? 'text-emerald-600' : 'text-rose-600'}`}>
                                                    {quantity}
                                                </span>
                                                <span className="text-[10px] font-black text-slate-400 uppercase">Units</span>
                                            </div>
                                        </td>
                                        <td className="p-6">
                                            <span className="text-xs font-black text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl uppercase tracking-tighter">
                                                {adj.supplier?.supplier || 'N/A'}
                                            </span>
                                        </td>
                                        <td className="p-6 font-black text-slate-900 text-sm">
                                            ₦{Number(adj.amount || 0).toLocaleString()}
                                        </td>
                                        <td className="p-6">
                                            {adj.expiry_date ? (
                                                <span className="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl uppercase tracking-widest">
                                                    {new Date(adj.expiry_date).toLocaleDateString()}
                                                </span>
                                            ) : (
                                                <span className="text-[10px] text-slate-300 italic font-medium">No date recorded</span>
                                            )}
                                        </td>
                                        <td className="p-6 text-right">
                                            <span className="text-[10px] font-mono text-slate-400">#{adj.id}</span>
                                        </td>
                                    </tr>
                                );
                            })}
                            {adjustmentsData.length === 0 && (
                                <tr>
                                    <td colSpan="6" className="p-12 text-center">
                                        <div className="flex flex-col items-center gap-3 grayscale opacity-30">
                                            <span className="text-4xl">📜</span>
                                            <p className="text-sm font-black text-slate-400 uppercase tracking-widest">No adjustment records found for this product</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination links={adjustments.links} />
        </div>
    );
}

StockHistory.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
