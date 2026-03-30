import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import axios from 'axios';

import { usePage } from '@inertiajs/react';

export default function Pos({ products }) {
    const { branding } = usePage().props;
    const [searchTerm, setSearchTerm] = useState('');
    const [cart, setCart] = useState([]);
    const [paymentMethod, setPaymentMethod] = useState('cash');
    const [cashPaid, setCashPaid] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [successData, setSuccessData] = useState(null);
    const [error, setError] = useState(null);

    const filteredProducts = products.filter(p => 
        (p.product_name?.toLowerCase() || p.name?.toLowerCase() || '').includes(searchTerm.toLowerCase()) ||
        (p.description?.toLowerCase() || '').includes(searchTerm.toLowerCase()) ||
        (p.sku?.toLowerCase() || '').includes(searchTerm.toLowerCase())
    );

    // Barcode scanner logic
    useEffect(() => {
        let scannerBuffer = '';
        let lastKeyTime = Date.now();

        const handleKeyDown = (e) => {
            const currentTime = Date.now();
            
            // If the time between key presses is very short, it's likely a scanner
            if (currentTime - lastKeyTime > 50) {
                scannerBuffer = ''; // Reset buffer if typing is slow
            }

            lastKeyTime = currentTime;

            if (e.key === 'Enter') {
                if (scannerBuffer.length > 2) {
                    const product = products.find(p => p.sku === scannerBuffer);
                    if (product) {
                        addToCart(product);
                        setSearchTerm(''); // Clear search if they were typing
                    }
                }
                scannerBuffer = '';
                return;
            }

            if (e.key.length === 1) {
                scannerBuffer += e.key;
            }
        };

        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [products, cart]);

    const addToCart = (product) => {
        const existing = cart.find(item => item.id === product.id);
        if (existing) {
            if (existing.quantity + 1 > product.quantity) {
                 setError(`Only ${product.quantity} items available in stock.`);
                 return;
            }
            setCart(cart.map(item => 
                item.id === product.id ? { ...item, quantity: item.quantity + 1 } : item
            ));
        } else {
            setCart([...cart, { ...product, quantity: 1 }]);
        }
        setError(null);
    };

    const removeFromCart = (id) => {
        setCart(cart.filter(item => item.id !== id));
    };

    const updateQuantity = (id, delta) => {
        setCart(cart.map(item => {
            if (item.id === id) {
                const newQty = Math.max(1, item.quantity + delta);
                // In a real app we'd check against product.quantity here too
                return { ...item, quantity: newQty };
            }
            return item;
        }));
    };

    const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const changeDue = paymentMethod === 'cash' && cashPaid ? parseFloat(cashPaid) - totalPrice : 0;

    const handleCheckout = async () => {
        if (cart.length === 0) return;
        if (paymentMethod === 'cash' && (!cashPaid || parseFloat(cashPaid) < totalPrice)) {
            setError('Insufficient cash amount.');
            return;
        }

        setIsSubmitting(true);
        setError(null);

        try {
            const response = await axios.post(route('checkout'), {
                items: cart.map(item => ({ id: item.id, quantity: item.quantity })),
                payment_method: paymentMethod,
                cash_paid: cashPaid ? parseFloat(cashPaid) : null
            });

            if (response.data.success) {
                setSuccessData(response.data);
                setCart([]);
                setCashPaid('');
            } else {
                setError(response.data.message || 'Checkout failed.');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'An error occurred during checkout.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div className="h-[calc(100vh-80px)] overflow-hidden flex flex-col md:flex-row bg-gray-50">
            <Head title="Supermart POS" />

            {/* Left Side: Product Selection */}
            <div className="flex-1 flex flex-col p-6 min-w-0">
                <div className="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h1 className="text-3xl font-black text-gray-900 tracking-tight">Supermart POS</h1>
                        <p className="text-gray-500 font-medium">Select items to add to cart</p>
                    </div>
                    <div className="relative w-full max-w-md">
                        <svg className="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input 
                            type="text" 
                            placeholder="Search products..." 
                            value={searchTerm}
                            onChange={e => setSearchTerm(e.target.value)}
                            className="w-full pl-10 pr-4 py-3 rounded-2xl border-none shadow-sm focus:ring-2 focus:ring-blue-500 bg-white"
                        />
                    </div>
                </div>

                <div className="flex-1 overflow-y-auto pr-2 custom-scrollbar grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 pb-6">
                    {filteredProducts.map(product => (
                        <button 
                            key={product.id} 
                            onClick={() => addToCart(product)}
                            className="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-orange-500/10 hover:border-orange-200 transition-all text-left flex flex-col group active:scale-95"
                        >
                            <div className="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center mb-4 group-hover:bg-orange-500 group-hover:text-white transition-colors">
                                🥫
                            </div>
                            <div className="flex items-center gap-2 mb-1 w-full">
                                <h3 className="font-bold text-gray-900 truncate flex-1">{product.product_name || product.name}</h3>
                                {product.sku && (
                                    <span className="text-[8px] bg-gray-900 text-white px-1 py-0.5 rounded font-mono uppercase shrink-0">
                                        {product.sku}
                                    </span>
                                )}
                            </div>
                            <p className="text-xs text-gray-400 mb-3 truncate">{product.description || 'Retail Item'}</p>
                            <div className="mt-auto flex items-center justify-between">
                                <span className="font-black text-lg text-gray-900">₦{Number(product.price).toLocaleString()}</span>
                                <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${product.quantity <= 10 ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600'}`}>
                                    {product.quantity} in stock
                                </span>
                            </div>
                        </button>
                    ))}
                </div>
            </div>

            {/* Right Side: Cart & Checkout */}
            <div className="w-full md:w-[400px] bg-white border-l border-gray-100 flex flex-col shadow-2xl">
                <div className="p-6 border-b border-gray-50">
                    <h2 className="text-xl font-black text-gray-900 flex items-center gap-2">
                        <span>🛒</span> Checkout
                        <span className="ml-auto bg-orange-50 text-orange-600 text-xs px-2 py-1 rounded-full">{cart.length} items</span>
                    </h2>
                </div>

                <div className="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
                    {cart.length === 0 ? (
                        <div className="h-full flex flex-col items-center justify-center text-center opacity-40 py-20 px-10">
                            <span className="text-6xl mb-4">🛒</span>
                            <p className="font-bold text-gray-500">Cart is empty</p>
                            <p className="text-xs">Select products to begin</p>
                        </div>
                    ) : (
                        cart.map(item => (
                            <div key={item.id} className="flex gap-3 p-3 rounded-2xl bg-gray-50 border border-gray-100 group animate-in slide-in-from-right-4">
                                <div className="flex-1 min-w-0">
                                    <p className="font-bold text-sm text-gray-900 truncate">{item.product_name || item.name}</p>
                                    <p className="text-xs text-orange-600 font-bold">₦{Number(item.price).toLocaleString()}</p>
                                    
                                    <div className="flex items-center gap-3 mt-2">
                                        <button 
                                            onClick={() => updateQuantity(item.id, -1)}
                                            className="h-7 w-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100"
                                        >-</button>
                                        <span className="font-black text-sm w-4 text-center">{item.quantity}</span>
                                        <button 
                                            onClick={() => updateQuantity(item.id, 1)}
                                            className="h-7 w-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100"
                                        >+</button>
                                    </div>
                                </div>
                                <div className="text-right flex flex-col justify-between items-end">
                                    <button 
                                        onClick={() => removeFromCart(item.id)}
                                        className="text-gray-300 hover:text-red-500 p-1"
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                    <p className="font-black text-gray-900">₦{(item.price * item.quantity).toLocaleString()}</p>
                                </div>
                            </div>
                        ))
                    )}
                </div>

                <div className="p-6 bg-gray-50 border-t border-gray-100 space-y-4">
                    {/* Payment Summary */}
                    <div className="space-y-2">
                        <div className="flex justify-between text-gray-500 text-sm font-medium">
                            <span>Subtotal</span>
                            <span>₦{totalPrice.toLocaleString()}</span>
                        </div>
                        <div className="flex justify-between text-gray-900 text-xl font-black">
                            <span>Total</span>
                            <span className="text-orange-600">₦{totalPrice.toLocaleString()}</span>
                        </div>
                    </div>

                    {/* Checkout Options */}
                    <div className="pt-4 space-y-4">
                        <div className="flex gap-2 p-1 bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <button 
                                onClick={() => setPaymentMethod('cash')}
                                className={`flex-1 py-2 text-xs font-bold rounded-lg transition-all ${paymentMethod === 'cash' ? 'bg-orange-600 text-white shadow-lg' : 'text-gray-500'}`}
                            >CASH</button>
                            <button 
                                onClick={() => setPaymentMethod('transfer')}
                                className={`flex-1 py-2 text-xs font-bold rounded-lg transition-all ${paymentMethod === 'transfer' ? 'bg-orange-600 text-white shadow-lg' : 'text-gray-500'}`}
                            >TRANSFER</button>
                            <button 
                                onClick={() => setPaymentMethod('card')}
                                className={`flex-1 py-2 text-xs font-bold rounded-lg transition-all ${paymentMethod === 'card' ? 'bg-orange-600 text-white shadow-lg' : 'text-gray-500'}`}
                            >CARD</button>
                        </div>

                        {paymentMethod === 'cash' && (
                            <div className="relative animate-in fade-in slide-in-from-top-2">
                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₦</span>
                                <input 
                                    type="number" 
                                    placeholder="Amount received" 
                                    value={cashPaid}
                                    onChange={e => setCashPaid(e.target.value)}
                                    className="w-full pl-8 pr-4 py-3 rounded-xl border-gray-200 focus:ring-orange-500"
                                />
                                {cashPaid && (
                                    <p className={`text-[10px] mt-2 font-bold px-2 py-1 rounded ${changeDue >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'}`}>
                                        {changeDue >= 0 ? `Change Due: ₦${changeDue.toLocaleString()}` : `Shortage: ₦${Math.abs(changeDue).toLocaleString()}`}
                                    </p>
                                )}
                            </div>
                        )}

                        {error && <p className="text-red-500 text-xs font-bold text-center animate-pulse">{error}</p>}

                        <button 
                            disabled={cart.length === 0 || isSubmitting}
                            onClick={handleCheckout}
                            className={`w-full py-4 rounded-2xl font-black text-white text-lg tracking-wide transition-all ${
                                cart.length === 0 || isSubmitting 
                                ? 'bg-gray-200 cursor-not-allowed text-gray-400' 
                                : 'bg-orange-600 hover:bg-black shadow-xl shadow-orange-500/20 active:scale-95'
                            }`}
                        >
                            {isSubmitting ? 'Finalizing...' : 'Pay & Print Receipt'}
                        </button>
                    </div>
                </div>
            </div>

            {/* Success Modal */}
            {successData && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300">
                    <div className="bg-white rounded-[40px] p-8 max-w-sm w-full shadow-2xl animate-in zoom-in-95 duration-300 text-center">
                        <div className="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">
                            ✓
                        </div>
                        <h3 className="text-2xl font-black text-gray-900 mb-2">Sale Completed!</h3>
                        <p className="text-gray-500 font-medium mb-6">Receipt: <span className="font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">{successData.receipt_number}</span></p>
                        
                        <div className="bg-orange-50 p-4 rounded-3xl mb-8">
                            <p className="text-sm font-bold text-orange-600 uppercase tracking-widest mb-1">Amount Paid</p>
                            <p className="text-3xl font-black text-orange-900">₦{successData.total_price.toLocaleString()}</p>
                        </div>

                        <div className="flex gap-3">
                            <button onClick={() => setSuccessData(null)} className="flex-1 py-4 bg-gray-100 hover:bg-gray-200 text-gray-900 font-black rounded-2xl transition-all">New Order</button>
                            <button 
                                onClick={() => window.print()}
                                className="flex-1 py-4 bg-gray-900 hover:bg-black text-white font-black rounded-2xl transition-all"
                            >Print Receipt</button>
                        </div>
                    </div>
                </div>
            )}

            {/* Print Only Receipt Layout */}
            <div className="hidden print:block fixed inset-0 bg-white z-[9999] p-4 text-black font-mono">
                <style dangerouslySetInnerHTML={{ __html: `
                    @media print {
                        body * { visibility: hidden; }
                        .print-container, .print-container * { visibility: visible; }
                        .print-container { position: absolute; left: 0; top: 0; width: 80mm; font-size: 12px; }
                        @page { margin: 0; size: 80mm auto; }
                    }
                `}} />
                <div className="print-container">
                    <div className="text-center mb-4">
                        {branding?.logo && <img src={branding.logo} className="h-12 mx-auto mb-2" alt="logo" />}
                        <h2 className="text-xl font-bold uppercase">{branding?.name || 'ENAPEL STORE'}</h2>
                        <p className="text-[10px]">{branding?.email || ''}</p>
                        <p className="text-[10px] uppercase">{new Date().toLocaleString()}</p>
                    </div>
                    
                    <div className="border-t border-b border-black py-2 my-2 space-y-1">
                        <div className="flex justify-between font-bold">
                            <span className="w-1/2 text-left">ITEM</span>
                            <span className="w-1/4 text-center">QTY</span>
                            <span className="w-1/4 text-right">TOTAL</span>
                        </div>
                        {(successData?.cart_items || cart).map(item => (
                            <div key={item.id} className="flex justify-between">
                                <span className="w-1/2 text-left truncate">{item.product_name || item.name}</span>
                                <span className="w-1/4 text-center">x{item.quantity}</span>
                                <span className="w-1/4 text-right">₦{(item.price * item.quantity).toLocaleString()}</span>
                            </div>
                        ))}
                    </div>

                    <div className="space-y-1">
                        <div className="flex justify-between text-lg font-bold">
                            <span>TOTAL</span>
                            <span>₦{successData?.total_price.toLocaleString() || totalPrice.toLocaleString()}</span>
                        </div>
                        <div className="flex justify-between text-[10px]">
                            <span>PAYMENT:</span>
                            <span className="uppercase">{successData?.payment_method || paymentMethod}</span>
                        </div>
                        {successData?.receipt_number && (
                            <div className="text-center mt-4 pt-2 border-t border-dotted border-black">
                                <p className="font-bold">RECEIPT: {successData.receipt_number}</p>
                                <p className="mt-2">THANK YOU FOR YOUR PATRONAGE!</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

Pos.layout = page => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
