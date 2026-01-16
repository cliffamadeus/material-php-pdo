<?php
require_once '../../config/config.php';
requireRole('manager');

// Get statistics for manager
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user' AND is_verified = 0");
$unverifiedUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

renderHeader('Manager Dashboard');
?>

<div class="nav">
    <a href="<?php echo BASE_URL; ?>/app/users/dashboard.php">Manage Users</a>
    <a href="<?php echo BASE_URL; ?>/app/users/user-create.php">Create User</a>
    <a href="<?php echo BASE_URL; ?>/app/auth/signout.php">Logout</a>
</div>

<h1>Manager Dashboard</h1>

<div class="info-box">
    <strong>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></strong><br>
    Role: <span class="badge badge-manager">Manager</span>
</div>

<h2>User Statistics</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;">
    <div style="background: #28a745; color: white; padding: 20px; border-radius: 8px;">
        <h3 style="margin: 0; font-size: 32px;"><?php echo $totalUsers; ?></h3>
        <p style="margin: 5px 0 0; opacity: 0.9;">Total Users</p>
    </div>
    <div style="background: #6c757d; color: white; padding: 20px; border-radius: 8px;">
        <h3 style="margin: 0; font-size: 32px;"><?php echo $unverifiedUsers; ?></h3>
        <p style="margin: 5px 0 0; opacity: 0.9;">Unverified Users</p>
    </div>
</div>

<h2>Manager Capabilities:</h2>
<ul>
    <li><strong>Manage Users:</strong> Create, edit, and delete regular users only</li>
    <li><strong>View Reports:</strong> Access user data and analytics</li>
    <li><strong>Team Management:</strong> Oversee user activities</li>
    <li><strong>Restrictions:</strong>
        <ul style="margin-top: 5px;">
            <li>Cannot manage Admins or other Managers</li>
            <li>Cannot delete own account</li>
            <li>Cannot change own role</li>
        </ul>
    </li>
</ul>

<h2>Access Hierarchy</h2>
<div class="info-box">
    <strong>Admin → Full Control</strong><br>
    ↓ Manages you<br>
    <strong>Manager (You) → Limited Control</strong><br>
    ↓ Can manage Users only<br>
    <strong>User → No Management Rights</strong>
</div>

<?php renderFooter(); ?>