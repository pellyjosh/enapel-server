import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head, useForm } from '@inertiajs/react';

export default function Rooms({ rooms, categories }) {
    const [isAdding, setIsAdding] = useState(false);
    const { data, setData, post, processing, reset, errors } = useForm({
        name: '',
        category_id: categories[0]?.id || '',
        price: '',
        status: 'available',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('hotel.rooms.store'), {
            onSuccess: () => {
                reset();
                setIsAdding(false);
            },
        });
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Room Management" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Hotel Rooms</h1>
                    <p className="text-gray-500 font-medium mt-1">Configure room categories, pricing, and availability.</p>
                </div>
                <button 
                    onClick={() => setIsAdding(true)}
                    className="bg-indigo-600 hover:bg-black text-white px-6 py-3 rounded-2xl font-bold transition-all shrink-0 shadow-lg shadow-indigo-500/20"
                >
                    + Add New Room
                </button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {rooms.map(room => (
                    <div key={room.id} className="bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/40 p-6 flex flex-col group hover:scale-[1.02] transition-all">
                        <div className="flex justify-between items-start mb-6">
                            <div className="w-14 h-14 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl">
                                🏨
                            </div>
                            <span className={`px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${
                                room.status === 'available' ? 'bg-emerald-50 text-emerald-600' : 
                                room.status === 'occupied' ? 'bg-rose-50 text-rose-600' : 'bg-orange-50 text-orange-600'
                            }`}>
                                {room.status}
                            </span>
                        </div>
                        <h3 className="text-xl font-black text-gray-900 mb-1">Room {room.name}</h3>
                        <p className="text-sm font-bold text-gray-400 mb-4">{room.category?.name || 'Standard'}</p>
                        
                        <div className="mt-auto pt-6 border-t border-gray-50 flex items-center justify-between">
                            <span className="text-2xl font-black text-indigo-900">₦{Number(room.price).toLocaleString()}</span>
                            <span className="text-xs text-gray-400 font-bold">per night</span>
                        </div>
                    </div>
                ))}
                {rooms.length === 0 && (
                    <div className="col-span-full">
                        <TablePlaceholder 
                            title="No rooms configured"
                            description="You haven't added any hotel rooms yet. Create your rooms to start accepting bookings."
                            icon="🏨"
                        />
                    </div>
                )}
            </div>

            {isAdding && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submit} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-indigo-900">
                            <h3 className="text-2xl font-black">Configure New Room</h3>
                            <button type="button" onClick={() => setIsAdding(false)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <div className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Room Number/Name</label>
                                    <input 
                                        type="text" 
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        placeholder="101"
                                        required
                                    />
                                    {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Category</label>
                                    <select 
                                        value={data.category_id}
                                        onChange={e => setData('category_id', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium appearance-none"
                                        required
                                    >
                                        {categories.map(c => (
                                            <option key={c.id} value={c.id}>{c.name}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Price per Night (₦)</label>
                                <input 
                                    type="number" 
                                    value={data.price}
                                    onChange={e => setData('price', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                    placeholder="25000"
                                />
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Initial Status</label>
                                <select 
                                    value={data.status}
                                    onChange={e => setData('status', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium appearance-none"
                                >
                                    <option value="available">Available</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-indigo-600 hover:bg-black text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 active:scale-95 transition-all"
                        >
                            {processing ? 'Configuring...' : 'Add Room'}
                        </button>
                    </form>
                </div>
            )}
        </div>
    );
}

Rooms.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
