import React, { useState, useEffect } from 'react';
import { Head, router, useForm, usePage } from '@inertiajs/react';

export default function Register(props) {
    const { branding, flash } = usePage().props;
    const { data, setData, post, processing, errors, reset } = useForm({
        license_key: props.license_key || '',
        name: '',
        email: '',
        business_name: '',
        logo: '',
        module: '',
        password: '',
        password_confirmation: '',
    });

    // Determine initial step based on available data or errors
    const [step, setStep] = useState(() => {
        if (props.license_key && props.license_valid) return 'account';
        if (errors.email || errors.password || errors.name) return 'account';
        return 'license';
    });
    
    const [isValidating, setIsValidating] = useState(false);
    const [licenseError, setLicenseError] = useState('');
    const [licenseData, setLicenseData] = useState(null);

    // If we have errors from the server, make sure we stay on the 'account' step
    useEffect(() => {
        if (Object.keys(errors).length > 0) {
            if (errors.license_key) {
                setStep('license');
                setLicenseError(errors.license_key);
            } else {
                setStep('account');
            }
        }
    }, [errors]);

    // Sync license key from props if provided
    useEffect(() => {
        if (props.license_key) {
            setData('license_key', props.license_key);
        }
    }, [props.license_key]);

    const validateLicense = async () => {
        if (!data.license_key.trim()) return;
        setIsValidating(true);
        setLicenseError('');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

        try {
            const res = await fetch('/api/license/validate-key', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ license_key: data.license_key.trim().toUpperCase() }),
            });
            const resData = await res.json();
            setLicenseData(resData);
            
            if (resData.valid === true && resData.tenant && !resData.already_activated) {
                setData(prev => ({
                    ...prev,
                    license_key: data.license_key.trim().toUpperCase(),
                    name: resData.tenant?.owner_name || '',
                    email: resData.tenant?.owner_email || '',
                    business_name: resData.tenant?.name || '',
                    logo: resData.tenant?.company_logo_url || '',
                    module: Array.isArray(resData.modules) ? resData.modules.join(',') : (resData.modules || ''),
                }));
                setStep('account');
            } else if (resData.valid === true && !resData.tenant && !resData.already_activated) {
                // We got a valid license but no tenant info (shouldn't happen with our cloud, but let's be safe)
                setLicenseError('License is valid, but tenant information is missing from the cloud response.');
            } else if (resData.already_activated) {
                // Handled in JSX
                setLicenseError('');
            } else {
                setLicenseError(resData.message || 'License validation failed.');
            }
        } catch (err) {
            setLicenseError('Could not connect to validation server.');
        } finally {
            setIsValidating(false);
        }
    };

    const handleRegister = (e) => {
        console.log("data", data);
        e.preventDefault();
        post('/register', {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <>
            <Head title="Enapel | Activate Software" />
            <div className="min-h-screen bg-gray-950 flex flex-col items-center justify-center p-4 relative overflow-hidden">
                {/* Ambient blobs */}
                <div className="absolute top-0 left-1/4 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none" />
                <div className="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[100px] pointer-events-none" />

                <div className="relative z-10 w-full max-w-md">
                    {/* Logo */}
                    <div className="flex flex-col items-center mb-8 text-center">
                        {branding?.logo ? (
                            <img src={branding.logo} alt={branding.name} className="h-16 w-auto object-contain mb-4" />
                        ) : (
                            <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-500/30 mb-4">
                                <span className="text-white font-black text-3xl">E</span>
                            </div>
                        )}
                        <h1 className="text-3xl font-black text-white tracking-tight">{branding?.name || 'Enapel'}</h1>
                        <p className="text-gray-500 text-sm font-medium mt-1">Business Operations Platform</p>
                    </div>

                    {/* Progress steps */}
                    <div className="flex items-center gap-2 justify-center mb-7">
                        <div className={`flex items-center gap-2 ${step === 'license' ? 'text-blue-400' : 'text-emerald-400'}`}>
                            <div className={`w-6 h-6 rounded-full flex items-center justify-center text-xs font-black ${step === 'license' ? 'bg-blue-600' : 'bg-emerald-600'}`}>
                                {step === 'license' ? '1' : '✓'}
                            </div>
                            <span className="text-xs font-bold uppercase tracking-widest">License</span>
                        </div>
                        <div className="w-12 h-px bg-gray-700" />
                        <div className={`flex items-center gap-2 ${step === 'account' ? 'text-blue-400' : 'text-gray-600'}`}>
                            <div className={`w-6 h-6 rounded-full flex items-center justify-center text-xs font-black ${step === 'account' ? 'bg-blue-600' : 'bg-gray-700'}`}>2</div>
                            <span className="text-xs font-bold uppercase tracking-widest">Account</span>
                        </div>
                    </div>

                    {/* Card */}
                    <div className="bg-gray-900 border border-gray-800 rounded-3xl p-8 shadow-2xl">
                        {step === 'license' ? (
                            <>
                                <div className="mb-6">
                                    <h2 className="text-2xl font-black text-white">Validate License</h2>
                                    <p className="text-gray-400 text-sm mt-2 leading-relaxed">
                                        Enter your license key to activate this terminal and set up your account.
                                    </p>
                                </div>

                                {flash?.status && (
                                    <div className="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                        <p className="text-emerald-400 text-sm font-medium">{flash.status}</p>
                                    </div>
                                )}

                                {flash?.license_message && (
                                    <div className="mb-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl">
                                        <p className="text-blue-400 text-sm font-medium">{flash.license_message}</p>
                                    </div>
                                )}

                                {licenseError && (
                                    <div className="mb-5 p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                                        <p className="text-red-400 text-sm font-medium">{licenseError}</p>
                                    </div>
                                )}

                                {!licenseData && (
                                    <div>
                                        <label className="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">License Key</label>
                                        <div className="flex gap-2">
                                            <input
                                                type="text"
                                                value={data.license_key}
                                                onChange={e => setData('license_key', e.target.value.toUpperCase())}
                                                onKeyDown={e => e.key === 'Enter' && validateLicense()}
                                                placeholder="XXXXXXXX-XXXXX"
                                                className="flex-1 bg-gray-800 border border-gray-700 text-white placeholder-gray-600 rounded-xl px-4 py-3.5 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                autoFocus
                                            />
                                            <button
                                                type="button"
                                                onClick={validateLicense}
                                                disabled={isValidating || !data.license_key.trim()}
                                                className="bg-blue-600 hover:bg-blue-500 disabled:bg-gray-700 disabled:text-gray-500 text-white font-black px-5 rounded-xl transition-all"
                                            >
                                                {isValidating ? (
                                                    <svg className="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                ) : 'Verify'}
                                            </button>
                                        </div>
                                    </div>
                                )}

                                {licenseData?.already_activated && (
                                    <div className="p-6 bg-blue-500/10 border border-blue-500/20 rounded-2xl text-center space-y-4 animate-in zoom-in-95">
                                        <div className="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto text-blue-400 text-xl font-bold">!</div>
                                        <p className="text-blue-100 font-bold">This license is already activated</p>
                                        <p className="text-blue-300/80 text-sm">
                                            An account for <span className="text-white">{licenseData.activated_email}</span> already exists on this server.
                                        </p>
                                        <a 
                                            href="/login" 
                                            className="inline-block w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-3 rounded-xl transition-all"
                                        >
                                            Sign In to Your Account
                                        </a>
                                        <button 
                                            onClick={() => { setLicenseData(null); setData('license_key', ''); }}
                                            className="text-xs text-blue-400 hover:text-white underline"
                                        >
                                            Use a different key
                                        </button>
                                    </div>
                                )}

                                <div className="mt-6 pt-6 border-t border-gray-800 text-center">
                                    <p className="text-gray-600 text-sm">Already activated? <a href="/login" className="text-blue-400 hover:text-blue-300 font-semibold">Sign in</a></p>
                                </div>
                            </>
                        ) : (
                            <>
                                <div className="mb-6">
                                    {/* Tenant info banner */}
                                    <div className="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 mb-5">
                                        <div className="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                        <div>
                                            <p className="text-emerald-400 text-xs font-bold uppercase tracking-widest">License Valid</p>
                                            <p className="text-white text-sm font-bold">{data.business_name || 'Verified'}</p>
                                        </div>
                                    </div>

                                    <h2 className="text-2xl font-black text-white">Create Your Account</h2>
                                    <p className="text-gray-400 text-sm mt-1">Set your password to complete setup.</p>
                                </div>

                                <form onSubmit={handleRegister} className="space-y-4">
                                    <div>
                                        <label className="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Email</label>
                                        <input
                                            type="email"
                                            value={data.email}
                                            readOnly
                                            className="w-full bg-gray-800/50 border border-gray-700 text-gray-300 rounded-xl px-4 py-3.5 text-sm cursor-not-allowed"
                                        />
                                        {errors.email && <p className="text-red-400 text-xs mt-1">{errors.email}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Password</label>
                                        <input
                                            type="password"
                                            value={data.password}
                                            onChange={e => setData('password', e.target.value)}
                                            placeholder="Create a strong password"
                                            className="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-600 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            autoFocus
                                        />
                                        {errors.password && <p className="text-red-400 text-xs mt-1">{errors.password}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Confirm Password</label>
                                        <input
                                            type="password"
                                            value={data.password_confirmation}
                                            onChange={e => setData('password_confirmation', e.target.value)}
                                            placeholder="Repeat your password"
                                            className="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-600 rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                        />
                                        {errors.password_confirmation && <p className="text-red-400 text-xs mt-1">{errors.password_confirmation}</p>}
                                    </div>

                                    {errors.license_key && (
                                        <div className="p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                                            <p className="text-red-400 text-sm font-medium">{errors.license_key}</p>
                                        </div>
                                    )}
                                    {errors.error && (
                                        <div className="p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                                            <p className="text-red-400 text-sm font-medium">{errors.error}</p>
                                        </div>
                                    )}

                                    <div className="flex gap-3 pt-1">
                                        <button
                                            type="button"
                                            onClick={() => { setStep('license'); setLicenseError(''); }}
                                            className="flex-none bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold px-4 py-3.5 rounded-xl transition-all text-sm"
                                        >
                                            ← Back
                                        </button>
                                        <button
                                            type="submit"
                                            disabled={processing || !data.password || !data.password_confirmation}
                                            className="flex-1 bg-blue-600 hover:bg-blue-500 disabled:bg-gray-700 disabled:text-gray-500 text-white font-black py-3.5 rounded-xl transition-all flex items-center justify-center gap-2"
                                        >
                                            {processing ? (
                                                <><svg className="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Creating Account...</>
                                            ) : 'Activate & Create Account'}
                                        </button>
                                    </div>
                                </form>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
