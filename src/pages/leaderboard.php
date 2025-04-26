<?php
session_start();
require_once 'src/config/db.php';
include 'src/includes/functions.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

$users = getLeaderboardData($conn);
$currentUserEmail = $_SESSION['email'];
?>

<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-trophy-fill"></i> Legaktívabb tagok</h2>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Felhasználónév</th>
                <th>Feltöltések</th>
                <th>Hozzászólások</th>
                <th>Aktivitási pont</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $i => $user): ?>
                <?php
                $rank = $i + 1;
                $highlightClass = ($user['EMAIL'] === $currentUserEmail) ? 'table-warning fw-bold' : '';
                ?>
                <tr class="<?= $highlightClass ?>">
                    <td><?=  $rank ?></td>
                    <td>
                        <?php if ($user['EMAIL'] === $currentUserEmail):?>
                        <a href="index.php?page=my_videos"
                        <?php else: ?>
                        <a href="index.php?page=user&email=<?= urlencode($user['EMAIL']) ?>"
                            <?php endif; ?>
                           class="text-decoration-none text-dark fw-semibold">
                            <i class="bi bi-person-circle text-primary me-1"></i>
                            <?= htmlspecialchars($user['USERNAME']) ?>
                        </a>
                    </td>
                    <td><?= $user['UPLOADS'] ?></td>
                    <td><?= $user['COMMENTS'] ?></td>
                    <td><?= $user['activity_points'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
