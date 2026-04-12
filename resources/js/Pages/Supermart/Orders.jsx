import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head } from '@inertiajs/react';


export default function Orders({ orders = { data: [], links: [] } }) {
    const ordersData = orders.data || [];
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Supermart Procurement" />

            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight text-blue-900">Retail Procurement</h1>
                <p className="text-gray-500 font-medium mt-1">Track purchase orders for FMCG and retail inventory.</p>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Order #</th>
                                <th className="p-6">Product</th>
                                <th className="p-6">Supplier</th>
                                <th className="p-6">Qty</th>
                                <th className="p-6">Cost</th>
                                <th className="p-6">Status</th>
                            </tr>
                        </thead>
                        </tbody>
                    </table>

                    {ordersData.length === 0 && (
                        <TablePlaceholder 
                            title="No procurement history"
                            description="You haven't made any retail purchase orders yet. Restock your inventory to see history here."
                            icon="📜"
                        />
                    )}
                </div>
            </div>
                </div>
            </div>

            <Pagination links={orders.links} />
        </div>
    );
}

Orders.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
