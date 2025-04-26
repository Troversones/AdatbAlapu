<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}


$backLink = 'index.php?page=home';
if (isset($_SESSION['video_back'])) {
    $from = $_SESSION['video_back']['from'] ?? 'home';
    $email = $_SESSION['video_back']['email'] ?? null;

    if ($from === 'home') {
        $backLink = 'index.php?page=home';
    } elseif ($from === 'my_videos') {
        $backLink = 'index.php?page=my_videos';
    } elseif ($from === 'user' && $email) {
        $backLink = 'index.php?page=user&email=' . urlencode($email);
    }
}

$videoId = $_GET['id'] ?? null;
require_once 'src/config/db.php';
include 'src/includes/functions.php';

if (!isset($_SESSION['viewed_' . $videoId])) {
    incrementViewCount($conn, $videoId);
    $_SESSION['viewed_' . $videoId] = true;
}

$video = $videoId ? getVideoById($conn, $videoId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reaction'])) {
    $reaction = $_POST['reaction'];
    if (in_array($reaction, ['like', 'dislike', 'remove'])) {
        setReaction($conn, $videoId, $_SESSION['email'], $reaction === 'remove' ? null : $reaction);
        header("Location: index.php?page=video&id=" . urlencode($videoId));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_subscribe'])) {
    toggleSubscription($conn, $_SESSION['email'], $video['uploader']);
    header("Location: index.php?page=video&id=" . urlencode($videoId));
    exit;
}

$reactions = getVideoReactions($conn, $videoId);
$userReaction = getUserReaction($conn, $videoId, $_SESSION['email']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_comment']) && !empty(trim($_POST['new_comment']))) {
        addComment($conn, $videoId, $_SESSION['email'], $_POST['new_comment']);
        header("Location: index.php?page=video&id=" . urlencode($videoId));
        exit;
    }

    if (isset($_POST['delete_comment'])) {
        deleteComment($conn, $_POST['delete_comment'], $_SESSION['email']);
        header("Location: index.php?page=video&id=" . urlencode($videoId));
        exit;
    }

    if (isset($_POST['comment_react']) && isset($_POST['reaction_type'])) {
        reactToComment($conn, $_POST['comment_react'], $_SESSION['email'], $_POST['reaction_type']);
        header("Location: index.php?page=video&id=" . urlencode($videoId));
        exit;
    }
}

$comments = getCommentsByVideo($conn, $videoId, $_SESSION['email']);



$more_videos = array_filter(getUserVideos($conn, $video['uploader']), function($v) use ($videoId) {
    return $v['ID'] != $videoId;
});


$playlists = ['Tananyagok', 'Frontend kedvencek', 'Később megnézendő'];
?>

<div class="container py-5">
    <?php if (!$video): ?>
        <div class="alert alert-danger">A megadott videó nem található.</div>
        <button onclick="location.href='<?= $backLink ?>'" class="btn btn-outline-secondary mb-4">← Vissza</button>
    <?php else: ?>
    <button onclick="location.href='<?= $backLink ?>'" class="btn btn-outline-secondary mb-4">← Vissza</button>



        <?php if (isset($_SESSION['email']) && $_SESSION['email'] === $video['uploader']): ?>
            <a href="index.php?page=edit&id=<?= $videoId ?>" class="btn btn-outline-secondary w-100 mb-4">
                <i class="bi bi-pencil-square"></i> Szerkesztés
            </a>
        <?php endif; ?>

        <div class="row  justify-content-center">
            <div class="col-12 w-100">
                <div class="ratio w-100 ratio-16x9 mb-4">
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
                    <form method="post" class="d-flex gap-2">
                        <button type="submit" name="reaction" value="<?= $userReaction === 'like' ? 'remove' : 'like' ?>" class="btn btn-outline-success btn-sm <?= $userReaction === 'like' ? 'active' : '' ?>  <?= $_SESSION['email'] === $video['uploader'] ? 'disabled' : '' ?>">
                            <i class="bi bi-hand-thumbs-up-fill"></i> <?= $reactions['LIKES'] ?? 0 ?>
                        </button>
                        <button type="submit" name="reaction" value="<?= $userReaction === 'dislike' ? 'remove' : 'dislike' ?>" class="btn btn-outline-danger btn-sm <?= $userReaction === 'dislike' ? 'active' : '' ?>  <?= $_SESSION['email'] === $video['uploader'] ? 'disabled' : '' ?>">
                            <i class="bi bi-hand-thumbs-down-fill"></i> <?= $reactions['DISLIKES'] ?? 0 ?>
                        </button>
                    </form>
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
                                    <a href="<?= ($_SESSION['email'] === $video['uploader'])
                                        ? 'index.php?page=my_videos'
                                        : 'index.php?page=user&email=' . urlencode($video['uploader']) ?>"
                                       class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($video['uploader']) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php if ($_SESSION['email'] !== $video['uploader']): ?>
                        <form method="post">
                            <button type="submit" name="toggle_subscribe" class="btn <?= isSubscribed($conn, $_SESSION['email'], $video['uploader']) ? 'btn-outline-danger' : 'btn-outline-primary' ?>  w-100 mt-2">
                                <i class="bi bi-bell-fill"></i>
                                <?= isSubscribed($conn, $_SESSION['email'], $video['uploader']) ? 'Leiratkozás' : 'Feliratkozás' ?>
                            </button>
                        </form>
                        <?php endif; ?>
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
                <div class="list-group mb-4">
                    <?php foreach ($comments as $comment): ?>
                        <?php include 'src/includes/comment_card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <form method="post" class="mb-4">
                    <div class="mb-1">
                        <textarea class="form-control" name="new_comment" rows="3" placeholder="Írd ide a hozzászólásod..." ></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="bi bi-chat-dots"></i> Hozzászólás küldése
                    </button>
                </form>

                </div>
            </div>




        <h4 class="mt-4 mb-2">Feltöltő egyéb videói</h4>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-sm-4 g-4">

            <?php
            if (!empty( $more_videos)):
            foreach ($more_videos as $video):
                include 'src/includes/video_card.php';
            endforeach;
            else:
            ?>
            <p>A feltöltőnek nincsenek egyéb videói</p>
            <?php endif; ?>
        </div>




    <?php endif; ?>
</div>