<?php
session_start();
require_once 'src/config/db.php';
require_once 'src/includes/functions.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

$playlistId = $_GET['id'] ?? null;
if (!$playlistId) {
    header("Location: index.php?page=playlists");
    exit;
}

$playlist = getPlaylistById($conn, $playlistId, $_SESSION['email']);
if (!$playlist) {
    header("Location: index.php?page=playlists");
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save']) && !empty($_POST['name'])) {
        $message = updatePlaylistName($conn, $playlistId, $_POST['name']);
        $playlist['name'] = $_POST['name'];
    }

    if (isset($_POST['delete'])) {
        $ret = deletePlaylist($conn, $playlistId);
        if ($ret === "success") {
            header("Location: index.php?page=playlists");
            exit;
        }elseif ($ret === "kedvencek_delete") {
            $message = "<div class='alert alert-danger'>A 'Kedvencek' lejátszási lista nem törölhető.</div>";
        }else{
            $message =  "<div class='alert alert-danger'>Hiba történt a törlés során.</div>";
        }

    }

    if (isset($_POST['delete_video']) && isset($_POST['video_id'])) {
        $message = removeVideoFromPlaylist($conn, $playlistId, $_POST['video_id']);
    }
}

$videos = getVideosInPlaylist($conn, $playlistId);
?>

<div class="container py-4">
    <?php if (!empty($message)): ?>
        <?= $message ?>
    <?php endif; ?>

    <form method="post" class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 w-75">
        <div class="d-flex align-items-center gap-2 flex-grow-1">
            <a href="index.php?page=playlists" class="btn btn-outline-secondary">
                ← Vissza
            </a>

            <input type="text" name="name" class="form-control w-25" value="<?= htmlspecialchars($playlist['NAME']) ?>" placeholder="Lejátszási lista neve">

            <button type="submit" name="save" class="btn btn-primary">
                <i class="bi bi-save"></i> Mentés
            </button>

            <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Biztosan törlöd a lejátszási listát?');">
                <i class="bi bi-trash"></i> Törlés
            </button>
        </div>
    </form>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php if (empty($videos)): ?>
            <p>Nincsenek videók ebben a lejátszási listában.</p>
        <?php else: ?>
            <?php foreach ($videos as $video): ?>
                <div class="col">
                    <?php include 'src/includes/video_card.php'; ?>

                    <form method="post" class="mt-2 text-center">
                        <input type="hidden" name="video_id" value="<?= $video['ID'] ?>">
                        <button type="submit" name="delete_video" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i> Videó törlése
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
