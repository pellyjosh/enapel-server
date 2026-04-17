import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import ConfirmationModal from '@/Components/ConfirmationModal';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';


export default function Suppliers({ suppliers = { data: [], links: [] } }) {
    const suppliersData = suppliers.data || [];
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingSupplier, setEditingSupplier] = useState(null);
    const [supplierToDelete, setSupplierToDelete] = useState(null);

    const { data, setData, post, put, processing, reset, errors } = useForm({
        supplier: '',
        company: '',
        phone: '',
        email: '',
        address: '',
    });

    const openCreateModal = () => {
        reset();
        setEditingSupplier(null);
        setIsModalOpen(true);
    };

    const openEditModal = (s) => {
        setEditingSupplier(s);
        setData({
            supplier: s.supplier || '',
            company: s.company || '',
            phone: s.phone || '',
            email: s.email || '',
            address: s.address || '',
        });
        setIsModalOpen(true);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editingSupplier) {
            put(route('supermart.suppliers.update', editingSupplier.id), {
                onSuccess: () => {
                    setIsModalOpen(false);
                    reset();
                },
            });
        } else {
            post(route('supermart.suppliers.store'), {
                onSuccess: () => {
                    setIsModalOpen(false);
                    reset();
                },
            });
        }
    };

    const handleDelete = () => {
        if (!supplierToDelete) return;
        router.delete(route('supermart.suppliers.delete', supplierToDelete.id), {
            onFinish: () => setSupplierToDelete(null)
        });
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Supermart Suppliers" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-blue-900">Retail Suppliers</h1>
                    <p className="text-gray-500 font-medium mt-1">Manage vendor relationships for groceries, electronics, and household goods.</p>
                </div>
                <button 
                    onClick={openCreateModal}
                    className="bg-blue-600 hover:bg-black text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20"
                >
                    Connect Vendor
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {suppliersData.map(s => (
                    <div key={s.id} className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-10 group hover:scale-[1.02] transition-all relative overflow-hidden">
                        <div className="flex justify-between items-start mb-8">
                            <div className="w-16 h-16 rounded-[24px] bg-blue-50 text-blue-600 flex items-center justify-center text-3xl">
                                🏙️
                            </div>
                            <div className="flex gap-2">
                                <button 
                                    onClick={() => openEditModal(s)}
                                    className="p-2 text-gray-400 hover:text-blue-600 transition-colors"
                                >
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button 
                                    onClick={() => setSupplierToDelete(s)}
                                    className="p-2 text-gray-400 hover:text-rose-600 transition-colors"
                                >
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </div>
                        </div>
                        <h3 className="text-2xl font-black text-gray-900 mb-2">{s.supplier || s.name}</h3>
                        <p className="text-xs font-black uppercase text-blue-500 mb-4 tracking-widest">{s.company}</p>
                        <div className="space-y-3 mb-8 min-h-[100px]">
                            <p className="text-sm font-medium text-gray-500 flex items-center gap-3 truncate">
                                <span className="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-xs shadow-inner">📱</span> 
                                <span className="font-bold">{s.phone || 'No Contact'}</span>
                            </p>
                            <p className="text-sm font-medium text-gray-500 flex items-center gap-3 truncate">
                                <span className="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-xs shadow-inner">✉️</span> 
                                <span className="italic">{s.email || 'No email'}</span>
                            </p>
                            <p className="text-sm font-medium text-gray-500 flex items-start gap-3">
                                <span className="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-xs shadow-inner shrink-0">📍</span> 
                                <span className="line-clamp-2 leading-relaxed italic">{s.address || 'Address not provided'}</span>
                            </p>
                        </div>
                        <button className="w-full py-4 border-2 border-gray-100 hover:border-blue-500 hover:text-blue-500 text-gray-400 font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all">
                            View Supply Chain
                        </button>
                    </div>
                ))}
            </div>

            {suppliersData.length === 0 && (
                <TablePlaceholder 
                    title="No vendors connected"
                    description="You haven't added any suppliers to the supermart yet. Connect your first vendor to start managing procurement."
                    icon="🏙️"
                />
            )}

            <Pagination links={suppliers.links} />

            {/* Create/Edit Modal */}
            {isModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={handleSubmit} className="bg-white rounded-[40px] p-10 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-8">
                            <h3 className="text-2xl font-black text-blue-900">
                                {editingSupplier ? 'Update Vendor' : 'Connect New Vendor'}
                            </h3>
                            <button
                                type="button"
                                onClick={() => setIsModalOpen(false)}
                                className="text-gray-400 hover:text-gray-900 transition-colors"
                            >
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-6">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Supplier Name</label>
                                <input
                                    type="text"
                                    value={data.supplier}
                                    onChange={e => setData('supplier', e.target.value)}
                                    className="w-full px-6 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="e.g. John Doe"
                                    required
                                />
                                {errors.supplier && <p className="text-red-500 text-xs mt-1">{errors.supplier}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Company Name</label>
                                <input
                                    type="text"
                                    value={data.company}
                                    onChange={e => setData('company', e.target.value)}
                                    className="w-full px-6 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="e.g. Aliko Groceries Ltd"
                                    required
                                />
                                {errors.company && <p className="text-red-500 text-xs mt-1">{errors.company}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Phone Number</label>
                                <input
                                    type="tel"
                                    value={data.phone}
                                    onChange={e => setData('phone', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium"
                                    placeholder="e.g. 08012345678"
                                    required
                                />
                                {errors.phone && <p className="text-red-500 text-xs mt-1">{errors.phone}</p>}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Email Address</label>
                                    <input
                                        type="email"
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium text-xs"
                                        placeholder="vendor@email.com"
                                    />
                                    {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email}</p>}
                                </div>
                                <div className="md:col-span-1">
                                    {/* Placeholder for future field */}
                                </div>
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Office Address</label>
                                <textarea
                                    value={data.address}
                                    onChange={e => setData('address', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium text-sm min-h-[100px]"
                                    placeholder="Enter full physical address..."
                                />
                                {errors.address && <p className="text-red-500 text-xs mt-1">{errors.address}</p>}
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full mt-10 py-5 bg-blue-600 hover:bg-black text-white font-black rounded-3xl shadow-xl shadow-blue-500/20 active:scale-95 transition-all uppercase tracking-widest text-xs"
                        >
                            {processing ? 'Saving...' : editingSupplier ? 'Update Vendor' : 'Connect Vendor'}
                        </button>
                    </form>
                </div>
            )}

            <ConfirmationModal 
                show={!!supplierToDelete}
                onClose={() => setSupplierToDelete(null)}
                onConfirm={handleDelete}
                title="Disconnect Vendor"
                message={`Are you sure you want to disconnect "${supplierToDelete?.supplier || supplierToDelete?.name}"? This will remove them from your active vendors list.`}
            />
        </div>
    );
}

Suppliers.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
