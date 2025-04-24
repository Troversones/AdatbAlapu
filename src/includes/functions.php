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

function getVideoById($conn, $id) {
    $video = null;

    $sql = "SELECT v.VIDEO_ID, v.CIM, v.LEIRAS, v.DATUM, v.NEZETTSEG, v.FELHASZNALO_EMAIL 
            FROM VIDEO v
            WHERE v.VIDEO_ID = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $id);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        $video = [
            'id' => $row['VIDEO_ID'],
            'title' => $row['CIM'],
            'description' => $row['LEIRAS'],
            'upload_date' => date('Y-m-d', strtotime($row['DATUM'])),
            'views' => $row['NEZETTSEG'],
            'uploader' => $row['FELHASZNALO_EMAIL'],
            'tags' => [],
        ];
    }
    oci_free_statement($stmt);

    if ($video) {
        $tagSql = "SELECT KATEGORIA_NEV FROM VIDEO_KATEGORIA WHERE VIDEO_ID = :id";
        $tagStmt = oci_parse($conn, $tagSql);
        oci_bind_by_name($tagStmt, ":id", $id);
        oci_execute($tagStmt);

        while ($tagRow = oci_fetch_assoc($tagStmt)) {
            $video['tags'][] = $tagRow['KATEGORIA_NEV'];
        }
        oci_free_statement($tagStmt);
    }

    return $video;
}
?>
