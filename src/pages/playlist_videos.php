<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}
?>




<div class="container py-4">
    <form method="post" class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 w-75">
        <div class="d-flex align-items-center gap-2 flex-grow-1">
            <a href="index.php?page=playlists" class="btn btn-outline-secondary ">
                ← Vissza
            </a>
            <input type="text" name="name" class="form-control w-25" value="peldanev" placeholder="Lejátszási lista neve">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Mentés
            </button>
        </div>
    </form>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-sm-4 g-4">
        <?php

        // ha rákattintunk egy kártyára akkor megnyitja az video oldalt ahol az id-t átadjuk és az alapján töltjuk be a videót

        // Dummy videó adatok tömbben
        $videos = [
            ['id' => 1, 'title' => 'Frontend alapok'],
            ['id' => 2, 'title' => 'PHP bevezető'],
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
