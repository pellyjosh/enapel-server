import React from 'react';

export default function ConfirmationModal({ 
    show, 
    onClose, 
    onConfirm, 
    title = 'Confirm Deletion', 
    message = 'Are you sure you want to delete this item? This action cannot be undone.', 
    confirmText = 'Delete',
    processing = false 
}) {
    if (!show) return null;

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
            <div className="bg-white rounded-[40px] p-8 max-w-md w-full shadow-2xl animate-in zoom-in-95 duration-300 text-center">
                <div className="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-6 border border-rose-100">
                    ⚠️
                </div>
                
                <h3 className="text-2xl font-black text-gray-900 mb-2">{title}</h3>
                <p className="text-gray-500 font-medium mb-8">
                    {message}
                </p>

                <div className="flex gap-3">
                    <button 
                        type="button"
                        onClick={onClose} 
                        disabled={processing}
                        className="flex-1 py-4 bg-gray-100 hover:bg-gray-200 text-gray-900 font-black rounded-2xl transition-all uppercase tracking-widest text-[10px]"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button"
                        onClick={onConfirm}
                        disabled={processing}
                        className="flex-1 py-4 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-rose-500/20 uppercase tracking-widest text-[10px]"
                    >
                        {processing ? 'Processing...' : confirmText}
                    </button>
                </div>
            </div>
        </div>
    );
}
