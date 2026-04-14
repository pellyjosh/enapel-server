import React from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import RevenueChart from '@/Components/Widgets/RevenueChart';
import HotelStats from '@/Components/Widgets/HotelStats';
import CommerceStats from '@/Components/Widgets/CommerceStats';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Dashboard() {
    const { url, props } = usePage();
    const { branding, auth, metrics, enabledModules, native_port } = props;

    // Parse URL for module parameter
    const params = new URLSearchParams(url.split('?')[1]);
    const moduleFromUrl = params.get('module');
    
    const currentModule = moduleFromUrl && ['global', ...enabledModules].includes(moduleFromUrl) 
        ? moduleFromUrl 
        : 'global';

    const renderDashboard = () => {
        switch (currentModule) {
            case 'hotel':
                return <HotelDashboard metrics={metrics.hotel} auth={auth} branding={branding} />;
            case 'pharmacy':
            case 'supermart':
                // For now, these share the commerce dashboard stats. 
                // Later we can split them to PharmacyDashboard and SupermartDashboard
                return <CommerceDashboard metrics={metrics.commerce} moduleName={currentModule} />;
            case 'global':
            default:
                return <GlobalDashboard metrics={metrics} auth={auth} branding={branding} enabledModules={enabledModules} />;
        }
    };

    return (
        <>
            <Head title={`${currentModule.charAt(0).toUpperCase() + currentModule.slice(1)} Dashboard`} />

            <div className="py-8 px-4 sm:px-6 lg:px-8">
                <div className="">
                    {renderDashboard()}
                </div>
            </div>
        </>
    );
}

function GlobalDashboard({ metrics, auth, branding, enabledModules }) {
    return (
        <div className="space-y-8">
            {/* Header Section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight">
                        Welcome, {auth.user.name.split(' ')[0]}
                    </h1>
                    <p className="text-gray-500 font-medium mt-1 flex items-center gap-3">
                        <span>Managing <span className="text-blue-600"> {branding.name} </span> Global Operations</span>
                        <span className="text-[10px] font-bold uppercase tracking-widest text-gray-400 bg-gray-100 px-2 py-0.5 rounded border border-gray-200">
                            Port {native_port}
                        </span>
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <div className="text-right hidden sm:block">
                        <p className="text-xs font-bold text-gray-400 uppercase tracking-widest">Today's Revenue</p>
                        <p className="text-xl font-black text-gray-900">₦{Number(metrics.overview.today_revenue).toLocaleString()}</p>
                    </div>
                </div>
            </div>

            {/* Main Grid */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div className="lg:col-span-8">
                    <RevenueChart data={metrics.overview.revenue_trend} />
                </div>
                <div className="lg:col-span-4 flex flex-col gap-6">
                    <div className="bg-blue-600 text-white p-8 rounded-3xl shadow-blue-200 shadow-2xl relative overflow-hidden">
                        <div className="relative z-10">
                            <p className="text-blue-100 text-xs font-bold uppercase tracking-widest">Total Staff</p>
                            <h2 className="text-5xl font-black mt-2">{metrics.overview.total_staff}</h2>
                            <Link href={route('staff')} className="mt-6 bg-white/20 hover:bg-white/30 transition-colors px-4 py-2 rounded-xl text-sm font-bold backdrop-blur-md inline-block">
                                Manage Team
                            </Link>
                        </div>
                    </div>
                    
                    <div className="bg-gray-900 text-white p-8 rounded-3xl shadow-xl">
                        <p className="text-gray-400 text-xs font-bold uppercase tracking-widest">Global Revenue</p>
                        <h2 className="text-3xl font-black mt-2">₦{Number(metrics.overview.total_revenue).toLocaleString()}</h2>
                        <p className="text-gray-500 text-xs mt-4 leading-relaxed">Cumulative performance across all active modules and terminals.</p>
                    </div>
                </div>
            </div>
            
            <div className="mt-12 bg-gradient-to-r from-gray-900 to-blue-900 rounded-[3rem] p-12 text-center relative overflow-hidden">
                <div className="relative z-10">
                    <h2 className="text-3xl font-black text-white mb-4">Enterprise Resource Planning</h2>
                    <p className="text-blue-200 max-w-xl mx-auto mb-8 font-medium">Your Enapel license grants you access to premium modules. Use the sidebar module switcher to explore your terminal settings.</p>
                    <div className="flex flex-wrap justify-center gap-4">
                        <Link href={route('global.settings.terminals')} className="bg-white text-gray-900 px-10 py-4 rounded-2xl font-black hover:scale-105 transition-transform shadow-xl inline-block">
                            Module Settings
                        </Link>
                        <button className="bg-blue-500/20 text-blue-100 border border-blue-400/30 px-10 py-4 rounded-2xl font-black backdrop-blur-md hover:bg-blue-500/30 transition-colors">
                            Generate Reports
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

function HotelDashboard({ metrics }) {
    return (
        <div className="space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight">Hotel Dashboard</h1>
                <p className="text-gray-500 font-medium mt-1">Real-time overview of your front desk and rooms.</p>
            </div>
            <HotelStats metrics={metrics} />
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
               <div className="bg-white border border-gray-100 p-8 rounded-3xl shadow-sm">
                   <h3 className="font-bold text-gray-900 mb-4">Quick Actions</h3>
                   <div className="grid grid-cols-2 gap-4">
                       <Link href={route('hotel.bookings')} className="p-4 bg-gray-50 hover:bg-gray-100 rounded-2xl text-left transition-colors block">
                           <span className="text-2xl mb-2 block">📅</span>
                           <span className="font-bold text-sm">New Booking</span>
                       </Link>
                       <Link href={route('hotel.bookings')} className="p-4 bg-gray-50 hover:bg-gray-100 rounded-2xl text-left transition-colors block">
                           <span className="text-2xl mb-2 block">🔑</span>
                           <span className="font-bold text-sm">Check In Guest</span>
                       </Link>
                   </div>
               </div>
            </div>
        </div>
    );
}

function CommerceDashboard({ metrics, moduleName }) {
    const title = moduleName === 'pharmacy' ? 'Pharmacy Dashboard' : 'Retail Dashboard';
    const desc = moduleName === 'pharmacy' ? 'Manage prescriptions, point of sale, and drug inventory.' : 'Manage point of sale, stock levels, and supply chain.';

    return (
        <div className="space-y-8 animate-in fade-in zoom-in-95 duration-500">
            <div>
                <h1 className="text-4xl font-black text-gray-900 tracking-tight">{title}</h1>
                <p className="text-gray-500 font-medium mt-1">{desc}</p>
            </div>
            <CommerceStats metrics={metrics} moduleName={moduleName} />
        </div>
    );
}

Dashboard.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
