import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head } from '@inertiajs/react';

export default function Invoices({ bookings, orders }) {
    // bookings are current occupied, we can also fetch past ones
    // for now let's just show a bill generator for occupied rooms
    
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Guest Invoices" />

            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Guest Billing</h1>
                <p className="text-gray-500 font-medium mt-1">Generate and manage invoices for room stays and services.</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {bookings.map(b => {
                    const roomServiceTotal = b.room_service?.reduce((acc, curr) => acc + Number(curr.total_price), 0) || 0;
                    const totalBill = Number(b.total_price) + roomServiceTotal;

                    return (
                        <div key={b.id} className="bg-white rounded-[40px] border border-gray-100 shadow-xl p-8 flex flex-col group hover:scale-[1.02] transition-all">
                            <div className="flex justify-between items-start mb-6">
                                <div className="text-indigo-600">
                                    <h3 className="text-2xl font-black">{b.guest?.name}</h3>
                                    <p className="text-xs font-bold uppercase tracking-widest text-gray-400">Room {b.room?.name}</p>
                                </div>
                                <span className="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-black uppercase">Active</span>
                            </div>

                            <div className="space-y-4 mb-8">
                                <div className="flex justify-between text-sm">
                                    <span className="text-gray-400 font-bold">Room Charge</span>
                                    <span className="text-gray-900 font-black">₦{Number(b.total_price).toLocaleString()}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-gray-400 font-bold">Room Service</span>
                                    <span className="text-gray-900 font-black">₦{roomServiceTotal.toLocaleString()}</span>
                                </div>
                                <div className="border-t border-dashed border-gray-200 pt-4 flex justify-between">
                                    <span className="text-gray-900 font-black">Total Due</span>
                                    <span className="text-2xl font-black text-indigo-900">₦{totalBill.toLocaleString()}</span>
                                </div>
                            </div>

                            <button className="w-full py-4 bg-gray-900 hover:bg-black text-white font-black rounded-2xl text-xs uppercase tracking-widest transition-all shadow-lg shadow-gray-200">
                                Generate Final Invoice
                            </button>
                        </div>
                    );
                })}
                {bookings.length === 0 && (
                    <div className="col-span-full">
                        <TablePlaceholder 
                            title="No active stays found"
                            description="There are currently no active guest bookings to generate invoices for. Check-in a guest to start billing for rooms and services."
                            icon="🧾"
                        />
                    </div>
                )}
            </div>
        </div>
    );
}

Invoices.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
