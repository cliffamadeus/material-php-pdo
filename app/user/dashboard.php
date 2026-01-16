<?php
require_once '../../config/config.php';
requireRole('user');

renderHeader('User Dashboard');
?>

<div class="nav">
    <a href="<?php echo BASE_URL; ?>/app/users/dashboard.php">User Management</a>
    <a href="<?php echo BASE_URL; ?>/app/auth/signout.php">Logout</a>
</div>

<h1>User Dashboard</h1>

<div class="info-box">
    <strong>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></strong><br>
    Role: <span class="badge badge-user">User</span>
</div>

<h2>User Capabilities:</h2>
<ul>
    <li>View personal information</li>
    <li>Update profile</li>
    <li>Access basic features</li>
</ul>

<?php renderFooter(); ?>
