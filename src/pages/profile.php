<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}
include 'src/includes/functions.php';
list($message, $userData) = handleProfileUpdate($conn);
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
</div>
