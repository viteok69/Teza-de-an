<?php
require_once '../config/config.php';

session_destroy();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_start();
flashMessage('success', 'Te-ai deconectat cu succes!');
redirect('../index.php');
?>
