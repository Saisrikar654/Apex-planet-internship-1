<?php
// Database Configuration
$db_host = 'localhost';
$db_name = 'blog';
$db_user = 'root';
$db_pass = '';
$db_charset = 'utf8mb4';

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors to users
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/error.log');

// Database Connection
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    $pdo->exec("SET time_zone = '+00:00'");
    
} catch (PDOException $e) {
    error_log('[' . date('Y-m-d H:i:s') . '] Database Error: ' . $e->getMessage());
    die('A database error occurred. Please try again later.');
}

// Session Configuration
session_set_cookie_params([
    'lifetime' => 86400,        // 1 day
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

// Security Functions
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validatePassword(string $password): array {
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    return $errors;
}

// User Role Functions
function getUserRole(int $userId): ?string {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log('Get User Role Error: ' . $e->getMessage());
        return null;
    }
}

function hasPermission(string $requiredRole): bool {
    if (!isset($_SESSION['user_id'])) return false;
    
    $userRole = $_SESSION['user_role'] ?? getUserRole($_SESSION['user_id']);
    if (!$userRole) return false;
    
    $roleHierarchy = ['admin' => 2, 'editor' => 1, 'user' => 0];
    return ($roleHierarchy[$userRole] ?? 0) >= $roleHierarchy[$requiredRole];
}

// Redirect Functions
function redirectIfNotLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit;
    }
}

function redirectIfNotAuthorized(string $requiredRole) {
    redirectIfNotLoggedIn();
    if (!hasPermission($requiredRole)) {
        header("Location: unauthorized.php");
        exit;
    }
}

// Database Helper Functions
function executeQuery(string $sql, array $params = []): PDOStatement {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log('[' . date('Y-m-d H:i:s') . '] Query Error: ' . $e->getMessage() . " | Query: $sql");
        throw new RuntimeException('Database operation failed');
    }
}

// Utility Functions
function redirect(string $url, int $statusCode = 303): void {
    header("Location: $url", true, $statusCode);
    exit;
}

function getCurrentUrl(): string {
    return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . 
           ($_SERVER['HTTP_HOST'] ?? 'localhost') . 
           ($_SERVER['REQUEST_URI'] ?? '/');
}
?>