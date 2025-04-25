<?php
session_start();
if (isset($_SESSION['email'])) {
    header("Location: index.php?page=home");
    exit;
}
require_once __DIR__ . '/../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $felhasznalonev = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $szuletesi_datum = trim($_POST['birthdate'] ?? '');
    $jelszo = trim($_POST['password'] ?? '');

    if ($felhasznalonev && $email && $jelszo && filter_var($email, FILTER_VALIDATE_EMAIL)) {

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $szuletesi_datum)) {
            $message = "<div class='alert alert-warning'>Hibás születési dátum formátum. (Pl.: 2000-12-31)</div>";
        } else {
            $hashedPassword = password_hash($jelszo, PASSWORD_DEFAULT);

            $sql = "INSERT INTO FELHASZNALO (EMAIL, FELHASZNALONEV, JELSZO, SZULETESI_DATUM)
                    VALUES (:email, :felhasznalonev, :jelszo, TO_DATE(:szuletesi_datum, 'YYYY-MM-DD'))";

            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ":email", $email);
            oci_bind_by_name($stmt, ":felhasznalonev", $felhasznalonev);
            oci_bind_by_name($stmt, ":jelszo", $hashedPassword);
            oci_bind_by_name($stmt, ":szuletesi_datum", $szuletesi_datum);

            $result = oci_execute($stmt);

            if ($result) {
                $message = '<div class="alert alert-success d-flex justify-content-between align-items-center">
                            <span>Sikeres regisztráció!</span>
                            <a href="index.php?page=login" class="btn btn-sm btn-outline-dark">Bejelentkezés</a>
                            </div>';
            } else {
                $e = oci_error($stmt);
                if (strpos($e['message'], 'ORA-00001') !== false) {
                    $message = "<div class='alert alert-warning'>Ez az email cím már regisztrálva van.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Hiba történt: " . htmlspecialchars($e['message']) . "</div>";
                }
            }

            oci_free_statement($stmt);
        }
    } else {
        $message = "<div class='alert alert-warning'>Érvénytelen vagy hiányzó adatok! Kérlek, tölts ki minden kötelező mezőt helyesen.</div>";
    }
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