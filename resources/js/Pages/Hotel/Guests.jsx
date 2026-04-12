import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head, useForm } from '@inertiajs/react';

export default function Guests({ guests }) {
    const [isAdding, setIsAdding] = useState(false);
    const { data, setData, post, processing, reset, errors } = useForm({
        name: '',
        email: '',
        phone: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('hotel.guests.store'), {
            onSuccess: () => {
                reset();
                setIsAdding(false);
            },
        });
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Guest Management" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Hotel Guests</h1>
                    <p className="text-gray-500 font-medium mt-1">Manage guest records and contact information.</p>
                </div>
                <button 
                    onClick={() => setIsAdding(true)}
                    className="bg-indigo-600 hover:bg-black text-white px-6 py-3 rounded-2xl font-bold transition-all shrink-0 shadow-lg shadow-indigo-500/20"
                >
                    + Register Guest
                </button>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Guest Name</th>
                                <th className="p-6">Email Address</th>
                                <th className="p-6">Phone Number</th>
                                <th className="p-6">Joined Date</th>
                                <th className="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {guests.map(g => (
                                <tr key={g.id} className="hover:bg-indigo-50/30 transition-colors group">
                                    <td className="p-6">
                                        <div className="flex items-center gap-3">
                                            <div className="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xs uppercase">
                                                {g.name.split(' ').map(n => n[0]).join('')}
                                            </div>
                                            <p className="font-bold text-gray-900">{g.name}</p>
                                        </div>
                                    </td>
                                    <td className="p-6 text-sm text-gray-600 font-medium">
                                        {g.email || '—'}
                                    </td>
                                    <td className="p-6 text-sm text-gray-600 font-medium">
                                        {g.phone || '—'}
                                    </td>
                                    <td className="p-6 text-sm text-gray-500">
                                        {new Date(g.created_at).toLocaleDateString()}
                                    </td>
                                    <td className="p-6 text-right">
                                        <button className="text-gray-400 hover:text-indigo-600 font-bold text-xs p-2">View History</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {guests.length === 0 && (
                        <TablePlaceholder 
                            title="No guests found"
                            description="Your guest register is currently empty. Register your first guest to start managing bookings and history."
                            icon="👥"
                        />
                    )}
                </div>
            </div>

            {isAdding && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submit} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-indigo-900">
                            <h3 className="text-2xl font-black">Register New Guest</h3>
                            <button type="button" onClick={() => setIsAdding(false)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Full Name</label>
                                <input 
                                    type="text" 
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium transition-all"
                                    placeholder="e.g. Michael Jordan"
                                    required
                                />
                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                                    <input 
                                        type="email" 
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium transition-all"
                                        placeholder="mike@example.com"
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Phone</label>
                                    <input 
                                        type="text" 
                                        value={data.phone}
                                        onChange={e => setData('phone', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium transition-all"
                                        placeholder="+234..."
                                    />
                                </div>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-indigo-600 hover:bg-black text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 active:scale-95 transition-all"
                        >
                            {processing ? 'Registering...' : 'Register Guest'}
                        </button>
                    </form>
                </div>
            )}
        </div>
    );
}

Guests.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
