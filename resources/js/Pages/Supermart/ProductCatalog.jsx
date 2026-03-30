import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import ConfirmationModal from '@/Components/ConfirmationModal';

import { Head, useForm, router } from '@inertiajs/react';
import { useState, useMemo } from 'react';


export default function ProductCatalog({ products = { data: [], links: [] }, categories = [] }) {
    const productsData = products.data || [];
    const [searchTerm, setSearchTerm] = useState('');
    const [isAdding, setIsAdding] = useState(false);
    const [editingProduct, setEditingProduct] = useState(null);
    const [productToDelete, setProductToDelete] = useState(null);


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
            onFinish: () => setProductToDelete(null)
        });
    };


    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Products Catalog" />

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
                                <th className="p-4">Category</th>
                                <th className="p-4">Aisle Quantity</th>
                                <th className="p-4 text-right">Retail Price</th>
                                <th className="p-4 pr-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {filteredProducts.map(product => {
                                const quantity = Number(product.quantity || 0);
                                const isLowStock = quantity <= 15;

                                return (
                                    <tr key={product.id} className="hover:bg-blue-50/30 transition-colors group">
                                        <td className="p-4 pl-6">
                                            <div className="flex items-center gap-3">
                                                <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-50 to-amber-100 text-orange-500 flex items-center justify-center shrink-0 border border-orange-200">
                                                    🥫
                                                </div>
                                                <div>
                                                    <div className="flex items-center gap-2">
                                                        <p className="font-bold text-gray-900 leading-tight">{product.name}</p>
                                                        {product.sku && (
                                                            <span className="text-[9px] bg-gray-900 text-white px-1.5 py-0.5 rounded font-mono uppercase tracking-tighter">
                                                                {product.sku}
                                                            </span>
                                                        )}
                                                    </div>
                                                    <p className="text-xs text-gray-500 truncate max-w-[200px]">{product.description || 'General Merchandise'}</p>
                                                </div>
                                            </div>
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
                                            <span className="font-black text-gray-900">₦{Number(product.price || 0).toLocaleString()}</span>
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
                                                <button
                                                    type="button"
                                                    onClick={() => setProductToDelete(product)}
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

                    {filteredProducts.length === 0 && (
                        <div className="p-12 text-center text-gray-500 font-medium">
                            No retail items found matching your criteria.
                        </div>
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
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
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
                                    {editErrors.price && <p className="text-red-500 text-xs mt-1">{editErrors.price}</p>}
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
