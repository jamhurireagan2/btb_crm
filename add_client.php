<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email'] ?? '');
    $policy_number = trim($_POST['policy_number']);
    $policy_type = $_POST['policy_type'];
    $expiry_date = $_POST['expiry_date'];

    // Validation
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
<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2><i class="fas fa-user-plus"></i> Add New Client</h2>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
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
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Save Client
            </button>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>