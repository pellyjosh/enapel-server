import React from 'react';

export default function RevenueChart({ data }) {
    return (
        <div className="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <h3 className="text-lg font-bold text-gray-900 mb-6">Revenue Trend (Last 7 Days)</h3>
            <div className="h-64 flex items-end gap-2">
                {data && data.length > 0 ? data.map((item, i) => {
                    const max = Math.max(...data.map(d => d.amount), 1);
                    const height = (item.amount / max) * 100;
                    return (
                        <div key={i} className="flex-1 flex flex-col items-center gap-2 group">
                            <div 
                                className="w-full bg-blue-500 rounded-t-lg transition-all duration-500 group-hover:bg-blue-600"
                                style={{ height: `${height}%` }}
                            ></div>
                            <span className="text-[10px] text-gray-400 font-medium rotate-45 mt-2">{item.date}</span>
                        </div>
                    );
                }) : (
                    <div className="w-full h-full flex items-center justify-center text-gray-400">
                        No recent sales data
                    </div>
                )}
            </div>
        </div>
    );
}
