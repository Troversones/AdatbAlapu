<?php
$playlists = [
    ['id' => 1, 'name' => 'Frontend tanfolyam', 'count' => 12],
    ['id' => 2, 'name' => 'Kedvenc vlogok', 'count' => 5],
    ['id' => 3, 'name' => 'PHP projektek', 'count' => 8],
];
?>

<div class="container py-4">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php foreach ($playlists as $playlist): ?>
            <div class="col">
                <?php include 'src/includes/playlist_card.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
