<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENAPEL | Onboarding & Registration</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;900&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #3B82F6;
            --primary-dark: #1E40AF;
            --accent: #10B981;
            --dark: #0A0F1D;
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
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            background-image: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.1), transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.05), transparent 40%);
        }

        .auth-container {
            width: 100%;
            max-width: 900px;
            padding: 2rem;
            animation: fadeIn 0.8s ease-out;
        }

        .auth-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.5);
        }

        /* --- Sidebar --- */
        .auth-sidebar {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(10, 15, 29, 0.8) 100%);
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid var(--glass-border);
        }

        .brand-logo {
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            font-size: 1.5rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 3rem;
            text-decoration: none;
        }

        .steps-list {
            list-style: none;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            opacity: 0.4;
            transition: all 0.3s;
        }

        .step-item.active {
            opacity: 1;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .step-item.active .step-number {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }

        .step-info span {
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .step-info h4 {
            font-size: 0.9rem;
            color: white;
        }

        /* --- Main Content --- */
        .auth-main {
            padding: 3.5rem;
        }

        .auth-header {
            margin-bottom: 2.5rem;
        }

        .auth-header h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* --- Form Elements --- */
        .form-step {
            display: none;
            animation: slideIn 0.5s ease;
        }

        .form-step.active {
            display: block;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            width: 18px;
        }

        input,
        select,
        textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            border-radius: 12px;
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .establishment-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .establishment-item {
            position: relative;
        }

        .establishment-item input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .est-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            gap: 0.5rem;
        }

        .establishment-item input:checked+.est-label {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.1);
            color: white;
        }

        .est-label i {
            width: 24px;
            color: var(--primary);
        }

        /* --- Footer Actions --- */
        .form-footer {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 1.8rem;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
        }

        .btn-ghost:hover {
            color: white;
        }

        /* --- Plan Summary Card --- */
        .plan-summary {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .plan-info h4 {
            font-size: 1.1rem;
            color: white;
        }

        .plan-price {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--primary);
        }

        /* --- Animations --- */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* --- Mobile --- */
        @media (max-width: 768px) {
            .auth-card {
                grid-template-columns: 1fr;
            }

            .auth-sidebar {
                display: none;
            }

            .auth-main {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Sidebar -->
            <div class="auth-sidebar">
                <div>
                    <a href="{{ route('welcome') }}" class="brand-logo">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Enapel" style="height: 32px;">
                        ENAPEL
                    </a>

                    <ul class="steps-list">
                        <li class="step-item active" id="step-nav-1">
                            <div class="step-number">1</div>
                            <div class="step-info">
                                <span>Step 1</span>
                                <h4>Business Profile</h4>
                            </div>
                        </li>
                        <li class="step-item" id="step-nav-2">
                            <div class="step-number">2</div>
                            <div class="step-info">
                                <span>Step 2</span>
                                <h4>Establishment</h4>
                            </div>
                        </li>
                        <li class="step-item" id="step-nav-3">
                            <div class="step-number">3</div>
                            <div class="step-info">
                                <span>Step 3</span>
                                <h4>Checkout</h4>
                            </div>
                        </li>
                    </ul>
                </div>

                <p style="font-size: 0.75rem; color: var(--text-muted); opacity: 0.6;">
                    &copy; {{ date('Y') }} Enapel Technologies. <br>Secured by Paystack.
                </p>
            </div>

            <!-- Main Content -->
            <div class="auth-main">
                <form action="{{ route('application.create') }}" id="wizardForm" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- Hidden field for Logo filename handled by JS -->
                    <input type="hidden" name="filename" id="filename">

                    <!-- Step 1: Business Profile -->
                    <div class="form-step active" id="step-1">
                        <div class="auth-header">
                            <h2>Let's get started</h2>
                            <p>Tell us a bit about your business to personalize your hub.</p>
                        </div>

                        <div class="form-group">
                            <label>Company Name</label>
                            <div class="input-wrapper">
                                <i data-lucide="briefcase"></i>
                                <input type="text" name="company_name" placeholder="Enapel Nigeria Ltd" required>
                            </div>
                        </div>

                        <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label>Business Email</label>
                                <div class="input-wrapper">
                                    <i data-lucide="mail"></i>
                                    <input type="email" name="email" placeholder="hello@company.com" required>
                                </div>
                            </div>
                            <div>
                                <label>Phone Number</label>
                                <div class="input-wrapper">
                                    <i data-lucide="phone"></i>
                                    <input type="text" name="phone" placeholder="08012345678" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Full Name (Admin)</label>
                            <div class="input-wrapper">
                                <i data-lucide="user"></i>
                                <input type="text" name="name" placeholder="John Doe" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" required style="padding-left: 1rem;">
                                <option value="" disabled selected>Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>

                        <div class="form-footer">
                            <div></div>
                            <button type="button" class="btn btn-primary" onclick="nextStep(2)">Continue <i
                                    data-lucide="arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Step 2: Establishment -->
                    <div class="form-step" id="step-2">
                        <div class="auth-header">
                            <h2>Business Type</h2>
                            <p>What kind of establishment are you running?</p>
                        </div>

                        <div class="establishment-grid">
                            <div class="establishment-item">
                                <input type="radio" name="establishment" value="Hotel" id="est_hotel">
                                <label for="est_hotel" class="est-label">
                                    <i data-lucide="hotel"></i>
                                    <span>Hotel</span>
                                </label>
                            </div>
                            <div class="establishment-item">
                                <input type="radio" name="establishment" value="Supermarket" id="est_super" checked>
                                <label for="est_super" class="est-label">
                                    <i data-lucide="shopping-cart"></i>
                                    <span>Supermarket</span>
                                </label>
                            </div>
                            <div class="establishment-item">
                                <input type="radio" name="establishment" value="Pharmacy" id="est_phar">
                                <label for="est_phar" class="est-label">
                                    <i data-lucide="pill"></i>
                                    <span>Pharmacy</span>
                                </label>
                            </div>
                            <div class="establishment-item">
                                <input type="radio" name="establishment" value="Retail Store" id="est_retail">
                                <label for="est_retail" class="est-label">
                                    <i data-lucide="store"></i>
                                    <span>Retail Store</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Additional Modules (Multi-select)</label>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <label
                                    style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.03); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer;">
                                    <input type="checkbox" name="module[]" value="Inventory" checked
                                        style="width: auto; padding: 0;"> Inventory
                                </label>
                                <label
                                    style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.03); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer;">
                                    <input type="checkbox" name="module[]" value="Personnel" checked
                                        style="width: auto; padding: 0;"> Staff Mgmt
                                </label>
                                <label
                                    style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.03); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer;">
                                    <input type="checkbox" name="module[]" value="Finance" checked
                                        style="width: auto; padding: 0;"> Finance
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Company Logo</label>
                            <div class="input-wrapper">
                                <i data-lucide="image"></i>
                                <input type="file" name="logo" id="logoInput" accept="image/*" required
                                    style="padding-top: 0.6rem;">
                            </div>
                        </div>

                        <div class="form-footer">
                            <button type="button" class="btn btn-ghost" onclick="prevStep(1)"><i
                                    data-lucide="arrow-left"></i> Back</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Review Plan <i
                                    data-lucide="arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Step 3: Checkout -->
                    <div class="form-step" id="step-3">
                        <div class="auth-header">
                            <h2>Complete Payment</h2>
                            <p>You're almost there! Finalize your subscription to activate your hub.</p>
                        </div>

                        <div class="plan-summary">
                            <div class="plan-info">
                                <span
                                    style="font-size: 0.75rem; color: var(--primary); font-weight: 800; text-transform: uppercase;">Selected
                                    Plan</span>
                                <h4 id="displayPlanName">Business Pro</h4>
                            </div>
                            <div class="plan-price" id="displayPlanPrice">₦55,000</div>
                        </div>

                        <div class="form-group">
                            <label>Subscription Duration</label>
                            <select name="duration" id="durationSelect" onchange="updateTotal()" required
                                style="padding-left: 1rem;">
                                <option value="1">1 Month</option>
                                <option value="3">3 Months (5% Discount)</option>
                                <option value="6">6 Months (10% Discount)</option>
                                <option value="12">1 Year (20% Discount)</option>
                            </select>
                        </div>

                        <input type="hidden" name="amount" id="finalAmountInput">

                        <div
                            style="background: rgba(255, 255, 255, 0.03); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="color: var(--text-muted);">Subtotal</span>
                                <span id="summarySubtotal">₦55,000</span>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; font-weight: 800; font-size: 1.1rem; color: white;">
                                <span>Total Payable</span>
                                <span id="summaryTotal">₦55,000</span>
                            </div>
                        </div>

                        <div class="form-footer">
                            <button type="button" class="btn btn-ghost" onclick="prevStep(2)"><i
                                    data-lucide="arrow-left"></i> Back</button>
                            <div style="display: flex; gap: 1rem;">
                                <button type="submit" class="btn btn-ghost"
                                    style="border: 1px solid var(--glass-border);"
                                    onclick="document.getElementById('wizardForm').action='{{ route('application.demo') }}'">Demo
                                    Onboarding <i data-lucide="play"></i></button>
                                <button type="submit" class="btn btn-primary" style="background: var(--accent);"
                                    onclick="document.getElementById('wizardForm').action='{{ route('application.create') }}'">Pay
                                    & Onboard <i data-lucide="credit-card"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Get plan from URL
        const urlParams = new URLSearchParams(window.location.search);
        const selectedPlan = urlParams.get('plan') || 'business';

        const plans = {
            'starter': {
                name: 'Starter Pack',
                price: 25000
            },
            'business': {
                name: 'Business Pro',
                price: 55000
            },
            'enterprise': {
                name: 'Enterprise Hub',
                price: 150000
            }
        };

        const planData = plans[selectedPlan] || plans.business;
        document.getElementById('displayPlanName').textContent = planData.name;

        function updateTotal() {
            const duration = parseInt(document.getElementById('durationSelect').value);
            let total = planData.price * duration;

            // Apply discounts
            if (duration === 3) total *= 0.95;
            if (duration === 6) total *= 0.90;
            if (duration === 12) total *= 0.80;

            const formattedTotal = new Intl.NumberFormat('en-NG', {
                style: 'currency',
                currency: 'NGN'
            }).format(total);
            const formattedSubtotal = new Intl.NumberFormat('en-NG', {
                style: 'currency',
                currency: 'NGN'
            }).format(planData.price * duration);

            document.getElementById('displayPlanPrice').textContent = formattedTotal;
            document.getElementById('summarySubtotal').textContent = formattedSubtotal;
            document.getElementById('summaryTotal').textContent = formattedTotal;
            document.getElementById('finalAmountInput').value = total;
        }

        updateTotal();

        function nextStep(step) {
            // Simple validation for Step 1
            if (step === 2) {
                const inputs = document.querySelectorAll('#step-1 input, #step-1 select');
                for (let input of inputs) {
                    if (input.hasAttribute('required') && !input.value) {
                        Swal.fire('Error', 'Please fill in all required fields.', 'error');
                        return;
                    }
                }
            }

            // Simple validation for Step 2
            if (step === 3) {
                const logoInput = document.getElementById('logoInput');
                if (!logoInput.value) {
                    Swal.fire('Error', 'Please upload your company logo.', 'error');
                    return;
                }
                const filenameInput = document.getElementById('filename');
                filenameInput.value = logoInput.files[0].name;
            }

            document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
            document.getElementById('step-' + step).classList.add('active');

            document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active'));
            document.getElementById('step-nav-' + step).classList.add('active');
        }

        function prevStep(step) {
            document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
            document.getElementById('step-' + step).classList.add('active');

            document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active'));
            document.getElementById('step-nav-' + step).classList.add('active');
        }

        // Handle Laravel Errors
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>'
            });
        @endif
    </script>
</body>

</html>
