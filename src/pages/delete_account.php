<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}

include 'src/includes/functions.php';

$email = $_SESSION['email'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Felhasználó törlése
    $stmt = oci_parse($conn, "DELETE FROM FELHASZNALO WHERE EMAIL = :email");
    oci_bind_by_name($stmt, ":email", $email);

    if (oci_execute($stmt)) {
        oci_free_statement($stmt);
        session_destroy();
        header("Location: index.php?page=login&account_deleted=1");
        exit;
    } else {
        $message = "<div class='alert alert-danger'>Hiba történt a fiók törlése közben.</div>";
        oci_free_statement($stmt);
    }
}
?>

<div class="container mt-5 mb-5 w-50 mx-auto">

    <?= $message ?>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Biztosan törölni szeretnéd a fiókodat? Ez a művelet <strong>visszavonhatatlan</strong> és minden adatod el fog veszni!
    </div>

    <form method="post" class="d-flex flex-column gap-3">
        <button type="submit" name="confirm_delete" class="btn btn-danger">
            <i class="bi bi-trash"></i> Fiók végleges törlése
        </button>
        <a href="index.php?page=profile" class="btn btn-outline-secondary">
            Mégse
        </a>
    </form>
</div>

