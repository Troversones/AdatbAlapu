<?php if (isset($comment)): ?>
<div class="list-group-item">
    <strong><?= htmlspecialchars($comment['FELHASZNALONEV']) ?>:</strong>
    <p class="mb-1"><?= nl2br(htmlspecialchars($comment['TARTALOM'])) ?></p>
    <div class="d-flex gap-2">
        <form method="post" class="d-inline">
            <input type="hidden" name="comment_react" value="<?= $comment['HOZZASZOLAS_ID'] ?>">
            <button class="btn btn-outline-success btn-sm  <?= ($comment['USER_REACTION'] === 'like' ? 'active' : '') ?> <?= $_SESSION['email'] === $comment['FELHASZNALO_EMAIL'] ? 'disabled' : '' ?>" name="reaction_type" value="like">
                <i class="bi bi-hand-thumbs-up-fill"></i> <?= $comment['LIKES'] ?? 0 ?>
            </button>
        </form>
        <form method="post" class="d-inline">
            <input type="hidden" name="comment_react" value="<?= $comment['HOZZASZOLAS_ID'] ?>">
            <button class="btn btn-outline-danger btn-sm  <?= ($comment['USER_REACTION'] === 'dislike' ? 'active' : '') ?> <?= $_SESSION['email'] === $comment['FELHASZNALO_EMAIL'] ? 'disabled' : '' ?>" name="reaction_type" value="dislike">
                <i class="bi bi-hand-thumbs-down-fill"></i> <?= $comment['DISLIKES'] ?? 0 ?>
            </button>
        </form>
        <?php if ($_SESSION['email'] === $comment['FELHASZNALO_EMAIL']): ?>
            <form method="post" class="d-inline">
                <input type="hidden" name="delete_comment" value="<?= $comment['HOZZASZOLAS_ID'] ?>">
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-trash"></i> Törlés</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
