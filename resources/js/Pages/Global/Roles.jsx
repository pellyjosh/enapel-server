import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head } from '@inertiajs/react';

export default function Roles({ roles = [] }) {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Role Management" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">System Roles</h1>
                    <p className="text-gray-500 font-medium mt-1">Define permissions and access levels for different staff categories.</p>
                </div>
                <button
                    className="bg-indigo-600 hover:bg-black text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/20"
                >
                    + Define Role
                </button>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Role Name</th>
                                <th className="p-6">Permissions</th>
                                <th className="p-6">Assigned Staff</th>
                                <th className="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50 text-sm">
                            {roles.map(role => (
                                <tr key={role.id} className="hover:bg-indigo-50/30 transition-colors group">
                                    <td className="p-6">
                                        <p className="font-bold text-gray-900">{role.name}</p>
                                    </td>
                                    <td className="p-6">
                                        <div className="flex flex-wrap gap-1">
                                            {role.permissions?.map(p => (
                                                <span key={p} className="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[9px] font-black uppercase">{p}</span>
                                            ))}
                                        </div>
                                    </td>
                                    <td className="p-6 text-gray-600 font-bold">{role.users_count || 0} users</td>
                                    <td className="p-6 text-right">
                                        <button className="text-indigo-600 font-black hover:underline">Edit Rights</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {roles.length === 0 && (
                        <TablePlaceholder 
                            title="No custom roles defined"
                            description="You are currently using system-default roles. Create custom roles to define granular access control for your team."
                            icon="🔑"
                        />
                    )}
                </div>
            </div>
        </div>
    );
}

Roles.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
