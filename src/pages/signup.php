<?php
session_start();
if (isset($_SESSION['email'])) {
    header("Location: index.php?page=home");
    exit;
}
require_once __DIR__ . '/../config/db.php';
include 'src/includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = registerUser(
        $conn,
        $_POST['username'] ?? '',
        $_POST['email'] ?? '',
        $_POST['birthdate'] ?? '',
        $_POST['password'] ?? ''
    );
}
?>

<div class="min-vh-100 d-flex justify-content-center align-items-center bg-light px-3">
    <div class="card p-4 shadow w-100" style="max-width: 480px;">
        <h4 class="text-center mb-4">Regisztráció</h4>
        <?php if (!empty($message)) echo $message; ?>
        <form action="" method="post" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Felhasználónév</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="pl. nagyjani">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email cím</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="valami@email.com">
            </div>
            <div class="mb-3">
                <label for="birthdate" class="form-label">Születési dátum</label>
                <input type="date" class="form-control" id="birthdate" name="birthdate">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Jelszó</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="********">
            </div>
            <div class="mb-4">
                <label for="password2" class="form-label">Jelszó mégegyszer</label>
                <input type="password" class="form-control" id="password2" name="password2" placeholder="********">
                <div id="password-match-message" class="form-text text-danger" style="display: none;">
                    A jelszavak nem egyeznek meg.
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2" id="submitBtn" disabled>Regisztráció</button>
        </form>
        <div class="text-center mt-3">
            <small>Van már fiókod? <a href="index.php?page=login">Jelentkezz be</a></small>
        </div>
    </div>
</div>
<script src="public/js/register_pwdcheck.js"></script>