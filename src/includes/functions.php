<?php
//egyéb functionok helye hogy ne a home page-en legyen php kód blokk
function getRecentVideos($conn, $limit = 20) {
    $videos = [];

    $sql = "SELECT VIDEO_ID AS id, CIM AS title FROM VIDEO ORDER BY DATUM DESC FETCH FIRST :limit ROWS ONLY";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ':limit', $limit);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $videos[] = $row;
    }

    oci_free_statement($stmt);

    return $videos;
}

function getUserVideos($conn, $email) {
    $videos = [];

    $sql = "SELECT VIDEO_ID AS id, CIM AS title 
            FROM VIDEO 
            WHERE FELHASZNALO_EMAIL = :email 
            ORDER BY DATUM DESC";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ':email', $email);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $videos[] = $row;
    }

    oci_free_statement($stmt);

    return $videos;
}
?>
