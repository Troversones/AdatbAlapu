<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}
$videoId = $_GET['id'] ?? null;
require_once 'src/config/db.php';
include 'src/includes/functions.php';


$video = $videoId ? getVideoById($conn, $videoId) : null;

$comments = [
    [
        'user' => 'htmlfan',
        'text' => 'Nagyon hasznos volt, köszi!',
        'likes' => 12,
        'dislikes' => 0
    ],
    [
        'user' => 'noobcoder',
        'text' => 'Nem teljesen értettem a grid részt :(',
        'likes' => 3,
        'dislikes' => 2
    ]
];

$more_videos = [
    ['title' => 'HTML alapok', 'uploader' => 'frontend_mester'],
    ['title' => 'CSS trükkök', 'uploader' => 'frontend_mester'],
    ['title' => 'PHP bevezető', 'uploader' => 'php_guru'],
    ['title' => 'PDO lekérdezések', 'uploader' => 'php_guru'],
    ['title' => 'Utazás Rómába', 'uploader' => 'travel_vlogger'],
    ['title' => 'Vlog: Reggeli rutin', 'uploader' => 'travel_vlogger']
];


$playlists = ['Tananyagok', 'Frontend kedvencek', 'Később megnézendő'];
?>

<div class="container py-5">
    <?php if (!$video): ?>
        <div class="alert alert-danger">A megadott videó nem található.</div>
        <button onclick="history.back()" class="btn btn-outline-secondary mb-4">← Vissza</button>
    <?php else: ?>
    <button onclick="history.back()" class="btn btn-outline-secondary mb-4">← Vissza</button>



        <?php if (isset($_SESSION['email']) && $_SESSION['email'] === $video['uploader']): ?>
            <a href="index.php?page=edit&id=<?= $videoId ?>" class="btn btn-outline-secondary w-100 mb-4">
                <i class="bi bi-pencil-square"></i> Szerkesztés
            </a>
        <?php endif; ?>

        <div class="row w-100 justify-content-center">
            <div class="col-12">
                <div class="ratio ratio-16x9 mb-4">
                    <video controls>
                        <source src="src/services/video_stream.php?id=<?= $video['id'] ?>" type="video/mp4">
                        A videó nem lejátszható.
                    </video>
                </div>

                <h3 class="mb-2"><?= $video['title'] ?></h3>
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <span class="text-muted">Feltöltve: <?= $video['upload_date'] ?></span> |
                        <span class="text-muted"><?= $video['views'] ?> megtekintés</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-success btn-sm"><i class="bi bi-hand-thumbs-up-fill"></i> Tetszik</button>
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-hand-thumbs-down-fill"></i> Nem tetszik</button>
                    </div>
                </div>

                <p><?= nl2br($video['description']) ?></p>
                <?php if (!empty($video['tags'])): ?>
                    <div class="mb-3">
                        <?php foreach ($video['tags'] as $tag): ?>
                            <span class="badge bg-secondary me-1">#<?= $tag ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="col-lg-4 mt-5 mt-lg-0">
                    <div class="card p-3 shadow-sm d-flex flex-column gap-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle fs-2 text-primary me-2"></i>
                            <div>
                                <div class="fw-semibold">
                                    <a href="index.php?page=user&username=<?= urlencode($video['uploader']) ?>"
                                       class="text-decoration-none text-dark">
                                        <?= $video['uploader'] ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-outline-primary w-100 mt-2">
                            <i class="bi bi-bell-fill"></i> Feliratkozás
                        </button>
                    </div>
                </div>
                <form method="post" class="mb-4 mt-3 w-50">
                    <div class="row g-2 align-items-center">
                        <div class="col-sm-8 col-md-6">
                            <select class="form-select" id="playlist_select" name="playlist_name" >
                                <option value="" disabled selected>Válassz listát...</option>
                                <?php foreach ($playlists as $list): ?>
                                    <option value="<?= htmlspecialchars($list) ?>"><?= htmlspecialchars($list) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-primary mt-3 mt-sm-0">
                                <i class="bi bi-plus-circle"></i> Hozzáadás
                            </button>
                        </div>
                    </div>
                </form>

                <h5 class="mt-5 mb-3">Hozzászólások</h5>
                <div class="list-group">
                    <?php foreach ($comments as $comment): ?>
                        <div class="list-group-item">
                            <strong><?= $comment['user'] ?>:</strong>
                            <p class="mb-1"><?= $comment['text'] ?></p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-success btn-sm"><i class="bi bi-hand-thumbs-up-fill"></i> <?= $comment['likes'] ?></button>
                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-hand-thumbs-down-fill"></i> <?= $comment['dislikes'] ?></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <form method="post" class="mb-4">
                        <div class="mb-1 mt-3">
                            <textarea class="form-control" id="new_comment" name="new_comment" rows="3" placeholder="Írd ide a hozzászólásod..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-chat-dots"></i> Hozzászólás küldése
                        </button>
                    </form>
                </div>
            </div>


        </div>

        <h4 class="mt-4 mb-2">Feltöltő egyéb videói</h4>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-sm-4 g-4">

            <?php
            foreach ($more_videos as $video):
                include 'src/includes/video_card.php';
            endforeach;
            ?>
        </div>




    <?php endif; ?>
</div>