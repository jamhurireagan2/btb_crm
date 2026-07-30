<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: dashboard.php?error=Client ID missing');
    exit;
}

$id = $_GET['id'];

// Fetch client data
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    header('Location: dashboard.php?error=Client not found');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email'] ?? '');
    $policy_type = $_POST['policy_type'];
    $expiry_date = $_POST['expiry_date'];

    if (empty($full_name) || empty($phone) || empty($policy_type) || empty($expiry_date)) {
        $error = 'Please fill in all required fields';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE clients 
                                   SET full_name = ?, phone = ?, email = ?, policy_type = ?, expiry_date = ? 
                                   WHERE id = ?");
            $stmt->execute([$full_name, $phone, $email, $policy_type, $expiry_date, $id]);
            
            header('Location: dashboard.php?msg=Client updated successfully!');
            exit;
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client - BTB Insurance</title>
    <link rel="stylesheet" href="assets/style.css">
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
            <a href="add_client.php">
                <i class="fas fa-user-plus"></i>
                <span>Add Client</span>
            </a>
            <a href="#" class="active">
                <i class="fas fa-edit"></i>
                <span>Edit Client</span>
            </a>
            <a href="#">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
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
                <h1><i class="fas fa-edit"></i> Edit Client</h1>
                <p class="page-subtitle">Update client information</p>
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
                        <input type="text" name="full_name" value="<?= htmlspecialchars($client['full_name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($client['phone']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($client['email'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Policy Type <span class="required">*</span></label>
                        <select name="policy_type" required>
                            <option value="Motor" <?= $client['policy_type'] == 'Motor' ? 'selected' : '' ?>>Motor Vehicle</option>
                            <option value="Life" <?= $client['policy_type'] == 'Life' ? 'selected' : '' ?>>Life Insurance</option>
                            <option value="Health" <?= $client['policy_type'] == 'Health' ? 'selected' : '' ?>>Health Insurance</option>
                            <option value="Property" <?= $client['policy_type'] == 'Property' ? 'selected' : '' ?>>Property Insurance</option>
                            <option value="Travel" <?= $client['policy_type'] == 'Travel' ? 'selected' : '' ?>>Travel Insurance</option>
                            <option value="Business" <?= $client['policy_type'] == 'Business' ? 'selected' : '' ?>>Business Insurance</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Expiry Date <span class="required">*</span></label>
                        <input type="date" name="expiry_date" value="<?= $client['expiry_date'] ?>" required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Update Client
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