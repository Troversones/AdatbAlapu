<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $felhasznalonev = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $szuletesi_datum = $_POST['birthdate'] ?? '';
    $jelszo = $_POST['password'] ?? '';

    if ($felhasznalonev && $email && $jelszo) {
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
            $message = "<div class='alert alert-success'>Sikeres regisztráció!</div>";
        } else {
            $e = oci_error($stmt);
            if (strpos($e['message'], 'ORA-00001') !== false) {
                $message = "<div class='alert alert-warning'>Ez az email cím már regisztrálva van.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Hiba történt: " . $e['message'] . "</div>";
            }
        }

        oci_free_statement($stmt);
    } else {
        $message = "<div class='alert alert-warning'>Az email, felhasználónév és jelszó mezők kitöltése kötelező!</div>";
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
            <button type="submit" class="btn btn-primary w-100 py-2">Regisztráció</button>
        </form>
        <div class="text-center mt-3">
            <small>Van már fiókod? <a href="index.php?page=login">Jelentkezz be</a></small>
        </div>
    </div>
</div>
