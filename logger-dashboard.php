<?php

require_once 'config/config.php';


// ==========================================
// STATISTICS
// ==========================================

$stmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM activity_logs
");

$totalActivities = $stmt->fetch(PDO::FETCH_ASSOC)['total'];


$stmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM activity_logs
    WHERE status = 'success'
");

$successfulActivities = $stmt->fetch(PDO::FETCH_ASSOC)['total'];


$stmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM activity_logs
    WHERE status = 'failed'
");

$failedActivities = $stmt->fetch(PDO::FETCH_ASSOC)['total'];


$stmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM activity_logs
    WHERE DATE(created_at) = CURDATE()
");

$todayActivities = $stmt->fetch(PDO::FETCH_ASSOC)['total'];


// ==========================================
// ACTIVITY BREAKDOWN
// ==========================================

$stmt = $pdo->query("
    SELECT action, COUNT(*) AS total
    FROM activity_logs
    GROUP BY action
    ORDER BY total DESC
");

$activityBreakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ==========================================
// RECENT ACTIVITIES
// ==========================================

$stmt = $pdo->query("
    SELECT
        id,
        user_id,
        email,
        action,
        status,
        ip_address,
        user_agent,
        created_at
    FROM activity_logs
    ORDER BY created_at DESC
    LIMIT 20
");

$recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Logger Dashboard</title>
<!--
    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 30px;
        }

        h1 {
            margin-bottom: 30px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 15px;
        }

        .number {
            font-size: 30px;
            font-weight: bold;
        }

        .section {
            background: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .failed {
            color: red;
            font-weight: bold;
        }

    </style>
-->
</head>

<body>


<h1>Activity Logger Dashboard</h1>


<!-- Statistics -->

<div class="stats">

    <div class="card">

        <h3>Total Activities</h3>

        <div class="number">
            <?php echo $totalActivities; ?>
        </div>

    </div>


    <div class="card">

        <h3>Successful Activities</h3>

        <div class="number">
            <?php echo $successfulActivities; ?>
        </div>

    </div>


    <div class="card">

        <h3>Failed Activities</h3>

        <div class="number">
            <?php echo $failedActivities; ?>
        </div>

    </div>


    <div class="card">

        <h3>Today's Activities</h3>

        <div class="number">
            <?php echo $todayActivities; ?>
        </div>

    </div>

</div>


<!-- Activity Breakdown -->

<div class="section">

    <h2>Activity Breakdown</h2>

    <table>

        <thead>

            <tr>
                <th>Activity</th>
                <th>Total</th>
            </tr>

        </thead>

        <tbody>

        <?php foreach ($activityBreakdown as $activity): ?>

            <tr>

                <td>
                    <?php
                    echo htmlspecialchars($activity['action']);
                    ?>
                </td>

                <td>
                    <?php
                    echo $activity['total'];
                    ?>
                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>


<!-- Recent Logs -->

<!-- Recent Logs -->

<div class="section">

    <h2>Recent Activities</h2>

    <table>

        <thead>

            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Action</th>
                <th>Status</th>
                <th>IP Address</th>
                <th>Browser / User-Agent</th>
            </tr>

        </thead>

        <tbody>

        <?php foreach ($recentActivities as $activity): ?>

            <tr>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $activity['created_at']
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $activity['email'] ?? 'Unknown'
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $activity['action']
                    );
                    ?>
                </td>

                <td>

                    <span class="<?php echo htmlspecialchars($activity['status']); ?>">

                        <?php
                        echo htmlspecialchars(
                            $activity['status']
                        );
                        ?>

                    </span>

                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $activity['ip_address']
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $activity['user_agent'] ?? 'Unknown'
                    );
                    ?>
                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>


</body>

</html>