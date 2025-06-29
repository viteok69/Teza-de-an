<?php
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'config/helpers.php'; 

$database = new Database();
$db = $database->getConnection();

$discussion_id = isset($_GET['id']) ? sanitizeInput($_GET['id']) : null;

if (!$discussion_id) {
    flashMessage('error', 'Discuția nu a fost găsită.');
    redirect('discussions.php');
}

// 1. Preluarea detaliilor discuției
$discussion = null;
try {
    $query = "SELECT d.*, u.username FROM discussions d LEFT JOIN users u ON d.user_id = u.id WHERE d.id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $discussion_id, PDO::PARAM_INT);
    $stmt->execute();
    $discussion = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching discussion: " . $e->getMessage());
    flashMessage('error', 'Eroare la încărcarea discuției.');
    redirect('discussions.php');
}

if (!$discussion) {
    flashMessage('error', 'Discuția specificată nu există.');
    redirect('discussions.php');
}

// 2. Gestionarea trimiterii comentariilor (dacă este o cerere POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    file_put_contents("debug.txt", print_r($_POST, true));
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
        exit();
    if (!isLoggedIn()) {
        flashMessage('error', 'Trebuie să fii autentificat pentru a comenta.');
        // Redirecționează către pagina de login, apoi utilizatorul poate fi redirecționat înapoi aici.
        redirect('auth/login.php?redirect=' . urlencode("discussion_detail.php?id=" . $discussion_id)); 
    }

    $comment_text = sanitizeInput($_POST['comment_text']);
    $user = getCurrentUser(); 
    var_dump($user); 
    exit(); 
    $user_id = $user['id'];

    if (empty($comment_text)) {
        flashMessage('error', 'Mesajul comentariului nu poate fi gol.');
        // Nu redirecționa, doar afișează eroarea pe aceeași pagină, formularul rămâne completat.
    } else {
        try {
            $insert_query = "INSERT INTO comments (discussion_id, user_id, comment_text) VALUES (:discussion_id, :user_id, :comment_text)";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':comment_text', $comment_text);
            
            if ($insert_stmt->execute()) {
                flashMessage('success', 'Comentariul tău a fost adăugat cu succes!');
                // Redirecționează către propria pagină pentru a goli datele POST și a afișa comentariile actualizate
                header("Location: discussion_detail.php?id=" . $discussion_id);
                exit();
            } else {
                flashMessage('error', 'Eroare la adăugarea comentariului.');
            }
        } catch (PDOException $e) {
            error_log("Error adding comment: " . $e->getMessage());
            flashMessage('error', 'A apărut o eroare tehnică la adăugarea comentariului.');
        }
    }
}

