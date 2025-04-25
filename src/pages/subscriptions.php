<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

require_once 'src/config/db.php';
require_once 'src/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unsubscribe_email'])) {
    unsubscribe($conn, $_SESSION['email'], $_POST['unsubscribe_email']);
}

$subscriptions = getSubscriptions($conn, $_SESSION['email']);
?>

<div class="container py-5">

    <?php if (empty($subscriptions)): ?>
        <p class="w-100 text-center fs-5">Nem vagy feliratkozva senkire.</p>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($subscriptions as $user): ?>
                <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="index.php?page=user&email=<?= urlencode($user['email']) ?>"
                           class="text-decoration-none text-dark d-flex align-items-center flex-grow-1 px-3 py-2">
                            <i class="bi bi-person-circle text-primary me-2"></i>
                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                        </a>

                        <form method="post" class="me-3">
                            <input type="hidden" name="unsubscribe_email" value="<?= htmlspecialchars($user['email']) ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-x-circle"></i> Leiratkozás
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
