<?php

require_once 'config/config.php';
require_once 'includes/activity-logger.php';

$message = '';
$messageType = '';


// ==========================================
// ACTIVITY LOGGER
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = trim($_POST['action'] ?? '');

    // Get current logged-in user
    $userId = $_SESSION['user_id'] ?? null;
    $email  = $_SESSION['email'] ?? null;

    if ($action === '') {

        $message = 'No activity selected.';
        $messageType = 'error';

    } else {

        // Randomly generate status
        $status = rand(0, 1) === 1
            ? 'success'
            : 'failed';

        $result = logActivity(
            $pdo,
            $userId,
            $email,
            $action,
            $status
        );

        if ($result) {

            $message =
                ucfirst(str_replace('_', ' ', $action))
                . ' logged as '
                . ucfirst($status)
                . '.';

            $messageType = $status;

        } else {

            $message = 'Failed to log activity.';
            $messageType = 'error';
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Activity Logger Laboratory</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .card {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
            color: #1976d2;
        }

        .description {
            color: #666;
            margin-bottom: 25px;
        }

        .message {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: bold;
        }

        .message.success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .message.failed {
            background: #ffebee;
            color: #c62828;
        }

        .message.error {
            background: #fff3e0;
            color: #e65100;
        }

        .section-title {
            margin-top: 25px;
            margin-bottom: 15px;
            color: #333;
        }

        .activity-grid {
            display: grid;
            grid-template-columns:
                repeat(3, 1fr);

            gap: 12px;
        }

        .activity-button {
            border: none;
            background: #1976d2;
            color: white;
            padding: 18px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            transition: 0.2s;
        }

        .activity-button:hover {
            background: #1565c0;
            transform: translateY(-2px);
        }

        .activity-button:active {
            transform: translateY(0);
        }

        .info-box {
            margin-top: 25px;
            padding: 15px;
            background: #e3f2fd;
            border-left: 4px solid #1976d2;
            color: #333;
            border-radius: 4px;
        }

        .legend {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        @media (max-width: 600px) {

            body {
                padding: 15px;
            }

            .card {
                padding: 20px;
            }

            .activity-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <h1>
            Activity Logger
        </h1>

        <p class="description">
            Laboratory activity for testing the
            activity logging system.
        </p>


        <?php if ($message): ?>

            <div class="message <?php echo htmlspecialchars($messageType); ?>">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <h2 class="section-title">
            Select Activity
        </h2>


        <form method="POST">

            <div class="activity-grid">

                <button
                    type="submit"
                    name="action"
                    value="login"
                    class="activity-button"
                >
                    Login
                </button>

                <button
                    type="submit"
                    name="action"
                    value="logout"
                    class="activity-button"
                >
                    Logout
                </button>

                <button
                    type="submit"
                    name="action"
                    value="view_dashboard"
                    class="activity-button"
                >
                    View Dashboard
                </button>

                <button
                    type="submit"
                    name="action"
                    value="create"
                    class="activity-button"
                >
                    Create Record
                </button>

                <button
                    type="submit"
                    name="action"
                    value="update"
                    class="activity-button"
                >
                    Update Record
                </button>

                <button
                    type="submit"
                    name="action"
                    value="delete"
                    class="activity-button"
                >
                    Delete Record
                </button>

                <button
                    type="submit"
                    name="action"
                    value="view_profile"
                    class="activity-button"
                >
                    View Profile
                </button>

                <button
                    type="submit"
                    name="action"
                    value="change_password"
                    class="activity-button"
                >
                    Change Password
                </button>

                <button
                    type="submit"
                    name="action"
                    value="search"
                    class="activity-button"
                >
                    Search
                </button>

            </div>

        </form>


        <div class="info-box">

            <strong>
                Laboratory Activity
            </strong>

            <p>
                Click an activity button to create
                an activity log.
            </p>

            <p>
                The application randomly assigns
                either <strong>Success</strong> or
                <strong>Failed</strong>.
            </p>

        </div>


        <div class="legend">

            <strong>Activity Flow:</strong>

            <ol>
                <li>Select an activity.</li>
                <li>Generate a random status.</li>
                <li>Call <code>logActivity()</code>.</li>
                <li>Save the record to the database.</li>
                <li>View the result in the dashboard.</li>
            </ol>

        </div>

    </div>

</div>

</body>

</html>