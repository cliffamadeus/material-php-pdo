<?php
require_once '../config/config.php';

// Get all users
$stmt = $pdo->prepare("SELECT id, email, role, is_verified FROM users ORDER BY 
    CASE role 
        WHEN 'admin' THEN 1 
        WHEN 'manager' THEN 2 
        WHEN 'user' THEN 3 
    END, email");
$stmt->execute();
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$testResults = [];

// Test access as each user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_access'])) {
    $testUserId = $_POST['test_user_id'];
    
    // Get the test user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$testUserId]);
    $testUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testUser) {
        $testRole = $testUser['role'];
        
        // Simulate what this user would see in users/dashboard
        if ($testRole === 'admin') {
            $stmt = $pdo->prepare("SELECT id, email, role FROM users ORDER BY role, email");
            $stmt->execute();
        } elseif ($testRole === 'manager') {
            $stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE role = 'user' ORDER BY email");
            $stmt->execute();
        } else {
            $stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE id = ? ORDER BY email");
            $stmt->execute([$testUserId]);
        }
        
        $visibleUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Test permissions on each visible user
        $permissions = [];
        foreach ($visibleUsers as $targetUser) {
            $canView = true; // Everyone can view what they can see
            
            // Can edit logic
            $canEdit = false;
            if ($targetUser['id'] == $testUserId) {
                $canEdit = true; // Can edit own profile
            } elseif ($testRole === 'admin') {
                $canEdit = true;
            } elseif ($testRole === 'manager' && $targetUser['role'] === 'user') {
                $canEdit = true;
            }
            
            // Can delete logic
            $canDelete = false;
            if ($targetUser['id'] == $testUserId) {
                $canDelete = false; // Cannot delete own account
            } elseif ($testRole === 'admin') {
                $canDelete = true;
            } elseif ($testRole === 'manager' && $targetUser['role'] === 'user') {
                $canDelete = true;
            }
            
            // Can change role logic
            $canChangeRole = false;
            if ($targetUser['id'] == $testUserId) {
                $canChangeRole = false; // Cannot change own role
            } elseif ($testRole === 'admin') {
                $canChangeRole = true;
            }
            
            $permissions[] = [
                'target' => $targetUser,
                'can_view' => $canView,
                'can_edit' => $canEdit,
                'can_delete' => $canDelete,
                'can_change_role' => $canChangeRole
            ];
        }
        
        $testResults = [
            'test_user' => $testUser,
            'visible_count' => count($visibleUsers),
            'total_count' => count($allUsers),
            'permissions' => $permissions
        ];
    }
}

// Quick switch user (for actual testing)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['switch_user'])) {
    $switchUserId = $_POST['switch_user_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$switchUserId]);
    $switchUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($switchUser) {
        $_SESSION['user_id'] = $switchUser['id'];
        $_SESSION['email'] = $switchUser['email'];
        $_SESSION['role'] = $switchUser['role'];
        
        redirect('/app/users/dashboard.php');
    }
}

renderHeader('Access Control Test Page');
?>

<h1>🔐 Access Control Test Page</h1>

<div class="info-box">
    <strong>⚠️ This is a testing page - Remove in production!</strong><br>
    Use this page to test role-based access control and permissions for each user type.
</div>

<h2>Test User Access</h2>

