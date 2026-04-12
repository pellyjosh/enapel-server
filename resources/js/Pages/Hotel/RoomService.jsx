import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function RoomService({ bookings, orders }) {
    const [isOrdering, setIsOrdering] = useState(false);
    const { data, setData, post, processing, reset, errors } = useForm({
        booking_id: '',
        item_name: '',
        quantity: 1,
        total_price: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('hotel.roomservice.store'), {
            onSuccess: () => {
                reset();
                setIsOrdering(false);
            },
        });
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Room Service" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Room Service</h1>
                    <p className="text-gray-500 font-medium mt-1">Manage food and service orders for occupied rooms.</p>
                </div>
                <button 
                    onClick={() => setIsOrdering(true)}
                    className="bg-indigo-600 hover:bg-black text-white px-6 py-3 rounded-2xl font-bold transition-all shrink-0 shadow-lg shadow-indigo-500/20"
                >
                    + Place Order
                </button>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Recent Orders */}
                <div className="lg:col-span-2 space-y-6">
                    <h2 className="text-xl font-black text-gray-900">Recent Service Orders</h2>
                    <div className="space-y-4">
                        {orders.map(order => (
                            <div key={order.id} className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 flex items-center justify-between group hover:shadow-md transition-all">
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                                        🍽️
                                    </div>
                                    <div>
                                        <p className="font-bold text-gray-900">{order.item_name} (x{order.quantity})</p>
                                        <p className="text-xs text-gray-400 font-medium uppercase tracking-tight">
                                            {order.booking?.guest?.name || 'Guest'} — Room {order.booking?.room?.name}
                                        </p>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <p className="font-black text-gray-900 text-lg">₦{Number(order.total_price).toLocaleString()}</p>
                                    <span className="text-[10px] font-black uppercase text-emerald-500">Ordered</span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Occupied Rooms Summary */}
                <div className="space-y-6">
                    <h2 className="text-xl font-black text-gray-900">Occupied Rooms</h2>
                    <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/40 p-8 space-y-6">
                        {bookings.map(b => (
                            <div key={b.id} className="flex items-center justify-between pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                                <div>
                                    <p className="font-bold text-gray-900">Room {b.room?.name}</p>
                                    <p className="text-xs text-gray-400 font-medium">{b.guest?.name}</p>
                                </div>
                                <button 
                                    onClick={() => {
                                        setData('booking_id', b.id);
                                        setIsOrdering(true);
                                    }}
                                    className="text-indigo-600 text-xs font-black uppercase hover:text-black transition-colors"
                                > Order </button>
                            </div>
                        ))}
                        {bookings.length === 0 && <p className="text-center text-gray-400 font-medium py-10">No rooms currently occupied.</p>}
                    </div>
                </div>
            </div>

            {isOrdering && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submit} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6 text-indigo-900">
                            <h3 className="text-2xl font-black">Room Service Order</h3>
                            <button type="button" onClick={() => setIsOrdering(false)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <div className="space-y-4">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Select Guest/Room</label>
                                <select 
                                    value={data.booking_id}
                                    onChange={e => setData('booking_id', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50"
                                    required
                                >
                                    <option value="">Choose Occupied Room...</option>
                                    {bookings.map(b => (
                                        <option key={b.id} value={b.id}>Room {b.room?.name} — {b.guest?.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Item Name</label>
                                <input 
                                    type="text" 
                                    value={data.item_name}
                                    onChange={e => setData('item_name', e.target.value)}
                                    className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                    placeholder="e.g. Club Sandwich"
                                    required
                                />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quantity</label>
                                    <input 
                                        type="number" 
                                        value={data.quantity}
                                        onChange={e => setData('quantity', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        min="1"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Price (₦)</label>
                                    <input 
                                        type="number" 
                                        value={data.total_price}
                                        onChange={e => setData('total_price', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        placeholder="5500"
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-indigo-600 hover:bg-black text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 active:scale-95 transition-all"
                        >
                            {processing ? 'Placing Order...' : 'Place Order'}
                        </button>
                    </form>
                </div>
            )}
        </div>
    );
}

RoomService.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
