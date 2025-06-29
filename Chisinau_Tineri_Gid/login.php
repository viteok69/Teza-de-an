<?php
session_start();
require_once 'config/database.php';
require_once 'config/helpers.php'; 


$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = 'Introduceți username-ul și parola.';
        $message_type = 'error';
    } else {
        try {
            // Căutăm utilizatorul în baza de date și selectăm și coloana 'is_admin'
            $query = "SELECT id, username, password, is_admin FROM users WHERE username = :username LIMIT 0,1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificăm dacă utilizatorul există ȘI dacă parola corespunde (folosind password_verify)
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = (bool)$user['is_admin']; // Stochează rolul de administrator în sesiune

                // Redirecționează în funcție de rolul utilizatorului
                if ($_SESSION['is_admin']) {
                    header('Location: admin.php');
                } else {
                    // Dacă nu este admin, îi redirecționăm pe pagina principală (sau o altă pagină non-admin)
                    header('Location: index.php');
                }
                exit; // Oprește executarea scriptului după redirecționare
            } else {
                $message = 'Username sau parolă incorecte.';
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Eroare la conectarea la baza de date: ' . $e->getMessage();
            $message_type = 'error';
            error_log("Login error: " . $e->getMessage()); // Înregistrează eroarea pentru depanare
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chișinău Guide</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet"> </head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-container">
                <a href="index.php" class="navbar-brand">
                    <i class="fas fa-map-marked-alt"></i>
                    Chișinău Guide
                </a>
                <button class="theme-toggle" onclick="toggleTheme()" title="Schimbă tema">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="container py-8 flex justify-center items-center min-h-[calc(100vh-64px)]">
        <div class="w-full max-w-md">
            <div class="card">
                <div class="card-header text-center">
                    <h1 class="text-3xl font-bold mb-2">
                        <i class="fas fa-sign-in-alt text-primary"></i>
                        Autentificare
                    </h1>
                    <p class="text-gray-600">Accesează-ți contul de administrator</p>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $message_type; ?> mb-6">
                            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-4">
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-input" 
                                   placeholder="Introduceți username-ul" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Parola</label>
                            <input type="password" id="password" name="password" class="form-input" 
                                   placeholder="Introduceți parola" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-sign-in-alt"></i>
                            Login
                        </button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="text-gray-500 text-sm">Nu ai cont? <a href="register.php" class="text-primary hover:underline">Înregistrează-te aici</a></p>
                        Demo: admin / o parolă setată pentru admin (ex: admin123) <br>
                        (Parola reală este hash-uită în DB)
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>