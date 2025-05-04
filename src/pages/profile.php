<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}
include 'src/includes/functions.php';
list($message, $userData) = handleProfileUpdate($conn);


$data = getPopularityStats($conn, $_SESSION['email']);
$labels = [];
$values = [];

foreach ($data as $row) {
    $labels[] = $row['CIM'];
    $values[] = $row['FELKAPOTTSAG'];
}
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Profil adatok</h2>

    <?= $message ?>

    <form method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Felhasználónév</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlentities($userData['FELHASZNALONEV']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email cím</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlentities($userData['EMAIL']) ?>" required>
        </div>

        <div class="d-flex justify-content-between">
            <div>
                <button type="submit" class="btn btn-primary me-2">Mentés</button>
                <a href="index.php?page=logout" class="btn btn-outline-danger">Kijelentkezés</a>
            </div>
            <a href="index.php?page=delete_account" class="btn btn-danger">Fiók törlése</a>
        </div>
    </form>

    <div class="container py-5">
        <h2 class="mb-4">Saját videóid felkapottsági mutatója</h2>
        <canvas id="popularityChart" height="100"></canvas>
    </div>

</div>

<script>
    const ctx = document.getElementById('popularityChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Felkapottsági pontszám',
                data: <?= json_encode($values) ?>,
                backgroundColor: 'rgba(13,110,253,0.7)',
                borderColor: 'rgba(13,110,253,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Pontszám' }
                },
                x: {
                    ticks: { autoSkip: false }
                }
            }
        }
    });
</script>