// 3. Preluarea comentariilor pentru această discuție
$comments = [];
try {
    // Selectăm și numele de utilizator pentru afișare
    $comments_query = "SELECT c.*, u.username FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.discussion_id = :discussion_id ORDER BY c.created_at ASC";
    $comments_stmt = $db->prepare($comments_query);
    $comments_stmt->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
    $comments_stmt->execute();
    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching comments: " . $e->getMessage());
    // Continuăm fără comentarii dacă există o eroare
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($discussion['title']); ?> - Discuții - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        /* Stiluri specifice pentru pagină (opțional) */
        .discussion-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .comment-card {
            border-left: 5px solid var(--primary); /* O linie colorată pentru comentarii */
        }
    </style>
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Acasă</a></li>
                    <li class="nav-item"><a class="nav-link" href="places.php">Locuri</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php">Categorii</a></li>
                    <li class="nav-item"><a class="nav-link active" href="discussions.php">Discuții</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item"><a class="btn btn-ghost btn-sm" href="dashboard.php"><i class="fas fa-user-circle"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="btn btn-primary btn-sm" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="btn btn-ghost btn-sm" href="auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
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
        <?php flashMessage('success'); ?>
        <?php flashMessage('error'); ?>

        <div class="card p-4 shadow-sm mb-6">
            <div class="card-body">
                <h1 class="card-title text-3xl font-bold mb-3"><?php echo htmlspecialchars($discussion['title']); ?></h1>
                <p class="card-subtitle text-muted mb-4">
                    De <?php echo htmlspecialchars($discussion['username'] ?? 'Utilizator Anonim'); ?>
                    pe <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($discussion['created_at']))); ?>
                </p>
                <div class="discussion-content mb-5">
                    <?php echo nl2br(htmlspecialchars($discussion['content'])); ?>
                    <?php if (!empty($discussion['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($discussion['image_url']); ?>" alt="Imagine discuție" class="img-fluid mt-3 rounded">
                    <?php endif; ?>
                </div>
                <a href="discussions.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Înapoi la discuții</a>
            </div>
        </div>

        <h2 class="text-2xl font-bold mb-4">Comentarii (<?php echo count($comments); ?>)</h2>

        <div class="card p-4 shadow-sm mb-6">
            <div class="card-body">
                <?php if (isLoggedIn()): ?>
                    <h3 class="text-xl font-bold mb-3">Lasă un comentariu</h3>
                    <form action="discussion_detail.php?id=<?php echo htmlspecialchars($discussion_id); ?>" method="POST">
                        <div class="mb-3">
                            <label for="comment_text" class="form-label">Mesajul tău</label>
                            <textarea class="form-control" id="comment_text" name="comment_text" rows="4" required><?php echo isset($_POST['comment_text']) ? htmlspecialchars($_POST['comment_text']) : ''; ?></textarea>
                        </div>
                        <button type="submit" name="submit_comment" class="btn btn-primary">
                            <i class="fas fa-comment"></i> Trimite Comentariul
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-center text-muted">
                        <a href="auth/login.php?redirect=<?php echo urlencode("discussion_detail.php?id=" . $discussion_id); ?>" class="text-primary fw-bold">Conectează-te</a> pentru a lăsa un comentariu.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="comments-section">
            <?php if (empty($comments)): ?>
                <p class="text-center text-muted">Nu există comentarii încă. Fii primul!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="card p-3 shadow-sm mb-3 comment-card">
                        <div class="card-body">
                            <p class="mb-1">
                                <strong class="text-primary"><?php echo htmlspecialchars($comment['username'] ?? 'Anonim'); ?></strong>
                                <small class="text-muted ms-2"><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($comment['created_at']))); ?></small>
                            </p>
                            <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="py-12 bg-dark text-white">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-3"><h6>Despre noi</h6><ul class="list-unstyled"><li><a href="about.php" class="text-muted">Povestea noastră</a></li><li><a href="team.php" class="text-muted">Echipa</a></li><li><a href="careers.php" class="text-muted">Cariere</a></li></ul></div>
                <div class="col-md-3"><h6>Explorare</h6><ul class="list-unstyled"><li><a href="places.php" class="text-muted">Toate locurile</a></li><li><a href="categories.php" class="text-muted">Categorii</a></li><li><a href="map.php" class="text-muted">Hartă interactivă</a></li></ul></div>
                <div class="col-md-3"><h6>Comunitate</h6><ul class="list-unstyled"><li><a href="discussions.php" class="text-muted">Discuții</a></li><li><a href="blog.php" class="text-muted">Blog</a></li><li><a href="events.php" class="text-muted">Evenimente</a></li></ul></div>
                <div class="col-md-3"><h6>Suport</h6><ul class="list-unstyled"><li><a href="help.php" class="text-muted">Ajutor</a></li><li><a href="privacy.php" class="text-muted">Confidențialitate</a></li><li><a href="terms.php" class="text-muted">Termeni</a></li></ul></div>
            </div><hr class="my-4"><div class="row align-items-center"><div class="col-md-6"><p class="mb-0 text-muted">&copy; 2024 Ghidul Tinerilor Chișinău. Toate drepturile rezervate.</p></div><div class="col-md-6 text-end"><div class="social-links"><a href="#" class="text-muted me-3"><i class="fab fa-facebook"></i></a><a href="#" class="text-muted me-3"><i class="fab fa-instagram"></i></a><a href="#" class="text-muted me-3"><i class="fab fa-telegram"></i></a><a href="#" class="text-muted"><i class="fab fa-tiktok"></i></a></div></div></div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>