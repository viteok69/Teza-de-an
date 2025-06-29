<?php
require_once 'config/config.php';
require_once 'config/database.php'; // Include fișierul de conectare la baza de date
require_once 'config/helpers.php'; // Asigură-te că helpers.php este inclus pentru sanitizeInput

$database = new Database();
$db = $database->getConnection(); // Obține conexiunea la baza de date

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Toate câmpurile sunt obligatorii.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Adresa de email nu este validă.';
    } else {
        try {
            // Interogarea SQL pentru inserarea datelor
            $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)";
            
            // Pregătește interogarea
            $stmt = $db->prepare($query);
            
            // Lipește valorile parametrilor
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            
            // Execută interogarea
            if ($stmt->execute()) {
                $success_message = 'Mesajul tău a fost trimis cu succes și salvat în baza de date! Îți vom răspunde în curând.';
                $_POST = []; // Golește formularul după trimitere
            } else {
                $error_message = 'A apărut o eroare la salvarea mesajului. Te rugăm să încerci din nou.';
            }
        } catch (PDOException $e) {
            // Înregistrează eroarea pentru depanare (nu afișa detaliile erorii direct utilizatorului)
            error_log("Eroare la salvarea mesajului de contact: " . $e->getMessage());
            $error_message = 'A apărut o eroare tehnică la salvarea mesajului. Te rugăm să încerci mai târziu.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Ghidul Tinerilor</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-map-marked-alt"></i>
                Ghidul Tinerilor
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Acasă</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="discussions.php">Discuții</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="btn btn-ghost btn-sm" href="dashboard.php"><i class="fas fa-user-circle"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="btn btn-primary btn-sm" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="btn btn-ghost btn-sm" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme">
                            <i id="theme-icon" class="fas fa-sun"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-8">
        <h1 class="text-3xl font-bold text-center mb-8">Contactează-ne</h1>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-4 shadow-sm">
                    <div class="card-body">
                        <p class="mb-4">Ai o întrebare, o sugestie sau vrei să ne spui ceva? Completează formularul de mai jos și îți vom răspunde în cel mai scurt timp.</p>
                        
                        <form action="contact.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Numele tău complet</label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Adresa ta de email</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subiect</label>
                                <input type="text" class="form-control" id="subject" name="subject" required value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Mesajul tău</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-paper-plane"></i> Trimite Mesajul
                            </button>
                        </form>
                    </div>
                </div>
                <br>
                <div class="mt-8 text-center">
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-envelope me-2"></i> info@ghidultinerilor.md
                    </p>
                    <br>
                    <div class="social-links mt-4">
                        <a href="#" class="text-secondary me-3"><i class="fab fa-facebook-f text-3xl"></i></a>
                        <a href="#" class="text-secondary me-3"><i class="fab fa-instagram text-3xl"></i></a>
                        <a href="#" class="text-secondary me-3"><i class="fab fa-telegram-plane text-3xl"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </main>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>