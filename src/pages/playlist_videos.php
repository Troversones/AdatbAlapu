<?php
session_start();
?>




<div class="container py-4">
    <a href="index.php?page=playlists" class="btn btn-outline-secondary mb-4">← Vissza</a>
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
