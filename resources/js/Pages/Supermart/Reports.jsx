import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Reports({ stats = {} }) {
    const { total_stock_value = 0, low_stock_items = 0, out_of_stock_items = 0, sales_velocity = 0, fast_moving_percent = 0, slow_moving_percent = 0 } = stats;

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Supermart Reports" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-blue-900">Retail Analytics</h1>
                    <p className="text-gray-500 font-medium mt-1">Sales performance, inventory turnover, and profit margins.</p>
                </div>
                <div className="flex gap-2">
                    <button onClick={() => window.print()} className="bg-white shadow-sm border border-gray-100 px-6 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all">Print Report</button>
                    <button className="bg-white shadow-sm border border-gray-100 px-6 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all">Export Data</button>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white p-6 rounded-[30px] border border-gray-100 shadow-sm">
                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Stock Value</p>
                    <p className="text-2xl font-black text-blue-900">₦{Number(total_stock_value).toLocaleString()}</p>
                </div>
                <div className="bg-white p-6 rounded-[30px] border border-gray-100 shadow-sm">
                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Low Stock Alerts</p>
                    <p className="text-2xl font-black text-orange-600">{low_stock_items}</p>
                </div>
                <div className="bg-white p-6 rounded-[30px] border border-gray-100 shadow-sm">
                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Out of Stock</p>
                    <p className="text-2xl font-black text-red-600">{out_of_stock_items}</p>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                 <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-10 h-80 flex flex-col justify-center items-center relative overflow-hidden">
                    <div className="relative z-10 text-center">
                        <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Daily Sales Velocity</h3>
                        <p className="text-6xl font-black text-blue-900">{sales_velocity}%</p>
                        <p className="text-xs font-bold text-gray-400 mt-2 uppercase">Compared to target</p>
                    </div>
                    <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-blue-50 to-transparent"></div>
                 </div>

                 <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-10 h-80 flex flex-col">
                    <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest mb-8">Inventory Distribution</h3>
                    <div className="space-y-6">
                        <div>
                            <div className="flex justify-between text-[10px] font-black uppercase text-gray-400 mb-2">
                                <span>Fast Moving</span>
                                <span>{fast_moving_percent}%</span>
                            </div>
                            <div className="w-full bg-gray-50 h-4 rounded-full overflow-hidden border border-gray-100">
                                <div className="bg-blue-600 h-full transition-all duration-1000" style={{ width: `${fast_moving_percent}%` }}></div>
                            </div>
                        </div>
                        <div>
                            <div className="flex justify-between text-[10px] font-black uppercase text-gray-400 mb-2">
                                <span>Slow Moving</span>
                                <span>{slow_moving_percent}%</span>
                            </div>
                            <div className="w-full bg-gray-50 h-4 rounded-full overflow-hidden border border-gray-100">
                                <div className="bg-orange-400 h-full transition-all duration-1000" style={{ width: `${slow_moving_percent}%` }}></div>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    );
}

Reports.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
