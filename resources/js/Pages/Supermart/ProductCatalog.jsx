import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import ConfirmationModal from '@/Components/ConfirmationModal';

import { Head, useForm, router } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import TablePlaceholder from '@/Components/TablePlaceholder';


export default function ProductCatalog({ products = { data: [], links: [] }, categories = [], all_products = [] }) {
    const productsData = products.data || [];
    const [searchTerm, setSearchTerm] = useState('');
    const [isAdding, setIsAdding] = useState(false);
    const [editingProduct, setEditingProduct] = useState(null);
    const [productToDelete, setProductToDelete] = useState(null);
    const [deleteError, setDeleteError] = useState(null);


    const categoryOptions = useMemo(
        () => (categories || []).map(c => c.name).filter(Boolean),
        [categories]
    );

    const {
        data,
        setData,
        post,
        processing,
        reset,
        errors,
    } = useForm({
        name: '',
        sku: '',
        category: categoryOptions[0] || '',
        description: '',
        quantity: '',
        price: '',
        cost_price: '',
        unit_name: 'Piece',
        units_per_pack: 1,
        pack_price_override: '',
        packs_per_carton: 1,
        carton_price_override: '',
        parent_id: '',
        variation_name: '',
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
        sku: '',
        category: '',
        description: '',
        quantity: '',
        price: '',
        cost_price: '',
        unit_name: '',
        units_per_pack: '',
        pack_price_override: '',
        packs_per_carton: '',
        carton_price_override: '',
        parent_id: '',
        variation_name: '',
    });

    const filteredProducts = productsData.filter(p =>
        (p.name?.toLowerCase() || '').includes(searchTerm.toLowerCase()) ||
        (p.category?.toLowerCase() || '').includes(searchTerm.toLowerCase())
    );

    const submitCreate = (e) => {
        e.preventDefault();
        post(route('supermart.catalog.store'), {
            onSuccess: () => {
                reset();
                setIsAdding(false);
            },
        });
    };

    const openEdit = (product) => {
        setEditingProduct(product);
        setEditData({
            name: product.name || '',
            sku: product.sku || '',
            category: product.category || '',
            description: product.description || '',
            quantity: product.quantity ?? '',
            price: product.price ?? '',
            cost_price: product.cost_price ?? '',
            unit_name: product.unit_name || 'Piece',
            units_per_pack: product.units_per_pack || 1,
            pack_price_override: product.pack_price_override ?? '',
            packs_per_carton: product.packs_per_carton || 1,
            carton_price_override: product.carton_price_override ?? '',
            parent_id: product.parent_id ?? '',
            variation_name: product.variation_name || '',
        });
    };

    const submitEdit = (e) => {
        e.preventDefault();
        if (!editingProduct) return;

        put(route('supermart.catalog.update', editingProduct.id), {
            onSuccess: () => {
                resetEdit();
                setEditingProduct(null);
            },
        });
    };

    const handleDelete = () => {
        if (!productToDelete) return;
        router.delete(route('supermart.catalog.delete', productToDelete.id), {
            onSuccess: () => {
                setProductToDelete(null);
                setDeleteError(null);
            },
            onError: (errors) => {
                setProductToDelete(null);
                setDeleteError(errors.delete || 'Could not delete the product.');
            },
        });
    };

    const hasInventory = (product) => {
        if (Number(product.quantity || 0) > 0) return true;
        return (product.variations || []).some(v => Number(v.quantity || 0) > 0);
    };


    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Products Catalog" />

            {deleteError && (
                <div className="flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl px-5 py-4 shadow-sm">
                    <svg className="w-5 h-5 mt-0.5 shrink-0 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <p className="text-sm font-semibold flex-1">{deleteError}</p>
                    <button onClick={() => setDeleteError(null)} className="text-rose-400 hover:text-rose-700 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            )}

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight">
                        Products Catalog
                    </h1>
                    <p className="text-gray-500 font-medium mt-1">
                        High-volume FMCG inventory tracking and pricing rules.
                    </p>
                </div>
                <div className="flex items-center gap-3 w-full md:w-auto">
                    <div className="relative flex-1 md:w-64">
                        <svg className="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input 
                            type="text" 
                            placeholder="Search grocery..." 
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
                        + Add Stock
                    </button>
                </div>
            </div>

            <div className="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-4 pl-6">Product / Brand</th>
                                <th className="p-4 font-black">SKU</th>
                                <th className="p-4 text-center">Unit</th>
                                <th className="p-4">Category</th>
                                <th className="p-4">Stock Level</th>
                                <th className="p-4 text-right">Cost</th>
                                <th className="p-4 text-right">Retail</th>
                                <th className="p-4 text-center">Margin</th>
                                <th className="p-4 pr-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {filteredProducts.map(product => {
                                const quantity = Number(product.quantity || 0);
                                const isLowStock = quantity <= 15;
                                const margin = product.price > 0 
                                    ? ((product.price - product.cost_price) / product.price) * 100 
                                    : 0;

                                return (
                                    <React.Fragment key={product.id}>
                                        <tr className="hover:bg-blue-50/30 transition-colors group">
                                            <td className="p-4 pl-6">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 text-blue-500 flex items-center justify-center shrink-0 border border-blue-200">
                                                        🥫
                                                    </div>
                                                    <div>
                                                        <div className="flex items-center gap-2">
                                                            <p className="font-bold text-gray-900 leading-tight">{product.name}</p>
                                                        </div>
                                                        <p className="text-xs text-gray-500 truncate max-w-[200px]">{product.description || 'General Merchandise'}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="p-4 font-mono text-[10px] font-bold text-gray-400">
                                                {product.sku || 'N/A'}
                                            </td>
                                            <td className="p-4 text-center">
                                                <span className="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded uppercase tracking-tighter">
                                                    {product.unit_name}
                                                    {product.units_per_pack > 1 && ` (x${product.units_per_pack})`}
                                                    {product.packs_per_carton > 1 && ` [📦 x${product.packs_per_carton}]`}
                                                </span>
                                            </td>
                                            <td className="p-4">
                                                <span className="font-mono text-xs bg-gray-100 px-2 py-1 rounded border border-gray-200 text-gray-600 block w-max uppercase">
                                                    {product.category || 'Retail'}
                                                </span>
                                            </td>
                                            <td className="p-4">
                                                <div className="flex items-center gap-2">
                                                    <div className="flex-1 w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                        <div 
                                                            className={`h-full rounded-full ${isLowStock ? 'bg-orange-500' : 'bg-emerald-500'}`}
                                                            style={{ width: `${Math.min(100, (quantity / 100) * 100)}%` }}
                                                        ></div>
                                                    </div>
                                                    <span className={`text-sm font-bold ${isLowStock ? 'text-orange-500' : 'text-gray-900'}`}>{quantity}</span>
                                                </div>
                                            </td>
                                            <td className="p-4 text-right">
                                                <span className="text-xs text-gray-400 font-medium">₦{Number(product.cost_price || 0).toLocaleString()}</span>
                                            </td>
                                            <td className="p-4 text-right">
                                                <span className="font-black text-gray-900">₦{Number(product.price || 0).toLocaleString()}</span>
                                            </td>
                                            <td className="p-4 text-center">
                                                <span className={`text-[10px] font-black px-2 py-1 rounded-full ${margin > 20 ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700'}`}>
                                                    {margin.toFixed(0)}%
                                                </span>
                                            </td>
                                            <td className="p-4 pr-6 text-right">
                                                <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                                    <button
                                                        type="button"
                                                        onClick={() => openEdit(product)}
                                                        className="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 px-3 py-1.5 rounded-lg"
                                                    >
                                                        Edit
                                                    </button>
                                                    {hasInventory(product) ? (
                                                        <span
                                                            title={`Cannot delete — product still has ${Number(product.quantity || 0)} unit(s) in inventory. Reduce stock to zero first.`}
                                                            className="text-gray-400 font-bold text-xs bg-gray-100 px-3 py-1.5 rounded-lg cursor-not-allowed select-none"
                                                        >
                                                            Delete
                                                        </span>
                                                    ) : (
                                                        <button
                                                            type="button"
                                                            onClick={() => setProductToDelete(product)}
                                                            className="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 px-3 py-1.5 rounded-lg"
                                                        >
                                                            Delete
                                                        </button>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                        {/* Render Variations */}
                                        {(product.variations || []).map(variation => (
                                            <tr key={variation.id} className="bg-gray-50/30 hover:bg-blue-50/50 transition-colors group">
                                                <td className="p-3 pl-12 border-l-4 border-blue-100">
                                                    <div className="flex items-center gap-2">
                                                        <div className="w-6 h-6 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-[10px]">
                                                            ↳
                                                        </div>
                                                        <div>
                                                            <p className="font-bold text-sm text-gray-700">{variation.variation_name || variation.name}</p>
                                                            {variation.sku && <span className="text-[8px] text-gray-400 font-mono">{variation.sku}</span>}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="p-3 text-center">
                                                    <span className="text-[9px] font-bold text-gray-500">{variation.unit_name}</span>
                                                </td>
                                                <td className="p-3"></td>
                                                <td className="p-3">
                                                    <div className="flex items-center gap-2">
                                                        <span className={`text-[11px] font-bold ${variation.quantity <= 10 ? 'text-orange-500' : 'text-gray-500'}`}>
                                                            {variation.quantity}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="p-3 text-right">
                                                    <span className="text-[10px] text-gray-400 font-medium">₦{Number(variation.cost_price || 0).toLocaleString()}</span>
                                                </td>
                                                <td className="p-3 text-right">
                                                    <span className="font-black text-sm text-gray-800">₦{Number(variation.price || 0).toLocaleString()}</span>
                                                </td>
                                                <td className="p-3"></td>
                                                <td className="p-3 pr-6 text-right">
                                                    <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                                        <button
                                                            type="button"
                                                            onClick={() => openEdit(variation)}
                                                            className="text-blue-600 hover:text-blue-800 font-bold text-[10px] bg-white border border-blue-100 px-2 py-1 rounded-lg"
                                                        >
                                                            Edit
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </React.Fragment>
                                );
                            })}
                        </tbody>
                    </table>

                    {filteredProducts.length === 0 && (
                        <TablePlaceholder 
                            title={searchTerm ? "No matches found" : "Catalog is empty"}
                            description={searchTerm 
                                ? `We couldn't find any products matching "${searchTerm}". Try a different search term.` 
                                : "Your product catalog is currently empty. Add your first retail product to get started."}
                            icon="🥫"
                        />
                    )}
                </div>
            </div>

            <Pagination links={products.links} />

            {isAdding && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitCreate} className="bg-white rounded-[40px] p-8 max-w-2xl w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-gray-900">
                            <h3 className="text-2xl font-black">Add New Product</h3>
                            <button type="button" onClick={() => setIsAdding(false)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Product Name</label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="Golden Morn 500g"
                                    required
                                />
                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Barcode / SKU (Optional)</label>
                                <input
                                    type="text"
                                    value={data.sku}
                                    onChange={e => setData('sku', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="Scanned code or manual ID"
                                />
                                {errors.sku && <p className="text-red-500 text-xs mt-1">{errors.sku}</p>}
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Category</label>
                                    <select
                                        value={data.category}
                                        onChange={e => setData('category', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        required
                                    >
                                        <option value="">Select Category</option>
                                        {categories.map(cat => (
                                            <option key={cat.id} value={cat.name}>{cat.name}</option>
                                        ))}
                                    </select>
                                    {errors.category && <p className="text-red-500 text-xs mt-1">{errors.category}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quantity</label>
                                    <input
                                        type="number"
                                        min="0"
                                        value={data.quantity}
                                        onChange={e => setData('quantity', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        placeholder="120"
                                        required
                                    />
                                    {errors.quantity && <p className="text-red-500 text-xs mt-1">{errors.quantity}</p>}
                                </div>
                            </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Retail Price (₦)</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value={data.price}
                                    onChange={e => setData('price', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="2000"
                                    required
                                />
                                {errors.price && <p className="text-red-500 text-xs mt-1">{errors.price}</p>}
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Cost Price (₦)</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value={data.cost_price}
                                    onChange={e => setData('cost_price', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="1500"
                                />
                                {errors.cost_price && <p className="text-red-500 text-xs mt-1">{errors.cost_price}</p>}
                            </div>
                        </div>

                                </div>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-50 pt-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Packs / Carton</label>
                                <input
                                    type="number"
                                    min="1"
                                    value={data.packs_per_carton}
                                    onChange={e => setData('packs_per_carton', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                />
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Carton Price (Optional)</label>
                                <input
                                    type="number"
                                    min="0"
                                    value={data.carton_price_override}
                                    onChange={e => setData('carton_price_override', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="Bulk rate for carton"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-50 pt-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Is this a variation of?</label>
                                <select
                                    value={data.parent_id}
                                    onChange={e => setData('parent_id', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium text-xs"
                                >
                                    <option value="">(None - Main Product)</option>
                                    {all_products.map(p => (
                                        <option key={p.id} value={p.id}>{p.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Variation Label</label>
                                <input
                                    type="text"
                                    value={data.variation_name}
                                    onChange={e => setData('variation_name', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="e.g. Vanilla, 500g, Large"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Internal Description</label>
                                <input
                                    type="text"
                                    value={data.description}
                                    onChange={e => setData('description', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="Family breakfast cereal"
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
                            {processing ? 'Saving...' : 'Add Product'}
                        </button>
                    </form>
                </div>
            )}

            {editingProduct && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitEdit} className="bg-white rounded-[40px] p-8 max-w-2xl w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-gray-900">
                            <h3 className="text-2xl font-black">Edit Product</h3>
                            <button type="button" onClick={() => setEditingProduct(null)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Product Name</label>
                                <input
                                    type="text"
                                    value={editData.name}
                                    onChange={e => setEditData('name', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    required
                                />
                                {editErrors.name && <p className="text-red-500 text-xs mt-1">{editErrors.name}</p>}
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Barcode / SKU (Optional)</label>
                                <input
                                    type="text"
                                    value={editData.sku}
                                    onChange={e => setEditData('sku', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                />
                                {editErrors.sku && <p className="text-red-500 text-xs mt-1">{editErrors.sku}</p>}
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Category</label>
                                    <select
                                        value={editData.category}
                                        onChange={e => setEditData('category', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                        required
                                    >
                                        <option value="">Select Category</option>
                                        {categories.map(cat => (
                                            <option key={cat.id} value={cat.name}>{cat.name}</option>
                                        ))}
                                    </select>
                                    {editErrors.category && <p className="text-red-500 text-xs mt-1">{editErrors.category}</p>}
                                </div>
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
                            </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Retail Price (₦)</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value={editData.price}
                                    onChange={e => setEditData('price', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Cost Price (₦)</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value={editData.cost_price}
                                    onChange={e => setEditData('cost_price', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                />
                            </div>
                        </div>

                                </div>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-50 pt-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Packs / Carton</label>
                                <input
                                    type="number"
                                    min="1"
                                    value={editData.packs_per_carton}
                                    onChange={e => setEditData('packs_per_carton', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                />
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Carton Price (Optional)</label>
                                <input
                                    type="number"
                                    min="0"
                                    value={editData.carton_price_override}
                                    onChange={e => setEditData('carton_price_override', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-50 pt-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Is this a variation of?</label>
                                <select
                                    value={editData.parent_id}
                                    onChange={e => setEditData('parent_id', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium text-xs"
                                >
                                    <option value="">(None - Main Product)</option>
                                    {all_products.map(p => (
                                        <option key={p.id} value={p.id}>{p.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Variation Label</label>
                                <input
                                    type="text"
                                    value={editData.variation_name}
                                    onChange={e => setEditData('variation_name', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                                <input
                                    type="text"
                                    value={editData.description}
                                    onChange={e => setEditData('description', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                />
                            </div>
                        </div>
                        </div>

                        <button
                            type="submit"
                            disabled={editProcessing}
                            className="w-full mt-8 py-4 bg-blue-600 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
                        >
                            {editProcessing ? 'Updating...' : 'Update Product'}
                        </button>
                    </form>
                </div>
            )}
            <ConfirmationModal 
                show={!!productToDelete}
                onClose={() => setProductToDelete(null)}
                onConfirm={handleDelete}
                title="Delete Product"
                message={`Are you sure you want to delete "${productToDelete?.name}"? This action cannot be undone.`}
            />
        </div>
    );

}

ProductCatalog.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
