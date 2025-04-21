<?php
session_start();
$videoId = $_GET['id'] ?? null;

$videos = [
    1 => [
        'title' => 'Frontend alapok',
        'description' => 'Ebben a videóban megismerkedünk a HTML, CSS és Bootstrap alapjaival.',
        'upload_date' => '2024-04-20',
        'views' => 1234,
        'uploader' => 'frontend_mester',
        'video_file' => 'public/videos/sample1.mp4',
        'tags' => ['frontend', 'html', 'css', 'bootstrap']
    ],
    2 => [
        'title' => 'PHP bevezető',
        'description' => 'Ismerd meg a PHP nyelv alapjait kezdőknek!',
        'upload_date' => '2024-04-15',
        'views' => 982,
        'uploader' => 'php_guru',
        'video_file' => 'public/videos/sample2.mp4',
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
    4 => [
        'title' => 'Bootstrap kártyák',
        'description' => 'Ebben a videóban megmutatjuk, hogyan lehet stílusos Bootstrap kártyákat készíteni frontend fejlesztéshez.',
        'upload_date' => '2024-04-12',
        'views' => 564,
        'uploader' => 'test',
        'video_file' => 'public/videos/bootstrap_cards.mp4',
         'tags' => ['sql', 'oracle', 'adatbázis']
    ],
    5 => [
        'title' => 'Adatbázis normalizálás',
        'description' => 'Ebben az oktatóanyagban megtanulhatod, hogyan normalizáld az adatbázisod 1NF-től 3NF-ig.',
        'upload_date' => '2024-04-18',
        'views' => 812,
        'uploader' => 'test',
        'video_file' => 'public/videos/db_normalization.mp4',
         'tags' => ['sql', 'oracle', 'adatbázis']
    ]


];

$video = $videoId && isset($videos[$videoId]) ? $videos[$videoId] : null;

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
?>

<div class="container py-5">
    <?php if (!$video): ?>
        <div class="alert alert-danger">A megadott videó nem található.</div>
        <button onclick="history.back()" class="btn btn-outline-secondary mb-4">← Vissza</button>
    <?php else: ?>
    <button onclick="history.back()" class="btn btn-outline-secondary mb-4">← Vissza</button>



        <?php if (isset($_SESSION['username']) && $_SESSION['username'] === $video['uploader']): ?>
            <a href="index.php?page=edit&id=<?= $videoId ?>" class="btn btn-outline-secondary w-100 mb-4">
                <i class="bi bi-pencil-square"></i> Szerkesztés
            </a>
        <?php endif; ?>

        <div class="row w-100 justify-content-center">
            <div class="col-12">
                <div class="ratio ratio-16x9 mb-4">
                    <video controls>
                        <source src="<?=$video['video_file'] ?>" type="video/mp4">
                        A videó nem lejátszható a böngészőben.
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