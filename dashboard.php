<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <span>BTB Insurance</span>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="active">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
            <a href="add_client.php">
                <i class="fas fa-user-plus"></i>
                Add Client
            </a>
            <a href="#">
                <i class="fas fa-file-alt"></i>
                Reports
            </a>
            <a href="#">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <p class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></p>
                    <p class="user-role">Administrator</p>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="page-title">
                <h1>Dashboard</h1>
                <p>Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>!</p>
            </div>
            <div class="top-bar-actions">
                <div class="search-box">
                    <form method="GET" style="display: flex; gap: 10px;">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" placeholder="Search clients..." 
                                   value="<?= htmlspecialchars($search_term) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if($search_term): ?>
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
                <a href="add_client.php" class="btn btn-success">
                    <i class="fas fa-user-plus"></i>
                    Add Client
                </a>
            </div>
        </header>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($totalClients) ?></h3>
                    <p>Total Clients</p>
                </div>
            </div>
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($activePolicies) ?></h3>
                    <p>Active Policies</p>
                </div>
            </div>
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($expiringSoon) ?></h3>
                    <p>Expiring in 30 Days</p>
                </div>
            </div>
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($expired) ?></h3>
                    <p>Expired Policies</p>
                </div>
            </div>
        </div>

        <!-- Client Table -->
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Client Records</h2>
                <span class="record-count"><?= count($clients) ?> records</span>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Phone</th>
                            <th>Policy #</th>
                            <th>Type</th>
                            <th>Expiry Date</th>
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
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <div class="client-name">
                                        <div class="client-avatar">
                                            <?= strtoupper(substr($client['full_name'], 0, 1)) ?>
                                        </div>
                                        <span><?= htmlspecialchars($client['full_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($client['phone']) ?></td>
                                <td><code class="policy-code"><?= htmlspecialchars($client['policy_number']) ?></code></td>
                                <td><span class="badge"><?= htmlspecialchars($client['policy_type']) ?></span></td>
                                <td><?= date('d M Y', strtotime($client['expiry_date'])) ?></td>
                                <td>
                                    <?php if($isExpired): ?>
                                        <span class="status-badge expired">Expired</span>
                                    <?php elseif($isExpiring): ?>
                                        <span class="status-badge expiring">Expiring Soon</span>
                                    <?php else: ?>
                                        <span class="status-badge active">Active</span>
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
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h3>No clients found</h3>
                                        <p><?= $search_term ? 'No results match your search.' : 'Start by adding your first client!' ?></p>
                                        <?php if(!$search_term): ?>
                                            <a href="add_client.php" class="btn btn-success" style="margin-top: 15px;">
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
            <p>&copy; <?= date('Y') ?> BTB Insurance Brokers Ltd. All rights reserved.</p>
            <p class="footer-version">v2.0</p>
        </footer>
    </main>
</body>
</html>