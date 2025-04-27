<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

require_once 'src/config/db.php';
require_once 'src/includes/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['playlist_name'])) {
    $playlistName = trim($_POST['playlist_name']);
    $message = createPlaylist($conn, $playlistName, $_SESSION['email']);
}

$playlists = getUserPlaylists($conn, $_SESSION['email']);
?>

<div class="container py-4">
    <?php if (!empty($message)): ?>
        <?= $message ?>
    <?php endif; ?>

    <div class="mb-4 w-50">
        <form method="post" class="d-flex gap-2">
            <input type="text" name="playlist_name" class="form-control w-50" placeholder="Lejátszási lista neve" >
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
