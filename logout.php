<?php
/**
 * Logout
 */

require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

logActivity('Logout', 'Authentication', $_SESSION['user_id'] ?? null);

session_destroy();
header('Location: ' . BASE_URL . '/login.php?msg=You%20have%20been%20logged%20out');
exit;
?>