import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import ConfirmationModal from '@/Components/ConfirmationModal';
import TablePlaceholder from '@/Components/TablePlaceholder';

import { Head, useForm, router } from '@inertiajs/react';
import { useState, useMemo } from 'react';


export default function Stock({ inventory = { data: [], links: [] }, allProducts = [], suppliers = [] }) {
    const inventoryData = inventory.data || [];
    const [isAdjusting, setIsAdjusting] = useState(false);
    const [isQuickAddingSupplier, setIsQuickAddingSupplier] = useState(false);
    const [adjustingId, setAdjustingId] = useState('');
    const [itemToDelete, setItemToDelete] = useState(null);

    const allProductsItems = allProducts || [];
    const suppliersList = suppliers || [];

    const itemsById = useMemo(() => {
        const map = new Map();
        allProductsItems.forEach(item => map.set(String(item.id), item));
        return map;
    }, [allProductsItems]);

    const currentItem = adjustingId ? itemsById.get(String(adjustingId)) : null;

    const { data, setData, put, processing, reset, errors } = useForm({
        quantity: '',
        price: '',
        expiry_date: '',
        supplier_id: '',
    });

    const quickSupplierForm = useForm({
        supplier: '',
        company: '',
        phone: '',
    });

    const handleQuickAddSupplier = (e) => {
        e.preventDefault();
        quickSupplierForm.post(route('supermart.suppliers.store'), {
            onSuccess: (page) => {
                // The suppliers prop will be updated automatically by Inertia
                setIsQuickAddingSupplier(false);
                quickSupplierForm.reset();
                
                // Try to find the newly added supplier (likely the last one or by name)
                const newSupplier = page.props.suppliers.find(s => s.supplier === quickSupplierForm.data.supplier);
                if (newSupplier) {
                    setData('supplier_id', String(newSupplier.id));
                }
            },
        });
    };

    const openAdjust = (item) => {
        setAdjustingId(String(item.id));
        setData({
            quantity: item.quantity ?? '',
            price: item.price ?? '',
            expiry_date: item.expiry_date ?? '',
            supplier_id: '',
        });
        setIsAdjusting(true);
    };

    const openAdjustFromHeader = () => {
        if (allProductsItems.length === 0) return;
        openAdjust(allProductsItems[0]);
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
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
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
                                <th className="p-6">Expiry</th>
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
                                            {item.expiry_date ? (
                                                <span className={`text-[10px] font-bold ${new Date(item.expiry_date) < new Date() ? 'text-rose-600 bg-rose-50' : 'text-gray-600 bg-gray-50'} px-2 py-1 rounded`}>
                                                    {new Date(item.expiry_date).toLocaleDateString()}
                                                </span>
                                            ) : (
                                                <span className="text-[10px] text-gray-400 font-medium italic">No date</span>
                                            )}
                                        </td>
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
                        <TablePlaceholder 
                            title="No inventory items"
                            description="Your supermart inventory is currently empty. Adjust stock or add products to see them here."
                            icon="📦"
                        />
                    )}
                </div>
            </div>

            <Pagination links={inventory.links} />

            {isAdjusting && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitAdjust} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300 overflow-y-auto max-h-[90vh]">
                        <div className="flex justify-between items-center mb-6 text-blue-900 px-2">
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

                        <div className="space-y-6 px-2">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Product</label>
                                <select
                                    value={adjustingId}
                                    onChange={(e) => {
                                        const id = e.target.value;
                                        setAdjustingId(id);
                                        const selected = itemsById.get(String(id));
                                        if (selected) {
                                            setData({
                                                ...data,
                                                quantity: selected.quantity ?? '',
                                                price: selected.price ?? '',
                                                expiry_date: selected.expiry_date ?? '',
                                            });
                                        }
                                    }}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium appearance-none"
                                    required
                                >
                                    <option value="" disabled>Select a product to adjust</option>
                                    {allProductsItems.map(item => (
                                        <option key={item.id} value={item.id}>
                                            {item.name} {item.variation_name ? `(${item.variation_name})` : ''}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <div className="flex justify-between items-center mb-2 px-1">
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Supplier (Procurement)</label>
                                    <button 
                                        type="button"
                                        onClick={() => setIsQuickAddingSupplier(true)}
                                        className="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-black transition-colors"
                                    >
                                        + Quick Add
                                    </button>
                                </div>
                                <select
                                    value={data.supplier_id}
                                    onChange={e => setData('supplier_id', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium appearance-none"
                                    required
                                >
                                    <option value="" disabled>Select supplier</option>
                                    {suppliersList.map(s => (
                                        <option key={s.id} value={s.id}>{s.supplier} - {s.company}</option>
                                    ))}
                                </select>
                                {errors.supplier_id && <p className="text-red-500 text-xs mt-1">{errors.supplier_id}</p>}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Expiry Date</label>
                                    <input
                                        type="date"
                                        value={data.expiry_date}
                                        onChange={e => setData('expiry_date', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    />
                                    {errors.expiry_date && <p className="text-red-500 text-xs mt-1">{errors.expiry_date}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">New Quantity</label>
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
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Unit Price (₦)</label>
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
                            {currentItem && (
                                <p className="text-[10px] text-gray-400 font-bold uppercase tracking-widest px-1">Category: {currentItem.category || 'Uncategorized'}</p>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full mt-8 py-5 bg-blue-600 hover:bg-black text-white font-black rounded-3xl shadow-xl shadow-blue-500/20 active:scale-95 transition-all uppercase tracking-widest text-xs"
                        >
                            {processing ? 'Saving...' : 'Update Stock'}
                        </button>
                    </form>
                </div>
            )}

            {/* Quick Add Supplier Modal */}
            {isQuickAddingSupplier && (
                <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={handleQuickAddSupplier} className="bg-white rounded-[40px] p-8 max-w-md w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-blue-900">
                            <h3 className="text-xl font-black uppercase tracking-tighter">Quick Add Vendor</h3>
                            <button
                                type="button"
                                onClick={() => {
                                    setIsQuickAddingSupplier(false);
                                    quickSupplierForm.reset();
                                }}
                                className="text-gray-400 hover:text-gray-900 transition-colors"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Name</label>
                                <input
                                    type="text"
                                    value={quickSupplierForm.data.supplier}
                                    onChange={e => quickSupplierForm.setData('supplier', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Company</label>
                                <input
                                    type="text"
                                    value={quickSupplierForm.data.company}
                                    onChange={e => quickSupplierForm.setData('company', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Phone</label>
                                <input
                                    type="tel"
                                    value={quickSupplierForm.data.phone}
                                    onChange={e => quickSupplierForm.setData('phone', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    required
                                />
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={quickSupplierForm.processing}
                            className="w-full mt-8 py-4 bg-gray-900 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all uppercase tracking-widest text-[10px]"
                        >
                            {quickSupplierForm.processing ? 'Saving...' : 'Create Vendor'}
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
