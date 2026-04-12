import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function HotelDashboard({ metrics }) {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Hotel Dashboard" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight">Hotel Dashboard</h1>
                    <p className="text-gray-500 font-medium mt-1">Real-time room status, guest management, and hospitality metrics.</p>
                </div>
                <div className="flex items-center gap-3">
                    <Link href={route('hotel.bookings')} className="bg-indigo-600 hover:bg-black text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
                        + New Booking
                    </Link>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link href={route('hotel.rooms')} className="bg-white border border-gray-100 p-6 rounded-[32px] shadow-xl shadow-gray-200/40 group hover:border-indigo-500/30 transition-all border-b-4 border-b-indigo-500 block">
                    <div className="bg-indigo-50 text-indigo-600 w-12 h-12 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <h3 className="text-3xl font-black text-gray-900 mb-1">{metrics.available_rooms}</h3>
                    <p className="text-[10px] font-black uppercase text-gray-400 tracking-widest">Available Rooms</p>
                </Link>

                <Link href={route('hotel.rooms')} className="bg-white border border-gray-100 p-6 rounded-[32px] shadow-xl shadow-gray-200/40 group hover:border-rose-500/30 transition-all border-b-4 border-b-rose-500 block">
                    <div className="bg-rose-50 text-rose-600 w-12 h-12 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    </div>
                    <h3 className="text-3xl font-black text-gray-900 mb-1">{metrics.occupied_rooms}</h3>
                    <p className="text-[10px] font-black uppercase text-gray-400 tracking-widest">Occupied Rooms</p>
                </Link>

                <Link href={route('hotel.guests')} className="bg-white border border-gray-100 p-6 rounded-[32px] shadow-xl shadow-gray-200/40 group hover:border-emerald-500/30 transition-all border-b-4 border-b-emerald-500 block">
                    <div className="bg-emerald-50 text-emerald-600 w-12 h-12 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3 className="text-3xl font-black text-gray-900 mb-1">{metrics.total_guests}</h3>
                    <p className="text-[10px] font-black uppercase text-gray-400 tracking-widest">Registered Guests</p>
                </Link>

                <Link href={route('hotel.roomservice')} className="bg-white border border-gray-100 p-6 rounded-[32px] shadow-xl shadow-gray-200/40 group hover:border-orange-500/30 transition-all border-b-4 border-b-orange-500 block">
                    <div className="bg-orange-50 text-orange-600 w-12 h-12 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </div>
                    <h3 className="text-3xl font-black text-gray-900 mb-1">{metrics.pending_orders}</h3>
                    <p className="text-[10px] font-black uppercase text-gray-400 tracking-widest">Service Orders</p>
                </Link>
            </div>

            <div className="bg-gray-900 rounded-[40px] p-12 text-white relative overflow-hidden">
                <div className="relative z-10 md:flex items-center justify-between">
                    <div className="max-w-xl">
                        <h2 className="text-3xl font-black mb-4 tracking-tighter">Maximize Property Performance</h2>
                        <p className="text-indigo-200 mb-8 font-medium">Keep track of every room, every guest, and every order from a single premium interface.</p>
                        <div className="flex gap-4">
                            <Link href={route('hotel.rooms')} className="bg-white text-gray-900 px-8 py-3 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-indigo-400 transition-colors">Configure Rooms</Link>
                            <Link href={route('hotel.housekeeping')} className="bg-white/10 text-white px-8 py-3 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-white/20 transition-colors">Housekeeping</Link>
                        </div>
                    </div>
                    <div className="hidden md:block opacity-20">
                         <span className="text-[200px] leading-none">🏨</span>
                    </div>
                </div>
                <div className="absolute top-0 right-0 w-64 h-64 bg-indigo-500 filter blur-[120px] opacity-20 -mr-32 -mt-32"></div>
                <div className="absolute bottom-0 left-0 w-64 h-64 bg-purple-500 filter blur-[120px] opacity-20 -ml-32 -mb-32"></div>
            </div>
        </div>
    );
}

HotelDashboard.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
