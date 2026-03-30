import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import { Head } from '@inertiajs/react';


export default function Suppliers({ suppliers = { data: [], links: [] } }) {
    const suppliersData = suppliers.data || [];
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Supermart Suppliers" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-blue-900">Retail Suppliers</h1>
                    <p className="text-gray-500 font-medium mt-1">Manage vendor relationships for groceries, electronics, and household goods.</p>
                </div>
                <button className="bg-blue-600 hover:bg-black text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">Connect Vendor</button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {suppliersData.map(s => (
                    <div key={s.id} className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-10 group hover:scale-[1.02] transition-all">
                        <div className="flex justify-between items-start mb-8">
                            <div className="w-16 h-16 rounded-[24px] bg-blue-50 text-blue-600 flex items-center justify-center text-3xl">
                                🏙️
                            </div>
                            <span className="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase">Active</span>
                        </div>
                        <h3 className="text-2xl font-black text-gray-900 mb-2">{s.supplier_name || s.name}</h3>
                        <div className="space-y-3 mb-8">
                            <p className="text-sm font-medium text-gray-500 flex items-center gap-2"><span>📱</span> {s.phone || 'No Contact'}</p>
                            <p className="text-sm font-medium text-gray-500 flex items-center gap-2"><span>📍</span> Lagos, Nigeria</p>
                        </div>
                        <button className="w-full py-4 border-2 border-gray-100 hover:border-blue-500 hover:text-blue-500 text-gray-400 font-black rounded-2xl text-[10px] uppercase tracking-widest transition-all">
                            View Supply Chain
                        </button>
                    </div>
                ))}
            </div>

            <Pagination links={suppliers.links} />
        </div>
    );
}

Suppliers.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
