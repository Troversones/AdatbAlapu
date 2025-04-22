<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

$videoId = $_GET['id'] ?? null;

$videos = [
    1 => [
        'title' => 'Frontend alapok',
        'description' => 'Ebben a videóban megismerkedünk a HTML, CSS és Bootstrap alapjaival.',
        'upload_date' => '2024-04-20',
        'views' => 1234,
        'uploader' => 'frontend_mester',
        'tags' => ['frontend', 'html', 'css', 'bootstrap']
    ],
    2 => [
        'title' => 'PHP bevezető',
        'description' => 'Ismerd meg a PHP nyelv alapjait kezdőknek!',
        'upload_date' => '2024-04-15',
        'views' => 982,
        'uploader' => 'php_guru',
        'tags' => ['php', 'backend', 'alapok']
    ],
    3 => [
        'title' => 'Oracle lekérdezések',
        'description' => 'Alap SQL lekérdezések Oracle-ben, beleértve SELECT, WHERE, JOIN műveleteket.',
        'upload_date' => '2024-04-10',
        'views' => 723,
        'uploader' => 'test',
        'video_file' => 'public/videos/oracle_queries.mp4',
        'tags' => ['sql', 'oracle', 'adatbázis']
    ],
];

$video = $videoId && isset($videos[$videoId]) ? $videos[$videoId] : null;
?>

<div class="container py-5">
    <button onclick="history.back()" class="btn btn-outline-secondary mb-4">← Vissza</button>
    <?php if (!$video): ?>
        <div class="alert alert-danger">A megadott videó nem található.</div>
    <?php else: ?>


        <form method="post">

            <div class="mb-3">
                <label for="title" class="form-label">Cím</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= $video['title'] ?>" maxlength="255" required>
            </div>


            <div class="mb-3">
                <label for="description" class="form-label">Leírás</label>
                <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000" required><?= $video['description'] ?></textarea>
            </div>

            <div class="mb-3">
                <label for="tags" class="form-label">#Tagek
                    <small class="text-muted">(vesszővel elválasztva, pl.: frontend, html, css)</small>
                </label>
                <input type="text" class="form-control" id="tags" name="tags"
                       value="<?= implode(', ', $video['tags']) ?>"
                       placeholder="pl.: php, backend, alapok">
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
    <?php endif; ?>
</div>


