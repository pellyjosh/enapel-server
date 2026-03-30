import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Settings() {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Hotel Settings" />

            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Hotel Settings</h1>
                <p className="text-gray-500 font-medium mt-1">Configure property identification, policies, and system behavior.</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8 space-y-6">
                    <h3 className="text-xl font-black text-gray-900">Property Information</h3>
                    <div className="space-y-4">
                        <div>
                            <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Hotel Name</label>
                            <input type="text" className="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 font-medium" defaultValue="Enapel Luxury Suites" />
                        </div>
                        <div>
                            <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Support Email</label>
                            <input type="email" className="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 font-medium" defaultValue="hospitality@enapel.com" />
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8 space-y-6">
                    <h3 className="text-xl font-black text-gray-900">Booking Policies</h3>
                    <div className="space-y-6 text-indigo-900">
                         <div className="flex items-center justify-between">
                            <div>
                                <p className="font-black text-sm uppercase tracking-tight">Standard Check-In</p>
                                <p className="text-xs text-gray-400 font-medium">Auto-apply to all bookings</p>
                            </div>
                            <span className="font-black">14:00</span>
                         </div>
                         <div className="flex items-center justify-between">
                            <div>
                                <p className="font-black text-sm uppercase tracking-tight">Standard Check-Out</p>
                                <p className="text-xs text-gray-400 font-medium">Guests must leave by this time</p>
                            </div>
                            <span className="font-black">11:00</span>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

Settings.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
