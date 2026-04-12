import React from 'react';
import { Link } from '@inertiajs/react';

export default function HotelStats({ metrics }) {
    if (!metrics) return null;

    return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Link href={route('hotel.rooms')} className="bg-indigo-900 text-white p-6 rounded-3xl shadow-xl hover:bg-black transition-colors block group">
                <p className="text-indigo-300 text-xs font-bold uppercase tracking-widest">Occupancy Rate</p>
                <h2 className="text-4xl font-black mt-2 transition-transform group-hover:scale-105 origin-left">{metrics.occupancy_rate}%</h2>
                <div className="mt-4 w-full bg-indigo-800 rounded-full h-2">
                    <div 
                        className="bg-indigo-400 h-2 rounded-full transition-all duration-1000" 
                        style={{ width: `${metrics.occupancy_rate}%` }}
                    ></div>
                </div>
            </Link>
            <Link href={route('hotel.bookings')} className="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-indigo-500 hover:shadow-md transition-all block group">
                <p className="text-gray-500 text-xs font-bold uppercase tracking-widest">Today's Arrivals</p>
                <h2 className="text-3xl font-black text-gray-900 mt-2 transition-transform group-hover:scale-105 origin-left">{metrics.today_arrivals}</h2>
                <p className="text-gray-400 text-sm mt-1">Guests checking in today</p>
            </Link>
        </div>
    );
}
