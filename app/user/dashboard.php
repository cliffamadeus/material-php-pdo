<?php
require_once '../../config/config.php';
requireRole('user');

// Get current user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

renderHeader('User Dashboard');
?>

<div class="nav">
    <a href="<?php echo BASE_URL; ?>/app/users/user-view.php?user_id=<?php echo $_SESSION['user_id']; ?>">My Profile</a>
    <a href="<?php echo BASE_URL; ?>/app/auth/signout.php">Logout</a>
</div>

<h1>User Dashboard</h1>

<div class="info-box">
    <strong>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></strong><br>
    Role: <span class="badge badge-user">User</span>
</div>

<h2>Your Account Information</h2>

<table>
    <tr>
        <th style="width: 200px;">Email:</th>
        <td><?php echo htmlspecialchars($currentUser['email']); ?></td>
    </tr>
    <tr>
        <th>Account Status:</th>
        <td>
            <span class="badge badge-<?php echo $currentUser['is_verified'] ? 'verified' : 'unverified'; ?>">
                <?php echo $currentUser['is_verified'] ? 'Verified' : 'Unverified'; ?>
            </span>
        </td>
    </tr>
    <tr>
        <th>Member Since:</th>
        <td><?php echo date('F j, Y', strtotime($currentUser['created_at'])); ?></td>
    </tr>
</table>

<h2>User Capabilities:</h2>
<ul>
    <li><strong>View Profile:</strong> Access your personal information</li>
    <li><strong>Update Profile:</strong> Change your email and password (via admin/manager)</li>
    <li><strong>Basic Access:</strong> Use application features</li>
    <li><strong>Restrictions:</strong>
        <ul style="margin-top: 5px;">
            <li>Cannot manage other users</li>
            <li>Cannot change own role</li>
            <li>Cannot delete own account</li>
            <li>No administrative access</li>
        </ul>
    </li>
</ul>

<h2>Access Hierarchy</h2>
<div class="info-box">
    <strong>Admin → Full Control</strong><br>
    ↓ Manages Managers<br>
    <strong>Manager → Limited Control</strong><br>
    ↓ Manages you<br>
    <strong>User (You) → View Only</strong><br>
    ↓ Can only view own profile
</div>

<div class="info-box" style="background: #fff3cd; border-left-color: #ffc107; margin-top: 20px;">
    <strong>Need help?</strong><br>
    Contact your manager or administrator to update your profile or request changes.
</div>

<?php renderFooter(); ?>