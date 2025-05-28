<?php
/**
 * Database Configuration and Helper Functions
 * 
 * Handles database connections, sessions, and security functions
 */

// ========================
// Database Configuration
// ========================
$db_host = 'localhost';
$db_name = 'blog';
$db_user = 'root';
$db_pass = '';
$db_charset = 'utf8mb4';

// ========================
// Error Reporting
// ========================
error_reporting(E_ALL);
ini_set('display_errors', 1);  // Disable in production

// ========================
// Database Connection
// ========================
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => true
    ];
    
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    $pdo->exec("SET time_zone = '+00:00'");
    
} catch (PDOException $e) {
    error_log('[' . date('Y-m-d H:i:s') . '] Database Error: ' . $e->getMessage() . "\n", 3, __DIR__.'/error.log');
    die(json_encode([
        'status' => 'error',
        'message' => 'Database connection failed',
        'error_code' => 500
    ]));
}

// ========================
// Session Configuration
// ========================
session_set_cookie_params([
    'lifetime' => 86400,        // 1 day
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

// ========================
// Database Helper Functions
// ========================

/**
 * Execute a prepared statement with parameters
 * 
 * @param string $sql The SQL query
 * @param array $params The parameters for the query
 * @return PDOStatement The executed statement
 */
function executeQuery(string $sql, array $params = []): PDOStatement {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log('[' . date('Y-m-d H:i:s') . '] Query Error: ' . $e->getMessage() . " | Query: $sql\n", 3, __DIR__.'/error.log');
        throw new RuntimeException('Database operation failed');
    }
}

// ========================
// Security Functions
// ========================

/**
 * Generate CSRF token and store in session
 * 
 * @return string The generated token
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token against session
 * 
 * @param string $token The token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize user input
 * 
 * @param mixed $data The data to sanitize
 * @return mixed The sanitized data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ========================
// Utility Functions
// ========================

/**
 * Redirect to another page
 * 
 * @param string $url The URL to redirect to
 * @param int $statusCode HTTP status code (default: 303)
 */
function redirect(string $url, int $statusCode = 303): void {
    header("Location: $url", true, $statusCode);
    exit;
}

/**
 * Get current URL
 * 
 * @return string The current URL
 */
function getCurrentUrl(): string {
    return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . 
           ($_SERVER['HTTP_HOST'] ?? 'localhost') . 
           ($_SERVER['REQUEST_URI'] ?? '/');
}
?>