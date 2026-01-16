<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'sample_php_pdo');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Email configuration (for verification)
define('MAIL_FROM', 'noreply@yourdomain.com');
define('BASE_URL', 'http://localhost/php-pdo');

function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/index.php');
    }
}

function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        die("Access denied. Required role: $role");
    }
}

function renderHeader($title) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?></title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #333; margin-bottom: 20px; }
            h2 { color: #555; margin: 20px 0 10px; }
            .nav { background: #007bff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            .nav a { color: white; text-decoration: none; margin-right: 15px; }
            .nav a:hover { text-decoration: underline; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
            input[type="text"], input[type="email"], input[type="password"], select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
            button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
            button:hover { background: #0056b3; }
            .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
            .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            table th { background: #007bff; color: white; }
            .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; }
            .badge-admin { background: #dc3545; color: white; }
            .badge-manager { background: #ffc107; color: #333; }
            .badge-user { background: #28a745; color: white; }
            .badge-verified { background: #28a745; color: white; }
            .badge-unverified { background: #6c757d; color: white; }
            .info-box { background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class="container">
    <?php
}

function renderFooter() {
    ?>
        </div>
    </body>
    </html>
    <?php
}
