<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENAPEL | Payment Status</title>

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
            --accent: #10B981;
            --error: #EF4444;
            --dark: #0A0F1D;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
            --radius-lg: 24px;
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
            background-image: radial-gradient(circle at center, rgba(59, 130, 246, 0.1), transparent 70%);
        }

        .status-container {
            width: 100%;
            max-width: 550px;
            padding: 2rem;
            animation: fadeIn 0.8s ease-out;
        }

        .status-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.5);
        }

        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }

        .status-success .status-icon {
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent);
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.3);
        }

        .status-error .status-icon {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error);
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.3);
        }

        h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        p {
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .details-grid {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 3rem;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .detail-label {
            color: var(--text-muted);
        }

        .detail-value {
            color: white;
            font-weight: 700;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

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
    </style>
</head>

<body>

    <div class="status-container">
        <div class="status-card {{ session('success') ? 'status-success' : 'status-error' }}">
            @if (session('success'))
                <div class="status-icon">
                    <i data-lucide="check-circle-2" style="width: 48px; height: 48px;"></i>
                </div>
                <h2>Payment Successful!</h2>
                <p>Welcome to ENAPEL. Your hub has been initialized and your license is now active. You can now login to
                    manage your business.</p>

                <div class="details-grid">
                    <div class="detail-row">
                        <span class="detail-label">Transaction ID</span>
                        <span class="detail-value">{{ session('transaction_id') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Amount Paid</span>
                        <span class="detail-value">₦{{ number_format(session('amount'), 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">License Key</span>
                        <span class="detail-value" style="color: var(--primary);">{{ session('license_key') }}</span>
                    </div>
                </div>

                <a href="{{ route('login') }}" class="btn btn-primary">
                    Proceed to Dashboard <i data-lucide="arrow-right"></i>
                </a>
            @else
                <div class="status-icon">
                    <i data-lucide="x-circle" style="width: 48px; height: 48px;"></i>
                </div>
                <h2>Payment Failed</h2>
                <p>{{ session('error') ?? 'We were unable to process your payment at this time. Please try again or contact support if the issue persists.' }}
                </p>

                <a href="{{ route('wizardform') }}" class="btn btn-primary"
                    style="background: rgba(255,255,255,0.05); color: white;">
                    <i data-lucide="arrow-left"></i> Try Again
                </a>
            @endif
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
