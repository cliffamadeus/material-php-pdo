<?php
require_once '../../config/config.php';

$message = '';
$success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        $message = "Email verified successfully! You can now login.";
        $success = true;
    } else {
        $message = "Invalid verification token.";
    }
} else {
    $message = "No verification token provided.";
}

renderHeader('Email Verification');
?>

<h1>Email Verification</h1>

<?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($message); ?></div>
    <p><a href="<?php echo BASE_URL; ?>/index.php">Go to Login</a></p>
<?php else: ?>
    <div class="error"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php renderFooter(); ?>