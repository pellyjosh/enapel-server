import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ConfirmationModal from '@/Components/ConfirmationModal';
import TablePlaceholder from '@/Components/TablePlaceholder';

import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Alerts({ lowStock = [], expiring = [] }) {
    const [restockingItem, setRestockingItem] = useState(null);
    const [itemToDelete, setItemToDelete] = useState(null);


    const {
        data,
        setData,
        put,
        processing,
        reset,
        errors,
    } = useForm({
        quantity: '',
        price: '',
    });

    const openRestock = (item) => {
        setRestockingItem(item);
        setData({
            quantity: item.quantity ?? '',
            price: item.price ?? '',
        });
    };

    const submitRestock = (e) => {
        e.preventDefault();
        if (!restockingItem) return;

        put(route('pharmacy.stock.update', restockingItem.id), {
            onSuccess: () => {
                reset();
                setRestockingItem(null);
            },
        });
    };

    const handleDispose = () => {
        if (!itemToDelete) return;
        router.delete(route('pharmacy.catalog.delete', itemToDelete.id), {
            onFinish: () => setItemToDelete(null)
        });
    };


    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Pharmacy Alerts" />

            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Critical Alerts</h1>
                <p className="text-gray-500 font-medium mt-1">Immediate action required for expiring or depleted stock items.</p>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8 space-y-6">
                    <div className="flex items-center gap-3 text-rose-600 mb-6">
                        <div className="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </div>
                        <h2 className="text-2xl font-black italic">Expiring in 30 Days</h2>
                    </div>

                    <div className="space-y-4">
                        {expiring.map(item => (
                            <div key={item.id} className="flex items-center justify-between p-4 bg-rose-50/50 rounded-2xl border border-rose-100 group hover:scale-[1.02] transition-all">
                                <div>
                                    <p className="font-bold text-gray-900">{item.name || item.product_name || 'Unnamed Drug'}</p>
                                    <p className="text-[10px] font-black uppercase text-rose-400">Expires: {new Date(item.expiry_date).toLocaleDateString()}</p>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setItemToDelete(item)}
                                    className="text-rose-600 text-xs font-black uppercase tracking-widest hover:underline decoration-2"
                                >
                                    Dispose
                                </button>
                            </div>
                        ))}
                        {expiring.length === 0 && (
                            <TablePlaceholder 
                                title="No expiring items"
                                description="Great! There are no medications expiring within the next 30 days."
                                icon="🛡️"
                            />
                        )}
                    </div>
                </div>

                <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8 space-y-6">
                    <div className="flex items-center gap-3 text-orange-600 mb-6">
                        <div className="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        </div>
                        <h2 className="text-2xl font-black italic">Low Stock Alerts</h2>
                    </div>

                    <div className="space-y-4">
                        {lowStock.map(item => (
                            <div key={item.id} className="flex items-center justify-between p-4 bg-orange-50/50 rounded-2xl border border-orange-100 group hover:scale-[1.02] transition-all">
                                <div>
                                    <p className="font-bold text-gray-900">{item.name || item.product_name || 'Unnamed Drug'}</p>
                                    <p className="text-xl font-black text-orange-600">{item.quantity} <span className="text-[10px] uppercase text-gray-400">Restock Needed</span></p>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => openRestock(item)}
                                    className="bg-orange-600 text-white text-[10px] font-black uppercase px-4 py-2 rounded-xl hover:bg-black transition-all"
                                >
                                    Restock
                                </button>
                            </div>
                        ))}
                        {lowStock.length === 0 && (
                            <TablePlaceholder 
                                title="Stock is optimal"
                                description="Excellent! All your medication stock levels are currently above the threshold."
                                icon="✅"
                            />
                        )}
                    </div>
                </div>
            </div>

            {restockingItem && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitRestock} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-orange-700">
                            <h3 className="text-2xl font-black">Restock Item</h3>
                            <button type="button" onClick={() => setRestockingItem(null)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quantity</label>
                                <input
                                    type="number"
                                    min="0"
                                    value={data.quantity}
                                    onChange={e => setData('quantity', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-orange-500 bg-gray-50 font-medium"
                                    required
                                />
                                {errors.quantity && <p className="text-red-500 text-xs mt-1">{errors.quantity}</p>}
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Selling Price (₦)</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value={data.price}
                                    onChange={e => setData('price', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-orange-500 bg-gray-50 font-medium"
                                />
                                {errors.price && <p className="text-red-500 text-xs mt-1">{errors.price}</p>}
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-orange-600 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
                        >
                            {processing ? 'Saving...' : 'Update Stock'}
                        </button>
                    </form>
                </div>
            )}
            <ConfirmationModal 
                show={!!itemToDelete}
                onClose={() => setItemToDelete(null)}
                onConfirm={handleDispose}
                title="Dispose Item"
                message={`Are you sure you want to dispose of ${itemToDelete?.name || itemToDelete?.product_name}? This will remove it from stock permanently.`}
            />
        </div>
    );

}

Alerts.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
