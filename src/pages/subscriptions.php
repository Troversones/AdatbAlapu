<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

// Dummy feliratkozott felhasználók listája
$subscriptions = ['frontend_mester', 'php_guru', 'travel_vlogger'];
?>

<div class="container py-5">

    <?php if (empty($subscriptions)): ?>
        <p>Nem vagy feliratkozva senkire.</p>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($subscriptions as $username): ?>
                <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-center">

                        <a href="index.php?page=user&username=<?= urlencode($username) ?>"
                           class="text-decoration-none text-dark d-flex align-items-center flex-grow-1 px-3 py-2">
                            <i class="bi bi-person-circle text-primary me-2"></i>
                            <strong><?= $username ?></strong>
                        </a>

                        <form method="post" action="#" class="me-3">
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
