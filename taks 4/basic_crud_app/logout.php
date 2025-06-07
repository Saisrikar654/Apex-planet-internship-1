<?php
// logout.php
require 'db.php';

// Start the session
session_start();

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Unset all session variables
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page with logout message
$_SESSION['logout_message'] = 'You have been successfully logged out.';
header("Location: login.php");
exit;
?>