<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

require_once 'src/config/db.php';
include 'src/includes/functions.php';
$fromPage = 'user';
$email = $_GET['email'] ?? null;
if($email === $_SESSION['email']) {
    header("Location: index.php?page=my_videos");
    exit;
}
$videos = [];

if ($email) {
    $videos = getUserVideos($conn, $email);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_subscription'])) {
    toggleSubscription($conn, $_SESSION['email'], $email);
    header("Location: index.php?page=user&email=" . urlencode($email));
    exit;
}

$isSubscribed = ($email && $_SESSION['email'] !== $email) ? isSubscribed($conn, $_SESSION['email'], $email) : false;
?>

<div class="container py-5">
    <?php if (!$email): ?>
        <div class="alert alert-danger">Felhasználó nem található.</div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="mb-0"><?= htmlspecialchars($email) ?></h2>
            <?php if ($_SESSION['email'] !== $email): ?>
                <form method="post">
                    <button type="submit" name="toggle_subscription" class="btn <?= $isSubscribed ? 'btn-outline-danger' : 'btn-outline-primary' ?>">
                        <i class="bi <?= $isSubscribed ? 'bi-x-circle' : 'bi-bell-fill' ?>"></i> <?= $isSubscribed ? 'Leiratkozás' : 'Feliratkozás' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (empty($videos)): ?>
            <p class="text-muted">Ez a felhasználó még nem töltött fel videókat.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-sm-4 g-4">
                <?php foreach ($videos as $video): ?>
                    <?php include 'src/includes/video_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
