<?php
$_SESSION['video_back'] = [
    'from' => $fromPage ?? 'home',
    'email' => $email ?? null
];
$stats = getVideoStats($conn, $video['ID']);
if (isset($video)): ?>
    <a href="index.php?page=video&id=<?= $video['ID'] ?>" class="text-decoration-none text-dark">
        <div class="col">
            <div class="card h-100 border rounded-4 shadow-lg bg-white">
                <img src="public/images/video_thumbnail.jpg" class="card-img-top mt-2 w-75 h-75 mx-auto rounded-3" alt="Thumbnail">
                <div class="card-body text-center">
                    <h6 class="card-title fw-semibold text-truncate"><?= htmlentities($video['TITLE']) ?></h6>
                </div>
                <div class="card-footer bg-light-subtle border-top rounded-bottom px-3 py-2">
                    <div class="d-flex justify-content-between text-secondary small">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-eye-fill me-1"></i> <?= $stats['VIEWS'] ?>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hand-thumbs-up-fill text-success me-1"></i> <?= $stats['LIKES'] ?>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hand-thumbs-down-fill text-danger me-1"></i> <?= $stats['DISLIKES'] ?>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-chat-left-text-fill text-primary me-1"></i> <?= $stats['COMMENTS'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>
