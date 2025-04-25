<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

// Dummy felhasználói statisztikák
$users = [
    ['username' => 'frontend_mester', 'uploads' => 8, 'comments' => 14],
    ['username' => 'php_guru', 'uploads' => 5, 'comments' => 22],
    ['username' => 'noobcoder', 'uploads' => 2, 'comments' => 30],
    ['username' => 'travel_vlogger', 'uploads' => 6, 'comments' => 9],
];

// Rangsorolás aktivitás szerint (feltöltés + komment pont)
usort($users, function($a, $b) {
    $scoreA = $a['uploads'] * 2 + $a['comments'];
    $scoreB = $b['uploads'] * 2 + $b['comments'];
    return $scoreB <=> $scoreA;
});
?>

<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-trophy-fill"></i>
        Legaktívabb tagok</h2>

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
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <a href="index.php?page=user&username=<?= urlencode($user['username']) ?>"
                           class="text-decoration-none text-dark fw-semibold">
                            <i class="bi bi-person-circle text-primary me-1"></i>
                            <?= $user['username'] ?>
                        </a>
                    </td>
                    <td><?= $user['uploads'] ?></td>
                    <td><?= $user['comments'] ?></td>
                    <td><?= $user['uploads'] * 2 + $user['comments'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
