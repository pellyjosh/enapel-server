import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function Bookings({ bookings, guests, rooms }) {
    const [isAdding, setIsAdding] = useState(false);
    const { data, setData, post, processing, reset, errors } = useForm({
        guest_id: '',
        room_id: '',
        check_in: '',
        check_out: '',
        total_price: '',
    });

    const calculatePrice = (roomId, checkIn, checkOut) => {
        if (!roomId || !checkIn || !checkOut) return;
        const room = rooms.find(r => r.id == roomId);
        if (!room) return;

        const start = new Date(checkIn);
        const end = new Date(checkOut);
        const nights = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
        
        setData('total_price', room.price * nights);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('hotel.bookings.store'), {
            onSuccess: () => {
                reset();
                setIsAdding(false);
            },
        });
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Booking Management" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Room Bookings</h1>
                    <p className="text-gray-500 font-medium mt-1">Manage check-ins, reservations, and room assignments.</p>
                </div>
                <button 
                    onClick={() => setIsAdding(true)}
                    className="bg-indigo-600 hover:bg-black text-white px-6 py-3 rounded-2xl font-bold transition-all shrink-0 shadow-lg shadow-indigo-500/20"
                >
                    + New Booking
                </button>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Guest</th>
                                <th className="p-6">Room</th>
                                <th className="p-6">Check-In</th>
                                <th className="p-6">Check-Out</th>
                                <th className="p-6">Total Price</th>
                                <th className="p-6">Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {bookings.map(b => (
                                <tr key={b.id} className="hover:bg-indigo-50/30 transition-colors group">
                                    <td className="p-6">
                                        <p className="font-bold text-gray-900">{b.guest?.name || 'Unknown Guest'}</p>
                                    </td>
                                    <td className="p-6 text-sm text-gray-600 font-bold uppercase">
                                        Room {b.room?.name || 'N/A'}
                                    </td>
                                    <td className="p-6 text-sm text-gray-500">
                                        {new Date(b.check_in).toLocaleDateString()}
                                    </td>
                                    <td className="p-6 text-sm text-gray-500">
                                        {new Date(b.check_out).toLocaleDateString()}
                                    </td>
                                    <td className="p-6 font-black text-gray-900">
                                        ₦{Number(b.total_price).toLocaleString()}
                                    </td>
                                    <td className="p-6">
                                        <span className="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-wider">
                                            {b.status}
                                        </span>
                                    </td>
                                </tr>
                            ))}
                            {bookings.length === 0 && (
                                <tr>
                                    <td colSpan="6" className="p-20 text-center text-gray-400 font-medium">
                                        No active bookings found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {isAdding && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submit} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-indigo-900">
                            <h3 className="text-2xl font-black">Create Reservation</h3>
                            <button type="button" onClick={() => setIsAdding(false)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Select Guest</label>
                                <select 
                                    value={data.guest_id}
                                    onChange={e => setData('guest_id', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50"
                                    required
                                >
                                    <option value="">Choose Guest...</option>
                                    {guests.map(g => (
                                        <option key={g.id} value={g.id}>{g.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Assign Room</label>
                                <select 
                                    value={data.room_id}
                                    onChange={e => {
                                        setData('room_id', e.target.value);
                                        calculatePrice(e.target.value, data.check_in, data.check_out);
                                    }}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50"
                                    required
                                >
                                    <option value="">Select Available Room...</option>
                                    {rooms.map(r => (
                                        <option key={r.id} value={r.id}>Room {r.name} (₦{Number(r.price).toLocaleString()})</option>
                                    ))}
                                </select>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Check-In</label>
                                    <input 
                                        type="date" 
                                        value={data.check_in}
                                        onChange={e => {
                                            setData('check_in', e.target.value);
                                            calculatePrice(data.room_id, e.target.value, data.check_out);
                                        }}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Check-Out</label>
                                    <input 
                                        type="date" 
                                        value={data.check_out}
                                        onChange={e => {
                                            setData('check_out', e.target.value);
                                            calculatePrice(data.room_id, data.check_in, e.target.value);
                                        }}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50"
                                        required
                                    />
                                </div>
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Amount (₦)</label>
                                <input 
                                    type="number" 
                                    value={data.total_price}
                                    readOnly
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 bg-indigo-50 font-black text-indigo-900"
                                />
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-indigo-600 hover:bg-black text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 active:scale-95 transition-all"
                        >
                            {processing ? 'Confirming...' : 'Place Booking'}
                        </button>
                    </form>
                </div>
            )}
        </div>
    );
}

Bookings.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
