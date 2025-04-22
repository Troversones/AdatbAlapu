<?php
session_start();
if (isset($_SESSION['email'])) {
    header("Location: index.php?page=home");
    exit;
}
require_once __DIR__ . '/../config/db.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT JELSZO, FELHASZNALONEV FROM FELHASZNALO WHERE EMAIL = :email";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":email", $email);
        oci_execute($stmt);

        if ($row = oci_fetch_assoc($stmt)) {
            $hashedPassword = $row['JELSZO'];

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $row['FELHASZNALONEV'];

                header("Location: index.php?page=home");
                exit;
            } else {
                $message = "<div class='alert alert-danger text-center'>Hibás jelszó.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger text-center'>Ez az email nincs regisztrálva.</div>";
        }

        oci_free_statement($stmt);
    } else {
        $message = "<div class='alert alert-warning text-center'>Kérlek, tölts ki minden mezőt.</div>";
    }
}
?>

<div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center bg-light">
    <div class="card mt-5 p-4 shadow-sm" style="width: 100%; max-width: 400px;">
        <h4 class="text-center mb-3">Bejelentkezés</h4>

        <?php if (!empty($message)) echo $message; ?>

        <form action="" method="post" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email cím</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="valami@email.com" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Jelszó</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Bejelentkezés</button>
        </form>

        <div class="text-center mt-3">
            <small>Még nincs fiókod? <a href="index.php?page=signup">Regisztrálj</a></small>
        </div>
    </div>
</div>