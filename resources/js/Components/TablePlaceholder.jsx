import React from 'react';

export default function TablePlaceholder({ 
    title = "No records found", 
    description = "There are currently no items to display in this list.", 
    icon = "📂",
    action = null 
}) {
    return (
        <div className="flex flex-col items-center justify-center py-20 px-6 text-center animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="w-24 h-24 rounded-[40px] bg-blue-50 text-blue-600 flex items-center justify-center text-5xl mb-8 shadow-xl shadow-blue-500/10 ring-8 ring-blue-50/50">
                {icon}
            </div>
            <h3 className="text-2xl font-black text-blue-900 tracking-tight mb-2">
                {title}
            </h3>
            <p className="text-gray-500 font-medium max-w-xs leading-relaxed mb-8">
                {description}
            </p>
            {action && (
                <div className="animate-in fade-in zoom-in-95 delay-300 duration-500">
                    {action}
                </div>
            )}
        </div>
    );
}
