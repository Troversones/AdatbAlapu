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



function getVideoDetailsForEdit($conn, $videoId, $email) {
    $sql = "SELECT VIDEO_ID, CIM, LEIRAS, FELHASZNALO_EMAIL FROM VIDEO WHERE VIDEO_ID = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $videoId);
    oci_execute($stmt);
    $video = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$video || $video['FELHASZNALO_EMAIL'] !== $email) {
        return null;
    }

    // Csatolt tagek
    $stmt = oci_parse($conn, "SELECT KATEGORIA_NEV FROM VIDEO_KATEGORIA WHERE VIDEO_ID = :id");
    oci_bind_by_name($stmt, ":id", $videoId);
    oci_execute($stmt);
    $tags = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $tags[] = $row['KATEGORIA_NEV'];
    }
    oci_free_statement($stmt);

    $video['TAGS'] = $tags;
    return $video;
}

function updateVideo($conn, $videoId, $title, $description, $tags) {
    $sql = "UPDATE VIDEO SET CIM = :title, LEIRAS = :description WHERE VIDEO_ID = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":title", $title);
    oci_bind_by_name($stmt, ":description", $description);
    oci_bind_by_name($stmt, ":id", $videoId);
    oci_execute($stmt);
    oci_free_statement($stmt);

    // Új kategóriák
    $stmt = oci_parse($conn, "DELETE FROM VIDEO_KATEGORIA WHERE VIDEO_ID = :id");
    oci_bind_by_name($stmt, ":id", $videoId);
    oci_execute($stmt);
    oci_free_statement($stmt);

    $tagList = array_filter(array_map('trim', explode(',', $tags)));
    foreach ($tagList as $tag) {
        $stmt = oci_parse($conn, "INSERT INTO VIDEO_KATEGORIA (VIDEO_ID, KATEGORIA_NEV) VALUES (:id, :tag)");
        oci_bind_by_name($stmt, ":id", $videoId);
        oci_bind_by_name($stmt, ":tag", $tag);
        oci_execute($stmt);
        oci_free_statement($stmt);
    }

    return true;
}

function deleteVideo($conn, $videoId) {
    $stmt = oci_parse($conn, "DELETE FROM VIDEO WHERE VIDEO_ID = :id");
    oci_bind_by_name($stmt, ":id", $videoId);
    oci_execute($stmt);
    oci_free_statement($stmt);
}
?>
