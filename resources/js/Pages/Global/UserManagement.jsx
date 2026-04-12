import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head } from '@inertiajs/react';

export default function UserManagement({ users = [] }) {
    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="User Management" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">User Management</h1>
                    <p className="text-gray-500 font-medium mt-1">Control system access, user permissions, and security profiles.</p>
                </div>
                <button
                    className="bg-indigo-600 hover:bg-black text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/20"
                >
                    + Create User
                </button>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">User</th>
                                <th className="p-6">Email</th>
                                <th className="p-6">Role</th>
                                <th className="p-6">Security</th>
                                <th className="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50 text-sm">
                            {users.map(user => (
                                <tr key={user.id} className="hover:bg-indigo-50/30 transition-colors group">
                                    <td className="p-6 font-bold text-gray-900">{user.name}</td>
                                    <td className="p-6 text-gray-600">{user.email}</td>
                                    <td className="p-6">
                                        <span className="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full font-black uppercase text-[10px]">
                                            {user.is_admin ? 'Admin' : 'User'}
                                        </span>
                                    </td>
                                    <td className="p-6 text-gray-400 font-medium">Verified</td>
                                    <td className="p-6 text-right">
                                        <button className="text-indigo-600 font-black hover:underline">Settings</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {users.length === 0 && (
                        <TablePlaceholder 
                            title="No users found"
                            description="There are currently no additional system users defined. Create profiles to grant access to other team members."
                            icon="🛡️"
                        />
                    )}
                </div>
            </div>
        </div>
    );
}

UserManagement.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
