<?php
if (function_exists('sanitizeInput')) return;

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Chișinău Guide');
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function sanitizeUrl($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

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

function getFlashMessage($name = '') {
    if (isset($_SESSION[$name])) {
        $message = $_SESSION[$name];
        unset($_SESSION[$name]);
        return $message;
    }
    return '';
}

function redirect($page) {
    header('Location: ' . $page);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        global $db; // Access the global $db connection
        if (!$db) {
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

function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

?>
