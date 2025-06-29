<?php
if (function_exists('sanitizeInput')) return;

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Chișinău Guide');
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function sanitizeUrl($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

// Flash Message System
function flashMessage($name = '', $message = '', $class = 'success') {
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            if (!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            if (!empty($_SESSION[$name . '_class'])) {
                unset($_SESSION[$name . '_class']);
            }
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="alert alert-' . htmlspecialchars($class) . '">' . htmlspecialchars($_SESSION[$name]) . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

// Get flash message directly without echoing
function getFlashMessage($name = '') {
    if (isset($_SESSION[$name])) {
        $message = $_SESSION[$name];
        unset($_SESSION[$name]);
        return $message;
    }
    return '';
}

// Redirect function
function redirect($page) {
    header('Location: ' . $page);
    exit;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current logged in user data
function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        global $db; // Access the global $db connection
        if (!$db) {
            // If $db is not yet available (e.g., in standalone scripts without Database class init)
            // Re-initialize Database connection if needed.
            // This is a fallback and ideally $db should be passed or initialized properly.
            try {
                $database = new Database();
                $db = $database->getConnection();
            } catch (PDOException $e) {
                error_log("Failed to get DB connection in getCurrentUser: " . $e->getMessage());
                return false;
            }
        }

        try {
            $query = "SELECT id, username, email, first_name, last_name, age, city, role, created_at FROM users WHERE id = :id LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log("Error fetching current user: " . $e->getMessage());
        }
    }
    return false;
}

// Check if current user is admin
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

?>