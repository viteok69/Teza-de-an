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
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // Parola brută
    $confirm_password = $_POST['confirm_password']; // Confirmarea parolei

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = 'Toate câmpurile sunt obligatorii.';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Adresă de email invalidă.';
        $message_type = 'error';
    } elseif ($password !== $confirm_password) {
        $message = 'Parolele nu se potrivesc.';
        $message_type = 'error';
    } elseif (strlen($password) < 6) {
        $message = 'Parola trebuie să aibă minim 6 caractere.';
        $message_type = 'error';
    } else {
        try {
            // Verifică dacă username-ul sau email-ul există deja
            $query_check = "SELECT id FROM users WHERE username = :username OR email = :email LIMIT 0,1";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':username', $username);
            $stmt_check->bindParam(':email', $email);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                $message = 'Username-ul sau adresa de email există deja.';
                $message_type = 'error';
            } else {
                // Hash-uiește parola înainte de a o stoca
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Inserează noul utilizator în baza de date
                $query_insert = "INSERT INTO users (username, email, password, is_admin, created_at) VALUES (:username, :email, :password, :is_admin, NOW())";
                $stmt_insert = $db->prepare($query_insert);

                $stmt_insert->bindParam(':username', $username);
                $stmt_insert->bindParam(':email', $email);
                $stmt_insert->bindParam(':password', $hashed_password);
                $is_admin = 0; // Utilizatorii obișnuiți nu sunt admini
                $stmt_insert->bindParam(':is_admin', $is_admin, PDO::PARAM_INT);

                if ($stmt_insert->execute()) {
                    $message = 'Contul a fost creat cu succes! Te poți autentifica acum.';
                    $message_type = 'success';
                    // Poți redirecționa direct la login sau afișa mesajul
                    // header('Location: login.php?registered=success');
                    // exit;
                } else {
                    $message = 'Eroare la crearea contului. Vă rugăm să încercați din nou.';
                    $message_type = 'error';
                }
            }
        } catch (PDOException $e) {
            $message = 'Eroare la baza de date: ' . $e->getMessage();
            $message_type = 'error';
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare - Chișinău Guide</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
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
                        <i class="fas fa-user-plus text-primary"></i>
                        Înregistrare
                    </h1>
                    <p class="text-gray-600">Creează-ți un cont nou</p>
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
                            <input type="text" id="username" name="username" class="form-input" placeholder="Alege un username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="Introduceți adresa de email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Parola</label>
                            <input type="password" id="password" name="password" class="form-input" placeholder="Alege o parolă" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirmă Parola</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Confirmă parola" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-user-plus"></i>
                            Înregistrare
                        </button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="text-gray-500 text-sm">Ai deja un cont? <a href="login.php" class="text-primary hover:underline">Autentifică-te aici</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>