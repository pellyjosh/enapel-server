import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ConfirmationModal from '@/Components/ConfirmationModal';
import TablePlaceholder from '@/Components/TablePlaceholder';

import { Head, useForm, router } from '@inertiajs/react';
import { format, isPast, isBefore, addDays } from 'date-fns';
import { useState } from 'react';

export default function DrugCatalog({ drugs = [] }) {
    const [searchTerm, setSearchTerm] = useState('');
    const [isAdding, setIsAdding] = useState(false);
    const [editingDrug, setEditingDrug] = useState(null);
    const [drugToDelete, setDrugToDelete] = useState(null);


    const {
        data,
        setData,
        post,
        processing,
        reset,
        errors,
    } = useForm({
        name: '',
        batch_number: '',
        expiry_date: '',
        description: '',
        quantity: '',
        price: '',
        category: '',
    });

    const {
        data: editData,
        setData: setEditData,
        put,
        processing: editProcessing,
        reset: resetEdit,
        errors: editErrors,
    } = useForm({
        name: '',
        batch_number: '',
        expiry_date: '',
        description: '',
        quantity: '',
        price: '',
        category: '',
    });

    const filteredDrugs = drugs.filter((drug) => {
        const name = (drug.name || drug.product_name || '').toLowerCase();
        const batch = (drug.batch_number || '').toLowerCase();
        const query = searchTerm.toLowerCase();

        return name.includes(query) || batch.includes(query);
    });

    const submitCreate = (e) => {
        e.preventDefault();
        post(route('pharmacy.catalog.store'), {
            onSuccess: () => {
                reset();
                setIsAdding(false);
            },
        });
    };

    const openEdit = (drug) => {
        setEditingDrug(drug);
        setEditData({
            name: drug.name || drug.product_name || '',
            batch_number: drug.batch_number || '',
            expiry_date: drug.expiry_date ? drug.expiry_date.slice(0, 10) : '',
            description: drug.description || '',
            quantity: drug.quantity ?? '',
            price: drug.price ?? '',
            category: drug.category || '',
        });
    };

    const submitEdit = (e) => {
        e.preventDefault();
        if (!editingDrug) return;

        put(route('pharmacy.catalog.update', editingDrug.id), {
            onSuccess: () => {
                resetEdit();
                setEditingDrug(null);
            },
        });
    };

    const handleDelete = () => {
        if (!drugToDelete) return;
        router.delete(route('pharmacy.catalog.delete', drugToDelete.id), {
            onFinish: () => setDrugToDelete(null)
        });
    };


    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Drug Catalog & Inventory" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight">
                        Drug Catalog
                    </h1>
                    <p className="text-gray-500 font-medium mt-1">
                        Manage your pharmaceutical inventory, prices, and expiry tracking.
                    </p>
                </div>
                <div className="flex items-center gap-3 w-full md:w-auto">
                    <div className="relative flex-1 md:w-64">
                        <svg className="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input
                            type="text"
                            placeholder="Search drugs or batches..."
                            value={searchTerm}
                            onChange={e => setSearchTerm(e.target.value)}
                            className="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        />
                    </div>
                    <button
                        type="button"
                        onClick={() => setIsAdding(true)}
                        className="bg-gray-900 hover:bg-black text-white px-6 py-2.5 rounded-xl font-bold transition-all shrink-0"
                    >
                        + Add Drug
                    </button>
                </div>
            </div>

            <div className="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-4 pl-6">Drug Name</th>
                                <th className="p-4">Batch / SKU</th>
                                <th className="p-4">Stock Limit</th>
                                <th className="p-4">Expiry Date</th>
                                <th className="p-4 text-right">Selling Price</th>
                                <th className="p-4 pr-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {filteredDrugs.map(drug => {
                                const expiryDate = drug.expiry_date ? new Date(drug.expiry_date) : null;
                                const hasExpiry = expiryDate && !Number.isNaN(expiryDate.getTime());
                                let expiryStatus = 'ok';

                                if (hasExpiry) {
                                    if (isPast(expiryDate)) expiryStatus = 'danger';
                                    else if (isBefore(expiryDate, addDays(new Date(), 60))) expiryStatus = 'warning';
                                }

                                const quantity = Number(drug.quantity || 0);
                                const isLowStock = quantity <= 10;
                                const price = Number(drug.price || 0);

                                return (
                                    <tr key={drug.id} className="hover:bg-blue-50/30 transition-colors group">
                                        <td className="p-4 pl-6">
                                            <div className="flex items-center gap-3">
                                                <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-50 to-blue-100 text-indigo-500 flex items-center justify-center shrink-0 border border-indigo-200">
                                                    💊
                                                </div>
                                                <div>
                                                    <p className="font-bold text-gray-900 leading-tight">{drug.name || drug.product_name || 'Unnamed Drug'}</p>
                                                    <p className="text-xs text-gray-500 truncate max-w-[200px]">{drug.description || 'No description'}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="p-4">
                                            <span className="font-mono text-xs bg-gray-100 px-2 py-1 rounded border border-gray-200 text-gray-600 block w-max">
                                                {drug.batch_number || 'NO-BATCH'}
                                            </span>
                                        </td>
                                        <td className="p-4">
                                            <div className="flex items-center gap-2">
                                                <div className="flex-1 w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                    <div
                                                        className={`h-full rounded-full ${isLowStock ? 'bg-red-500' : 'bg-emerald-500'}`}
                                                        style={{ width: `${Math.min(100, (quantity / 50) * 100)}%` }}
                                                    ></div>
                                                </div>
                                                <span className={`text-sm font-bold ${isLowStock ? 'text-red-500' : 'text-gray-900'}`}>{quantity}</span>
                                            </div>
                                        </td>
                                        <td className="p-4">
                                            {hasExpiry ? (
                                                <span className={`px-2.5 py-1 text-xs font-bold rounded-lg border flex items-center gap-1.5 w-max ${
                                                    expiryStatus === 'danger' ? 'bg-red-50 text-red-600 border-red-200' :
                                                    expiryStatus === 'warning' ? 'bg-orange-50 text-orange-600 border-orange-200' :
                                                    'bg-emerald-50 text-emerald-600 border-emerald-200'
                                                }`}>
                                                    <span className={`w-1.5 h-1.5 rounded-full ${expiryStatus === 'danger' ? 'bg-red-500 animate-pulse' : expiryStatus === 'warning' ? 'bg-orange-500 animate-pulse' : 'bg-emerald-500'}`}></span>
                                                    {format(expiryDate, 'MMM d, yyyy')}
                                                </span>
                                            ) : (
                                                <span className="text-gray-400 text-sm font-medium">Not tracked</span>
                                            )}
                                        </td>
                                        <td className="p-4 text-right">
                                            <span className="font-black text-gray-900">₦{price.toLocaleString()}</span>
                                        </td>
                                        <td className="p-4 pr-6 text-right">
                                            <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                                <button
                                                    type="button"
                                                    onClick={() => openEdit(drug)}
                                                    className="text-blue-600 hover:text-blue-800 font-bold text-sm bg-blue-50 px-3 py-1.5 rounded-lg"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => setDrugToDelete(drug)}
                                                    className="text-rose-600 hover:text-rose-800 font-bold text-sm bg-rose-50 px-3 py-1.5 rounded-lg"
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

                    {filteredDrugs.length === 0 && (
                        <TablePlaceholder 
                            title={searchTerm ? "No drugs found" : "Catalog is empty"}
                            description={searchTerm 
                                ? `We couldn't find any medications matching "${searchTerm}". Try a different search term.` 
                                : "Your drug catalog is currently empty. Add your first medication to start tracking inventory."}
                            icon="💊"
                        />
                    )}
                </div>
            </div>

            {isAdding && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitCreate} className="bg-white rounded-[40px] p-8 max-w-2xl w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-gray-900">
                            <h3 className="text-2xl font-black">Add New Drug</h3>
                            <button type="button" onClick={() => setIsAdding(false)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Drug Name</label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="Paracetamol 500mg"
                                    required
                                />
                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Batch Number</label>
                                    <input
                                        type="text"
                                        value={data.batch_number}
                                        onChange={e => setData('batch_number', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        placeholder="BCH-4021"
                                    />
                                    {errors.batch_number && <p className="text-red-500 text-xs mt-1">{errors.batch_number}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Expiry Date</label>
                                    <input
                                        type="date"
                                        value={data.expiry_date}
                                        onChange={e => setData('expiry_date', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    />
                                    {errors.expiry_date && <p className="text-red-500 text-xs mt-1">{errors.expiry_date}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quantity</label>
                                    <input
                                        type="number"
                                        min="0"
                                        value={data.quantity}
                                        onChange={e => setData('quantity', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        placeholder="200"
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
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        placeholder="1500"
                                        required
                                    />
                                    {errors.price && <p className="text-red-500 text-xs mt-1">{errors.price}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Drug Category (optional)</label>
                                    <input
                                        type="text"
                                        value={data.category}
                                        onChange={e => setData('category', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        placeholder="Analgesics"
                                    />
                                    {errors.category && <p className="text-red-500 text-xs mt-1">{errors.category}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                                    <input
                                        type="text"
                                        value={data.description}
                                        onChange={e => setData('description', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        placeholder="Pain relief tablets"
                                    />
                                    {errors.description && <p className="text-red-500 text-xs mt-1">{errors.description}</p>}
                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-gray-900 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
                        >
                            {processing ? 'Saving...' : 'Add Drug'}
                        </button>
                    </form>
                </div>
            )}

            {editingDrug && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitEdit} className="bg-white rounded-[40px] p-8 max-w-2xl w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-gray-900">
                            <h3 className="text-2xl font-black">Edit Drug</h3>
                            <button type="button" onClick={() => setEditingDrug(null)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Drug Name</label>
                                <input
                                    type="text"
                                    value={editData.name}
                                    onChange={e => setEditData('name', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    required
                                />
                                {editErrors.name && <p className="text-red-500 text-xs mt-1">{editErrors.name}</p>}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Batch Number</label>
                                    <input
                                        type="text"
                                        value={editData.batch_number}
                                        onChange={e => setEditData('batch_number', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    />
                                    {editErrors.batch_number && <p className="text-red-500 text-xs mt-1">{editErrors.batch_number}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Expiry Date</label>
                                    <input
                                        type="date"
                                        value={editData.expiry_date}
                                        onChange={e => setEditData('expiry_date', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    />
                                    {editErrors.expiry_date && <p className="text-red-500 text-xs mt-1">{editErrors.expiry_date}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quantity</label>
                                    <input
                                        type="number"
                                        min="0"
                                        value={editData.quantity}
                                        onChange={e => setEditData('quantity', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        required
                                    />
                                    {editErrors.quantity && <p className="text-red-500 text-xs mt-1">{editErrors.quantity}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Selling Price (₦)</label>
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={editData.price}
                                        onChange={e => setEditData('price', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        required
                                    />
                                    {editErrors.price && <p className="text-red-500 text-xs mt-1">{editErrors.price}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Drug Category (optional)</label>
                                    <input
                                        type="text"
                                        value={editData.category}
                                        onChange={e => setEditData('category', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    />
                                    {editErrors.category && <p className="text-red-500 text-xs mt-1">{editErrors.category}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                                    <input
                                        type="text"
                                        value={editData.description}
                                        onChange={e => setEditData('description', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    />
                                    {editErrors.description && <p className="text-red-500 text-xs mt-1">{editErrors.description}</p>}
                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={editProcessing}
                            className="w-full mt-8 py-4 bg-blue-600 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
                        >
                            {editProcessing ? 'Updating...' : 'Update Drug'}
                        </button>
                    </form>
                </div>
            )}
            <ConfirmationModal 
                show={!!drugToDelete}
                onClose={() => setDrugToDelete(null)}
                onConfirm={handleDelete}
                title="Delete Drug"
                message={`Are you sure you want to delete ${drugToDelete?.name || drugToDelete?.product_name}? This action cannot be undone.`}
            />
        </div>
    );

}

DrugCatalog.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
