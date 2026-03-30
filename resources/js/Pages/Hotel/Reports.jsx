import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Reports({ metrics }) {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Hotel Reports" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Hospitality Analytics</h1>
                    <p className="text-gray-500 font-medium mt-1">Deep dive into occupancy, revenue, and operation performance.</p>
                </div>
                <div className="flex gap-2">
                    <button className="bg-white border border-gray-100 shadow-sm px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all">Download PDF</button>
                    <button className="bg-white border border-gray-100 shadow-sm px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all">Export Excel</button>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8">
                    <h3 className="text-xl font-black text-gray-900 mb-6">Occupancy Trends</h3>
                    <div className="h-64 bg-gray-50 rounded-3xl flex items-end justify-between p-6 gap-2">
                        {[40, 60, 45, 80, 55, 90, 70].map((h, i) => (
                            <div key={i} className="flex-1 bg-indigo-500 rounded-lg group relative cursor-pointer hover:bg-black transition-all" style={{ height: `${h}%` }}>
                                <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 bg-black text-white px-2 py-1 rounded text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">
                                    {h}%
                                </div>
                            </div>
                        ))}
                    </div>
                    <div className="flex justify-between mt-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                    </div>
                </div>

                <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8 flex flex-col">
                    <h3 className="text-xl font-black text-gray-900 mb-6">Revenue Breakdown</h3>
                    <div className="space-y-6 flex-1 flex flex-col justify-center">
                        <div>
                            <div className="flex justify-between text-sm font-bold mb-2">
                                <span className="text-gray-400 uppercase tracking-widest text-[10px]">Room Revenue</span>
                                <span className="text-gray-900 font-black">75%</span>
                            </div>
                            <div className="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                                <div className="bg-indigo-600 h-full w-[75%]"></div>
                            </div>
                        </div>
                        <div>
                            <div className="flex justify-between text-sm font-bold mb-2">
                                <span className="text-gray-400 uppercase tracking-widest text-[10px]">Room Service</span>
                                <span className="text-gray-900 font-black">20%</span>
                            </div>
                            <div className="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                                <div className="bg-emerald-500 h-full w-[20%]"></div>
                            </div>
                        </div>
                        <div>
                            <div className="flex justify-between text-sm font-bold mb-2">
                                <span className="text-gray-400 uppercase tracking-widest text-[10px]">Others</span>
                                <span className="text-gray-900 font-black">5%</span>
                            </div>
                            <div className="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                                <div className="bg-orange-500 h-full w-[5%]"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

Reports.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
