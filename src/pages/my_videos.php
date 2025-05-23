<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

require_once __DIR__ . '/../config/db.php';
include 'src/includes/functions.php';

$email = $_SESSION['email'];
$videos = getUserVideos($conn, $email);
$fromPage = 'my_videos';

?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">

        <a href="index.php?page=upload" class="btn btn-primary">
            <i class="bi bi-upload"></i> Videó feltöltése
        </a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-sm-4 g-4">
        <?php
        if (!empty($videos)) :
        foreach ($videos as $video):
            include 'src/includes/video_card.php';
        endforeach;
        else :
        ?>
        <p class="w-100 text-center fs-5">Nincsenek még feltöltött videóid.</p>
        <?php endif; ?>
    </div>
</div>

