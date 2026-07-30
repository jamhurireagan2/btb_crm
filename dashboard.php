<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get statistics
$totalClients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$expiringSoon = $pdo->query("SELECT COUNT(*) FROM clients WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND expiry_date >= CURDATE()")->fetchColumn();
$expired = $pdo->query("SELECT COUNT(*) FROM clients WHERE expiry_date < CURDATE()")->fetchColumn();
$activePolicies = $totalClients - $expired;

// Handle search
$search_term = $_GET['search'] ?? '';
$clients = [];

if ($search_term) {
    $sql = "SELECT * FROM clients 
            WHERE full_name LIKE ? 
            OR phone LIKE ? 
            OR policy_number LIKE ? 
            OR email LIKE ?
            ORDER BY created_at DESC";
    $search_param = "%$search_term%";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$search_param, $search_param, $search_param, $search_param]);
    $clients = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT * FROM clients ORDER BY created_at DESC LIMIT 50");
    $clients = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BTB Insurance</title>
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
            <div class="nav-search">
                <form method="GET">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search clients..." 
                           value="<?= htmlspecialchars($search_term) ?>">
                    <?php if($search_term): ?>
                        <a href="dashboard.php" class="clear-search"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                    <button type="submit" class="search-btn"><i class="fas fa-arrow-right"></i></button>
                </form>
            </div>
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
            <a href="dashboard.php" class="active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="add_client.php">
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
    <main class="main-content" id="mainContent">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($totalClients) ?></h3>
                    <p>Total Clients</p>
                </div>
                <div class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> 12%
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($activePolicies) ?></h3>
                    <p>Active Policies</p>
                </div>
                <div class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> 8%
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($expiringSoon) ?></h3>
                    <p>Expiring in 30 Days</p>
                </div>
                <div class="stat-trend down">
                    <i class="fas fa-arrow-down"></i> 5%
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red-dark">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($expired) ?></h3>
                    <p>Expired Policies</p>
                </div>
                <div class="stat-trend down">
                    <i class="fas fa-arrow-down"></i> 3%
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="add_client.php" class="quick-action">
                <i class="fas fa-user-plus"></i>
                <span>Add New Client</span>
            </a>
            <a href="#" class="quick-action">
                <i class="fas fa-file-export"></i>
                <span>Export Report</span>
            </a>
            <a href="#" class="quick-action">
                <i class="fas fa-bell"></i>
                <span>Renewal Alerts</span>
            </a>
        </div>

        <!-- Client Table -->
        <div class="table-container">
            <div class="table-header">
                <div>
                    <h2><i class="fas fa-list"></i> Client Records</h2>
                    <p class="table-subtitle">Manage your insurance clients</p>
                </div>
                <span class="record-count">
                    <i class="fas fa-database"></i> <?= count($clients) ?> records
                </span>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Policy</th>
                            <th>Type</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($clients) > 0): ?>
                            <?php foreach($clients as $index => $client): 
                                $today = new DateTime();
                                $expiry = new DateTime($client['expiry_date']);
                                $diff = $today->diff($expiry)->days;
                                $isExpired = $expiry < $today;
                                $isExpiring = !$isExpired && $diff <= 30;
                            ?>
                            <tr>
                                <td>
                                    <div class="client-info">
                                        <div class="client-avatar">
                                            <?= strtoupper(substr($client['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="client-name"><?= htmlspecialchars($client['full_name']) ?></div>
                                            <div class="client-email"><?= htmlspecialchars($client['email'] ?? 'No email') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <div><i class="fas fa-phone"></i> <?= htmlspecialchars($client['phone']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="policy-code"><?= htmlspecialchars($client['policy_number']) ?></span>
                                </td>
                                <td>
                                    <span class="badge"><?= htmlspecialchars($client['policy_type']) ?></span>
                                </td>
                                <td><?= date('d M Y', strtotime($client['expiry_date'])) ?></td>
                                <td>
                                    <?php if($isExpired): ?>
                                        <span class="status-badge expired">
                                            <i class="fas fa-times-circle"></i> Expired
                                        </span>
                                    <?php elseif($isExpiring): ?>
                                        <span class="status-badge expiring">
                                            <i class="fas fa-clock"></i> <?= $diff ?> days
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge active">
                                            <i class="fas fa-check-circle"></i> Active
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_client.php?id=<?= $client['id'] ?>" class="btn-action edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_client.php?id=<?= $client['id'] ?>" class="btn-action delete" 
                                           onclick="return confirm('Are you sure you want to delete this client?')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h3>No clients found</h3>
                                        <p><?= $search_term ? 'No results match your search.' : 'Start by adding your first client!' ?></p>
                                        <?php if(!$search_term): ?>
                                            <a href="add_client.php" class="btn-red">
                                                <i class="fas fa-user-plus"></i> Add Client
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <footer class="content-footer">
            <p>&copy; <?= date('Y') ?> <strong>BTB Insurance Brokers Ltd</strong>. All rights reserved.</p>
            <p class="footer-version">v3.0 <span class="dot">•</span> Premium</p>
        </footer>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const icons = document.querySelectorAll('.theme-toggle-nav i, .theme-toggle-float i');
            icons.forEach(icon => {
                if (document.body.classList.contains('dark-mode')) {
                    icon.className = 'fas fa-sun';
                } else {
                    icon.className = 'fas fa-moon';
                }
            });
        }

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
</body>
</html>