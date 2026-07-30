<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
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
<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2><i class="fas fa-edit"></i> Edit Client</h2>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
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
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Client
            </button>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>