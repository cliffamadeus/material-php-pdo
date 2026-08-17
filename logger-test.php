<?php

require_once 'config/config.php';
require_once 'includes/activity-logger.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = trim($_POST['action'] ?? '');
    $status = $_POST['status'] ?? 'success';

    // Get current logged-in user
    $user_id = $_SESSION['user_id'] ?? null;
    $email   = $_SESSION['email'] ?? null;

    if ($action === '') {

        $message = 'Action is required.';

    } else {

        $result = logActivity(
            $pdo,
            $user_id,
            $email,
            $action,
            $status
        );

        if ($result) {
            $message = 'Activity logged successfully.';
        } else {
            $message = 'Failed to log activity.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Activity Logger Test</title>
</head>

<body>

    <h1>Activity Logger</h1>

    <?php if ($message): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <form method="POST">

        <div>

            <label for="action">
                Activity
            </label>

            <select name="action" id="action" required>

                <option value="">
                    Select Activity
                </option>

                <option value="login">
                    Login
                </option>

                <option value="logout">
                    Logout
                </option>

                <option value="view_dashboard">
                    View Dashboard
                </option>

                <option value="create">
                    Create Record
                </option>

                <option value="update">
                    Update Record
                </option>

                <option value="delete">
                    Delete Record
                </option>

            </select>

        </div>


        <br>


        <div>

            <label for="status">
                Status
            </label>

            <select name="status" id="status">

                <option value="success">
                    Success
                </option>

                <option value="failed">
                    Failed
                </option>

            </select>

        </div>


        <br>


        <button type="submit">
            Log Activity
        </button>

    </form>

</body>

</html>