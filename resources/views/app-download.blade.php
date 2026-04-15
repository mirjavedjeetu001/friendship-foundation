<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $appSettings = \App\Models\MonthlySetting::getSettings(); @endphp
    
    <title>Download App - {{ $appSettings->app_name ?? 'Allied Group' }}</title>
    
    <meta name="description" content="Allied Group Savings Management App. View your savings, deposits, withdrawals, and balance all in one place.">
    <meta name="author" content="Allied Group">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/app') }}">
    <meta property="og:title" content="Download App - {{ $appSettings->app_name ?? 'Allied Group' }}">
    <meta property="og:description" content="Allied Group Savings Management App. Track your savings, deposits, and withdrawals easily.">
    @if($appSettings->logo)
    <meta property="og:image" content="{{ $appSettings->logo_url }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-dark: #3730A3;
            --primary-light: #818CF8;
            --accent-color: #06B6D4;
            --success-color: #10B981;
            --dark-color: #1F2937;
            --light-bg: #F3F4F6;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        .bg-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 15s infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) rotate(720deg); opacity: 0; }
        }
        
        /* Download Section */
        .download-section {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }
        
        .download-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
        }
        
        .download-content h1 .highlight {
            background: linear-gradient(135deg, var(--accent-color), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .download-subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        /* Stats */
        .download-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.1);
            padding: 12px 20px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .stat-item i {
            font-size: 1.5rem;
            color: var(--accent-color);
        }
        
        .stat-item strong {
            display: block;
            font-size: 1.3rem;
            color: #fff;
        }
        
        .stat-item small {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
        }
        
        /* Download Buttons */
        .download-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        
        .btn-download-main {
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: #fff;
            padding: 15px 30px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }
        
        .btn-download-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.4);
            color: #fff;
        }
        
        .btn-download-main i {
            font-size: 2rem;
        }
        
        .btn-download-main small {
            display: block;
            font-size: 0.75rem;
            opacity: 0.9;
        }
        
        .btn-download-main strong {
            font-size: 1.1rem;
        }
        
        .btn-download-playstore {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.6);
            padding: 15px 30px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.2);
            cursor: not-allowed;
        }
        
        .btn-download-playstore i {
            font-size: 2rem;
        }
        
        .btn-download-playstore small {
            display: block;
            font-size: 0.75rem;
        }
        
        /* Trust Badges */
        .trust-badges {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .trust-badges span {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }
        
        .trust-badges i {
            color: var(--success-color);
        }
        
        /* Phone Mockup */
        .phone-mockup {
            position: relative;
            display: flex;
            justify-content: center;
        }
        
        .phone-frame {
            width: 280px;
            height: 560px;
            background: linear-gradient(145deg, #2d2d2d, #1a1a1a);
            border-radius: 40px;
            padding: 12px;
            box-shadow: 0 50px 100px rgba(0,0,0,0.5);
            position: relative;
        }
        
        .phone-frame::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 25px;
            background: #1a1a1a;
            border-radius: 15px;
            z-index: 10;
        }
        
        .phone-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            text-align: center;
        }
        
        .phone-screen .app-icon-large {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .phone-screen .app-icon-large i {
            font-size: 40px;
            color: var(--primary-color);
        }
        
        .phone-screen h3 {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 25px;
        }
        
        .phone-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            width: 100%;
        }
        
        .phone-feature-item {
            background: rgba(255,255,255,0.15);
            padding: 12px 10px;
            border-radius: 12px;
            text-align: center;
        }
        
        .phone-feature-item i {
            font-size: 1.3rem;
            color: #fff;
            margin-bottom: 5px;
            display: block;
        }
        
        .phone-feature-item span {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.9);
        }
        
        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: #fff;
            position: relative;
            z-index: 1;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 10px;
        }
        
        .section-title .underline {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            margin: 0 auto;
            border-radius: 2px;
        }
        
        .feature-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.8rem;
            color: #fff;
        }
        
        .feature-card h5 {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
        }
        
        .feature-card p {
            color: #6B7280;
            font-size: 0.95rem;
            margin: 0;
        }
        
        /* Install Guide */
        .install-section {
            padding: 80px 0;
            background: var(--light-bg);
        }
        
        .install-steps {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .install-step {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: #fff;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        
        .step-content h5 {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .step-content p {
            color: #6B7280;
            margin: 0;
            font-size: 0.95rem;
        }
        
        /* Footer */
        .app-footer {
            background: var(--dark-color);
            color: #fff;
            padding: 30px 0;
            text-align: center;
        }
        
        .app-footer a {
            color: var(--accent-color);
            text-decoration: none;
        }
        
        .app-footer a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .download-content h1 {
                font-size: 2rem;
            }
            
            .phone-frame {
                width: 240px;
                height: 480px;
            }
        }
        
        @media (max-width: 767px) {
            .download-section {
                padding: 30px 0;
            }
            
            .download-content {
                text-align: center;
                margin-bottom: 40px;
            }
            
            .download-content h1 {
                font-size: 1.8rem;
            }
            
            .download-stats {
                justify-content: center;
            }
            
            .download-buttons {
                justify-content: center;
            }
            
            .trust-badges {
                justify-content: center;
            }
            
            .phone-frame {
                width: 220px;
                height: 440px;
            }
            
            .btn-download-main,
            .btn-download-playstore {
                padding: 12px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Particles Background -->
    <div class="bg-particles">
        @for ($i = 0; $i < 20; $i++)
        <div class="particle" style="left: {{ rand(0, 100) }}%; animation-delay: {{ $i * 0.5 }}s; animation-duration: {{ rand(10, 20) }}s;"></div>
        @endfor
    </div>
    
    <!-- Download Section -->
    <section class="download-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 download-content">
                    <h1>
                        <span class="highlight">{{ $appSettings->app_name ?? 'Allied Group' }}</span><br>
                        Download App
                    </h1>
                    <p class="download-subtitle">
                        Simplify your group savings management. View your savings, deposits, withdrawals, balance, and payment status all in one place on your mobile device.
                    </p>
                    
                    <div class="download-stats">
                        <div class="stat-item">
                            <i class="fas fa-download"></i>
                            <div>
                                <strong>{{ number_format($totalDownloads) }}+</strong>
                                <small>Downloads</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star"></i>
                            <div>
                                <strong>4.9</strong>
                                <small>Rating</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-weight-hanging"></i>
                            <div>
                                <strong>{{ $appSize }}</strong>
                                <small>Size</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="download-buttons">
                        <a href="{{ route('app.download.file') }}" class="btn-download-main" id="downloadBtn">
                            <i class="fab fa-android"></i>
                            <div>
                                <small>Free Download</small>
                                <strong>Android APK</strong>
                            </div>
                        </a>
                        <div class="btn-download-playstore">
                            <i class="fab fa-google-play"></i>
                            <div>
                                <small>Coming Soon</small>
                                <strong>Google Play</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="trust-badges">
                        <span><i class="fas fa-check-circle me-1"></i> Virus Free</span>
                        <span><i class="fas fa-lock me-1"></i> Secure</span>
                        <span><i class="fas fa-certificate me-1"></i> Digitally Signed</span>
                    </div>
                </div>
                
                <div class="col-lg-6 text-center">
                    <div class="phone-mockup">
                        <div class="phone-frame">
                            <div class="phone-screen">
                                <div class="app-icon-large">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3>{{ $appSettings->app_name ?? 'Allied Group' }}</h3>
                                <div class="phone-features">
                                    <div class="phone-feature-item">
                                        <i class="fas fa-wallet"></i>
                                        <span>Savings</span>
                                    </div>
                                    <div class="phone-feature-item">
                                        <i class="fas fa-hand-holding-usd"></i>
                                        <span>Deposits</span>
                                    </div>
                                    <div class="phone-feature-item">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>Withdrawals</span>
                                    </div>
                                    <div class="phone-feature-item">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Balance</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>App Features</h2>
                <div class="underline"></div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #4F46E5, #3730A3);">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h5>Savings Tracking</h5>
                        <p>View all your savings at a glance</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #10B981, #059669);">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <h5>Deposit History</h5>
                        <p>Detailed records of all deposits</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h5>Withdrawals</h5>
                        <p>Request and track withdrawals</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #EF4444, #DC2626);">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h5>Notifications</h5>
                        <p>Payment reminders & updates</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Install Guide -->
    <section class="install-section">
        <div class="container">
            <div class="section-title">
                <h2>How to Install</h2>
                <div class="underline"></div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="install-steps">
                        <div class="install-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h5>Download APK</h5>
                                <p>Click the green "Android APK" button above</p>
                            </div>
                        </div>
                        <div class="install-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h5>Open File</h5>
                                <p>After download completes, open the APK file from notification or file manager</p>
                            </div>
                        </div>
                        <div class="install-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h5>Grant Permission</h5>
                                <p>Allow "Install from unknown sources" if prompted</p>
                            </div>
                        </div>
                        <div class="install-step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h5>Install</h5>
                                <p>Click "Install" button and open the app after installation</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-5">
                        <p class="text-muted mb-3">Want to login on website?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i> Login to Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="app-footer">
        <div class="container">
            <p class="mb-2">
                &copy; {{ date('Y') }} {{ $appSettings->app_name ?? 'Allied Group' }} | All Rights Reserved
            </p>
            <p class="mb-0">
                <small>Developed by <a href="tel:01811480222">Mir Javed Jeetu</a> | <a href="https://metasoftinfo.com">MetaSoft</a></small>
            </p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
