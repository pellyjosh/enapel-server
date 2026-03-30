import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import ConfirmationModal from '@/Components/ConfirmationModal';

import { Head, useForm, router } from '@inertiajs/react';
import { useState, useMemo } from 'react';


export default function Stock({ inventory = { data: [], links: [] } }) {
    const inventoryData = inventory.data || [];
    const [isAdjusting, setIsAdjusting] = useState(false);
    const [adjustingId, setAdjustingId] = useState('');
    const [itemToDelete, setItemToDelete] = useState(null);


    const itemsById = useMemo(() => {
        const map = new Map();
        inventoryData.forEach(item => map.set(String(item.id), item));
        return map;
    }, [inventoryData]);

    const currentItem = adjustingId ? itemsById.get(String(adjustingId)) : null;

    const { data, setData, put, processing, reset, errors } = useForm({
        quantity: '',
        price: '',
    });

    const openAdjust = (item) => {
        setAdjustingId(String(item.id));
        setData({
            quantity: item.quantity ?? '',
            price: item.price ?? '',
        });
        setIsAdjusting(true);
    };

    const openAdjustFromHeader = () => {
        if (inventoryData.length === 0) return;
        openAdjust(inventoryData[0]);
    };

    const submitAdjust = (e) => {
        e.preventDefault();
        if (!adjustingId) return;

        put(route('supermart.stock.update', adjustingId), {
            onSuccess: () => {
                reset();
                setIsAdjusting(false);
                setAdjustingId('');
            },
        });
    };

    const handleDelete = () => {
        if (!itemToDelete) return;
        router.delete(route('supermart.catalog.delete', itemToDelete.id), {
            onFinish: () => setItemToDelete(null)
        });
    };


    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Supermart Inventory" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-blue-900">Retail Inventory</h1>
                    <p className="text-gray-500 font-medium mt-1">Manage stock levels, categories, and replenishment for all retail products.</p>
                </div>
                <button
                    type="button"
                    onClick={openAdjustFromHeader}
                    className="bg-blue-600 hover:bg-black text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20"
                >
                    Adjust Stock
                </button>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Product</th>
                                <th className="p-6">Category</th>
                                <th className="p-6">Quantity</th>
                                <th className="p-6">Unit Price</th>
                                <th className="p-6">Status</th>
                                <th className="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {inventoryData.map(item => {
                                const quantity = Number(item.quantity || 0);
                                const isLow = quantity <= 10;
                                const isOutOfStock = quantity === 0;

                                return (
                                    <tr key={item.id} className="hover:bg-blue-50/30 transition-colors group">
                                        <td className="p-6">
                                            <div className="flex items-center gap-2">
                                                <div className="flex items-center gap-2 mb-1 w-full">
                                                    <h3 className="font-bold text-gray-900 truncate flex-1">{item.name}</h3>
                                                    {item.sku && (
                                                        <span className="text-[8px] bg-gray-900 text-white px-1 py-0.5 rounded font-mono uppercase shrink-0">
                                                            {item.sku}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="p-6">
                                            <span className="bg-gray-100 px-3 py-1 rounded-full text-[10px] font-black uppercase text-gray-500">{item.category || 'Uncategorized'}</span>
                                        </td>
                                        <td className="p-6">
                                            <span className={`text-xl font-black ${isOutOfStock ? 'text-rose-600' : isLow ? 'text-orange-600' : 'text-gray-900'}`}>{quantity}</span>
                                        </td>
                                        <td className="p-6 font-black text-gray-900">₦{Number(item.price || 0).toLocaleString()}</td>
                                        <td className="p-6">
                                            {isOutOfStock ? (
                                                <span className="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Out of Stock</span>
                                            ) : isLow ? (
                                                <span className="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Replenish</span>
                                            ) : (
                                                <span className="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Available</span>
                                            )}
                                        </td>
                                        <td className="p-6 text-right">
                                            <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                                <button
                                                    type="button"
                                                    onClick={() => openAdjust(item)}
                                                    className="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 px-3 py-1.5 rounded-lg"
                                                >
                                                    Adjust
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

                    {inventoryData.length === 0 && (
                        <div className="p-12 text-center text-gray-500 font-medium">
                            No inventory items found yet.
                        </div>
                    )}
                </div>
            </div>

            <Pagination links={inventory.links} />

            {isAdjusting && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitAdjust} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-blue-900">
                            <h3 className="text-2xl font-black">Adjust Stock</h3>
                            <button
                                type="button"
                                onClick={() => {
                                    setIsAdjusting(false);
                                    setAdjustingId('');
                                    reset();
                                }}
                                className="text-gray-400 hover:text-gray-900 transition-colors"
                            >
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Product</label>
                                <select
                                    value={adjustingId}
                                    onChange={(e) => {
                                        const id = e.target.value;
                                        setAdjustingId(id);
                                        const selected = itemsById.get(String(id));
                                        if (selected) {
                                            setData({
                                                quantity: selected.quantity ?? '',
                                                price: selected.price ?? '',
                                            });
                                        }
                                    }}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium appearance-none"
                                    required
                                >
                                    {inventoryData.map(item => (
                                        <option key={item.id} value={item.id}>{item.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">New Quantity</label>
                                    <input
                                        type="number"
                                        min="0"
                                        value={data.quantity}
                                        onChange={e => setData('quantity', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        required
                                    />
                                    {errors.quantity && <p className="text-red-500 text-xs mt-1">{errors.quantity}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Unit Price (₦)</label>
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={data.price}
                                        onChange={e => setData('price', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    />
                                    {errors.price && <p className="text-red-500 text-xs mt-1">{errors.price}</p>}
                                </div>
                            </div>
                            {currentItem && (
                                <p className="text-xs text-gray-500">Current category: {currentItem.category || 'Uncategorized'}</p>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-blue-600 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
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
                title="Delete Stock Item"
                message={`Are you sure you want to delete "${itemToDelete?.name}"? This action cannot be undone.`}
            />
        </div>
    );

}

Stock.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
