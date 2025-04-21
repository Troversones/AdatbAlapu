<?php
session_start();

// felhasználónév az URL-ből
$username = $_GET['username'] ?? null;


$Videos = [
    ['title' => 'HTML alapok', 'uploader' => 'frontend_mester'],
    ['title' => 'CSS trükkök', 'uploader' => 'frontend_mester'],
    ['title' => 'PHP bevezető', 'uploader' => 'php_guru'],
    ['title' => 'PDO lekérdezések', 'uploader' => 'php_guru'],
    ['title' => 'Utazás Rómába', 'uploader' => 'travel_vlogger'],
    ['title' => 'Vlog: Reggeli rutin', 'uploader' => 'travel_vlogger']
];

// csak az adott felhasználó videói
$videos = array_filter($Videos, function($v) use ($username) {
    return $v['uploader'] === $username;
});
?>

<div class="container py-5">
    <button onclick="history.back()" class="btn btn-outline-secondary mb-4">← Vissza</button>
    <?php if (!$username): ?>
        <div class="alert alert-danger">Felhasználó nem található.</div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="mb-0"><?= $username ?></h2>
            <button class="btn btn-outline-primary">
                <i class="bi bi-bell-fill"></i> Feliratkozás
            </button>
        </div>

        <?php if (empty($videos)): ?>
            <p class="text-muted">Ez a felhasználó még nem töltött fel videókat.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-sm-4 g-4">
                <?php
                foreach ($videos as $video):
                    include 'src/includes/video_card.php';
                endforeach;
                ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

