import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ConfirmationModal from '@/Components/ConfirmationModal';
import TablePlaceholder from '@/Components/TablePlaceholder';

import { Head, Link, useForm, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Stock({ inventory = [] }) {
    const [editingItem, setEditingItem] = useState(null);
    const [itemToDelete, setItemToDelete] = useState(null);


    const {
        data: editData,
        setData: setEditData,
        put,
        processing,
        reset,
        errors,
    } = useForm({
        quantity: '',
        price: '',
        batch_number: '',
        expiry_date: '',
    });

    const openEdit = (item) => {
        setEditingItem(item);
        setEditData({
            quantity: item.quantity ?? '',
            price: item.price ?? '',
            batch_number: item.batch_number || '',
            expiry_date: item.expiry_date ? item.expiry_date.slice(0, 10) : '',
        });
    };

    const submitEdit = (e) => {
        e.preventDefault();
        if (!editingItem) return;

        put(route('pharmacy.stock.update', editingItem.id), {
            onSuccess: () => {
                reset();
                setEditingItem(null);
            },
        });
    };

    const handleDelete = () => {
        if (!itemToDelete) return;
        router.delete(route('pharmacy.catalog.delete', itemToDelete.id), {
            onFinish: () => setItemToDelete(null)
        });
    };


    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Pharmacy Inventory" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Drug Inventory</h1>
                    <p className="text-gray-500 font-medium mt-1">Monitor stock levels, batch numbers, and expirations.</p>
                </div>
                <Link href="/pharmacy/catalog" className="bg-indigo-600 hover:bg-black text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">Update Catalog</Link>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Medication</th>
                                <th className="p-6">Batch #</th>
                                <th className="p-6">Current Stock</th>
                                <th className="p-6">Expiry Date</th>
                                <th className="p-6">Status</th>
                                <th className="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {inventory.map(item => {
                                const quantity = Number(item.quantity || 0);
                                const isLow = quantity <= 10;
                                const expiryDate = item.expiry_date ? new Date(item.expiry_date) : null;
                                const isExpiring = expiryDate && expiryDate < new Date(Date.now() + 30 * 24 * 60 * 60 * 1000);
                                const categoryLabel = item.category ? item.category.replace(/^drug\s*[-:]\s*/i, '') : 'Drug';

                                return (
                                    <tr key={item.id} className="hover:bg-indigo-50/30 transition-colors group">
                                        <td className="p-6">
                                            <p className="font-bold text-gray-900">{item.name || item.product_name || 'Unnamed Drug'}</p>
                                            <p className="text-[10px] text-gray-400 font-black uppercase tracking-tighter">{categoryLabel}</p>
                                        </td>
                                        <td className="p-6 font-mono text-xs text-gray-500 tracking-tighter">{item.batch_number || 'NO-BATCH'}</td>
                                        <td className="p-6">
                                            <span className={`text-xl font-black ${isLow ? 'text-rose-600' : 'text-gray-900'}`}>{quantity}</span>
                                            <span className="text-[10px] text-gray-400 font-bold ml-1 uppercase">Units</span>
                                        </td>
                                        <td className="p-6 text-sm font-medium text-gray-500">
                                            {item.expiry_date ? new Date(item.expiry_date).toLocaleDateString() : 'N/A'}
                                        </td>
                                        <td className="p-6">
                                            {isExpiring ? (
                                                <span className="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Expiring</span>
                                            ) : isLow ? (
                                                <span className="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Low Stock</span>
                                            ) : (
                                                <span className="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Optimal</span>
                                            )}
                                        </td>
                                        <td className="p-6 text-right">
                                            <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                                <button
                                                    type="button"
                                                    onClick={() => openEdit(item)}
                                                    className="text-indigo-600 hover:text-indigo-800 font-bold text-xs bg-indigo-50 px-3 py-1.5 rounded-lg"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => setItemToDelete(item)}
                                                    className="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 px-3 py-1.5 rounded-lg"
                                                >
                                                    Delete
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>

                    {inventory.length === 0 && (
                        <TablePlaceholder 
                            title="No medications found"
                            description="Your pharmacy inventory is currently empty. Add medications to the catalog to start tracking stock."
                            icon="💊"
                        />
                    )}
                </div>
            </div>

            {editingItem && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitEdit} className="bg-white rounded-[40px] p-8 max-w-xl w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-indigo-900">
                            <h3 className="text-2xl font-black">Adjust Stock</h3>
                            <button type="button" onClick={() => setEditingItem(null)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quantity</label>
                                    <input
                                        type="number"
                                        min="0"
                                        value={editData.quantity}
                                        onChange={e => setEditData('quantity', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
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
                                        value={editData.price}
                                        onChange={e => setEditData('price', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                    />
                                    {errors.price && <p className="text-red-500 text-xs mt-1">{errors.price}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Batch Number</label>
                                    <input
                                        type="text"
                                        value={editData.batch_number}
                                        onChange={e => setEditData('batch_number', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                    />
                                    {errors.batch_number && <p className="text-red-500 text-xs mt-1">{errors.batch_number}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Expiry Date</label>
                                    <input
                                        type="date"
                                        value={editData.expiry_date}
                                        onChange={e => setEditData('expiry_date', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                    />
                                    {errors.expiry_date && <p className="text-red-500 text-xs mt-1">{errors.expiry_date}</p>}
                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-indigo-600 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
                        >
                            {processing ? 'Saving...' : 'Update Stock'}
                        </button>
                    </form>
                </div>
            )}
            <ConfirmationModal 
                show={!!itemToDelete}
                onClose={() => setItemToDelete(null)}
                onConfirm={handleDelete}
                title="Delete Medication"
                message={`Are you sure you want to delete ${itemToDelete?.name || itemToDelete?.product_name}? This action cannot be undone.`}
            />
        </div>
    );

}

Stock.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
