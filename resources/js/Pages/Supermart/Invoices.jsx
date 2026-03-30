import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';


export default function Invoices({ receipts = { data: [], links: [] } }) {
    const { branding } = usePage().props;
    const [selectedReceipt, setSelectedReceipt] = useState(null);
    const receiptsData = receipts.data || [];

    const handlePrint = (receipt) => {
        setSelectedReceipt(receipt);
        setTimeout(() => {
            window.print();
        }, 100);
    };
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Supermart Invoices" />

            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight text-blue-900">Sales Invoices</h1>
                <p className="text-gray-500 font-medium mt-1">Review and manage point-of-sale transaction receipts.</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {receiptsData.map(r => (
                    <div key={r.id} className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8 flex flex-col group hover:border-blue-500 transition-all border-b-8 border-b-blue-600">
                        <div className="flex justify-between items-start mb-6">
                            <div className="text-blue-600">
                                <h3 className="text-xl font-black">#{r.receipt_number}</h3>
                                <p className="text-[10px] font-bold uppercase tracking-widest text-gray-400">{new Date(r.created_at).toLocaleString()}</p>
                            </div>
                            <span className="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase">Paid</span>
                        </div>

                        <div className="space-y-3 mb-8 flex-1">
                            {r.sales?.map(s => (
                                <div key={s.id} className="flex justify-between text-xs font-medium">
                                    <span className="text-gray-500">{s.product?.name} x {s.quantity}</span>
                                    <span className="text-gray-900 font-bold">₦{Number(s.price * s.quantity).toLocaleString()}</span>
                                </div>
                            ))}
                        </div>

                        <div className="border-t border-dashed border-gray-100 pt-6 flex justify-between items-center">
                            <span className="text-gray-400 font-black text-xs uppercase tracking-widest">Total Amount</span>
                            <div className="flex items-center gap-4">
                                <span className="text-2xl font-black text-blue-900">₦{Number(r.total_price || 0).toLocaleString()}</span>
                                <button 
                                    onClick={() => handlePrint(r)}
                                    className="p-2 bg-gray-50 rounded-xl hover:bg-gray-100 text-gray-400 hover:text-blue-600"
                                >
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
                {receiptsData.length === 0 && (
                    <div className="col-span-full py-20 text-center opacity-40 italic">
                         <p className="font-black text-gray-400">No sales invoices generated today.</p>
                    </div>
                )}
            </div>

            <Pagination links={receipts.links} />

            {/* Print Only Receipt Layout */}
            {selectedReceipt && (
                <div className="hidden print:block fixed inset-0 bg-white z-[9999] p-4 text-black font-mono">
                    <style dangerouslySetInnerHTML={{ __html: `
                        @media print {
                            body * { visibility: hidden; }
                            .print-container, .print-container * { visibility: visible; }
                            .print-container { position: absolute; left: 0; top: 0; width: 80mm; font-size: 12px; }
                            @page { margin: 0; size: 80mm auto; }
                        }
                    `}} />
                    <div className="print-container">
                        <div className="text-center mb-4">
                            {branding?.logo && <img src={branding.logo} className="h-12 mx-auto mb-2" alt="logo" />}
                            <h2 className="text-xl font-bold uppercase">{branding?.name || 'ENAPEL STORE'}</h2>
                            <p className="text-[10px]">{branding?.email || ''}</p>
                            <p className="text-[10px] uppercase font-bold mt-2">SALES INVOICE</p>
                            <p className="text-[10px] uppercase">{new Date(selectedReceipt.created_at).toLocaleString()}</p>
                        </div>
                        
                        <div className="border-t border-b border-black py-2 my-2 space-y-1">
                            <div className="flex justify-between font-bold">
                                <span className="w-1/2 text-left">ITEM</span>
                                <span className="w-1/4 text-center">QTY</span>
                                <span className="w-1/4 text-right">TOTAL</span>
                            </div>
                            {selectedReceipt.sales?.map(s => (
                                <div key={s.id} className="flex justify-between">
                                    <span className="w-1/2 text-left truncate flex items-center gap-1">
                                        {s.product?.name || s.product_name}
                                        {(s.product?.sku || (s.product_sku)) && (
                                            <span className="text-[7px] border border-black px-1 rounded">{s.product?.sku || s.product_sku}</span>
                                        )}
                                    </span>
                                    <span className="w-1/4 text-center">x{s.quantity}</span>
                                    <span className="w-1/4 text-right">₦{Number(s.total_price || (s.price * s.quantity)).toLocaleString()}</span>
                                </div>
                            ))}
                        </div>

                        <div className="space-y-1">
                            <div className="flex justify-between text-lg font-bold">
                                <span>TOTAL</span>
                                <span>₦{Number(selectedReceipt.total_amount || selectedReceipt.total_price).toLocaleString()}</span>
                            </div>
                            <div className="flex justify-between text-[10px]">
                                <span>PAYMENT:</span>
                                <span className="uppercase">{selectedReceipt.payment_method || 'PAID'}</span>
                            </div>
                            <div className="text-center mt-4 pt-2 border-t border-dotted border-black">
                                <p className="font-bold">RECEIPT: {selectedReceipt.receipt_number}</p>
                                <p className="mt-2 text-[10px]">THANK YOU FOR YOUR PATRONAGE!</p>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

Invoices.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
