<?php
require_once '../../config/config.php';
requireLogin();

$message = '';
$success = false;
$verificationLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    
    if (!in_array($role, ['admin', 'manager', 'user'])) {
        $role = 'user';
    }
    
    $verificationToken = bin2hex(random_bytes(32));
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, role, verification_token, is_verified, created_at) 
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$email, $hashedPassword, $role, $verificationToken]);
        
        $verificationLink = BASE_URL . "/app/auth/verify-email.php?token=" . $verificationToken;
        $message = "User created successfully! Verification email sent.";
        $success = true;
        
    } catch(PDOException $e) {
        $message = "Error creating user: " . $e->getMessage();
    }
}

renderHeader('Create User');
?>

<div class="nav">
    <a href="<?php echo BASE_URL; ?>/app/users/dashboard.php">Back to Users</a>
    <a href="<?php echo BASE_URL; ?>/app/auth/signout.php">Logout</a>
</div>

<h1>Create New User</h1>

<?php if ($message): ?>
    <div class="<?php echo $success ? 'success' : 'error'; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<?php if ($verificationLink): ?>
    <div class="info-box">
        <strong>Email Verification Link (simulated):</strong><br>
        <a href="<?php echo $verificationLink; ?>" target="_blank"><?php echo $verificationLink; ?></a>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    
    <div class="form-group">
        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="user">User</option>
            <option value="manager">Manager</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    
    <button type="submit">Create User</button>
</form>

<?php renderFooter(); ?>