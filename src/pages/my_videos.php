<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">

        <a href="index.php?page=upload" class="btn btn-primary">
            <i class="bi bi-upload"></i> Videó feltöltése
        </a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-sm-4 g-4">
        <?php
        $videos = [
            ['id' => 3, 'title' => 'Oracle lekérdezések'],
            ['id' => 4, 'title' => 'Bootstrap kártyák'],
            ['id' => 5, 'title' => 'Adatbázis normalizálás'],
        ];

        foreach ($videos as $video):
            include 'src/includes/video_card.php';
        endforeach;
        ?>
    </div>
</div>

