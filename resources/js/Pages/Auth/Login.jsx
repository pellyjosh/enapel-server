import React, { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';

export default function Login() {
    const { branding, flash } = usePage().props;
    const [form, setForm] = useState({ email: '', password: '', remember: false });
    const [errors, setErrors] = useState({});
    const [processing, setProcessing] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});
        router.post('/login', form, {
            onError: (errs) => { setErrors(errs); setProcessing(false); },
            onFinish: () => setProcessing(false),
        });
    };

    const logoSrc = branding?.logo || null;
    const brandName = branding?.name || 'Enapel';

    return (
        <>
            <Head title={`${brandName} | Login`} />
            <div className="min-h-screen bg-gray-950 flex relative overflow-hidden">
                {/* === LEFT PANEL — Branding === */}
                <div className="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 relative">
                    {/* Ambient glow */}
                    <div className="absolute inset-0 bg-gradient-to-br from-blue-950 via-gray-950 to-gray-950" />
                    <div className="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-blue-600/15 rounded-full blur-[120px]" />
                    <div className="absolute bottom-1/4 right-0 w-[300px] h-[300px] bg-indigo-600/10 rounded-full blur-[80px]" />

                    {/* Content */}
                    <div className="relative z-10">
                        <div className="flex items-center gap-3">
                            {logoSrc ? (
                                <img src={logoSrc} alt={brandName} className="h-10 w-auto object-contain" />
                            ) : (
                                <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                    <span className="text-white font-black text-xl">E</span>
                                </div>
                            )}
                            <span className="text-white font-black text-xl tracking-tight">{brandName}</span>
                        </div>
                    </div>

                    <div className="relative z-10">
                        <blockquote className="space-y-4">
                            <p className="text-4xl font-black text-white leading-tight">
                                Manage your entire<br />
                                <span className="text-blue-400">business in one place.</span>
                            </p>
                            <p className="text-gray-400 text-lg leading-relaxed max-w-sm">
                                Inventory, sales, staff, finance, hotels and more — all powered by your Enapel license.
                            </p>
                        </blockquote>
                    </div>

                    <div className="relative z-10 flex gap-6">
                        {['Inventory', 'Sales', 'Finance', 'Hotels', 'Staff'].map((mod) => (
                            <div key={mod} className="bg-white/5 border border-white/10 rounded-xl px-3 py-1.5">
                                <p className="text-gray-400 text-xs font-bold uppercase tracking-widest">{mod}</p>
                            </div>
                        ))}
                    </div>
                </div>

                {/* === RIGHT PANEL — Login Form === */}
                <div className="flex-1 flex flex-col items-center justify-center p-6 lg:p-16">
                    {/* Mobile logo */}
                    <div className="flex lg:hidden flex-col items-center mb-8">
                        {logoSrc ? (
                            <img src={logoSrc} alt={brandName} className="h-14 w-auto object-contain mb-3" />
                        ) : (
                            <div className="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-3">
                                <span className="text-white font-black text-2xl">E</span>
                            </div>
                        )}
                        <span className="text-white font-black text-2xl">{brandName}</span>
                    </div>

                    <div className="w-full max-w-md">
                        <div className="mb-8">
                            <h1 className="text-3xl font-black text-white">Welcome back</h1>
                            <p className="text-gray-400 mt-2">Sign in to your account to continue</p>
                        </div>

                        {/* Flash / status messages */}
                        {flash?.status && (
                            <div className="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                <p className="text-emerald-400 text-sm font-medium">{flash.status}</p>
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-5">
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                                    Email Address
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    value={form.email}
                                    onChange={e => setForm(f => ({ ...f, email: e.target.value }))}
                                    placeholder="you@company.com"
                                    className="w-full bg-gray-900 border border-gray-700 text-white placeholder-gray-600 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    autoComplete="email"
                                    autoFocus
                                    required
                                />
                                {errors.email && <p className="text-red-400 text-xs mt-1.5">{errors.email}</p>}
                            </div>

                            <div>
                                <div className="flex items-center justify-between mb-2">
                                    <label className="block text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Password
                                    </label>
                                    <Link href="/forgot-password" className="text-blue-400 hover:text-blue-300 text-xs font-semibold transition-colors">
                                        Forgot password?
                                    </Link>
                                </div>
                                <input
                                    type="password"
                                    id="password"
                                    value={form.password}
                                    onChange={e => setForm(f => ({ ...f, password: e.target.value }))}
                                    placeholder="••••••••••"
                                    className="w-full bg-gray-900 border border-gray-700 text-white placeholder-gray-600 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    autoComplete="current-password"
                                    required
                                />
                                {errors.password && <p className="text-red-400 text-xs mt-1.5">{errors.password}</p>}
                            </div>

                            <div className="flex items-center gap-3">
                                <button
                                    type="button"
                                    id="remember_toggle"
                                    onClick={() => setForm(f => ({ ...f, remember: !f.remember }))}
                                    className={`relative w-10 h-5 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 ${form.remember ? 'bg-blue-600' : 'bg-gray-700'}`}
                                >
                                    <span className={`absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 ${form.remember ? 'translate-x-5' : 'translate-x-0'}`} />
                                </button>
                                <label className="text-gray-400 text-sm cursor-pointer" onClick={() => setForm(f => ({ ...f, remember: !f.remember }))}>
                                    Remember me for 30 days
                                </label>
                            </div>

                            <button
                                type="submit"
                                id="login_submit"
                                disabled={processing}
                                className="w-full bg-blue-600 hover:bg-blue-500 disabled:bg-gray-700 disabled:text-gray-500 text-white font-black py-4 rounded-xl transition-all duration-200 hover:shadow-xl hover:shadow-blue-500/20 flex items-center justify-center gap-2 mt-2"
                            >
                                {processing ? (
                                    <>
                                        <svg className="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        Signing In...
                                    </>
                                ) : 'Sign In to Dashboard'}
                            </button>
                        </form>

                        <p className="text-center text-gray-500 text-sm mt-6">
                            New installation?{' '}
                            <Link href="/register" className="text-blue-400 hover:text-blue-300 font-semibold transition-colors">
                                Activation with License Key
                            </Link>
                        </p>

                        <p className="text-center text-gray-500 text-sm mt-3">
                            Disaster recovery needed?{' '}
                            <Link href={route('disaster-recovery.restore.create')} className="text-blue-400 hover:text-blue-300 font-semibold transition-colors">
                                Restore from backup
                            </Link>
                        </p>

                        {/* Security badge */}
                        <div className="mt-8 flex items-center justify-center gap-2 text-gray-600">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <span className="text-xs">Protected by Enapel Cloud License Validation</span>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
