<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

require_once 'src/config/db.php';
include 'src/includes/functions.php';

$videoId = $_GET['id'] ?? null;

$message = null;

if (!$videoId || !is_numeric($videoId)) {
    $message = "<div class='alert alert-danger'>Hiányzó vagy érvénytelen videó ID.</div>";
    exit;
}

$email = $_SESSION['email'];
$video = getVideoDetailsForEdit($conn, $videoId, $email);
if (!$video) {
    $message = "<div class='alert alert-danger'>Nincs jogosultság a videó szerkesztésére.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $tags = $_POST['tags'] ?? '';

        if (empty($title) || empty($description)) {
            $message = "<div class='alert alert-danger'>A cím és a leírás nem lehet üres!</div>";
        } else {
            updateVideo($conn, $videoId, $title, $description, $tags);
            $video = getVideoDetailsForEdit($conn, $videoId, $email);
            $message = "<div class='alert alert-success mt-3'>Sikeresen mentve!</div>";
        }
    }

    if (isset($_POST['delete'])) {
        deleteVideo($conn, $videoId);
        echo "<div class='container py-5'> <div class='alert alert-success mt-3'>Videó törölve.</div> </div>";
        echo "<script>setTimeout(() => window.location.href = 'index.php?page=my_videos', 1500);</script>";
        exit;
    }
}
?>
<div class="container py-5">
<?= $message ?>

    <a href="index.php?page=video&id=<?= urlencode($videoId) ?>" class="btn btn-outline-secondary mb-4">← Vissza</a>
    <form method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Cím</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($video['CIM']) ?>" maxlength="255" >
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Leírás</label>
            <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000" ><?= htmlspecialchars($video['LEIRAS']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="tags" class="form-label">#Tagek <small class="text-muted">(vesszővel elválasztva)</small></label>
            <input type="text" class="form-control" id="tags" name="tags" placeholder="frontend, css, php"
                   value="<?= htmlspecialchars(implode(', ', $video['TAGS'])) ?>">
        </div>

        <div class="d-flex justify-content-between flex-wrap gap-2">
            <button type="submit" name="save" class="btn btn-success">
                <i class="bi bi-save"></i> Mentés
            </button>
            <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Biztosan törlöd ezt a videót?');">
                <i class="bi bi-trash"></i> Videó törlése
            </button>
        </div>
    </form>
</div>
