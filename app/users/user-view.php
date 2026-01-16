<?php
require_once '../../config/config.php';
requireLogin();

$userId = $_GET['user_id'] ?? 0;

$stmt = $pdo->prepare("SELECT id, email, role, is_verified, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

renderHeader('View User');
?>

<div class="nav">
    <a href="<?php echo BASE_URL; ?>/app/users/dashboard.php">Back to Users</a>
    <a href="<?php echo BASE_URL; ?>/app/auth/signout.php">Logout</a>
</div>

<h1>User Details</h1>

<?php if ($user): ?>
    <table>
        <tr>
            <th>ID</th>
            <td><?php echo $user['id']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
        </tr>
        <tr>
            <th>Role</th>
            <td>
                <span class="badge badge-<?php echo $user['role']; ?>">
                    <?php echo ucfirst($user['role']); ?>
                </span>
            </td>
        </tr>
        <tr>
            <th>Verified</th>
            <td>
                <span class="badge badge-<?php echo $user['is_verified'] ? 'verified' : 'unverified'; ?>">
                    <?php echo $user['is_verified'] ? 'Yes' : 'No'; ?>
                </span>
            </td>
        </tr>
        <tr>
            <th>Created</th>
            <td><?php echo date('F j, Y, g:i a', strtotime($user['created_at'])); ?></td>
        </tr>
    </table>
    
    <p style="margin-top: 20px;">
        <a href="user-update.php?user_id=<?php echo $user['id']; ?>">
            <button>Edit User</button>
        </a>
    </p>
<?php else: ?>
    <p>User not found.</p>
<?php endif; ?>

<?php renderFooter(); ?>