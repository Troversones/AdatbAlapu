<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}
$username = $_SESSION['username'];



require_once 'src/config/db.php';

?>
<div class="container py-5">
<?php


$email = $_SESSION['email'];
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video_file'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $videoTmpPath = $_FILES['video_file']['tmp_name'] ?? '';

    if (empty($title) || empty($description) || empty($videoTmpPath)) {
        echo "<div class='alert alert-danger'>Minden mező kitöltése kötelező!</div>";
    } else {
        $videoBlob = file_get_contents($videoTmpPath);

        $sql = "INSERT INTO VIDEO (FELHASZNALO_EMAIL, CIM, LEIRAS, VIDEO_FILE)
        VALUES (:email, :title, :description, EMPTY_BLOB())
        RETURNING VIDEO_FILE, VIDEO_ID INTO :blob, :id";

        $stmt = oci_parse($conn, $sql);

        $lob = oci_new_descriptor($conn, OCI_D_LOB);
        $videoId = null;

        oci_bind_by_name($stmt, ":email", $email);
        oci_bind_by_name($stmt, ":title", $title);
        oci_bind_by_name($stmt, ":description", $description);
        oci_bind_by_name($stmt, ":blob", $lob, -1, OCI_B_BLOB);
        oci_bind_by_name($stmt, ":id", $videoId, -1, SQLT_INT);

        $videoBlob = file_get_contents($videoTmpPath);
        $success = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
        if ( $success && $lob->save($videoBlob)) {
            oci_commit($conn);
            $lob->free();
            oci_free_statement($stmt);
            $tagList = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
            foreach ($tagList as $tag) {
                $stmtTag = oci_parse($conn, "INSERT INTO VIDEO_KATEGORIA (VIDEO_ID, KATEGORIA_NEV) VALUES (:id, :tag)");
                oci_bind_by_name($stmtTag, ":id", $videoId);
                oci_bind_by_name($stmtTag, ":tag", $tag);
                oci_execute($stmtTag);
                oci_free_statement($stmtTag);
            }

            echo "<div class='alert alert-success mt-3'>Sikeres feltöltés!</div>";
        } else {
            oci_rollback($conn);
            echo "<div class='alert alert-danger mt-3'>Hiba történt a mentéskor.</div>";
        }
    }
}
?>


    <button onclick="window.location.href='index.php?page=my_videos'" class="btn btn-outline-secondary mb-4">← Vissza</button>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="video_file" class="form-label">Videófájl</label>
            <input class="form-control" type="file" id="video_file" name="video_file" accept="video/mp4,video/*" >
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Cím</label>
            <input type="text" class="form-control" id="title" name="title" maxlength="255" >
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Leírás</label>
            <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000" ></textarea>
        </div>

        <div class="mb-3">
            <label for="tags" class="form-label">#Tagek <small class="text-muted">(vesszővel elválasztva, pl.: frontend, html, css)</small></label>
            <input type="text" class="form-control" id="tags" name="tags" placeholder="frontend, html, css">
        </div>

        <div class="mb-3">
            <label class="form-label">Feltöltő</label>
            <input type="text" class="form-control" value="<?= $username ?>" readonly>
        </div>


        <button type="submit" class="btn btn-primary">
            <i class="bi bi-upload"></i> Feltöltés
        </button>
    </form>
</div>
