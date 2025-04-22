<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}
$username = $_SESSION['username'];
?>

<div class="container py-5">
    <button onclick="history.back()" class="btn btn-outline-secondary mb-4">← Vissza</button>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="video_file" class="form-label">Videófájl</label>
            <input class="form-control" type="file" id="video_file" name="video_file" accept="video/mp4,video/*" required>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Cím</label>
            <input type="text" class="form-control" id="title" name="title" maxlength="255" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Leírás</label>
            <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000" required></textarea>
        </div>

        <div class="mb-3">
            <label for="tags" class="form-label">#Tagek <small class="text-muted">(vesszővel elválasztva, pl.: frontend, html, css)</small></label>
            <input type="text" class="form-control" id="tags" name="tags" placeholder="frontend, html, css">
        </div>

        <div class="mb-3">
            <label class="form-label">Feltöltő</label>
            <input type="text" class="form-control" value="<?= $username ?>" readonly>
        </div>


        <button type="submit" class="btn btn-primary">
            <i class="bi bi-upload"></i> Feltöltés
        </button>
    </form>
</div>
