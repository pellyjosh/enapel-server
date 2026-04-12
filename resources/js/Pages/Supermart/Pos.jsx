import React, { useState, useEffect, useMemo } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import axios from 'axios';
import { 
    Search, 
    ShoppingCart, 
    Plus, 
    Minus, 
    Trash2, 
    CreditCard, 
    Banknote, 
    ArrowRightLeft,
    ChevronRight,
    Package,
    AlertCircle,
    CheckCircle2,
    Receipt,
    X,
    QrCode,
    Filter
} from 'lucide-react';
import TablePlaceholder from '@/Components/TablePlaceholder';

export default function Pos({ products, categories }) {
    const { branding } = usePage().props;
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedCategory, setSelectedCategory] = useState('All');
    const [cart, setCart] = useState([]);
    const [paymentMethod, setPaymentMethod] = useState('cash');
    const [cashPaid, setCashPaid] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [successData, setSuccessData] = useState(null);
    const [error, setError] = useState(null);
    const [selectedProduct, setSelectedProduct] = useState(null);
    const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(false);

    // Barcode scanner logic
    useEffect(() => {
        let scannerBuffer = '';
        let lastKeyTime = Date.now();

        const handleKeyDown = (e) => {
            if (e.target.tagName === 'INPUT') return;

            const currentTime = Date.now();
            if (currentTime - lastKeyTime > 50) {
                scannerBuffer = '';
            }

            if (e.key === 'Enter') {
                if (scannerBuffer.length > 2) {
                    const product = products.find(p => p.sku === scannerBuffer);
                    if (product) {
                        addToCart(product);
                        setSearchTerm('');
                    }
                }
                scannerBuffer = '';
            } else if (e.key.length === 1) {
                scannerBuffer += e.key;
            }
            lastKeyTime = currentTime;
        };

        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [products, cart]);

    const filteredProducts = useMemo(() => products.filter(p => {
        const matchesSearch = (p.name?.toLowerCase() || '').includes(searchTerm.toLowerCase()) ||
                            (p.sku?.toLowerCase() || '').includes(searchTerm.toLowerCase());
        const matchesCategory = selectedCategory === 'All' || p.category === selectedCategory;
        return matchesSearch && matchesCategory;
    }), [products, searchTerm, selectedCategory]);

    const addToCart = (product) => {
        const existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            if (existingItem.quantity >= product.quantity) {
                setError(`Only ${product.quantity} items available in stock.`);
                setTimeout(() => setError(null), 3000);
                return;
            }
            setCart(cart.map(item => 
                item.id === product.id ? { ...item, quantity: item.quantity + 1 } : item
            ));
        } else {
            setCart([...cart, { ...product, stock: product.quantity, quantity: 1, is_pack: false, is_carton: false }]);
        }
        if (isSidebarCollapsed) setIsSidebarCollapsed(false);
    };

    const updateQuantity = (id, delta) => {
        setCart(cart.map(item => {
            if (item.id === id) {
                const newQty = item.quantity + delta;
                if (newQty < 1) return item;
                
                if (delta > 0 && newQty > (item.stock || 0)) {
                    setError(`Only ${item.stock} items available.`);
                    setTimeout(() => setError(null), 2000);
                    return item;
                }
                return { ...item, quantity: newQty };
            }
            return item;
        }));
    };

    const removeFromCart = (id) => {
        setCart(cart.filter(item => item.id !== id));
    };

    const clearCart = () => {
        if (window.confirm('Clear all items from cart?')) {
            setCart([]);
        }
    };

    const calculateItemPrice = (item) => {
        if (item.is_carton && item.packs_per_carton > 1) {
            return item.carton_price_override || (
                (item.pack_price_override || (item.price * item.units_per_pack)) * item.packs_per_carton
            );
        }
        if (item.is_pack && item.units_per_pack > 1) {
            return item.pack_price_override || (item.price * item.units_per_pack);
        }
        return item.price;
    };

    const totalPrice = cart.reduce((sum, item) => sum + (calculateItemPrice(item) * item.quantity), 0);
    const changeDue = cashPaid ? Number(cashPaid) - totalPrice : 0;

    const handleCheckout = async () => {
        if (cart.length === 0) return;
        
        setError(null);
        if (paymentMethod === 'cash') {
            if (!cashPaid || cashPaid === '') {
                setError('Cash amount is required for this transaction.');
                return;
            }
            if (Number(cashPaid) < totalPrice) {
                setError(`Insufficient cash. The total is ₦${totalPrice.toLocaleString()}.`);
                return;
            }
        }

        setIsSubmitting(true);
        console.log('Finalizing sale...', { cart, paymentMethod, cashPaid });
        try {
            const response = await axios.post(route('checkout'), {
                items: cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity,
                    is_pack: item.is_pack,
                    is_carton: item.is_carton,
                    price: calculateItemPrice(item)
                })),
                payment_method: paymentMethod,
                cash_paid: Number(cashPaid) || totalPrice,
                total: totalPrice
            });

            if (response.data.success) {
                setSuccessData(response.data);
                // We keep the cart items temporarily for the receipt print preview
                // It will be cleared when the user clicks "Start New Sale"
            } else {
                setError(response.data.message || 'Checkout failed.');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'An error occurred during checkout.');
        } finally {
            setIsSubmitting(false);
        }
    };

    const startNewSale = () => {
        setSuccessData(null);
        setCart([]);
        setCashPaid('');
        setPaymentMethod('cash');
    };

    const handleProductClick = (product) => {
        if ((product.variations && product.variations.length > 0) || (product.units_per_pack > 1) || (product.packs_per_carton > 1)) {
            setSelectedProduct(product);
        } else {
            addToCart(product);
        }
    };

    const handleVariationSelect = (variation) => {
        addToCart(variation);
        setSelectedProduct(null);
    };

    return (
        <div className="h-[calc(100vh-80px)] overflow-hidden flex flex-col md:flex-row bg-[#F8FAFC] bg-gradient-premium relative">
            <Head title="Point Of Sale" />
            
            {/* Ambient Background Accents */}
            <div className="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] bg-blue-500/5 blur-[120px] rounded-full -z-10 animate-pulse"></div>
            <div className="absolute bottom-[-10%] right-[-10%] w-[40%] h-[60%] bg-orange-500/5 blur-[120px] rounded-full -z-10 animate-pulse" style={{ animationDelay: '2s' }}></div>

            {/* Left Side: Product Selection Section */}
            <div className="flex-1 flex flex-col p-10 min-w-0 relative">
                {/* Header Row */}
                <div className="mb-8 flex items-center justify-between gap-8">
                    <div className="space-y-1">
                        <div className="flex items-center gap-4">
                             <div className="h-14 w-14 bg-white rounded-[20px] flex items-center justify-center shadow-premium inner-glow overflow-hidden">
                                {branding?.logo ? (
                                    <img src={branding.logo} alt="Logo" className="w-full h-full object-cover" />
                                ) : (
                                    <ShoppingCart className="w-7 h-7 text-orange-600" />
                                )}
                             </div>
                             <div>
                                <h1 className="text-4xl font-black text-[#0F172A] tracking-tighter leading-none">
                                    Point Of Sale
                                </h1>
                                <p className="text-[#64748B] font-bold text-[10px] uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                                    <span className="w-2 h-2 rounded-full bg-emerald-500 shadow-glow-emerald"></span>
                                    Terminal 01 • {new Date().toLocaleDateString()}
                                </p>
                             </div>
                        </div>
                    </div>
                    
                    <div className="flex-1 max-w-2xl">
                        <div className="relative group/search">
                            <div className="absolute inset-0 bg-blue-500/5 blur-2xl group-focus-within/search:bg-orange-500/10 transition-all rounded-full"></div>
                            <Search className="w-6 h-6 absolute left-6 top-1/2 -translate-y-1/2 text-[#94A3B8] group-focus-within/search:text-orange-500 transition-colors" />
                            <input 
                                type="text" 
                                placeholder="Search products, SKUs..." 
                                value={searchTerm}
                                onChange={e => setSearchTerm(e.target.value)}
                                className="w-full pl-16 pr-8 py-6 rounded-full border-none glass-card focus:ring-8 focus:ring-orange-500/5 text-[#1E293B] font-bold placeholder-[#94A3B8] shadow-soft transition-all text-lg"
                            />
                        </div>
                    </div>
                </div>

                {/* Category Chips */}
                <div className="mb-10 flex items-center gap-4 overflow-x-auto pb-4 custom-scrollbar no-scrollbar scroll-smooth">
                    <button 
                        onClick={() => setSelectedCategory('All')}
                        className={`px-8 py-4 rounded-3xl font-black text-xs uppercase tracking-widest transition-all shrink-0 border-2 ${selectedCategory === 'All' ? 'bg-orange-600 border-orange-400 text-white shadow-xl shadow-orange-500/20' : 'bg-white/50 border-white/80 text-slate-500 hover:bg-white hover:border-orange-200'}`}
                    >
                        All Items
                    </button>
                    {categories?.map(cat => (
                        <button 
                            key={cat.id}
                            onClick={() => setSelectedCategory(cat.name)}
                            className={`px-8 py-4 rounded-3xl font-black text-xs uppercase tracking-widest transition-all shrink-0 border-2 ${selectedCategory === cat.name ? 'bg-orange-600 border-orange-400 text-white shadow-xl shadow-orange-500/20' : 'bg-white/50 border-white/80 text-slate-500 hover:bg-white hover:border-orange-200'}`}
                        >
                            {cat.name}
                        </button>
                    ))}
                </div>

                <div className="flex-1 overflow-y-auto pr-4 custom-scrollbar grid gap-6 pb-12 items-start" 
                     style={{ gridTemplateColumns: 'repeat(auto-fill, minmax(220px, 1fr))' }}>
                    {filteredProducts.map(product => (
                        <button 
                            key={product.id} 
                            onClick={() => handleProductClick(product)}
                            className="bg-white/40 glass-card rounded-[40px] p-2 hover:shadow-2xl hover:shadow-orange-500/10 hover:-translate-y-2 group transition-all duration-500 text-left flex flex-col active:scale-[0.97] inner-glow relative min-h-[340px]"
                        >
                            <div className="aspect-[5/4] bg-white/80 rounded-[34px] flex items-center justify-center relative overflow-hidden group-hover:bg-white transition-colors duration-500 shadow-sm shrink-0">
                                <span className="text-6xl transform group-hover:scale-125 transition-transform duration-700 drop-shadow-md">
                                    {product.category?.toLowerCase().includes('drink') ? '🥤' : 
                                     product.category?.toLowerCase().includes('fruit') ? '🍎' : 
                                     product.category?.toLowerCase().includes('snack') ? '🍪' : '🥫'}
                                </span>
                                
                                {product.variations?.length > 0 && (
                                    <div className="absolute bottom-4 left-4 bg-white/95 backdrop-blur px-3 py-1.5 rounded-2xl shadow-lg border border-orange-100 flex items-center gap-2 animate-in slide-in-from-bottom-2">
                                        <div className="flex items-center justify-center w-5 h-5 bg-orange-100 rounded-full text-orange-600">
                                            <Plus className="w-3 h-3" />
                                        </div>
                                        <span className="text-[10px] font-black text-slate-900 uppercase tracking-widest leading-none">
                                            {product.variations.length} Variants
                                        </span>
                                    </div>
                                )}
                            </div>

                            <div className="p-6 flex flex-col flex-1">
                                <div className="mb-4">
                                    <div className="flex items-center gap-2 mb-1.5">
                                        <span className="px-2 py-0.5 rounded-lg bg-slate-100 text-[8px] font-black text-slate-400 uppercase tracking-tighter">
                                            {product.category || 'General'}
                                        </span>
                                    </div>
                                    <h3 className="font-black text-[#1E293B] text-lg leading-tight line-clamp-2 min-h-[3.5rem] group-hover:text-orange-600 transition-colors capitalize">
                                        {product.name}
                                    </h3>
                                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest opacity-70">
                                        {product.sku || 'N/A'}
                                    </p>
                                </div>

                                <div className="mt-auto flex items-end justify-between gap-2">
                                    <div className="flex flex-col">
                                        <span className="text-[10px] text-slate-400 font-black uppercase tracking-widest leading-none mb-1.5">Price</span>
                                        <span className="font-black text-[#0F172A] text-2xl leading-none tracking-tighter whitespace-nowrap">
                                            <span className="text-orange-500 text-sm mr-1">₦</span>
                                            {Math.floor(product.price).toLocaleString()}
                                        </span>
                                    </div>
                                    <div className={`px-3 py-2 rounded-2xl text-right transition-all border shrink-0 ${product.quantity <= 10 ? 'bg-orange-50 border-orange-100 text-orange-600 shadow-glow-orange' : 'bg-slate-50 border-slate-100 text-slate-400'}`}>
                                        <p className="text-[8px] font-black uppercase tracking-[0.2em] leading-none mb-1.5">Stock</p>
                                        <p className="text-sm font-black leading-none">{product.quantity}</p>
                                    </div>
                                </div>
                            </div>
                        </button>
                    ))}
                </div>
                {filteredProducts.length === 0 && (
                    <div className="flex-1 flex items-center justify-center">
                        <TablePlaceholder 
                            title={searchTerm ? "No products found" : "Category is empty"}
                            description={searchTerm 
                                ? `We couldn't find any products matching "${searchTerm}".` 
                                : "There are no products available in this category at the moment."}
                            icon="🛒"
                        />
                    </div>
                )}
            </div>

            {/* Right Side: Enhanced Collapsible Checkout Sidebar */}
            <div className={`relative transition-all duration-700 cubic-bezier(0.4, 0, 0.2, 1) glass-dark flex flex-col z-20 shadow-2xl ${isSidebarCollapsed ? 'w-[80px]' : 'w-full md:w-[480px]'}`}>
                {/* Visual Glow Accent */}
                <div className="absolute top-[10%] right-[-20%] w-[300px] h-[300px] bg-orange-500/10 blur-[100px] rounded-full -z-10 animate-pulse"></div>

                {/* Toggle Button */}
                <button 
                    onClick={() => setIsSidebarCollapsed(!isSidebarCollapsed)}
                    className="absolute -left-5 top-1/2 -translate-y-1/2 w-10 h-24 bg-[#1E293B] border border-white/10 rounded-2xl flex items-center justify-center text-white hover:bg-orange-600 transition-all z-30 shadow-2xl active:scale-90"
                >
                    <ChevronRight className={`w-7 h-7 transition-all duration-700 ${isSidebarCollapsed ? '' : 'rotate-180'}`} />
                </button>

                {isSidebarCollapsed ? (
                    /* Collapsed Icon-Only Mode */
                    <div className="h-full flex flex-col items-center py-12 space-y-10">
                        <div 
                           onClick={() => setIsSidebarCollapsed(false)}
                           className="h-16 w-16 bg-white/5 rounded-[24px] flex items-center justify-center text-orange-500 border border-white/10 relative cursor-pointer hover:bg-white/10 transition-all shadow-glow-orange"
                        >
                            <ShoppingCart className="w-8 h-8" />
                            {cart.length > 0 && (
                                <span className="absolute -top-2 -right-2 bg-orange-600 text-white text-[10px] font-black px-2.5 py-1.5 rounded-full border-2 border-[#111827] shadow-2xl animate-bounce">
                                    {cart.length}
                                </span>
                            )}
                        </div>
                        <div className="flex-1 flex flex-col items-center gap-4 overflow-y-auto w-full px-2 custom-scrollbar no-scrollbar">
                            {cart.map((item, idx) => (
                                idx < 8 && (
                                    <div key={item.id} className="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-2xl border border-white/5 animate-in fade-in zoom-in duration-500 hover:scale-110 transition-transform cursor-help shadow-inner">
                                        {item.category?.toLowerCase().includes('drink') ? '🥤' : 
                                         item.category?.toLowerCase().includes('fruit') ? '🍎' : '🥫'}
                                    </div>
                                )
                            ))}
                            {cart.length > 8 && <span className="text-slate-500 font-bold text-xs">+{cart.length - 8}</span>}
                        </div>
                        <div className="mt-auto pb-10 flex flex-col items-center gap-6 w-full">
                             <div className="w-10 h-1 bg-white/10 rounded-full"></div>
                             {cart.length > 0 && (
                                 <button 
                                    onClick={() => setIsSidebarCollapsed(false)}
                                    className="w-14 h-14 bg-orange-600 rounded-2xl flex items-center justify-center text-white shadow-glow-orange active:scale-90 transition-all"
                                 >
                                    <CreditCard className="w-6 h-6" />
                                 </button>
                             )}
                        </div>
                    </div>
                ) : (
                    /* Full Mode - Restored & Fully Functional */
                    <>
                        <div className="p-6 border-b border-white/10 relative overflow-hidden">
                            <div className="absolute top-0 right-0 w-32 h-32 bg-orange-500/5 blur-3xl rounded-full"></div>
                            <div className="flex items-center justify-between relative z-10">
                                <div>
                                    <h2 className="text-xl font-black text-white tracking-tighter leading-none">
                                        Checkout
                                    </h2>
                                    <div className="flex items-center gap-3 mt-2">
                                        <p className="text-slate-500 text-[9px] font-black uppercase tracking-[0.2em] flex items-center gap-1.5">
                                           <Receipt className="w-3 h-3 text-orange-500" /> ID-{new Date().getSeconds()}
                                        </p>
                                        <button 
                                            onClick={clearCart}
                                            className="text-rose-500/70 hover:text-rose-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-1 transition-colors"
                                        >
                                            <Trash2 className="w-2.5 h-2.5" /> Clear
                                        </button>
                                    </div>
                                </div>
                                <div className="h-10 w-10 bg-white/5 rounded-xl flex items-center justify-center text-orange-500 border border-white/10 relative shadow-inner">
                                    <ShoppingCart className="w-5 h-5" />
                                    {cart.length > 0 && (
                                        <span className="absolute -top-1.5 -right-1.5 bg-orange-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full border border-slate-900 shadow-xl">
                                            {cart.length}
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar scrollbar-dark">
                            {error && (
                                <div className="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-center gap-3 text-rose-400 text-xs font-bold animate-in slide-in-from-top-4 mb-4">
                                    <AlertCircle className="w-4 h-4 shrink-0" />
                                    <span>{error}</span>
                                    <button onClick={() => setError(null)} className="ml-auto opacity-50 hover:opacity-100"><X className="w-3 h-3" /></button>
                                </div>
                            )}

                            {cart.length === 0 ? (
                                <div className="h-full flex flex-col items-center justify-center text-center py-10">
                                    <TablePlaceholder 
                                        title="Cart is empty"
                                        description="Start adding items to complete a sale."
                                        icon="🛒"
                                    />
                                </div>
                            ) : (
                                cart.map((item, index) => (
                                    <div key={`${item.id}-${index}`} 
                                        className="flex gap-4 p-4 rounded-[28px] bg-white/[0.03] border border-white/5 group hover:bg-white/[0.08] transition-all relative overflow-hidden animate-in slide-in-from-right duration-300"
                                        style={{ animationDelay: `${index * 30}ms` }}
                                    >
                                        <div className="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-2xl shrink-0 border border-white/5 shadow-inner">
                                            {item.category?.toLowerCase().includes('drink') ? '🥤' : 
                                             item.category?.toLowerCase().includes('fruit') ? '🍎' : '🥫'}
                                        </div>
                                        <div className="flex-1 min-w-0 flex flex-col justify-center">
                                            <div className="flex items-start justify-between gap-3">
                                                <div className="min-w-0 overflow-hidden">
                                                    <p className="font-black text-xs text-white truncate leading-tight group-hover:text-orange-400 transition-colors uppercase">{item.name}</p>
                                                    <p className="text-[8px] text-slate-500 font-black tracking-widest uppercase mt-0.5 opacity-60">{item.sku || 'GEN'}</p>
                                                </div>
                                                <button 
                                                    onClick={() => removeFromCart(item.id)}
                                                    className="text-slate-600 hover:text-rose-500 p-1.5 transition-all shrink-0"
                                                >
                                                    <X className="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                            <div className="flex items-center justify-between mt-3">
                                                <p className="text-sm font-black text-white">
                                                    <span className="text-orange-500 text-[10px] mr-1">₦</span>
                                                    {Number(calculateItemPrice(item)).toLocaleString()}
                                                </p>
                                                
                                                <div className="flex items-center gap-3 bg-black/50 px-2 py-1 rounded-xl border border-white/5">
                                                    <button 
                                                        onClick={() => updateQuantity(item.id, -1)}
                                                        className="w-6 h-6 rounded-lg text-slate-500 hover:text-white flex items-center justify-center transition-all bg-white/5"
                                                    ><Minus className="w-3 h-3" /></button>
                                                    <span className="font-black text-xs text-white w-4 text-center">{item.quantity}</span>
                                                    <button 
                                                        onClick={() => updateQuantity(item.id, 1)}
                                                        className="w-6 h-6 rounded-lg text-slate-500 hover:text-white flex items-center justify-center transition-all bg-white/5"
                                                    ><Plus className="w-3 h-3" /></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>

                        {/* Finalize Section - The Core "Missing" part being refined */}
                        <div className="p-6 bg-black/40 border-t border-white/10 space-y-4 backdrop-blur-3xl relative">
                            <div className="flex justify-between items-center group/total cursor-pointer">
                                <span className="text-white font-black text-sm tracking-widest uppercase opacity-60">Total Due</span>
                                <span className="text-3xl font-black text-white text-glow-orange tracking-tighter">
                                    <span className="text-orange-600 text-base mr-1.5 font-black">₦</span>
                                    {totalPrice.toLocaleString()}
                                </span>
                            </div>

                            <div className="space-y-4">
                                <div className="grid grid-cols-3 gap-2 p-1.5 bg-white/5 rounded-2xl border border-white/5">
                                    {[
                                        { id: 'cash', label: 'CASH', icon: Banknote },
                                        { id: 'transfer', label: 'BANK', icon: ArrowRightLeft },
                                        { id: 'card', label: 'CARD', icon: CreditCard }
                                    ].map((m) => (
                                        <button 
                                            key={m.id} 
                                            onClick={() => setPaymentMethod(m.id)}
                                            className={`flex items-center justify-center gap-2 py-3 rounded-xl transition-all border ${paymentMethod === m.id ? 'bg-orange-600 border-orange-400 text-white shadow-lg' : 'bg-transparent border-transparent text-slate-500 hover:text-slate-300'}`}
                                        >
                                            <m.icon className="w-4 h-4" />
                                            <span className="text-[8px] font-black uppercase tracking-widest">{m.label}</span>
                                        </button>
                                    ))}
                                </div>

                                {paymentMethod === 'cash' && (
                                    <div className="relative animate-in slide-in-from-bottom-2">
                                        <span className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 font-black text-sm">₦</span>
                                        <input 
                                            type="number" 
                                            placeholder="Amount Received" 
                                            value={cashPaid}
                                            onChange={e => setCashPaid(e.target.value)}
                                            className="w-full pl-10 pr-4 py-4 rounded-2xl bg-white/5 border-white/10 text-white font-black focus:ring-2 focus:ring-orange-500/20 text-lg outline-none"
                                        />
                                        {cashPaid && (
                                            <div className={`mt-2 flex items-center gap-2 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest ${changeDue >= 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'}`}>
                                                {changeDue >= 0 ? `Change: ₦${changeDue.toLocaleString()}` : `Short: ₦${Math.abs(changeDue).toLocaleString()}`}
                                            </div>
                                        )}
                                    </div>
                                )}

                                <button 
                                    disabled={cart.length === 0 || isSubmitting}
                                    onClick={handleCheckout}
                                    className={`w-full py-4 rounded-2xl font-black text-white text-base tracking-[0.2em] transition-all relative overflow-hidden group/pay active:scale-95 ${
                                        cart.length === 0 || isSubmitting 
                                        ? 'bg-white/5 cursor-not-allowed text-slate-700' 
                                        : 'bg-orange-600 hover:bg-orange-500 shadow-lg'
                                    }`}
                                >
                                    {isSubmitting ? 'PROCESSING...' : 'FINALIZE SALE'}
                                </button>
                            </div>
                        </div>
                    </>
                )}
            </div>

            {/* Variation Selection Modal */}
            {selectedProduct && (
                <div className="fixed inset-0 z-[100] flex items-center justify-center p-8 bg-slate-950/90 backdrop-blur-3xl animate-in fade-in duration-700">
                    <div className="bg-white w-full max-w-2xl rounded-[64px] shadow-premium overflow-hidden animate-in zoom-in-95 duration-500 border border-white/20">
                        <div className="p-14 border-b border-slate-100 bg-gradient-to-br from-orange-50/50 to-blue-50/50 relative">
                            <button 
                                onClick={() => setSelectedProduct(null)}
                                className="absolute top-8 right-8 p-5 bg-white/80 backdrop-blur hover:bg-rose-50 rounded-[28px] text-slate-400 hover:text-rose-500 shadow-xl transition-all border border-white group"
                            >
                                <X className="w-6 h-6 group-hover:rotate-90 transition-transform duration-500" />
                            </button>
                            
                            <p className="text-orange-600 font-black text-[11px] uppercase tracking-[0.6em] mb-4">Package/Variation Selector</p>
                            <h2 className="text-4xl font-black text-[#0F172A] tracking-tighter leading-tight pr-12">{selectedProduct.name}</h2>
                            <div className="flex items-center gap-4 mt-6">
                                <span className="px-5 py-2.5 bg-white shadow-sm border border-slate-200 rounded-2xl text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                    REF: {selectedProduct.sku || 'N/A'}
                                </span>
                                <span className="px-5 py-2.5 bg-emerald-500/10 text-emerald-600 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                                    Availability: {selectedProduct.quantity} UNITS
                                </span>
                            </div>
                        </div>

                        <div className="p-12 max-h-[50vh] overflow-y-auto custom-scrollbar bg-[#F8FAFC] space-y-5">
                            <div className="grid grid-cols-1 gap-4">
                                {/* Base Option */}
                                <button 
                                    onClick={() => handleVariationSelect({ ...selectedProduct, is_pack: false, is_carton: false })}
                                    className="p-7 bg-white rounded-[44px] border-2 border-transparent hover:border-orange-500 transition-all flex items-center justify-between group shadow-premium hover:shadow-2xl"
                                >
                                    <div className="flex items-center gap-6">
                                        <div className="w-18 h-18 bg-slate-50 rounded-3xl flex items-center justify-center text-4xl group-hover:bg-orange-50 transition-colors duration-500 shadow-inner">📦</div>
                                        <div className="text-left">
                                            <p className="font-black text-2xl text-[#0F172A]">Standard Unit ({selectedProduct.unit_name || 'Piece'})</p>
                                            <p className="text-xs text-[#94A3B8] font-bold uppercase tracking-widest mt-1">Single product sale</p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className="text-4xl font-black text-[#0F172A] tracking-tighter mb-1">₦{Math.floor(selectedProduct.price).toLocaleString()}</p>
                                        <span className="text-[10px] text-emerald-500 font-black uppercase tracking-widest bg-emerald-50 px-3 py-1.5 rounded-xl">Available</span>
                                    </div>
                                </button>

                                {/* Pack Option if applicable */}
                                {selectedProduct.units_per_pack > 1 && (
                                    <button 
                                        onClick={() => handleVariationSelect({ ...selectedProduct, is_pack: true, is_carton: false })}
                                        className="p-7 bg-white rounded-[44px] border-2 border-transparent hover:border-orange-500 transition-all flex items-center justify-between group shadow-premium hover:shadow-2xl animate-in slide-in-from-bottom-4"
                                    >
                                        <div className="flex items-center gap-6">
                                            <div className="w-18 h-18 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center text-4xl group-hover:bg-blue-500 group-hover:text-white transition-all duration-700 shadow-inner">🎁</div>
                                            <div className="text-left">
                                                <p className="font-black text-2xl text-[#0F172A]">Full Pack (x{selectedProduct.units_per_pack})</p>
                                                <p className="text-xs text-blue-600 font-black uppercase tracking-widest mt-1">Bulk units</p>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-4xl font-black text-[#0F172A] tracking-tighter mb-1">
                                                ₦{Math.floor(selectedProduct.pack_price_override || (selectedProduct.price * selectedProduct.units_per_pack)).toLocaleString()}
                                            </p>
                                            <span className="text-[10px] text-blue-500 font-black uppercase tracking-widest bg-blue-50 px-3 py-1.5 rounded-xl">Bulk Rate</span>
                                        </div>
                                    </button>
                                )}

                                {/* Carton Option if applicable */}
                                {selectedProduct.packs_per_carton > 1 && (
                                    <button 
                                        onClick={() => handleVariationSelect({ ...selectedProduct, is_pack: false, is_carton: true })}
                                        className="p-7 bg-white rounded-[44px] border-2 border-transparent hover:border-orange-500 transition-all flex items-center justify-between group shadow-premium hover:shadow-2xl animate-in slide-in-from-bottom-8"
                                    >
                                        <div className="flex items-center gap-6">
                                            <div className="w-18 h-18 bg-orange-50 text-orange-600 rounded-3xl flex items-center justify-center text-4xl group-hover:bg-orange-600 group-hover:text-white transition-all duration-700 shadow-inner">🚛</div>
                                            <div className="text-left">
                                                <p className="font-black text-2xl text-[#0F172A]">Full Carton (x{selectedProduct.packs_per_carton} packs)</p>
                                                <p className="text-xs text-orange-600 font-black uppercase tracking-widest mt-1">Wholesale rate</p>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-4xl font-black text-[#0F172A] tracking-tighter mb-1">
                                                ₦{Math.floor(
                                                    selectedProduct.carton_price_override || (
                                                        (selectedProduct.pack_price_override || (selectedProduct.price * selectedProduct.units_per_pack)) * selectedProduct.packs_per_carton
                                                    )
                                                ).toLocaleString()}
                                            </p>
                                            <span className="text-[10px] text-orange-500 font-black uppercase tracking-widest bg-orange-50 px-3 py-1.5 rounded-xl">Wholesale</span>
                                        </div>
                                    </button>
                                )}

                                {/* Variants */}
                                {selectedProduct.variations.map((variation, vidx) => (
                                    <button 
                                        key={variation.id}
                                        onClick={() => handleVariationSelect(variation)}
                                        className="p-7 bg-white rounded-[44px] border-2 border-transparent hover:border-orange-500 transition-all flex items-center justify-between group shadow-premium hover:shadow-2xl animate-in slide-in-from-bottom-8 duration-500"
                                        style={{ animationDelay: `${vidx * 100}ms` }}
                                    >
                                        <div className="flex items-center gap-6">
                                            <div className="w-18 h-18 bg-orange-50 rounded-3xl flex items-center justify-center text-4xl group-hover:bg-orange-500 group-hover:text-white transition-all duration-700 shadow-inner">✨</div>
                                            <div className="text-left">
                                                <p className="font-black text-2xl text-[#0F172A]">{variation.variation_name || variation.name}</p>
                                                <p className="text-xs text-orange-600 font-black uppercase tracking-widest mt-1">{variation.unit_name || 'Individual Variant'}</p>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-4xl font-black text-[#0F172A] tracking-tighter mb-1">₦{Math.floor(variation.price).toLocaleString()}</p>
                                            <span className="text-[10px] text-slate-400 font-black uppercase tracking-widest bg-slate-50 px-3 py-1.5 rounded-xl">Stock: {variation.quantity}</span>
                                        </div>
                                    </button>
                                ))}
                            </div>
                        </div>
                        
                        <div className="p-12 bg-white border-t border-slate-100 flex justify-center">
                            <button 
                                onClick={() => setSelectedProduct(null)}
                                className="px-16 py-6 rounded-full bg-slate-100 text-slate-500 font-black text-xs tracking-[0.5em] hover:bg-slate-900 hover:text-white transition-all duration-500 active:scale-95 uppercase"
                            >
                                Cancel Selection
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Success Modal - Refined Data Handling */}
            {successData && (
                <div className="fixed inset-0 z-[120] flex items-center justify-center p-8 bg-slate-950/95 backdrop-blur-3xl animate-in fade-in duration-700">
                    <div className="bg-white w-full max-w-lg rounded-[72px] shadow-premium overflow-hidden p-16 text-center animate-in zoom-in-90 duration-700 border border-white/20 relative">
                        {/* Sparkle effects */}
                        <div className="absolute top-10 right-10 w-4 h-4 bg-orange-400 rounded-full animate-ping"></div>
                        <div className="absolute bottom-20 left-10 w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>

                        <div className="w-36 h-36 bg-emerald-500 rounded-[54px] flex items-center justify-center mx-auto mb-12 shadow-glow-emerald rotate-12 transition-transform hover:rotate-0 duration-1000">
                            <CheckCircle2 className="w-20 h-20 text-white" />
                        </div>
                        <h2 className="text-6xl font-black text-slate-900 tracking-tighter mb-4 leading-none uppercase">Sale Complete</h2>
                        <p className="text-slate-500 font-bold text-xl leading-relaxed max-w-[320px] mx-auto opacity-80">
                            Transaction authorized. {successData.payment_method === 'cash' && 'Cash Drawer Opened.'} Inventory stock has been deducted.
                        </p>
                        
                        <div className="my-14 p-10 bg-slate-50 rounded-[48px] border-2 border-slate-100 space-y-6 relative overflow-hidden">
                            <div className="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 blur-2xl rounded-full"></div>
                            <div className="flex justify-between items-center px-2">
                                <span className="text-slate-400 font-black text-xs uppercase tracking-[0.2em]">Sale Receipt</span>
                                <span className="font-black text-slate-900 text-base">#{successData.receipt_number}</span>
                            </div>
                            <div className="h-px bg-slate-200/60 w-full"></div>
                            <div className="flex justify-between items-center px-2">
                                <span className="text-slate-400 font-black text-xs uppercase tracking-[0.2em]">Total Amount</span>
                                <span className="text-4xl font-black text-orange-600 tracking-tighter">₦{Number(successData.total_price).toLocaleString()}</span>
                            </div>
                        </div>

                        <div className="space-y-5">
                            <button 
                                onClick={() => window.print()}
                                className="w-full py-8 rounded-[40px] bg-slate-900 text-white font-black text-xl tracking-[0.2em] hover:bg-orange-600 shadow-premium transition-all active:scale-95 flex items-center justify-center gap-5 group"
                            >
                                <Receipt className="w-7 h-7 group-hover:animate-bounce" />
                                PRINT RECEIPT
                            </button>
                            <button 
                                onClick={startNewSale}
                                className="w-full py-7 rounded-[40px] bg-white text-slate-400 font-black text-xs tracking-[0.5em] hover:text-orange-600 transition-all border-2 border-slate-100 hover:border-orange-200 uppercase"
                            >
                                Start New Sale
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Print Container */}
            <div className="hidden print:block fixed inset-0 bg-white z-[9999] p-8 text-black font-mono">
                <div className="print-container p-4 w-[80mm] mx-auto text-sm leading-relaxed translate-x-0">
                    <style dangerouslySetInnerHTML={{ __html: `
                        @media print {
                            body * { visibility: hidden; }
                            .print-container, .print-container * { visibility: visible; }
                            .print-container { position: absolute; left: 0; top: 0; width: 80mm; font-family: 'Courier New', Courier, monospace; }
                            @page { margin: 0; size: 80mm auto; }
                        }
                    `}} />
                    <div className="text-center mb-8">
                        <h1 className="text-2xl font-black uppercase tracking-tighter">{branding?.name || 'ENAPEL STORE'}</h1>
                        <p className="text-[11px] font-bold mt-2">{branding?.address || 'Terminal Branch 01'}</p>
                        <p className="text-[10px] mt-1">{branding?.phone || '+234 POS SYSTEM'}</p>
                        <p className="text-[10px] mt-4 font-black">DATE: {new Date().toLocaleString()}</p>
                        <div className="border-b-2 border-dashed border-black mt-6"></div>
                    </div>
                    
                    <div className="space-y-4 mb-8">
                        <div className="flex justify-between font-black text-[12px] border-b border-black pb-2">
                            <span className="w-1/2">ITEM</span>
                            <span className="w-1/6 text-center">QTY</span>
                            <span className="w-1/3 text-right">PRICE</span>
                        </div>
                        {(successData?.cart_items || cart).map((item, i) => (
                            <div key={i} className="flex justify-between items-start text-[11px] font-bold py-1">
                                <span className="w-1/2 leading-tight uppercase">
                                    {(item.inventory?.name || item.product_name || item.name)}
                                    {item.is_pack && <span className="block text-[9px] opacity-60 italic">(PACK)</span>}
                                    {item.is_carton && <span className="block text-[9px] opacity-60 italic">(CARTON)</span>}
                                </span>
                                <span className="w-1/6 text-center font-black">x{item.quantity}</span>
                                <span className="w-1/3 text-right">
                                    ₦{Number(item.price * item.quantity).toLocaleString()}
                                </span>
                            </div>
                        ))}
                    </div>

                    <div className="border-t-2 border-dashed border-black pt-6 space-y-3">
                        <div className="flex justify-between text-xl font-black tracking-tighter">
                            <span>TOTAL DUE</span>
                            <span>₦{Number(successData?.total_price || totalPrice).toLocaleString()}</span>
                        </div>
                        <div className="flex justify-between text-[11px] font-bold uppercase pt-2">
                            <span>PAYMENT VIA</span>
                            <span>{successData?.payment_method || paymentMethod}</span>
                        </div>
                        <div className="flex justify-between text-[11px] font-bold">
                            <span>AMOUNT PAID</span>
                            <span>₦{Number(successData?.cash_paid || cashPaid || totalPrice).toLocaleString()}</span>
                        </div>
                        {changeDue > 0 && (
                            <div className="flex justify-between text-[11px] font-black italic">
                                <span>CHANGE REFUNDED</span>
                                <span>₦{changeDue.toLocaleString()}</span>
                            </div>
                        )}
                        
                        <div className="text-center mt-12 pt-6 border-t border-dotted border-black">
                            <p className="font-black text-xs">RECEIPT: #{successData?.receipt_number || 'TEMP-REF'}</p>
                            <div className="mt-6 font-black uppercase text-[10px] tracking-[0.3em] text-center">
                                * * * THANK YOU * * *
                                <p className="mt-3 text-[8px] font-bold italic opacity-60">System by Hubolux Technologies</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

Pos.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
