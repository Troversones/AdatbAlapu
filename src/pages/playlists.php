<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}


$playlists = [
    ['id' => 1, 'name' => 'Frontend tanfolyam', 'count' => 12],
    ['id' => 2, 'name' => 'Kedvenc vlogok', 'count' => 5],
    ['id' => 3, 'name' => 'PHP projektek', 'count' => 8],
];
?>

<div class="container py-4">
    <div class="mb-4 w-50">
        <form method="post" class="d-flex gap-2">
            <input type="text" name="playlist_name" class="form-control w-50" placeholder="Lejátszási lista neve" required>
            <button type="submit" class="btn btn-primary w-25">
                <i class="bi bi-folder-plus"></i> Létrehozás
            </button>
        </form>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php foreach ($playlists as $playlist): ?>
            <div class="col">
                <?php include 'src/includes/playlist_card.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
