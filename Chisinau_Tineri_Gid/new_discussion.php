<?php
session_start();
require_once 'config/database.php';
require_once 'config/helpers.php';

// Redirecționează dacă utilizatorul nu este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $content = sanitizeInput($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $message = 'Titlul și conținutul discuției sunt obligatorii.';
        $message_type = 'error';
    } else {
        try {
            // Inserează noua discuție în baza de date
            // Asigură-te că ai un tabel 'discussions' în baza de date!
            $query = "INSERT INTO discussions (user_id, title, content, created_at) VALUES (:user_id, :title, :content, NOW())";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);

            if ($stmt->execute()) {
                $message = 'Discuția a fost creată cu succes!';
                $message_type = 'success';
                // Redirecționează la pagina de discuții după succes
                header('Location: discussions.php?status=new_discussion_success');
                exit;
            } else {
                $message = 'Eroare la crearea discuției. Vă rugăm să încercați din nou.';
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Eroare la baza de date: ' . $e->getMessage();
            $message_type = 'error';
            error_log("New discussion error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discuție Nouă - Chișinău Guide</title>
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
                <button class="navbar-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="navbar-menu" id="navbar-nav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">Acasă</a></li>
                        <li class="nav-item"><a class="nav-link active" href="discussions.php">Discuții</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if (isAdmin()): ?>
                                <li class="nav-item"><a class="btn btn-primary" href="admin.php"><i class="fas fa-tools"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li class="nav-item"><a class="btn btn-secondary" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="btn btn-primary" href="login.php"><i class="fas fa-sign-in-alt"></i> Autentificare</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-12">
        <div class="container flex justify-center">
            <div class="w-full max-w-2xl">
                <div class="card">
                    <div class="card-header text-center">
                        <h1 class="text-3xl font-bold mb-2">
                            <i class="fas fa-plus-circle text-primary"></i>
                            Începe o Discuție Nouă
                        </h1>
                        <p class="text-gray-600">Formulează-ți ideile și întrebările pentru comunitate.</p>
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
                                <label for="title" class="form-label">Titlul Discuției</label>
                                <input type="text" id="title" name="title" class="form-input" placeholder="Ex: Cele mai bune locuri pentru studiu în Chișinău" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="content" class="form-label">Conținutul Discuției</label>
                                <textarea id="content" name="content" class="form-input" rows="10" placeholder="Scrie aici mesajul tău detaliat..." required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-full">
                                <i class="fas fa-paper-plane"></i>
                                Creează Discuția
                            </button>
                            <a href="discussions.php" class="btn btn-secondary w-full mt-2">
                                <i class="fas fa-arrow-left"></i> Anulează
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-12" style="background: var(--gray-900); color: var(--white);">
        <div class="container text-center">
            <div class="mb-6">
                <h3 class="text-2xl font-bold mb-2" style="color: var(--white);">Ghidul Tinerilor Chișinău</h3>
                <p style="color: var(--gray-300);">Descoperă cele mai bune locuri din capitala Moldovei</p>
            </div>
            
            <div class="flex justify-center gap-6 mb-6">
                <a href="#" class="text-gray-300 hover:text-white transition-colors"><i class="fab fa-facebook-f text-xl"></i></a>
                <a href="#" class="text-gray-300 hover:text-white transition-colors"><i class="fab fa-instagram text-xl"></i></a>
                <a href="#" class="text-300 hover:text-white transition-colors"><i class="fab fa-telegram text-xl"></i></a>
            </div>
            
            <p style="color: var(--gray-400);">
                © <?php echo date('Y'); ?> Ghidul Tinerilor Chișinău. Toate drepturile rezervate.
            </p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>