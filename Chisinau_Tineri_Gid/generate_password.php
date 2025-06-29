<?php
$password_text_clar = 'admin123'; 
$password_hashed = password_hash($password_text_clar, PASSWORD_DEFAULT);
echo "Hash-ul parolei pentru '{$password_text_clar}': <br>";
echo "<strong>{$password_hashed}</strong>";
?>