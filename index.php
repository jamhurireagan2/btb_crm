<?php
session_start();
// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB Insurance - Premium Client Management</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="landing-page">
    <!-- Navigation -->
    <nav class="landing-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <span>BTB Insurance</span>
            </div>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="login.php" class="btn-login">Sign In</a>
                <button class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-certificate"></i>
                    Insurance Management Platform
                </div>
                <h1>Manage Your <span class="highlight">Insurance Clients</span> With Confidence</h1>
                <p>Streamline your client management, track policies, and never miss a renewal with our powerful digital platform.</p>
                <div class="hero-buttons">
                    <a href="login.php" class="btn-primary">
                        <i class="fas fa-rocket"></i> Get Started
                    </a>
                    <a href="#features" class="btn-secondary">
                        <i class="fas fa-play-circle"></i> Learn More
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <h3>500+</h3>
                        <p>Active Clients</p>
                    </div>
                    <div class="stat-item">
                        <h3>1,200+</h3>
                        <p>Policies Managed</p>
                    </div>
                    <div class="stat-item">
                        <h3>98%</h3>
                        <p>Satisfaction Rate</p>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-card">
                    <div class="card-header">
                        <div class="card-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <span>Client Overview</span>
                    </div>
                    <div class="card-body">
                        <div class="card-stat">
                            <div class="stat-label">Total Clients</div>
                            <div class="stat-value">2</div>
                        </div>
                        <div class="card-stat">
                            <div class="stat-label">Active Policies</div>
                            <div class="stat-value">1</div>
                        </div>
                        <div class="card-stat">
                            <div class="stat-label">Expiring Soon</div>
                            <div class="stat-value text-warning">0</div>
                        </div>
                        <div class="card-stat">
                            <div class="stat-label">Renewals This Month</div>
                            <div class="stat-value text-success">5</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Features</span>
                <h2>Everything You Need to <span class="highlight">Manage Clients</span></h2>
                <p>Powerful tools designed to simplify insurance client management</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Client Management</h3>
                    <p>Add, edit, and manage all your clients in one centralized dashboard.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3>Policy Tracking</h3>
                    <p>Track policy types, numbers, and expiry dates effortlessly.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Renewal Alerts</h3>
                    <p>Get notified about upcoming renewals and never miss a deadline.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Smart Search</h3>
                    <p>Quickly find clients by name, phone, or policy number.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Reports & Analytics</h3>
                    <p>Visual insights into your client base and policy performance.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure & Reliable</h3>
                    <p>Enterprise-grade security with encrypted data and secure access.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section" id="about">
        <div class="container">
            <div class="about-grid">
                <div class="about-content">
                    <span class="section-tag">About Us</span>
                    <h2>BTB Insurance Brokers <span class="highlight">Since 2010</span></h2>
                    <p>We provide comprehensive insurance solutions and digital tools to help brokers manage their client relationships efficiently.</p>
                    <ul class="about-list">
                        <li><i class="fas fa-check-circle"></i> Trusted by 500+ clients</li>
                        <li><i class="fas fa-check-circle"></i> 24/7 Access to your portfolio</li>
                        <li><i class="fas fa-check-circle"></i> Expert support team</li>
                        <li><i class="fas fa-check-circle"></i> Modern, intuitive platform</li>
                    </ul>
                    <a href="login.php" class="btn-primary">Get Started Now</a>
                </div>
                <div class="about-image">
                    <div class="about-card">
                        <i class="fas fa-building"></i>
                        <h3>BTB Insurance</h3>
                        <p>Brokers Ltd</p>
                        <div class="about-stats">
                            <div>
                                <span>15+</span>
                                <p>Years Experience</p>
                            </div>
                            <div>
                                <span>98%</span>
                                <p>Client Satisfaction</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to <span class="highlight">Streamline</span> Your Client Management?</h2>
                <p>Join hundreds of insurance professionals using BTB Insurance platform.</p>
                <a href="login.php" class="btn-primary">
                    <i class="fas fa-arrow-right"></i> Get Started Free
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="brand-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <span>BTB Insurance</span>
                    <p>Professional insurance client management platform for brokers and agents.</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <a href="#features">Features</a>
                    <a href="#about">About</a>
                    <a href="login.php">Sign In</a>
                </div>
                <div class="footer-links">
                    <h4>Support</h4>
                    <a href="#">Help Center</a>
                    <a href="#">Contact Us</a>
                    <a href="#">Privacy Policy</a>
                </div>
                <div class="footer-social">
                    <h4>Connect With Us</h4>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> BTB Insurance Brokers Ltd. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const icon = document.querySelector('.theme-toggle i');
            if (document.body.classList.contains('dark-mode')) {
                icon.className = 'fas fa-sun';
            } else {
                icon.className = 'fas fa-moon';
            }
        }

        function toggleMobileMenu() {
            document.querySelector('.nav-links').classList.toggle('show');
        }
    </script>
</body>
</html>