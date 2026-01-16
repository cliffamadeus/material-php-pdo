<?php
require_once '../../config/config.php';
requireRole('admin');

renderHeader('Admin Dashboard');
?>

<div class="nav">
    <a href="<?php echo BASE_URL; ?>/app/users/dashboard.php">User Management</a>
    <a href="<?php echo BASE_URL; ?>/app/auth/signout.php">Logout</a>
</div>

<h1>Admin Dashboard</h1>

<div class="info-box">
    <strong>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></strong><br>
    Role: <span class="badge badge-admin">Admin</span>
</div>

<h2>Admin Capabilities:</h2>
<ul>
    <li>Full access to all resources</li>
    <li>Manage all users</li>
    <li>View all dashboards</li>
    <li>System configuration</li>
</ul>

<?php renderFooter(); ?>
