import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ConfirmationModal from '@/Components/ConfirmationModal';
import TablePlaceholder from '@/Components/TablePlaceholder';
import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Staff({ staff = [] }) {
    const [searchTerm, setSearchTerm] = useState('');
    const [isAdding, setIsAdding] = useState(false);
    const [editingStaff, setEditingStaff] = useState(null);
    const [staffToDelete, setStaffToDelete] = useState(null);

    const {
        data,
        setData,
        post,
        processing,
        reset,
        errors,
    } = useForm({
        name: '',
        phone: '',
        designation: '',
        role: '',
        dob: '',
        salary: '',
    });

    const {
        data: editData,
        setData: setEditData,
        put,
        processing: editProcessing,
        reset: resetEdit,
        errors: editErrors,
    } = useForm({
        name: '',
        phone: '',
        designation: '',
        role: '',
        dob: '',
        salary: '',
    });

    const filteredStaff = staff.filter((s) => {
        const name = (s.name || '').toLowerCase();
        const staffId = (s.staffid || '').toLowerCase();
        const query = searchTerm.toLowerCase();
        return name.includes(query) || staffId.includes(query);
    });

    const submitCreate = (e) => {
        e.preventDefault();
        post(route('staff.store'), {
            onSuccess: () => {
                reset();
                setIsAdding(false);
            },
        });
    };

    const openEdit = (s) => {
        setEditingStaff(s);
        setEditData({
            name: s.name || '',
            phone: s.phone || '',
            designation: s.designation || '',
            role: s.role || '',
            dob: s.dob || '',
            salary: s.salary || '',
        });
    };

    const submitEdit = (e) => {
        e.preventDefault();
        if (!editingStaff) return;
        put(route('staff.update', editingStaff.id), {
            onSuccess: () => {
                resetEdit();
                setEditingStaff(null);
            },
        });
    };

    const handleDelete = () => {
        if (!staffToDelete) return;
        router.post(route('staff.delete', staffToDelete.id), {}, {
            onFinish: () => setStaffToDelete(null)
        });
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8 space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Personnel Management" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight text-indigo-900">Staff Records</h1>
                    <p className="text-gray-500 font-medium mt-1">Manage employee profiles, designations, and payroll details.</p>
                </div>
                <div className="flex items-center gap-3 w-full md:w-auto">
                    <div className="relative flex-1 md:w-64">
                        <svg className="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input
                            type="text"
                            placeholder="Search staff..."
                            value={searchTerm}
                            onChange={e => setSearchTerm(e.target.value)}
                            className="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                        />
                    </div>
                    <button
                        onClick={() => setIsAdding(true)}
                        className="bg-indigo-600 hover:bg-black text-white px-6 py-2.5 rounded-xl font-bold transition-all shrink-0 shadow-lg shadow-indigo-500/20"
                    >
                        + Add Staff
                    </button>
                </div>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Staff Member</th>
                                <th className="p-6">Contact</th>
                                <th className="p-6">Role & Status</th>
                                <th className="p-6">Salary</th>
                                <th className="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {filteredStaff.map(s => (
                                <tr key={s.id} className="hover:bg-indigo-50/30 transition-colors group">
                                    <td className="p-6">
                                        <div className="flex items-center gap-3">
                                            <div className="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xs uppercase shadow-inner">
                                                {s.name?.split(' ').map(n => n[0]).join('').slice(0, 2)}
                                            </div>
                                            <div>
                                                <p className="font-bold text-gray-900 leading-tight">{s.name}</p>
                                                <p className="text-[10px] font-black text-indigo-500 uppercase tracking-widest">{s.staffid}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="p-6">
                                        <div className="space-y-1">
                                            <p className="text-sm font-medium text-gray-600">📞 {s.phone || 'N/A'}</p>
                                            <p className="text-[10px] text-gray-400 font-bold uppercase tracking-tighter italic">Born: {s.dob || '—'}</p>
                                        </div>
                                    </td>
                                    <td className="p-6">
                                        <div className="space-y-1">
                                            <p className="text-sm font-black text-gray-900">{s.designation}</p>
                                            <span className="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider">{s.role}</span>
                                        </div>
                                    </td>
                                    <td className="p-6">
                                        <p className="font-black text-gray-900 text-lg">₦{Number(s.salary || 0).toLocaleString()}</p>
                                    </td>
                                    <td className="p-6 text-right">
                                        <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                            <button
                                                onClick={() => openEdit(s)}
                                                className="text-indigo-600 hover:text-white hover:bg-indigo-600 font-bold text-xs p-2.5 rounded-xl border border-indigo-100 transition-all"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                onClick={() => setStaffToDelete(s)}
                                                className="text-rose-600 hover:text-white hover:bg-rose-600 font-bold text-xs p-2.5 rounded-xl border border-rose-100 transition-all"
                                            >
                                                Dismiss
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {filteredStaff.length === 0 && (
                        <TablePlaceholder 
                            title={searchTerm ? "No staff found" : "No records yet"}
                            description={searchTerm 
                                ? `We couldn't find any employees matching "${searchTerm}".` 
                                : "The personnel database is currently empty. Add your first staff member to start managing records."}
                            icon="👥"
                        />
                    )}
                </div>
            </div>

            {/* Add Modal */}
            {isAdding && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitCreate} className="bg-white rounded-[40px] p-8 max-w-2xl w-full shadow-2xl animate-in zoom-in-95 duration-300 max-h-[90vh] overflow-y-auto">
                        <div className="flex justify-between items-center mb-6 text-indigo-900">
                            <h3 className="text-2xl font-black">Register New Staff</h3>
                            <button type="button" onClick={() => setIsAdding(false)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Full Name</label>
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                    {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Phone Number</label>
                                    <input
                                        type="tel"
                                        value={data.phone}
                                        onChange={e => setData('phone', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                    {errors.phone && <p className="text-red-500 text-xs mt-1">{errors.phone}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Date of Birth</label>
                                    <input
                                        type="date"
                                        value={data.dob}
                                        onChange={e => setData('dob', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                </div>
                            </div>
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Designation</label>
                                    <input
                                        type="text"
                                        value={data.designation}
                                        onChange={e => setData('designation', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">System Role</label>
                                    <input
                                        type="text"
                                        value={data.role}
                                        onChange={e => setData('role', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Monthly Salary (₦)</label>
                                    <input
                                        type="number"
                                        value={data.salary}
                                        onChange={e => setData('salary', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium font-black"
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-indigo-600 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
                        >
                            {processing ? 'Registering...' : 'Complete Registration'}
                        </button>
                    </form>
                </div>
            )}

            {/* Edit Modal */}
            {editingStaff && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitEdit} className="bg-white rounded-[40px] p-8 max-w-2xl w-full shadow-2xl animate-in zoom-in-95 duration-300 max-h-[90vh] overflow-y-auto">
                        <div className="flex justify-between items-center mb-6 text-indigo-900">
                            <h3 className="text-2xl font-black">Edit Staff Profile</h3>
                            <button type="button" onClick={() => setEditingStaff(null)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Full Name</label>
                                    <input
                                        type="text"
                                        value={editData.name}
                                        onChange={e => setEditData('name', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Phone Number</label>
                                    <input
                                        type="tel"
                                        value={editData.phone}
                                        onChange={e => setEditData('phone', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Date of Birth</label>
                                    <input
                                        type="date"
                                        value={editData.dob}
                                        onChange={e => setEditData('dob', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                </div>
                            </div>
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Designation</label>
                                    <input
                                        type="text"
                                        value={editData.designation}
                                        onChange={e => setEditData('designation', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">System Role</label>
                                    <input
                                        type="text"
                                        value={editData.role}
                                        onChange={e => setEditData('role', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Monthly Salary (₦)</label>
                                    <input
                                        type="number"
                                        value={editData.salary}
                                        onChange={e => setEditData('salary', e.target.value)}
                                        className="w-full px-5 py-4 rounded-2xl border-gray-100 focus:ring-2 focus:ring-indigo-500 bg-gray-50 font-medium font-black"
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={editProcessing}
                            className="w-full mt-8 py-4 bg-indigo-600 hover:bg-black text-white font-black rounded-2xl shadow-xl active:scale-95 transition-all"
                        >
                            {editProcessing ? 'Updating...' : 'Update Record'}
                        </button>
                    </form>
                </div>
            )}

            <ConfirmationModal 
                show={!!staffToDelete}
                onClose={() => setStaffToDelete(null)}
                onConfirm={handleDelete}
                title="Dismiss Staff Member"
                message={`Are you sure you want to dismiss ${staffToDelete?.name}? This will remove them from the active personnel records.`}
            />
        </div>
    );
}

Staff.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
