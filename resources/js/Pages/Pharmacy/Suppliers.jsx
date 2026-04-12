import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head } from '@inertiajs/react';

export default function Suppliers({ suppliers }) {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Pharmacy Suppliers" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Medical Suppliers</h1>
                    <p className="text-gray-500 font-medium mt-1">Manage vendor contacts, procurement history, and lead times.</p>
                </div>
                <button className="bg-indigo-600 hover:bg-black text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/20">Add Supplier</button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {suppliers.map(s => (
                    <div key={s.id} className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8 flex flex-col group hover:scale-[1.02] transition-all">
                        <div className="w-14 h-14 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mb-6">
                            🚚
                        </div>
                        <h3 className="text-2xl font-black text-gray-900 mb-1">{s.supplier_name || s.name}</h3>
                        <p className="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Verified Vendor</p>
                        
                        <div className="space-y-4 mb-8">
                             <div className="flex items-center gap-3 text-sm text-gray-600 font-medium">
                                <span className="text-gray-400">📞</span> {s.phone || 'N/A'}
                             </div>
                             <div className="flex items-center gap-3 text-sm text-gray-600 font-medium">
                                <span className="text-gray-400">📧</span> {s.email || 'N/A'}
                             </div>
                        </div>

                        <button className="mt-auto w-full py-3 bg-gray-50 hover:bg-indigo-50 text-indigo-600 font-black rounded-xl text-[10px] uppercase tracking-widest transition-all">
                            Order History
                        </button>
                    </div>
                ))}
                {suppliers.length === 0 && (
                    <div className="col-span-full">
                        <TablePlaceholder 
                            title="No suppliers found"
                            description="You haven't registered any medical suppliers yet. Add your first vendor to start managing procurement."
                            icon="🚚"
                        />
                    </div>
                )}
            </div>
        </div>
    );
}

Suppliers.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