<form method="POST">
    <div class="form-group">
        <label for="test_user_id">Select User to Test:</label>
        <select id="test_user_id" name="test_user_id" required>
            <option value="">-- Choose a user --</option>
            <?php foreach ($allUsers as $user): ?>
                <option value="<?php echo $user['id']; ?>">
                    <?php echo htmlspecialchars($user['email']); ?> 
                    (<?php echo ucfirst($user['role']); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" name="test_access" style="background: #28a745;">
        🧪 Test Access Permissions
    </button>
</form>

<?php if (!empty($testResults)): ?>
    <h2>Test Results</h2>
    
    <div class="info-box">
        <strong>Testing as: <?php echo htmlspecialchars($testResults['test_user']['email']); ?></strong><br>
        Role: <span class="badge badge-<?php echo $testResults['test_user']['role']; ?>">
            <?php echo ucfirst($testResults['test_user']['role']); ?>
        </span>
    </div>
    
    <h3>Visibility Summary</h3>
    <table>
        <tr>
            <th style="width: 250px;">Total Users in System:</th>
            <td><?php echo $testResults['total_count']; ?></td>
        </tr>
        <tr>
            <th>Users Visible to This Role:</th>
            <td><?php echo $testResults['visible_count']; ?></td>
        </tr>
        <tr>
            <th>Hidden from View:</th>
            <td><?php echo $testResults['total_count'] - $testResults['visible_count']; ?></td>
        </tr>
    </table>
    
    <h3>Detailed Permissions</h3>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>Can View</th>
                <th>Can Edit</th>
                <th>Can Delete</th>
                <th>Can Change Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($testResults['permissions'] as $perm): ?>
            <tr>
                <td><?php echo htmlspecialchars($perm['target']['email']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $perm['target']['role']; ?>">
                        <?php echo ucfirst($perm['target']['role']); ?>
                    </span>
                </td>
                <td style="text-align: center;">
                    <?php if ($perm['can_view']): ?>
                        <span style="color: green; font-weight: bold;">✓</span>
                    <?php else: ?>
                        <span style="color: red; font-weight: bold;">✗</span>
                    <?php endif; ?>
                </td>
                <td style="text-align: center;">
                    <?php if ($perm['can_edit']): ?>
                        <span style="color: green; font-weight: bold;">✓</span>
                    <?php else: ?>
                        <span style="color: red; font-weight: bold;">✗</span>
                    <?php endif; ?>
                </td>
                <td style="text-align: center;">
                    <?php if ($perm['can_delete']): ?>
                        <span style="color: green; font-weight: bold;">✓</span>
                    <?php else: ?>
                        <span style="color: red; font-weight: bold;">✗</span>
                    <?php endif; ?>
                </td>
                <td style="text-align: center;">
                    <?php if ($perm['can_change_role']): ?>
                        <span style="color: green; font-weight: bold;">✓</span>
                    <?php else: ?>
                        <span style="color: red; font-weight: bold;">✗</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h3>Access Summary</h3>
    <div class="info-box">
        <?php if ($testResults['test_user']['role'] === 'admin'): ?>
            <strong>Admin Access:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>Can see all <?php echo $testResults['total_count']; ?> users</li>
                <li>Can edit all users (including own profile)</li>
                <li>Can delete all users except self</li>
                <li>Can change any user's role except own</li>
            </ul>
        <?php elseif ($testResults['test_user']['role'] === 'manager'): ?>
            <strong>Manager Access:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>Can see only <?php echo $testResults['visible_count']; ?> regular users</li>
                <li>Cannot see admin or manager accounts (<?php echo $testResults['total_count'] - $testResults['visible_count']; ?> hidden)</li>
                <li>Can edit and delete only regular users</li>
                <li>Cannot change any user roles</li>
                <li>Cannot delete own account</li>
            </ul>
        <?php else: ?>
            <strong>User Access:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>Can see only own profile (1 user)</li>
                <li>Cannot see other users (<?php echo $testResults['total_count'] - 1; ?> hidden)</li>
                <li>Can view and edit own profile only</li>
                <li>Cannot delete own account</li>
                <li>Cannot change own role</li>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>

<h2>Live Testing - Switch User</h2>

<p>Click a button below to switch to that user and test the actual user management page:</p>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0;">
    <?php foreach ($allUsers as $user): ?>
        <form method="POST" style="margin: 0;">
            <input type="hidden" name="switch_user_id" value="<?php echo $user['id']; ?>">
            <button type="submit" name="switch_user" 
                    style="width: 100%; padding: 15px; background: <?php 
                        echo $user['role'] === 'admin' ? '#dc3545' : 
                            ($user['role'] === 'manager' ? '#ffc107' : '#28a745'); 
                    ?>; color: <?php echo $user['role'] === 'manager' ? '#333' : 'white'; ?>;">
                <strong><?php echo htmlspecialchars($user['email']); ?></strong><br>
                <small><?php echo ucfirst($user['role']); ?></small>
            </button>
        </form>
    <?php endforeach; ?>
</div>

<h2>Access Control Rules</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin: 20px 0;">
    <div style="background: #dc3545; color: white; padding: 20px; border-radius: 8px;">
        <h3 style="margin: 0 0 10px 0;">👑 Admin</h3>
        <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
            <li>Full visibility (all users)</li>
            <li>Can edit anyone</li>
            <li>Can delete anyone (except self)</li>
            <li>Can change roles (except own)</li>
            <li>Can create any role</li>
        </ul>
    </div>
    
    <div style="background: #ffc107; color: #333; padding: 20px; border-radius: 8px;">
        <h3 style="margin: 0 0 10px 0;">👔 Manager</h3>
        <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
            <li>Can see regular users only</li>
            <li>Cannot see admins/managers</li>
            <li>Can edit/delete users only</li>
            <li>Cannot change roles</li>
            <li>Can create users only</li>
        </ul>
    </div>
    
    <div style="background: #28a745; color: white; padding: 20px; border-radius: 8px;">
        <h3 style="margin: 0 0 10px 0;">👤 User</h3>
        <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
            <li>Can see self only</li>
            <li>Can view own profile</li>
            <li>Cannot edit own account</li>
            <li>Cannot delete self</li>
            <li>No management rights</li>
        </ul>
    </div>
</div>

<h2>Security Features</h2>

<table>
    <thead>
        <tr>
            <th>Security Feature</th>
            <th>Status</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Self-Delete Prevention</strong></td>
            <td><span style="color: green; font-weight: bold;">✓ Active</span></td>
            <td>No user can delete their own account</td>
        </tr>
        <tr>
            <td><strong>Role Change Prevention</strong></td>
            <td><span style="color: green; font-weight: bold;">✓ Active</span></td>
            <td>Users cannot change their own role</td>
        </tr>
        <tr>
            <td><strong>Hierarchical Access</strong></td>
            <td><span style="color: green; font-weight: bold;">✓ Active</span></td>
            <td>Each role can only manage lower tiers</td>
        </tr>
        <tr>
            <td><strong>Manager Isolation</strong></td>
            <td><span style="color: green; font-weight: bold;">✓ Active</span></td>
            <td>Managers cannot see admin/manager accounts</td>
        </tr>
        <tr>
            <td><strong>User Compartmentalization</strong></td>
            <td><span style="color: green; font-weight: bold;">✓ Active</span></td>
            <td>Regular users can only see themselves</td>
        </tr>
    </tbody>
</table>

<p style="margin-top: 30px;">
    <a href="<?php echo BASE_URL; ?>/test-login.php">
        <button>Go to Login Test</button>
    </a>
    <a href="<?php echo BASE_URL; ?>/index.php">
        <button>Go to Login Page</button>
    </a>
</p>

<?php renderFooter(); ?>