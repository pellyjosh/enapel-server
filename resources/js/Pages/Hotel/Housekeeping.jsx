import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head, router } from '@inertiajs/react';

export default function Housekeeping({ rooms }) {
    const toggleClean = (id, currentStatus) => {
        router.post(route('hotel.housekeeping.update', id), {
            is_clean: !currentStatus,
            status: 'available'
        });
    };

    const setStatus = (id, newStatus) => {
        router.post(route('hotel.housekeeping.update', id), {
            is_clean: true,
            status: newStatus
        });
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Housekeeping Management" />

            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Housekeeping</h1>
                <p className="text-gray-500 font-medium mt-1">Track room cleanliness and maintenance status.</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {rooms.map(room => (
                    <div key={room.id} className="bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/40 p-8">
                        <div className="flex justify-between items-center mb-8">
                            <div>
                                <h3 className="text-2xl font-black text-gray-900">Room {room.name}</h3>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-widest">{room.status}</p>
                            </div>
                            <div className={`w-12 h-12 rounded-2xl flex items-center justify-center text-xl ${
                                room.is_clean ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-orange-600'
                            }`}>
                                {room.is_clean ? '✨' : '🧹'}
                            </div>
                        </div>

                        <div className="space-y-4">
                            <div className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                                <span className="text-sm font-bold text-gray-500">Cleanliness Status</span>
                                <button 
                                    onClick={() => toggleClean(room.id, room.is_clean)}
                                    className={`px-4 py-1.5 rounded-full text-[10px] font-black uppercase transition-all ${
                                        room.is_clean ? 'bg-emerald-500 text-white' : 'bg-orange-500 text-white'
                                    }`}
                                >
                                    {room.is_clean ? 'Clean' : 'Needs Cleaning'}
                                </button>
                            </div>

                            <div className="flex gap-2">
                                <button 
                                    onClick={() => setStatus(room.id, 'available')}
                                    className="flex-1 py-3 px-2 bg-white border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-tight hover:bg-emerald-50 hover:text-emerald-600 transition-all"
                                >
                                    Set Available
                                </button>
                                <button 
                                    onClick={() => setStatus(room.id, 'maintenance')}
                                    className="flex-1 py-3 px-2 bg-white border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-tight hover:bg-orange-50 hover:text-orange-600 transition-all"
                                >
                                    Maintenance
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
            {rooms.length === 0 && (
                <TablePlaceholder 
                    title="No housekeeping data"
                    description="There are no rooms currently configured for housekeeping tracking. Add rooms to the hotel module to start managing cleanliness."
                    icon="🧹"
                />
            )}
        </div>
    );
}

Housekeeping.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
