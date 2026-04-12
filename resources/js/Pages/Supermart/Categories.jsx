import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import ConfirmationModal from '@/Components/ConfirmationModal';

import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import TablePlaceholder from '@/Components/TablePlaceholder';


export default function Categories({ categories = { data: [], links: [] } }) {
    const categoriesData = categories.data || [];
    const [isAdding, setIsAdding] = useState(false);
    const [editingCategory, setEditingCategory] = useState(null);
    const [categoryToDelete, setCategoryToDelete] = useState(null);


    const { data, setData, post, processing, reset, errors } = useForm({
        name: '',
        description: '',
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
        description: '',
    });

    const submitCreate = (e) => {
        e.preventDefault();
        post(route('supermart.categories.store'), {
            onSuccess: () => {
                reset();
                setIsAdding(false);
            },
        });
    };

    const openEdit = (category) => {
        setEditingCategory(category);
        setEditData({
            name: category.name || '',
            description: category.description || '',
        });
    };

    const submitEdit = (e) => {
        e.preventDefault();
        if (!editingCategory) return;
        put(route('supermart.categories.update', editingCategory.id), {
            onSuccess: () => {
                resetEdit();
                setEditingCategory(null);
            },
        });
    };

    const handleDelete = () => {
        if (!categoryToDelete) return;
        router.delete(route('supermart.categories.delete', categoryToDelete.id), {
            onFinish: () => setCategoryToDelete(null)
        });
    };


    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Supermart Categories" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-blue-900">Market Categories</h1>
                    <p className="text-gray-500 font-medium mt-1">Organize your store inventory by grouping products into searchable categories.</p>
                </div>
                <button
                    type="button"
                    onClick={() => setIsAdding(true)}
                    className="bg-blue-600 hover:bg-black text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all"
                >
                    New Category
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                {categoriesData.map((category) => (
                    <div key={category.id} className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8 flex flex-col text-center group hover:border-blue-500/30 transition-all">
                        <div className="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-4 mx-auto group-hover:scale-110 transition-transform">
                            📁
                        </div>
                        <h3 className="text-xl font-black text-gray-900 uppercase tracking-tight">{category.name || 'Misc'}</h3>
                        <p className="text-[10px] font-black text-gray-400 mt-2 uppercase tracking-widest">{category.items_count || 0} items</p>
                        {category.description && (
                            <p className="text-xs text-gray-500 mt-4 line-clamp-3">{category.description}</p>
                        )}
                        <div className="mt-6 flex items-center justify-center gap-2">
                            <button
                                type="button"
                                onClick={() => openEdit(category)}
                                className="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 px-3 py-1.5 rounded-lg"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                onClick={() => setCategoryToDelete(category)}
                                className="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 px-3 py-1.5 rounded-lg"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                ))}
            </div>

            <Pagination links={categories.links} />

            {categoriesData.length === 0 && (
                <TablePlaceholder 
                    title="No categories"
                    description="You haven't created any market categories yet. Organize your productos to make them easier to find."
                    icon="📂"
                />
            )}

            {isAdding && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitCreate} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-blue-900">
                            <h3 className="text-2xl font-black">Create Category</h3>
                            <button type="button" onClick={() => setIsAdding(false)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Category Name</label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="Beverages"
                                    required
                                />
                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                                <textarea
                                    value={data.description}
                                    onChange={e => setData('description', e.target.value)}
                                    rows="4"
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="Soft drinks, juices, and energy drinks"
                                />
                                {errors.description && <p className="text-red-500 text-xs mt-1">{errors.description}</p>}
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-blue-600 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
                        >
                            {processing ? 'Saving...' : 'Save Category'}
                        </button>
                    </form>
                </div>
            )}

            {editingCategory && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitEdit} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-blue-900">
                            <h3 className="text-2xl font-black">Edit Category</h3>
                            <button type="button" onClick={() => setEditingCategory(null)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Category Name</label>
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
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                                <textarea
                                    value={editData.description}
                                    onChange={e => setEditData('description', e.target.value)}
                                    rows="4"
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                />
                                {editErrors.description && <p className="text-red-500 text-xs mt-1">{editErrors.description}</p>}
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={editProcessing}
                            className="w-full mt-8 py-4 bg-blue-600 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
                        >
                            {editProcessing ? 'Updating...' : 'Update Category'}
                        </button>
                    </form>
                </div>
            )}
            <ConfirmationModal 
                show={!!categoryToDelete}
                onClose={() => setCategoryToDelete(null)}
                onConfirm={handleDelete}
                title="Delete Category"
                message={`Are you sure you want to delete category "${categoryToDelete?.name}"? All associated products will be uncategorized.`}
            />
        </div>

    );
}

Categories.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
