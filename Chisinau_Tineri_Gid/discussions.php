<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/config.php';
require_once 'config/database.php'; // Adaugă această linie pentru a include clasa Database
require_once 'config/helpers.php';   // Adaugă această linie pentru a include funcțiile helper (ex: isLoggedIn())

$database = new Database();
$db = $database->getConnection();

// Preluăm discuțiile din baza de date
$discussions = [];
try {
    // Asigură-te că tabela 'discussions' și 'users' există și sunt populate cu date.
    // 'LEFT JOIN users' este folosit pentru a prelua numele de utilizator al autorului discuției.
    $query = "SELECT d.*, u.username FROM discussions d LEFT JOIN users u ON d.user_id = u.id ORDER BY d.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching discussions: " . $e->getMessage());
    flashMessage('error', 'Eroare la încărcarea discuțiilor.');
    // Poți redirecționa sau afișa o eroare vizibilă utilizatorului aici, dacă este necesar
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discuții - <?php echo SITE_NAME; ?></title>
    
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
                        <a class="nav-link active" href="discussions.php">Discuții</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="theme-toggle me-3" onclick="toggleTheme()" title="Schimbă tema">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">Ieșire</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">Conectare</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="auth/register.php">Înregistrare</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5 mt-5 bg-gradient text-white">
        <div class="container">
            <div class="text-center">
                <h1 class="display-4 fw-bold mb-3">Discuții Comunitate</h1>
                <p class="lead">Conectează-te cu alți tineri din Chișinău și participă la discuții</p>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php flashMessage('success'); ?>
            <?php flashMessage('error'); ?>
            <div class="text-center mb-5">
                <?php if (isLoggedIn()): ?>
                    <a href="new_discussion.php" class="btn btn-success btn-lg">
                        <i class="fas fa-plus-circle"></i> Creează o discuție nouă
                    </a>
                <?php else: ?>
                    <p class="lead text-muted">
                        <a href="auth/login.php" class="text-primary fw-bold">Conectează-te</a> pentru a crea o discuție nouă.
                    </p>
                <?php endif; ?>
            </div>

            <div class="row">
                <?php if (empty($discussions)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-comments fa-5x text-muted mb-4"></i>
                        <h2 class="mb-3">Momentan nu există discuții.</h2>
                        <p class="lead text-muted mb-4">Fii primul care începe o discuție!</p>
                        <?php if (!isLoggedIn()): ?>
                            <a href="auth/register.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-user-plus"></i> Înregistrează-te pentru a participa
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($discussions as $discussion): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-2xl font-bold mb-2"><?php echo htmlspecialchars($discussion['title']); ?></h5>
                                    <p class="card-subtitle text-muted mb-3">
                                        De <?php echo htmlspecialchars($discussion['username'] ?? 'Anonim'); ?>
                                        pe <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($discussion['created_at']))); ?>
                                    </p>
                                    <p class="card-text">
                                        <?php
                                            // Afișează doar o parte din conținut
                                            $short_content = strip_tags($discussion['content']); // Elimină tag-urile HTML dacă există
                                            echo htmlspecialchars(substr($short_content, 0, 200));
                                            if (strlen($short_content) > 200) {
                                                echo '...';
                                            }
                                        ?>
                                    </p>
                                    <a href="discussion_detail.php?id=<?php echo htmlspecialchars($discussion['id']); ?>" class="btn btn-primary mt-3">
                                        <i class="fas fa-arrow-right"></i> Citește mai mult
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="brand-title text-white">
                        <i class="fas fa-map-marked-alt"></i>
                        Ghidul Tinerilor Chișinău
                    </h5>
                    <p class="text-muted">Platforma ta pentru a descoperi și explora cele mai bune oportunități din Chișinău.</p>
                </div>
                
                <div class="col-md-3">
                    <h6>Link-uri Utile</h6>
                    <ul class="list-unstyled">
                        <li><a href="places.php" class="text-muted">Locuri</a></li>
                        <li><a href="discussions.php" class="text-muted">Discuții</a></li>
                        <li><a href="contact.php" class="text-muted">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h6>Suport</h6>
                    <ul class="list-unstyled">
                        <li><a href="help.php" class="text-muted">Ajutor</a></li>
                        <li><a href="privacy.php" class="text-muted">Confidențialitate</a></li>
                        <li><a href="terms.php" class="text-muted">Termeni</a></li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; 2024 Ghidul Tinerilor Chișinău. Toate drepturile rezervate.</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="social-links">
                        <a href="#" class="text-muted me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-telegram"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>