import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Restore({ settings, nodeState, availableNasBundles = [] }) {
    const form = useForm({
        passphrase: '',
        nas_path: settings?.nas_path || '',
        bundle_uuid: '',
        bundle_path: '',
        role: nodeState?.role || 'primary',
    });

    const submit = (e) => {
        e.preventDefault();
        form.post(route('disaster-recovery.restore.store'));
    };

    return (
        <>
            <Head title="Restore From Backup" />
            <div className="min-h-screen bg-gray-950 text-white px-4 py-10">
                <div className="max-w-6xl mx-auto grid lg:grid-cols-[1.1fr_0.9fr] gap-8">
                    <div className="bg-white/5 border border-white/10 rounded-[2rem] p-8 backdrop-blur">
                        <p className="text-xs font-bold uppercase tracking-[0.35em] text-blue-300">Backup & Recovery</p>
                        <h1 className="text-4xl font-black tracking-tight mt-3">Restore this node from backup</h1>
                        <p className="text-gray-300 mt-3 max-w-2xl">
                            Use the latest backup chain from the shared backup folder or restore one backup file directly. You need the backup password to unlock the backup.
                        </p>

                        <form onSubmit={submit} className="mt-8 space-y-5">
                            <label className="block">
                                <span className="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Backup Password</span>
                                <input type="password" className="mt-2 w-full rounded-2xl bg-gray-900 border border-gray-700 text-white" value={form.data.passphrase} onChange={(e) => form.setData('passphrase', e.target.value)} />
                            </label>
                            <label className="block">
                                <span className="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Shared Backup Folder</span>
                                <input className="mt-2 w-full rounded-2xl bg-gray-900 border border-gray-700 text-white font-mono text-sm" value={form.data.nas_path} onChange={(e) => form.setData('nas_path', e.target.value)} />
                            </label>
                            <label className="block">
                                <span className="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Specific Backup ID</span>
                                <input className="mt-2 w-full rounded-2xl bg-gray-900 border border-gray-700 text-white font-mono text-sm" placeholder="Optional" value={form.data.bundle_uuid} onChange={(e) => form.setData('bundle_uuid', e.target.value)} />
                            </label>
                            <label className="block">
                                <span className="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Direct Backup File Path</span>
                                <input className="mt-2 w-full rounded-2xl bg-gray-900 border border-gray-700 text-white font-mono text-sm" placeholder="Optional local backup file" value={form.data.bundle_path} onChange={(e) => form.setData('bundle_path', e.target.value)} />
                            </label>
                            <label className="block">
                                <span className="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Restore As</span>
                                <select className="mt-2 w-full rounded-2xl bg-gray-900 border border-gray-700 text-white" value={form.data.role} onChange={(e) => form.setData('role', e.target.value)}>
                                    <option value="primary">Main Server</option>
                                    <option value="standby">Backup Server</option>
                                </select>
                            </label>

                            <div className="flex flex-wrap gap-3 pt-2">
                                <button type="submit" disabled={form.processing} className="px-6 py-3 rounded-2xl bg-blue-600 font-black">
                                    Restore Backup
                                </button>
                                <Link href="/login" className="px-6 py-3 rounded-2xl bg-white/10 border border-white/10 font-bold">
                                    Back to Login
                                </Link>
                            </div>
                        </form>
                    </div>

                    <div className="bg-white/5 border border-white/10 rounded-[2rem] p-8 backdrop-blur">
                        <h2 className="text-2xl font-black">Backups Found In Shared Folder</h2>
                        <div className="mt-6 space-y-3 max-h-[32rem] overflow-y-auto pr-1">
                            {availableNasBundles.length ? availableNasBundles.map((bundle) => (
                                <button
                                    key={bundle.path}
                                    type="button"
                                    onClick={() => {
                                        form.setData('bundle_uuid', bundle.header?.bundle_uuid || '');
                                        form.setData('bundle_path', '');
                                    }}
                                    className="w-full text-left rounded-3xl border border-white/10 bg-white/5 px-5 py-4 hover:bg-white/10 transition-colors"
                                >
                                    <p className="font-black">{bundle.header?.type} {bundle.header?.full ? 'full' : 'incremental'}</p>
                                    <p className="text-xs font-mono text-gray-400 mt-2 break-all">{bundle.header?.bundle_uuid}</p>
                                    <p className="text-sm text-gray-300 mt-2">{bundle.path}</p>
                                </button>
                            )) : (
                                <div className="rounded-3xl border border-dashed border-white/15 px-5 py-6 text-gray-400">
                                    No backup files are visible from this shared folder yet.
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
