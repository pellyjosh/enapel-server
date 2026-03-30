import React, { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';

export default function LicenseRequired() {
    const { flash } = usePage().props;
    const [licenseKey, setLicenseKey] = useState('');
    const [processing, setProcessing] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        router.post('/license-required/configure', { license_key: licenseKey }, {
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <>
            <Head title="License Required — Enapel" />
            <div className="min-h-screen bg-gray-950 flex flex-col items-center justify-center p-4 relative overflow-hidden">
                {/* Ambient gradient blobs */}
                <div className="absolute top-0 left-1/4 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none" />
                <div className="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-800/10 rounded-full blur-[100px] pointer-events-none" />

                <div className="relative z-10 w-full max-w-md">
                    {/* Logo */}
                    <div className="flex flex-col items-center mb-10">
                        <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-500/30 mb-4">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <h1 className="text-3xl font-black text-white tracking-tight">Enapel</h1>
                        <p className="text-gray-500 text-sm font-medium mt-1">Business Operations Platform</p>
                    </div>

                    {/* Card */}
                    <div className="bg-gray-900 border border-gray-800 rounded-3xl p-8 shadow-2xl">
                        <div className="mb-7">
                            <div className="inline-flex items-center gap-2 bg-amber-500/10 text-amber-400 border border-amber-500/20 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4">
                                <span className="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span>
                                Activation Required
                            </div>
                            <h2 className="text-2xl font-black text-white">Enter Your License Key</h2>
                            <p className="text-gray-400 text-sm mt-2 leading-relaxed">
                                This terminal needs a valid license key to operate. Enter the key provided by Enapel to activate this installation.
                            </p>
                        </div>

                        {/* Error / Success messages */}
                        {flash?.license_error && (
                            <div className="mb-5 p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                                <p className="text-red-400 text-sm font-medium">{flash.license_message || 'License validation failed.'}</p>
                            </div>
                        )}
                        {flash?.status && (
                            <div className="mb-5 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                <p className="text-emerald-400 text-sm font-medium">{flash.status}</p>
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-5">
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                                    License Key
                                </label>
                                <input
                                    type="text"
                                    id="license_key"
                                    value={licenseKey}
                                    onChange={(e) => setLicenseKey(e.target.value.toUpperCase())}
                                    placeholder="XXXXXXXX-XXXXX"
                                    className="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-600 rounded-xl px-4 py-3.5 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    required
                                    autoFocus
                                />
                            </div>

                            <button
                                type="submit"
                                disabled={processing || !licenseKey}
                                className="w-full bg-blue-600 hover:bg-blue-500 disabled:bg-gray-700 disabled:text-gray-500 text-white font-black py-3.5 rounded-xl transition-all duration-200 hover:shadow-lg hover:shadow-blue-500/20 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                {processing ? (
                                    <>
                                        <svg className="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Validating...
                                    </>
                                ) : (
                                    'Activate License'
                                )}
                            </button>
                        </form>

                        <div className="mt-6 pt-6 border-t border-gray-800 flex items-center justify-center gap-1.5">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="text-gray-600">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            <p className="text-gray-600 text-xs">
                                Secured by <span className="text-gray-500 font-semibold">Enapel Cloud</span> license validation
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
