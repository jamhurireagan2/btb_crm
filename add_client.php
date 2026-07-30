<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email'] ?? '');
    $policy_number = trim($_POST['policy_number']);
    $policy_type = $_POST['policy_type'];
    $expiry_date = $_POST['expiry_date'];

    if (empty($full_name) || empty($phone) || empty($policy_number) || empty($expiry_date)) {
        $error = 'Please fill in all required fields';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO clients (full_name, phone, email, policy_number, policy_type, expiry_date) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $phone, $email, $policy_number, $policy_type, $expiry_date]);
            
            header('Location: dashboard.php?msg=Client added successfully!');
            exit;
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Policy number already exists. Please use a unique policy number.';
            } else {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Client - BTB Insurance</title>
    <link rel="stylesheet" href="assets/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="nav-left">
            <button class="mobile-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-brand">
                <div class="brand-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <span>BTB Insurance</span>
            </div>
        </div>
        <div class="nav-right">
            <div class="nav-user">
                <span class="user-greeting">Hi, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                </div>
                <button class="theme-toggle-nav" onclick="toggleTheme()" title="Toggle Theme">
                    <i class="fas fa-moon"></i>
                </button>
                <a href="logout.php" class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-menu">
            <a href="dashboard.php">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="add_client.php" class="active">
                <i class="fas fa-user-plus"></i>
                <span>Add Client</span>
            </a>
            <a href="#">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="#">
                <i class="fas fa-calendar-alt"></i>
                <span>Renewals</span>
            </a>
            <a href="#">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-user-plus"></i> Add New Client</h1>
                <p class="page-subtitle">Fill in the client details below</p>
            </div>
            <a href="dashboard.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="form-container">
            <?php if($error): ?>
                <div class="alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name <span class="required">*</span></label>
                        <input type="text" name="full_name" placeholder="Enter client's full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone" placeholder="Enter phone number" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" placeholder="Enter email address">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-file-alt"></i> Policy Number <span class="required">*</span></label>
                        <input type="text" name="policy_number" placeholder="Enter unique policy number" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Policy Type <span class="required">*</span></label>
                        <select name="policy_type" required>
                            <option value="">Select Policy Type</option>
                            <option value="Motor">Motor Vehicle</option>
                            <option value="Life">Life Insurance</option>
                            <option value="Health">Health Insurance</option>
                            <option value="Property">Property Insurance</option>
                            <option value="Travel">Travel Insurance</option>
                            <option value="Business">Business Insurance</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Expiry Date <span class="required">*</span></label>
                        <input type="date" name="expiry_date" required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Client
                    </button>
                    <a href="dashboard.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const icons = document.querySelectorAll('.theme-toggle-nav i');
            icons.forEach(icon => {
                if (document.body.classList.contains('dark-mode')) {
                    icon.className = 'fas fa-sun';
                } else {
                    icon.className = 'fas fa-moon';
                }
            });
        }
    </script>
</body>
</html>