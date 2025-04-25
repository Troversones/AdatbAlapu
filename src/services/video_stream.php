<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    http_response_code(400);
    exit("Hiányzó vagy érvénytelen ID");
}

$sql = "SELECT VIDEO_FILE FROM VIDEO WHERE VIDEO_ID = :id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":id", $id);
oci_execute($stmt);

if ($row = oci_fetch_assoc($stmt)) {
    $lob = $row['VIDEO_FILE'];

    if ($lob && $lob->size() > 0) {
        if (ob_get_length()) {
            ob_end_clean();
        }

        header("Content-Type: video/mp4");
        header("Content-Length: " . $lob->size());
        header("Accept-Ranges: bytes");

        ini_set('zlib.output_compression', '0');

        while (!$lob->eof()) {
            echo $lob->read(8192);
            flush();
        }

        $lob->free();
        oci_free_statement($stmt);
        exit;
    }
}

http_response_code(404);
exit("Nem található videó vagy üres");
