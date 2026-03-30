import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function AuthenticatedLayout({ children }) {
    const { url, props } = usePage();
    const { branding, auth, enabledModules } = props;
    const currentPath = url.split('?')[0];
    
    // Parse URL for module parameter to set initial active sidebar state
    const params = new URLSearchParams(url.split('?')[1]);
    let moduleFromUrl = params.get('module');

    // Infere from URL path if query param is not available
    if (!moduleFromUrl) {
        if (url.startsWith('/hotel')) moduleFromUrl = 'hotel';
        else if (url.startsWith('/pharmacy')) moduleFromUrl = 'pharmacy';
        else if (url.startsWith('/supermart')) moduleFromUrl = 'supermart';
    }
    
    // Determine the default valid module.
    // If we have a URL param, check if it's valid. Otherwise default to global.
    const defaultModule = moduleFromUrl && ['global', ...enabledModules].includes(moduleFromUrl) 
        ? moduleFromUrl 
        : 'global';

    const [sidebarOpen, setSidebarOpen] = useState(true);
    const [currentModule, setCurrentModule] = useState(defaultModule);

    // Sync state if URL changes organically (e.g. forward/backward navigation)
    useEffect(() => {
        if (moduleFromUrl && ['global', ...enabledModules].includes(moduleFromUrl) && moduleFromUrl !== currentModule) {
            setCurrentModule(moduleFromUrl);
        }
    }, [url, enabledModules]);

    // Make sure the requested module is actually enabled
    useEffect(() => {
        if (currentModule !== 'global' && !enabledModules.includes(currentModule)) {
            setCurrentModule('global');
        }
    }, [currentModule, enabledModules]);

    const logoSrc = branding?.logo ? `/storage/${branding.logo}` : null;
    const brandName = branding?.name || 'Enapel';

    // The modules the user can switch between
    const availableModules = [
        { id: 'global', name: 'Global / Overview', icon: '🌍' },
        ...(enabledModules.includes('hotel') ? [{ id: 'hotel', name: 'Hotel Management', icon: '🏨' }] : []),
        ...(enabledModules.includes('pharmacy') ? [{ id: 'pharmacy', name: 'Pharmacy', icon: '💊' }] : []),
        ...(enabledModules.includes('supermart') ? [{ id: 'supermart', name: 'Supermart / Retail', icon: '🛒' }] : []),
    ];

    // Navigation Menus based on the selected module
    const navigationLinks = {
        global: [
            { section: 'Overview' },
            { name: 'Dashboard', href: '/dashboard?module=global', icon: '📊', active: true },
            { name: 'Analytics & Reports', href: '#', icon: '📈' },
            { section: 'Company Management' },
            { name: 'Staff & HR', href: '#', icon: '👥' },
            { name: 'Payroll', href: '#', icon: '💸' },
            { name: 'Finance & Accounting', href: '#', icon: '💰' },
            { name: 'Expenses', href: '#', icon: '🧾' },
            { section: 'Global Settings' },
            { name: 'Terminals & Devices', href: '/global/settings/terminals', icon: '💻' },
            { name: 'System Settings', href: '#', icon: '⚙️' },
        ],
        hotel: [
            { section: 'Front Desk' },
            { name: 'Hotel Dashboard', href: '/hotel/dashboard', icon: '📊' },
            { name: 'Bookings & Reservations', href: '/hotel/bookings', icon: '📅' },
            { name: 'Guest Management', href: '/hotel/guests', icon: '🧑‍🤝‍🧑' },
            { section: 'Operations' },
            { name: 'Rooms & Layout', href: '/hotel/rooms', icon: '🛏️' },
            { name: 'Housekeeping', href: '/hotel/housekeeping', icon: '🧹' },
            { name: 'Room Service', href: '/hotel/roomservice', icon: '🍽️' },
            { section: 'Finance' },
            { name: 'Invoices & Billing', href: '/hotel/invoices', icon: '💳' },
            { name: 'Hotel Reports', href: '/hotel/reports', icon: '📊' },
            { name: 'Hotel Settings', href: '/hotel/settings', icon: '🛠️' },
        ],
        pharmacy: [
            { section: 'Sales & Dispensing' },
            { name: 'Pharmacy Dashboard', href: '/pharmacy/dashboard', icon: '📊' },
            { name: 'Point of Sale (POS)', href: '/pharmacy/pos', icon: '💳' },
            { name: 'Prescriptions', href: '/pharmacy/prescriptions', icon: '📝' },
            { name: 'Customer Sales', href: '/pharmacy/sales', icon: '🛍️' },
            { section: 'Inventory Management' },
            { name: 'Drug Catalog', href: '/pharmacy/catalog', icon: '💊' },
            { name: 'Stock & Batches', href: '/pharmacy/stock', icon: '📦' },
            { name: 'Expiry Alerts', href: '/pharmacy/alerts', icon: '⚠️' },
            { section: 'Supply Chain' },
            { name: 'Suppliers & Vendors', href: '/pharmacy/suppliers', icon: '🚚' },
            { name: 'Purchase Orders', href: '/pharmacy/orders', icon: '📄' },
        ],
        supermart: [
            { section: 'Retail Operations' },
            { name: 'Retail Dashboard', href: '/supermart/dashboard', icon: '📊' },
            { name: 'Point of Sale (POS)', href: '/supermart/pos', icon: '💳' },
            { name: 'Orders & Deliveries', href: '/supermart/orders', icon: '🚚' },
            { section: 'Inventory & Stock' },
            { name: 'Products Catalog', href: '/supermart/catalog', icon: '🥫' },
            { name: 'Categories & Brands', href: '/supermart/categories', icon: '🏷️' },
            { name: 'Stock Adjustments', href: '/supermart/stock', icon: '秤' },
            { section: 'Supply Chain' },
            { name: 'Suppliers Procurement', href: '/supermart/suppliers', icon: '🤝' },
            { name: 'Purchase Invoices', href: '/supermart/invoices', icon: '🧾' },
            { name: 'Sales Reports', href: '/supermart/reports', icon: '📈' },
        ],
        clinic: [
            { section: 'Clinical Operations' },
            { name: 'Clinic Dashboard', href: '/dashboard', icon: '📊' },
            { name: 'Patient Records', href: '#', icon: '🏥' },
            { name: 'Appointments', href: '#', icon: '🗓️' },
            { name: 'Consultations', href: '#', icon: '🩺' },
            { section: 'Laboratory' },
            { name: 'Lab Tests & Results', href: '#', icon: '🔬' },
            { name: 'Medical Reports', href: '#', icon: '📄' },
            { section: 'Billing' },
            { name: 'Invoicing', href: '#', icon: '💳' },
        ]
    };

    const currentNav = navigationLinks[currentModule] || navigationLinks.global;

    return (
        <div className="flex h-screen bg-gray-50 font-sans selection:bg-blue-100 selection:text-blue-900 overflow-hidden">
            {/* Sidebar */}
            <aside className={`bg-gray-950 text-white flex flex-col transition-all duration-300 ${sidebarOpen ? 'w-72' : 'w-20'}`}>
                {/* Brand Header */}
                <div className="h-20 flex items-center justify-between px-4 border-b border-gray-800">
                    <div className={`flex items-center gap-3 overflow-hidden ${!sidebarOpen && 'justify-center w-full'}`}>
                        {logoSrc ? (
                            <img src={logoSrc} alt={brandName} className="h-8 w-8 object-contain bg-white rounded-lg p-1 shrink-0" />
                        ) : (
                            <div className="h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                                <span className="text-white font-black text-sm">E</span>
                            </div>
                        )}
                        {sidebarOpen && <span className="font-black text-xl tracking-tight whitespace-nowrap truncate">{brandName}</span>}
                    </div>
                    {sidebarOpen && (
                        <button onClick={() => setSidebarOpen(false)} className="text-gray-400 hover:text-white p-1">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M15 18l-6-6 6-6" /></svg>
                        </button>
                    )}
                </div>

                {/* Module Switcher */}
                <div className="p-4 border-b border-gray-800">
                    {sidebarOpen ? (
                        <div>
                            <p className="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Active Module</p>
                            <select 
                                value={currentModule}
                                onChange={(e) => setCurrentModule(e.target.value)}
                                className="w-full bg-gray-900 border border-gray-700 text-white text-sm rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer appearance-none"
                                style={{
                                    backgroundImage: `url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E")`,
                                    backgroundPosition: 'right 0.5rem center',
                                    backgroundRepeat: 'no-repeat',
                                    backgroundSize: '1.5em 1.5em',
                                    paddingRight: '2.5rem'
                                }}
                            >
                                {availableModules.map(mod => (
                                    <option key={mod.id} value={mod.id}>{mod.icon} {mod.name}</option>
                                ))}
                            </select>
                        </div>
                    ) : (
                        <div className="flex justify-center" title="Switch Module">
                            <button onClick={() => setSidebarOpen(true)} className="h-10 w-10 bg-gray-900 rounded-xl flex items-center justify-center text-lg hover:bg-gray-800 transition-colors">
                                {availableModules.find(m => m.id === currentModule)?.icon}
                            </button>
                        </div>
                    )}
                </div>

                {/* Navigation Links */}
                <div className="flex-1 overflow-y-auto px-3 py-6 space-y-1 custom-scrollbar">
                    {currentNav.map((item, index) => {
                        if (item.section) {
                            return sidebarOpen ? (
                                <p key={index} className="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-6 mb-2 px-3">
                                    {item.section}
                                </p>
                            ) : (
                                <div key={index} className="h-px bg-gray-800 my-4 mx-2"></div>
                            );
                        }

                        const isActive = item.href && item.href !== '#' && currentPath === item.href.split('?')[0];

                        return (
                            <Link
                                key={index}
                                href={item.href}
                                className={`flex items-center gap-3 px-3 py-3 rounded-xl transition-all ${
                                    isActive ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-500/20' : 'text-gray-400 hover:text-white hover:bg-white/5 font-medium'
                                }`}
                                title={!sidebarOpen ? item.name : undefined}
                            >
                                <span className="text-xl shrink-0">{item.icon}</span>
                                {sidebarOpen && <span className="text-sm cursor-pointer">{item.name}</span>}
                            </Link>
                        );
                    })}
                </div>

                {/* User / Bottom Section */}
                <div className="p-4 border-t border-gray-800">
                    <div className={`flex items-center ${sidebarOpen ? 'gap-3' : 'justify-center'}`}>
                        <div className="h-10 w-10 bg-gradient-to-tr from-gray-700 to-gray-600 rounded-xl flex items-center justify-center font-black text-white shrink-0 shadow-inner">
                            {auth.user.name[0]}
                        </div>
                        {sidebarOpen && (
                            <div className="flex-1 overflow-hidden">
                                <p className="text-sm font-bold text-white truncate">{auth.user.name}</p>
                                <p className="text-xs text-gray-500 truncate">{auth.user.email}</p>
                            </div>
                        )}
                        {sidebarOpen && (
                            <Link href="/logout" method="post" as="button" className="text-gray-500 hover:text-white p-2 text-sm bg-white/5 hover:bg-red-500/20 hover:text-red-400 rounded-lg transition-colors">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            </Link>
                        )}
                    </div>
                </div>
            </aside>

            {/* Main Content Area */}
            <main className="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
                {/* Top Header Row (Mobile Toggle + Search/Actions) */}
                <header className="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-6 shrink-0 z-10 sticky top-0">
                    <div className="flex items-center gap-4">
                        {!sidebarOpen && (
                            <button onClick={() => setSidebarOpen(true)} className="text-gray-500 hover:text-gray-900 p-2 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                            </button>
                        )}
                        <div className="hidden md:flex items-center bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 w-64 focus-within:ring-2 focus-within:ring-blue-500 focus-within:bg-white transition-all">
                            <svg className="w-4 h-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <input type="text" placeholder="Search everywhere..." className="bg-transparent border-none outline-none text-sm w-full placeholder-gray-400 text-gray-900" />
                        </div>
                    </div>
                    <div className="flex items-center gap-4">
                        <button className="relative p-2 text-gray-400 hover:text-gray-900 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            <span className="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                        </button>
                        <div className="h-8 w-px bg-gray-200"></div>
                        <div className="flex items-center gap-2">
                           <span className="text-sm font-bold text-gray-700">Terminal 1</span>
                           <span className="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse"></span>
                        </div>
                    </div>
                </header>

                {/* Page Content */}
                <div className="flex-1 overflow-y-auto">
                    {children}
                </div>
            </main>
        </div>
    );
}
