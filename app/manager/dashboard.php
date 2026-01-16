<?php
require_once '../../config/config.php';
requireRole('manager');

renderHeader('Manager Dashboard');
?>

<div class="nav">
    <a href="<?php echo BASE_URL; ?>/app/users/dashboard.php">User Management</a>
    <a href="<?php echo BASE_URL; ?>/app/auth/signout.php">Logout</a>
</div>

<h1>Manager Dashboard</h1>

<div class="info-box">
    <strong>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></strong><br>
    Role: <span class="badge badge-manager">Manager</span>
</div>

<h2>Manager Capabilities:</h2>
<ul>
    <li>View user reports</li>
    <li>Manage team members</li>
    <li>Generate analytics</li>
    <li>Access manager tools</li>
</ul>

<?php renderFooter(); ?>