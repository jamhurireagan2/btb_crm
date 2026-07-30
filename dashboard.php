<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

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
    $stmt = $pdo->query("SELECT * FROM clients ORDER BY created_at DESC");
    $clients = $stmt->fetchAll();
}

// Get statistics
$totalClients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$expiringSoon = $pdo->query("SELECT COUNT(*) FROM clients WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
?>
<?php include 'includes/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
    <h2><i class="fas fa-users"></i> Client Records</h2>
    <a href="add_client.php" class="btn btn-success">
        <i class="fas fa-user-plus"></i> Add New Client
    </a>
</div>

<!-- Statistics Cards -->
<div class="stats">
    <div class="stat-card">
        <h3><?= $totalClients ?></h3>
        <p><i class="fas fa-users"></i> Total Clients</p>
    </div>
    <div class="stat-card" style="border-left-color: #28a745;">
        <h3><?= $totalClients - $expiringSoon ?></h3>
        <p><i class="fas fa-check-circle"></i> Active Policies</p>
    </div>
    <div class="stat-card" style="border-left-color: #dc3545;">
        <h3><?= $expiringSoon ?></h3>
        <p><i class="fas fa-exclamation-triangle"></i> Expiring in 30 Days</p>
    </div>
</div>

<!-- Search Feature -->
<div class="search-box">
    <form method="GET" style="display: flex; gap: 10px; width: 100%; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="🔍 Search by Name, Phone, Email, or Policy #" 
               value="<?= htmlspecialchars($search_term) ?>" style="flex: 1;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Search
        </button>
        <?php if($search_term): ?>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php endif; ?>
    </form>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Email</th>
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
                    $isExpiring = strtotime($client['expiry_date']) <= strtotime('+30 days');
                    $isExpired = strtotime($client['expiry_date']) < time();
                ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><strong><?= htmlspecialchars($client['full_name']) ?></strong></td>
                    <td><?= htmlspecialchars($client['phone']) ?></td>
                    <td><?= htmlspecialchars($client['email'] ?? 'N/A') ?></td>
                    <td><code><?= htmlspecialchars($client['policy_number']) ?></code></td>
                    <td><span class="badge"><?= htmlspecialchars($client['policy_type']) ?></span></td>
                    <td><?= date('d M Y', strtotime($client['expiry_date'])) ?></td>
                    <td>
                        <?php if($isExpired): ?>
                            <span style="color: #dc3545; font-weight: bold;">⚠️ Expired</span>
                        <?php elseif($isExpiring): ?>
                            <span style="color: #ffc107; font-weight: bold;">⏳ Expiring Soon</span>
                        <?php else: ?>
                            <span style="color: #28a745; font-weight: bold;">✅ Active</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_client.php?id=<?= $client['id'] ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_client.php?id=<?= $client['id'] ?>" class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this client?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>No clients found</h3>
                            <p><?= $search_term ? 'No results match your search.' : 'Start by adding your first client!' ?></p>
                            <?php if(!$search_term): ?>
                                <a href="add_client.php" class="btn btn-success" style="margin-top: 10px;">
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

<?php include 'includes/footer.php'; ?>