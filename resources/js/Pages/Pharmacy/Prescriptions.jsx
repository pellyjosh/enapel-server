import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ConfirmationModal from '@/Components/ConfirmationModal';
import TablePlaceholder from '@/Components/TablePlaceholder';

import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Prescriptions({ prescriptions = [] }) {
    const [isAdding, setIsAdding] = useState(false);
    const [editingPrescription, setEditingPrescription] = useState(null);
    const [modalConfig, setModalConfig] = useState({ show: false, title: '', message: '', onConfirm: () => {} });


    const { data, setData, post, processing, reset, errors } = useForm({
        patient_name: '',
        doctor_name: '',
        notes: '',
    });

    const {
        data: editData,
        setData: setEditData,
        put,
        processing: editProcessing,
        reset: resetEdit,
        errors: editErrors,
    } = useForm({
        patient_name: '',
        doctor_name: '',
        notes: '',
        status: 'pending',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('pharmacy.prescriptions.store'), {
            onSuccess: () => {
                reset();
                setIsAdding(false);
            },
        });
    };

    const openEdit = (prescription) => {
        setEditingPrescription(prescription);
        setEditData({
            patient_name: prescription.patient_name || '',
            doctor_name: prescription.doctor_name || '',
            notes: prescription.notes || '',
            status: prescription.status || 'pending',
        });
    };

    const submitEdit = (e) => {
        e.preventDefault();
        if (!editingPrescription) return;

        put(route('pharmacy.prescriptions.update', editingPrescription.id), {
            onSuccess: () => {
                resetEdit();
                setEditingPrescription(null);
            },
        });
    };

    const dispense = (id) => {
        setModalConfig({
            show: true,
            title: 'Dispense Prescription',
            message: 'Are you sure you want to mark this prescription as dispensed?',
            onConfirm: () => {
                router.post(route('pharmacy.prescriptions.dispense', id));
                setModalConfig(prev => ({ ...prev, show: false }));
            }
        });
    };

    const handleDelete = (prescription) => {
        setModalConfig({
            show: true,
            title: 'Delete Prescription',
            message: `Are you sure you want to delete the prescription for ${prescription.patient_name}? This action cannot be undone.`,
            onConfirm: () => {
                router.delete(route('pharmacy.prescriptions.delete', prescription.id));
                setModalConfig(prev => ({ ...prev, show: false }));
            }
        });
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <Head title="Prescription Management" />

            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight">Prescriptions</h1>
                    <p className="text-gray-500 font-medium mt-1">Manage patient prescriptions and dispensing status.</p>
                </div>
                <button
                    onClick={() => setIsAdding(true)}
                    className="bg-blue-600 hover:bg-black text-white px-6 py-3 rounded-2xl font-bold transition-all shrink-0 shadow-lg shadow-blue-500/20"
                >
                    + New Prescription
                </button>
            </div>

            <div className="bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-gray-50/50 border-b border-gray-100 uppercase text-[10px] font-black tracking-widest text-gray-400">
                                <th className="p-6">Patient Name</th>
                                <th className="p-6">Doctor</th>
                                <th className="p-6">Date</th>
                                <th className="p-6">Status</th>
                                <th className="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {prescriptions.map(p => {
                                const createdAt = p.created_at ? new Date(p.created_at) : null;
                                const statusClass = p.status === 'dispensed'
                                    ? 'bg-emerald-50 text-emerald-600'
                                    : p.status === 'cancelled'
                                        ? 'bg-rose-50 text-rose-600'
                                        : 'bg-orange-50 text-orange-600';

                                return (
                                    <tr key={p.id} className="hover:bg-blue-50/30 transition-colors group">
                                        <td className="p-6">
                                            <p className="font-bold text-gray-900">{p.patient_name}</p>
                                            <p className="text-xs text-gray-500 truncate max-w-[200px]">{p.notes || 'No specific notes'}</p>
                                        </td>
                                        <td className="p-6 text-sm text-gray-600 font-medium">
                                            {p.doctor_name || 'Generic Provider'}
                                        </td>
                                        <td className="p-6 text-sm text-gray-500">
                                            {createdAt ? createdAt.toLocaleDateString() : '—'}
                                        </td>
                                        <td className="p-6">
                                            <span className={`px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusClass}`}>
                                                {p.status}
                                            </span>
                                        </td>
                                        <td className="p-6 text-right">
                                            <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                                {p.status === 'pending' && (
                                                    <button
                                                        onClick={() => dispense(p.id)}
                                                        className="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/10"
                                                    >
                                                        Dispense
                                                    </button>
                                                )}
                                                <button
                                                    type="button"
                                                    onClick={() => openEdit(p)}
                                                    className="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 px-3 py-2 rounded-xl"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => handleDelete(p)}
                                                    className="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 px-3 py-2 rounded-xl"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>

                    {prescriptions.length === 0 && (
                        <TablePlaceholder 
                            title="No prescriptions"
                            description="There are currently no patient prescriptions to display. Create a new prescription to start managing patient orders."
                            icon="📄"
                        />
                    )}
                </div>
            </div>

            {isAdding && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submit} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-2xl font-black text-gray-900">New Prescription</h3>
                            <button type="button" onClick={() => setIsAdding(false)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Patient Name</label>
                                <input
                                    type="text"
                                    value={data.patient_name}
                                    onChange={e => setData('patient_name', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50"
                                    placeholder="e.g. John Doe"
                                    required
                                />
                                {errors.patient_name && <p className="text-red-500 text-xs mt-1">{errors.patient_name}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Doctor Name</label>
                                <input
                                    type="text"
                                    value={data.doctor_name}
                                    onChange={e => setData('doctor_name', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50"
                                    placeholder="e.g. Dr. Smith"
                                />
                                {errors.doctor_name && <p className="text-red-500 text-xs mt-1">{errors.doctor_name}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Prescription Notes</label>
                                <textarea
                                    value={data.notes}
                                    onChange={e => setData('notes', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 h-32"
                                    placeholder="Enter drug details, dosage, etc."
                                ></textarea>
                                {errors.notes && <p className="text-red-500 text-xs mt-1">{errors.notes}</p>}
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full mt-8 py-4 bg-blue-600 hover:bg-black text-white font-black rounded-2xl shadow-xl shadow-blue-500/20 active:scale-95 transition-all disabled:opacity-50"
                        >
                            {processing ? 'Saving...' : 'Create Prescription'}
                        </button>
                    </form>
                </div>
            )}

            {editingPrescription && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <form onSubmit={submitEdit} className="bg-white rounded-[40px] p-8 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-300">
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-2xl font-black text-gray-900">Edit Prescription</h3>
                            <button type="button" onClick={() => setEditingPrescription(null)} className="text-gray-400 hover:text-gray-900 transition-colors">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Patient Name</label>
                                <input
                                    type="text"
                                    value={editData.patient_name}
                                    onChange={e => setEditData('patient_name', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50"
                                    required
                                />
                                {editErrors.patient_name && <p className="text-red-500 text-xs mt-1">{editErrors.patient_name}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Doctor Name</label>
                                <input
                                    type="text"
                                    value={editData.doctor_name}
                                    onChange={e => setEditData('doctor_name', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50"
                                />
                                {editErrors.doctor_name && <p className="text-red-500 text-xs mt-1">{editErrors.doctor_name}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Status</label>
                                <select
                                    value={editData.status}
                                    onChange={e => setEditData('status', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="dispensed">Dispensed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                {editErrors.status && <p className="text-red-500 text-xs mt-1">{editErrors.status}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Prescription Notes</label>
                                <textarea
                                    value={editData.notes}
                                    onChange={e => setEditData('notes', e.target.value)}
                                    className="w-full px-5 py-3 rounded-2xl border-gray-100 focus:ring-2 focus:ring-blue-500 bg-gray-50 h-28"
                                ></textarea>
                                {editErrors.notes && <p className="text-red-500 text-xs mt-1">{editErrors.notes}</p>}
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={editProcessing}
                            className="w-full mt-8 py-4 bg-blue-600 hover:bg-black text-white font-black rounded-2xl shadow-xl shadow-blue-500/20 active:scale-95 transition-all disabled:opacity-50"
                        >
                            {editProcessing ? 'Updating...' : 'Update Prescription'}
                        </button>
                    </form>
                </div>
            )}
            <ConfirmationModal 
                show={modalConfig.show}
                onClose={() => setModalConfig(prev => ({ ...prev, show: false }))}
                onConfirm={modalConfig.onConfirm}
                title={modalConfig.title}
                message={modalConfig.message}
                confirmText={modalConfig.title.startsWith('Delete') ? 'Delete' : 'Confirm'}
            />
        </div>
    );

}

Prescriptions.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
