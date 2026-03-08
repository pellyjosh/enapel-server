<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENAPEL | Premium All-in-One Business Operations Platform</title>
    <meta name="description"
        content="ENAPEL is a powerful operations platform for hotels, supermarkets, and pharmacies. Streamline your business with one unified system.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;900&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #3B82F6;
            --primary-dark: #1E40AF;
            --primary-light: #DBEAFE;
            --accent: #10B981;
            --dark: #0A0F1D;
            --dark-card: rgba(15, 23, 42, 0.8);
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
            --radius-lg: 24px;
            --radius-md: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 1.5rem;
            position: relative;
        }

        /* --- Preloader --- */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dark);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s ease, visibility 0.8s;
        }

        .loader-content {
            text-align: center;
        }

        .loader-logo {
            font-size: 2.5rem;
            font-weight: 900;
            color: white;
            margin-bottom: 1rem;
            letter-spacing: 4px;
            position: relative;
            overflow: hidden;
        }

        .loader-logo::after {
            content: '';
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shine 1.5s infinite;
        }

        @keyframes shine {
            100% {
                left: 100%;
            }
        }

        .loader-bar {
            width: 120px;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }

        .loader-progress {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: var(--primary);
            animation: progress 2.5s ease-in-out forwards;
        }

        @keyframes progress {
            0% {
                width: 0;
            }

            50% {
                width: 70%;
            }

            100% {
                width: 100%;
            }
        }

        /* --- Navigation --- */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1.5rem 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        nav.scrolled {
            background: rgba(10, 15, 29, 0.8);
            backdrop-filter: blur(20px);
            padding: 1rem 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo img {
            height: 40px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: white;
        }

        .btn-nav {
            background: var(--primary);
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            color: white !important;
            font-weight: 700;
        }

        /* --- Hero Section --- */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 8rem 0;
            background-image: linear-gradient(to bottom, rgba(10, 15, 29, 0.4), var(--dark)), url("{{ asset('assets/images/hero_bg.png') }}");
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.15), transparent 40%);
        }

        .hero-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            color: var(--primary);
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2rem;
            animation: fadeInDown 1s ease;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            line-height: 1.05;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #fff 0%, #94A3B8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 1.2s ease;
        }

        .hero p {
            color: var(--text-muted);
            font-size: 1.25rem;
            max-width: 650px;
            margin: 0 auto 3rem;
            animation: fadeInUp 1.4s ease;
        }

        .hero-btns {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            animation: fadeInUp 1.6s ease;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: scale(1.05);
            background: var(--primary-dark);
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* --- Dashboard Mockup --- */
        .mockup-container {
            margin-top: 5rem;
            perspective: 2000px;
            animation: mockupReveal 2s ease forwards;
            opacity: 0;
            transform: translateY(100px);
        }

        .dashboard-mockup {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            border-radius: 20px;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.5), 0 30px 60px -30px rgba(0, 0, 0, 0.6);
            border: 8px solid rgba(255, 255, 255, 0.05);
            transform: rotateX(15deg) rotateY(-5deg);
            transition: transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .mockup-container:hover .dashboard-mockup {
            transform: rotateX(0deg) rotateY(0deg);
        }

        /* --- Services/Modules --- */
        .section {
            padding: 10rem 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-header h2 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }

        .section-header p {
            color: var(--text-muted);
            font-size: 1.15rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 3rem 2rem;
            border-radius: var(--radius-lg);
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: var(--primary);
            transform: translateY(-10px);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s;
        }

        .card:hover .card-icon {
            background: var(--primary);
            color: white;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }

        .card h3 {
            font-size: 1.5rem;
            color: white;
        }

        .card p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* --- Downloads --- */
        .downloads {
            background: linear-gradient(to bottom, var(--dark), #111827);
        }

        .download-card {
            border: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(15, 23, 42, 0.4);
        }

        .setup-btn {
            background: none;
            border: 1px solid var(--glass-border);
            color: var(--text-muted);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .setup-btn:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .setup-steps {
            height: 0;
            overflow: hidden;
            transition: all 0.5s ease;
            text-align: left;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .setup-steps.active {
            height: auto;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--glass-border);
        }

        .step {
            margin-bottom: 0.75rem;
            display: flex;
            gap: 0.75rem;
        }

        .step-num {
            color: var(--primary);
            font-weight: 800;
        }

        /* --- Pricing --- */
        .pricing-grid {
            align-items: flex-start;
        }

        .price-card {
            padding: 4rem 2.5rem;
            text-align: center;
        }

        .price-card.featured {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.02) 100%);
            border: 2px solid var(--primary);
            position: relative;
        }

        .featured-label {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--primary);
            color: white;
            font-size: 0.7rem;
            font-weight: 900;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            letter-spacing: 1px;
        }

        .price-val {
            font-size: 3.5rem;
            font-weight: 900;
            margin: 1.5rem 0;
            color: white;
        }

        .price-val span {
            font-size: 1rem;
            color: var(--text-muted);
        }

        .price-features {
            text-align: left;
            margin: 2rem 0 3rem;
            list-style: none;
        }

        .price-features li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #CBD5E1;
        }

        .price-features i {
            color: var(--accent);
            width: 20px;
        }

        /* --- FAQ --- */
        .faq-item {
            background: var(--glass);
            padding: 1.5rem 2rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            border: 1px solid var(--glass-border);
            cursor: pointer;
        }

        .faq-q {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .faq-a {
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s ease;
            color: var(--text-muted);
            margin-top: 0;
        }

        .faq-item.active {
            border-color: var(--primary);
        }

        .faq-item.active .faq-a {
            max-height: 200px;
            margin-top: 1rem;
        }

        .faq-item.active i {
            transform: rotate(180deg);
        }

        /* --- Footer --- */
        footer {
            padding: 8rem 0 4rem;
            border-top: 1px solid var(--glass-border);
            background: #020617;
        }

        .footer-grid {
            grid-template-columns: 2fr 1fr 1fr 1fr;
        }

        .footer-brand h2 {
            margin-bottom: 1.5rem;
        }

        .footer-links h4 {
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .footer-links ul li {
            list-style: none;
            margin-bottom: 1rem;
        }

        .footer-links ul li a {
            color: var(--text-muted);
            transition: 0.3s;
        }

        .footer-links ul li a:hover {
            color: white;
        }

        .copyright {
            margin-top: 6rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* --- Animations --- */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes mockupReveal {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 1s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* --- Responsive --- */
        @media (max-width: 992px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 3rem;
            }

            .hero h1 {
                font-size: 3.5rem;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero-btns {
                flex-direction: column;
            }

            .section {
                padding: 6rem 0;
            }

            .price-card.featured {
                transform: none;
            }
        }
    </style>
</head>

<body>

    <!-- Preloader -->
    <div id="preloader">
        <div class="loader-content">
            <div class="loader-logo">ENAPEL</div>
            <div class="loader-bar">
                <div class="loader-progress"></div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav id="navbar">
        <div class="container nav-inner">
            <a href="#" class="logo">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Enapel">
                ENAPEL
            </a>
            <div class="nav-links">
                <a href="#home">Home</a>
                <a href="#services">Services</a>
                <a href="#downloads">Downloads</a>
                <a href="#pricing">Pricing</a>
                <a href="#faq">FAQ</a>
                <a href="#contact" class="btn-nav">Book a Demo</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge"><i data-lucide="sparkles" style="width:14px;"></i> The Ultimate Business
                    Hub</span>
                <h1>One Platform. <br>Every Operation.</h1>
                <p>Scale your hotel, pharmacy, or retail chain with Enapel's unified management engine. Built for
                    high-growth businesses that demand reliability.</p>
                <div class="hero-btns">
                    <a href="#pricing" class="btn btn-primary">Start Your Free Trial <i
                            data-lucide="chevron-right"></i></a>
                    <a href="#downloads" class="btn btn-glass">View Software Suite</a>
                </div>
            </div>

            <div class="mockup-container">
                <img src="{{ asset('assets/images/dashboard_mockup.png') }}" alt="Enapel Dashboard"
                    class="dashboard-mockup">
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="section" id="services">
        <div class="container">
            <div class="section-header reveal">
                <h2>Intelligent Modules</h2>
                <p>Powerful, interconnected tools designed for the modern business ecosystem.</p>
            </div>
            <div class="grid">
                <div class="card reveal">
                    <div class="card-icon"><i data-lucide="hotel"></i></div>
                    <h3>Hotel Management</h3>
                    <p>Automated room categories, dynamic availability tracking, and integrated guest workflows.</p>
                </div>
                <div class="card reveal">
                    <div class="card-icon"><i data-lucide="shopping-cart"></i></div>
                    <h3>Retail & POS</h3>
                    <p>Smart barcode search, real-time inventory reorder status, and high-speed checkout lanes.</p>
                </div>
                <div class="card reveal">
                    <div class="card-icon"><i data-lucide="pill"></i></div>
                    <h3>Pharmacy Engine</h3>
                    <p>Precise medication tracking, dosage monitoring, and patient visit history logs.</p>
                </div>
                <div class="card reveal">
                    <div class="card-icon"><i data-lucide="pie-chart"></i></div>
                    <h3>Advanced Analytics</h3>
                    <p>Deep-dive financial summaries, sales trends, and cross-branch performance insights.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Downloads -->
    <section class="section downloads" id="downloads">
        <div class="container">
            <div class="section-header reveal">
                <h2>Flexible Deployment</h2>
                <p>Choose the architecture that fits your scale. Cloud-native or local server-backed.</p>
            </div>
            <div class="grid">
                <div class="card download-card reveal">
                    <div class="card-icon"><i data-lucide="server"></i></div>
                    <h3>Enapel Central</h3>
                    <p>The core server platform for multi-branch sync and large scale terminal hubs.</p>
                    <a href="#" class="btn btn-primary" style="padding: 0.8rem;">Download Hub</a>
                    <button class="setup-btn" onclick="toggleSetup('server-setup')">Setup Guide <i
                            data-lucide="chevron-down"></i></button>
                    <div id="server-setup" class="setup-steps">
                        <div class="step"><span class="step-num">01.</span> Run Central Installer on your main PC.
                        </div>
                        <div class="step"><span class="step-num">02.</span> Initialize database and staff roles.</div>
                        <div class="step"><span class="step-num">03.</span> Connect terminals via Network IP.</div>
                    </div>
                </div>
                <div class="card download-card reveal">
                    <div class="card-icon"><i data-lucide="monitor"></i></div>
                    <h3>Desktop Suite</h3>
                    <p>Optimized POS and back-office software for Windows and MacOS workstations.</p>
                    <a href="#" class="btn btn-glass" style="padding: 0.8rem;">Download Desktop</a>
                    <button class="setup-btn" onclick="toggleSetup('desktop-setup')">Setup Guide <i
                            data-lucide="chevron-down"></i></button>
                    <div id="desktop-setup" class="setup-steps">
                        <div class="step"><span class="step-num">01.</span> Install the Desktop application.</div>
                        <div class="step"><span class="step-num">02.</span> Login with Central or Cloud credentials.
                        </div>
                        <div class="step"><span class="step-num">03.</span> Start daily operations sync.</div>
                    </div>
                </div>
                <div class="card download-card reveal">
                    <div class="card-icon"><i data-lucide="smartphone"></i></div>
                    <h3>Mobile App</h3>
                    <p>Keep the pulse of your business in your pocket. Real-time metrics for iPad & Android.</p>
                    <a href="#" class="btn btn-glass" style="padding: 0.8rem;">Get the App</a>
                    <button class="setup-btn" onclick="toggleSetup('mobile-setup')">Setup Guide <i
                            data-lucide="chevron-down"></i></button>
                    <div id="mobile-setup" class="setup-steps">
                        <div class="step"><span class="step-num">01.</span> Download from App Store / Play Store.
                        </div>
                        <div class="step"><span class="step-num">02.</span> Scan the Hub QR link.</div>
                        <div class="step"><span class="step-num">03.</span> Access reports remotely.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section class="section" id="pricing">
        <div class="container">
            <div class="section-header reveal">
                <h2>Transparent Pricing</h2>
                <p>No hidden fees. Just powerful tools to help your business scale with confidence.</p>
            </div>
            <div class="grid pricing-grid">
                <div class="card price-card reveal">
                    <h3>Starter</h3>
                    <div class="price-val">₦25k<span>/mo</span></div>
                    <ul class="price-features">
                        <li><i data-lucide="check"></i> 1 Business Module</li>
                        <li><i data-lucide="check"></i> Up to 5 Terminal Logins</li>
                        <li><i data-lucide="check"></i> Standard Analytics</li>
                        <li><i data-lucide="check"></i> Email Support</li>
                    </ul>
                    <a href="{{ route('wizardform', ['plan' => 'starter']) }}" class="btn btn-glass"
                        style="width:100%;">Select Plan</a>
                </div>
                <div class="card price-card featured reveal">
                    <span class="featured-label">MOST POPULAR</span>
                    <h3>Business Pro</h3>
                    <div class="price-val">₦55k<span>/mo</span></div>
                    <ul class="price-features">
                        <li><i data-lucide="check"></i> All Operation Modules</li>
                        <li><i data-lucide="check"></i> Unlimited Terminal Logins</li>
                        <li><i data-lucide="check"></i> Multi-branch Sync</li>
                        <li><i data-lucide="check"></i> Priority 24/7 Support</li>
                        <li><i data-lucide="check"></i> API Hub Access</li>
                    </ul>
                    <a href="{{ route('wizardform', ['plan' => 'business']) }}" class="btn btn-primary"
                        style="width:100%;">Get Started Now</a>
                </div>
                <div class="card price-card reveal">
                    <h3>Enterprise</h3>
                    <div class="price-val">Custom</div>
                    <ul class="price-features">
                        <li><i data-lucide="check"></i> Custom White-labeling</li>
                        <li><i data-lucide="check"></i> Dedicated Account Manager</li>
                        <li><i data-lucide="check"></i> On-premise Server Sync</li>
                        <li><i data-lucide="check"></i> Custom API Integrations</li>
                    </ul>
                    <a href="{{ route('wizardform', ['plan' => 'enterprise']) }}" class="btn btn-glass"
                        style="width:100%;">Contact Sales</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section" id="faq">
        <div class="container">
            <div class="section-header reveal">
                <h2>FAQ</h2>
            </div>
            <div style="max-width: 800px; margin: 0 auto;">
                <div class="faq-item reveal" onclick="toggleFaq(this)">
                    <div class="faq-q">Is Enapel purely Cloud-based? <i data-lucide="chevron-down"></i></div>
                    <div class="faq-a">Enapel is hybrid. You can run locally for extreme speed/reliability and sync
                        with the cloud for remote management and backups.</div>
                </div>
                <div class="faq-item reveal" onclick="toggleFaq(this)">
                    <div class="faq-q">Do you support hardware like printers and scanners? <i
                            data-lucide="chevron-down"></i></div>
                    <div class="faq-a">Yes, Enapel Desktop supports most industry-standard thermal receipt printers,
                        barcode scanners, and label printers natively.</div>
                </div>
                <div class="faq-item reveal" onclick="toggleFaq(this)">
                    <div class="faq-q">Can I manage multiple hotels/stores? <i data-lucide="chevron-down"></i></div>
                    <div class="faq-a">Absolutely. Our Business and Enterprise plans include multi-location sync,
                        allowing you to see consolidated reports in real-time.</div>
                </div>
                <div class="faq-item reveal" onclick="toggleFaq(this)">
                    <div class="faq-q">How safe is my data? <i data-lucide="chevron-down"></i></div>
                    <div class="faq-a">We use enterprise-grade encryption for all transmissions. If running locally,
                        your data stays on your server, under your control.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="section"
        style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--dark) 100%); text-align: center;">
        <div class="container reveal">
            <h2 style="font-size: clamp(2rem, 5vw, 3.5rem); margin-bottom: 2rem;">Ready to transform your business?
            </h2>
            <p style="margin-bottom: 3rem; opacity: 0.8; font-size: 1.2rem;">Join the growing list of businesses
                operating with next-gen efficiency.</p>
            <div class="hero-btns">
                <a href="#" class="btn btn-primary">Book a Live Demo</a>
                <a href="#" class="btn btn-glass">Talk to an Expert</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="grid footer-grid">
                <div class="footer-brand">
                    <h2>ENAPEL</h2>
                    <p>Standardizing operations for the modern African enterprise. Reliable, Scalable, Intelligent.</p>
                    <div style="display: flex; gap: 1.5rem; color: var(--text-muted);">
                        <i data-lucide="twitter"></i>
                        <i data-lucide="linkedin"></i>
                        <i data-lucide="instagram"></i>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Platform</h4>
                    <ul>
                        <li><a href="#services">Modules</a></li>
                        <li><a href="#downloads">Cloud Sync</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Security</a></li>
                        <li><a href="#">API Docs</a></li>
                        <li><a href="#">Help Center</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Privacy</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; {{ date('Y') }} ENAPEL. All Rights Reserved.</p>
                <div style="display: flex; gap: 2rem;">
                    <span>Lagos, Nigeria</span>
                    <span>hello@enapel.com</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Icons
        lucide.createIcons();

        // Reveal Animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // FAQ Toggle
        function toggleFaq(el) {
            el.classList.toggle('active');
        }

        // Setup Guide Toggle
        function toggleSetup(id) {
            const el = document.getElementById(id);
            el.classList.toggle('active');
        }

        // Preloader
        window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            setTimeout(() => {
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.visibility = 'hidden';
                }, 800);
            }, 1500);
        });
    </script>
</body>

</html>
