<?php
require_once '../../config/config.php';
requireLogin();

$stmt = $pdo->prepare("SELECT id, email, role, is_verified, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

renderHeader('User Management');
?>

<div class="nav">
    <a href="<?php echo BASE_URL; ?>/app/<?php echo $_SESSION['role']; ?>/dashboard.php">Dashboard</a>
    <a href="<?php echo BASE_URL; ?>/app/users/user-create.php">Create User</a>
    <a href="<?php echo BASE_URL; ?>/app/auth/signout.php">Logout</a>
</div>

<h1>User Management</h1>

<p>Total Users: <strong><?php echo count($users); ?></strong></p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Role</th>
            <th>Verified</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td>
                <span class="badge badge-<?php echo $user['role']; ?>">
                    <?php echo ucfirst($user['role']); ?>
                </span>
            </td>
            <td>
                <span class="badge badge-<?php echo $user['is_verified'] ? 'verified' : 'unverified'; ?>">
                    <?php echo $user['is_verified'] ? 'Yes' : 'No'; ?>
                </span>
            </td>
            <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
            <td>
                <a href="user-view.php?user_id=<?php echo $user['id']; ?>">View</a> | 
                <a href="user-update.php?user_id=<?php echo $user['id']; ?>">Edit</a> | 
                <a href="user-delete.php?user_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php renderFooter(); ?>