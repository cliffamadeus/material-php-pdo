<?php

function logActivity(
    $pdo,
    $user_id,
    $email,
    $action,
    $status = 'success'
) {
    try {

        // Get client IP address
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'Unknown';

        // Handle forwarded IP addresses
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }

        // Get browser / user agent
        $user_agent = substr(
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            0,
            255
        );

        /*
        |--------------------------------------------------------------------------
        | Insert Activity Log
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (
                user_id,
                user_email,
                activity_log_action,
                activity_log_status,
                activity_log_ip_address,
                activity_log_user_agent
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $user_id,
            $email,
            $action,
            $status,
            $ip,
            $user_agent
        ]);

    } catch (PDOException $e) {

        error_log(
            "Activity Log Error: " . $e->getMessage()
        );

        return false;
    }
}