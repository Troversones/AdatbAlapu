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

function getVideoReactions($conn, $videoId) {
    $stmt = oci_parse($conn, "
        SELECT 
            SUM(CASE WHEN TIPUS = 'like' THEN 1 ELSE 0 END) AS likes,
            SUM(CASE WHEN TIPUS = 'dislike' THEN 1 ELSE 0 END) AS dislikes
        FROM VIDEO_REAKCIO
        WHERE VIDEO_ID = :id
    ");
    oci_bind_by_name($stmt, ":id", $videoId);
    oci_execute($stmt);
    $result = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    return $result;
}

function getUserReaction($conn, $videoId, $email) {
    $stmt = oci_parse($conn, "
        SELECT TIPUS FROM VIDEO_REAKCIO WHERE VIDEO_ID = :id AND FELHASZNALO_EMAIL = :email
    ");
    oci_bind_by_name($stmt, ":id", $videoId);
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);
    $result = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    return $result['TIPUS'] ?? null;
}

function setReaction($conn, $videoId, $email, $type) {
    $check = oci_parse($conn, "
        SELECT TIPUS FROM VIDEO_REAKCIO
        WHERE VIDEO_ID = :id AND FELHASZNALO_EMAIL = :email
    ");
    oci_bind_by_name($check, ":id", $videoId);
    oci_bind_by_name($check, ":email", $email);
    oci_execute($check);
    $existing = oci_fetch_assoc($check);
    oci_free_statement($check);

    $existingType = $existing['TIPUS'] ?? null;

    if ($existingType === $type) {
        $delete = oci_parse($conn, "
            DELETE FROM VIDEO_REAKCIO
            WHERE VIDEO_ID = :id AND FELHASZNALO_EMAIL = :email
        ");
        oci_bind_by_name($delete, ":id", $videoId);
        oci_bind_by_name($delete, ":email", $email);
        oci_execute($delete);
        oci_free_statement($delete);

    } else {
        if ($existingType) {
            $update = oci_parse($conn, "
                UPDATE VIDEO_REAKCIO
                SET TIPUS = :type
                WHERE VIDEO_ID = :id AND FELHASZNALO_EMAIL = :email
            ");
            oci_bind_by_name($update, ":type", $type);
            oci_bind_by_name($update, ":id", $videoId);
            oci_bind_by_name($update, ":email", $email);
            oci_execute($update);
            oci_free_statement($update);

        } else {
            if (in_array($type, ['like', 'dislike'])) {
                $insert = oci_parse($conn, "
                    INSERT INTO VIDEO_REAKCIO (VIDEO_ID, FELHASZNALO_EMAIL, TIPUS)
                    VALUES (:id, :email, :type)
                ");
                oci_bind_by_name($insert, ":id", $videoId);
                oci_bind_by_name($insert, ":email", $email);
                oci_bind_by_name($insert, ":type", $type);
                oci_execute($insert);
                oci_free_statement($insert);
            }
        }
    }
}

function incrementViewCount($conn, $videoId) {
    $sql = "UPDATE VIDEO SET NEZETTSEG = NEZETTSEG + 1 WHERE VIDEO_ID = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $videoId);
    oci_execute($stmt);
    oci_free_statement($stmt);
}

function isSubscribed($conn, $subscriberEmail, $creatorEmail) {
    $stmt = oci_parse($conn, "SELECT COUNT(*) AS CNT FROM FELIRATKOZAS WHERE FELHASZNALO_EMAIL = :subscriber AND FELIRATKOZOTT_EMAIL = :creator");
    oci_bind_by_name($stmt, ":subscriber", $subscriberEmail);
    oci_bind_by_name($stmt, ":creator", $creatorEmail);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    return $row['CNT'] > 0;
}

function toggleSubscription($conn, $subscriberEmail, $creatorEmail) {
    if (isSubscribed($conn, $subscriberEmail, $creatorEmail)) {
        $stmt = oci_parse($conn, "DELETE FROM FELIRATKOZAS WHERE FELHASZNALO_EMAIL = :subscriber AND FELIRATKOZOTT_EMAIL = :creator");
    } else {
        $stmt = oci_parse($conn, "INSERT INTO FELIRATKOZAS (FELHASZNALO_EMAIL, FELIRATKOZOTT_EMAIL) VALUES (:subscriber, :creator)");
    }
    oci_bind_by_name($stmt, ":subscriber", $subscriberEmail);
    oci_bind_by_name($stmt, ":creator", $creatorEmail);
    oci_execute($stmt);
    oci_free_statement($stmt);
}

function getSubscriptions($conn, $email) {
    $subscriptions = [];
    $sql = "SELECT f.FELIRATKOZOTT_EMAIL, u.FELHASZNALONEV 
            FROM FELIRATKOZAS f 
            JOIN FELHASZNALO u ON f.FELIRATKOZOTT_EMAIL = u.EMAIL 
            WHERE f.FELHASZNALO_EMAIL = :email
            GROUP BY f.FELIRATKOZOTT_EMAIL, u.FELHASZNALONEV";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $subscriptions[] = [
            'email' => $row['FELIRATKOZOTT_EMAIL'],
            'username' => $row['FELHASZNALONEV']
        ];
    }

    oci_free_statement($stmt);
    return $subscriptions;
}

function unsubscribe($conn, $subscriberEmail, $creatorEmail) {
    $stmt = oci_parse($conn, "DELETE FROM FELIRATKOZAS WHERE FELHASZNALO_EMAIL = :subscriber AND FELIRATKOZOTT_EMAIL = :creator");
    oci_bind_by_name($stmt, ":subscriber", $subscriberEmail);
    oci_bind_by_name($stmt, ":creator", $creatorEmail);
    oci_execute($stmt);
    oci_free_statement($stmt);
}

function handleProfileUpdate($conn) {
    $message = '';
    $currentEmail = $_SESSION['email'];

    $sql = "SELECT FELHASZNALONEV, EMAIL FROM FELHASZNALO WHERE EMAIL = :email";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":email", $currentEmail);
    oci_execute($stmt);

    $userData = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $newUsername = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
        $newEmail = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $message = "<div class='alert alert-danger'>Érvénytelen email formátum.</div>";
        } else {
            $emailCheckSql = "SELECT COUNT(*) AS CNT FROM FELHASZNALO WHERE EMAIL = :new_email AND EMAIL != :current_email";
            $emailCheckStmt = oci_parse($conn, $emailCheckSql);
            oci_bind_by_name($emailCheckStmt, ":new_email", $newEmail);
            oci_bind_by_name($emailCheckStmt, ":current_email", $currentEmail);
            oci_execute($emailCheckStmt);
            $emailRow = oci_fetch_assoc($emailCheckStmt);
            oci_free_statement($emailCheckStmt);

            $usernameCheckSql = "SELECT COUNT(*) AS CNT FROM FELHASZNALO WHERE FELHASZNALONEV = :new_username AND EMAIL != :current_email";
            $usernameCheckStmt = oci_parse($conn, $usernameCheckSql);
            oci_bind_by_name($usernameCheckStmt, ":new_username", $newUsername);
            oci_bind_by_name($usernameCheckStmt, ":current_email", $currentEmail);
            oci_execute($usernameCheckStmt);
            $usernameRow = oci_fetch_assoc($usernameCheckStmt);
            oci_free_statement($usernameCheckStmt);

            if ($emailRow['CNT'] > 0) {
                $message = "<div class='alert alert-danger'>Ez az email cím már foglalt.</div>";
            } elseif ($usernameRow['CNT'] > 0) {
                $message = "<div class='alert alert-danger'>Ez a felhasználónév már foglalt.</div>";
            } else {
                $updateSql = "UPDATE FELHASZNALO SET FELHASZNALONEV = :username, EMAIL = :new_email WHERE EMAIL = :current_email";
                $updateStmt = oci_parse($conn, $updateSql);

                oci_bind_by_name($updateStmt, ":username", $newUsername);
                oci_bind_by_name($updateStmt, ":new_email", $newEmail);
                oci_bind_by_name($updateStmt, ":current_email", $currentEmail);

                if (oci_execute($updateStmt)) {
                    $_SESSION['email'] = $newEmail;
                    $_SESSION['username'] = $newUsername;
                    $message = "<div class='alert alert-success'>Profil sikeresen frissítve.</div>";

                    $userData['EMAIL'] = $newEmail;
                    $userData['FELHASZNALONEV'] = $newUsername;
                } else {
                    $message = "<div class='alert alert-danger'>Hiba történt a mentés során.</div>";
                }

                oci_free_statement($updateStmt);
            }
        }
    }
    return [$message, $userData];
}


function addComment($conn, $videoId, $userEmail, $content) {
    $sql = "INSERT INTO HOZZASZOLAS (FELHASZNALO_EMAIL, VIDEO_ID, TARTALOM) 
            VALUES (:email, :videoId, EMPTY_CLOB()) 
            RETURNING TARTALOM INTO :content";

    $stmt = oci_parse($conn, $sql);

    $lob = oci_new_descriptor($conn, OCI_D_LOB);

    oci_bind_by_name($stmt, ":email", $userEmail);
    oci_bind_by_name($stmt, ":videoId", $videoId);
    oci_bind_by_name($stmt, ":content", $lob, -1, OCI_B_CLOB);

    $success = oci_execute($stmt, OCI_NO_AUTO_COMMIT);

    if ($success && $lob->save($content)) {
        oci_commit($conn);
    } else {
        oci_rollback($conn);
    }

    $lob->free();
    oci_free_statement($stmt);
}


function getCommentsByVideo($conn, $videoId, $currentUserEmail) {
    $comments = [];
    $sql = "SELECT 
                h.HOZZASZOLAS_ID, 
                TO_CHAR(h.TARTALOM) AS TARTALOM, 
                h.FELHASZNALO_EMAIL, 
                h.DATUM,
                u.FELHASZNALONEV,
                SUM(CASE WHEN r.TIPUS = 'like' THEN 1 ELSE 0 END) AS likes,
                SUM(CASE WHEN r.TIPUS = 'dislike' THEN 1 ELSE 0 END) AS dislikes,
                (SELECT TIPUS FROM HOZZASZOLAS_REAKCIO WHERE FELHASZNALO_EMAIL = :currentUser AND HOZZASZOLAS_ID = h.HOZZASZOLAS_ID) AS USER_REACTION
            FROM HOZZASZOLAS h
            JOIN FELHASZNALO u ON h.FELHASZNALO_EMAIL = u.EMAIL
            LEFT JOIN HOZZASZOLAS_REAKCIO r ON h.HOZZASZOLAS_ID = r.HOZZASZOLAS_ID
            WHERE h.VIDEO_ID = :videoId
            GROUP BY h.HOZZASZOLAS_ID, TO_CHAR(h.TARTALOM), h.FELHASZNALO_EMAIL, h.DATUM, u.FELHASZNALONEV
            ORDER BY h.DATUM DESC";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":videoId", $videoId);
    oci_bind_by_name($stmt, ":currentUser", $currentUserEmail);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $comments[] = $row;
    }

    oci_free_statement($stmt);
    return $comments;
}

function deleteComment($conn, $commentId, $userEmail) {
    $sql = "DELETE FROM HOZZASZOLAS WHERE HOZZASZOLAS_ID = :id AND FELHASZNALO_EMAIL = :email";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $commentId);
    oci_bind_by_name($stmt, ":email", $userEmail);
    oci_execute($stmt);
    oci_free_statement($stmt);
}

function reactToComment($conn, $commentId, $userEmail, $newType) {
    $check = oci_parse($conn, "
        SELECT TIPUS FROM HOZZASZOLAS_REAKCIO 
        WHERE HOZZASZOLAS_ID = :id AND FELHASZNALO_EMAIL = :email
    ");
    oci_bind_by_name($check, ":id", $commentId);
    oci_bind_by_name($check, ":email", $userEmail);
    oci_execute($check);
    $existing = oci_fetch_assoc($check);
    oci_free_statement($check);

    $existingType = $existing['TIPUS'] ?? null;

    if ($existingType === $newType) {
        $delete = oci_parse($conn, "
            DELETE FROM HOZZASZOLAS_REAKCIO 
            WHERE HOZZASZOLAS_ID = :id AND FELHASZNALO_EMAIL = :email
        ");
        oci_bind_by_name($delete, ":id", $commentId);
        oci_bind_by_name($delete, ":email", $userEmail);
        oci_execute($delete);
        oci_free_statement($delete);
    } else {
        if ($existingType) {
            $update = oci_parse($conn, "
                UPDATE HOZZASZOLAS_REAKCIO 
                SET TIPUS = :type 
                WHERE HOZZASZOLAS_ID = :id AND FELHASZNALO_EMAIL = :email
            ");
            oci_bind_by_name($update, ":type", $newType);
            oci_bind_by_name($update, ":id", $commentId);
            oci_bind_by_name($update, ":email", $userEmail);
            oci_execute($update);
            oci_free_statement($update);
        } else {
            if (in_array($newType, ['like', 'dislike'])) {
                $insert = oci_parse($conn, "
                    INSERT INTO HOZZASZOLAS_REAKCIO (HOZZASZOLAS_ID, FELHASZNALO_EMAIL, TIPUS)
                    VALUES (:id, :email, :type)
                ");
                oci_bind_by_name($insert, ":id", $commentId);
                oci_bind_by_name($insert, ":email", $userEmail);
                oci_bind_by_name($insert, ":type", $newType);
                oci_execute($insert);
                oci_free_statement($insert);
            }
        }
    }
}

function getLeaderboardData($conn) {
    $users = [];

    $sql = "
        SELECT 
            f.FELHASZNALONEV AS username, 
            f.EMAIL AS email,
            COUNT(DISTINCT v.VIDEO_ID) AS uploads,
            COUNT(DISTINCT h.HOZZASZOLAS_ID) AS comments
        FROM FELHASZNALO f
        LEFT JOIN VIDEO v ON v.FELHASZNALO_EMAIL = f.EMAIL
        LEFT JOIN HOZZASZOLAS h ON h.FELHASZNALO_EMAIL = f.EMAIL
        GROUP BY f.FELHASZNALONEV, f.EMAIL
    ";

    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $uploads = $row['UPLOADS'] ?? 0;
        $comments = $row['COMMENTS'] ?? 0;
        $row['activity_points'] = $uploads * 2 + $comments;
        $users[] = $row;
    }

    oci_free_statement($stmt);

    usort($users, function($a, $b) {
        return $b['activity_points'] <=> $a['activity_points'];
    });

    return $users;
}

function deleteAccount($conn, $email){
    $stmt = oci_parse($conn, "DELETE FROM FELHASZNALO WHERE EMAIL = :email");
    oci_bind_by_name($stmt, ":email", $email);

    if (oci_execute($stmt)) {
        oci_free_statement($stmt);
        return true;
    }
    oci_free_statement($stmt);
    return false;
}

function uploadVideo($conn, $email, $title, $description, $tags, $videoTmpPath) {
    if (empty($title) || empty($description) || empty($videoTmpPath)) {
        return "<div class='alert alert-danger'>Minden mező kitöltése kötelező!</div>";
    }

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

    $success = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
    if ($success && $lob->save($videoBlob)) {
        oci_commit($conn);
        $lob->free();
        oci_free_statement($stmt);

        $tagList = array_filter(array_map('trim', explode(',', $tags)));
        foreach ($tagList as $tag) {
            $stmtTag = oci_parse($conn, "INSERT INTO VIDEO_KATEGORIA (VIDEO_ID, KATEGORIA_NEV) VALUES (:id, :tag)");
            oci_bind_by_name($stmtTag, ":id", $videoId);
            oci_bind_by_name($stmtTag, ":tag", $tag);
            oci_execute($stmtTag);
            oci_free_statement($stmtTag);
        }

        return "<div class='alert alert-success mt-3'>Sikeres feltöltés!</div>";
    } else {
        oci_rollback($conn);
        if ($lob) $lob->free();
        if ($stmt) oci_free_statement($stmt);
        return "<div class='alert alert-danger mt-3'>Hiba történt a mentéskor.</div>";
    }
}

function createPlaylist($conn, $name, $userEmail) {
    $name = trim($name);

    if (empty($name)) {
        return "<div class='alert alert-danger'>Add meg a lejátszási lista nevét.</div>";
    }

    if (strcasecmp($name, 'Kedvencek') == 0) {
        return "<div class='alert alert-danger'>Nem hozhatsz létre új 'Kedvencek' nevű lejátszási listát.</div>";
    }

    $sql = "INSERT INTO LEJATSZASI_LISTA (NEV, FELHASZNALO_EMAIL)
            VALUES (:name, :email)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":email", $userEmail);

    if (oci_execute($stmt)) {
        oci_free_statement($stmt);
        return "<div class='alert alert-success'>Sikeres létrehozás!</div>";
    } else {
        oci_free_statement($stmt);
        return "<div class='alert alert-danger'>Hiba történt a létrehozás során.</div>";
    }
}

function getUserPlaylists($conn, $userEmail) {
    $playlists = [];

    $sql = "
        SELECT l.LEJATSZASI_LISTA_ID AS id, l.NEV AS name, 
               COUNT(v.VIDEO_ID) AS count
        FROM LEJATSZASI_LISTA l
        LEFT JOIN LEJATSZASI_LISTA_VIDEO v ON l.LEJATSZASI_LISTA_ID = v.LEJATSZASI_LISTA_ID
        WHERE l.FELHASZNALO_EMAIL = :email
        GROUP BY l.LEJATSZASI_LISTA_ID, l.NEV
        ORDER BY l.NEV
    ";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":email", $userEmail);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $playlists[] = $row;
    }

    oci_free_statement($stmt);

    return $playlists;
}

function addVideoToPlaylist($conn, $playlistId, $videoId) {
    $check = oci_parse($conn, "
        SELECT COUNT(*) AS CNT 
        FROM LEJATSZASI_LISTA_VIDEO 
        WHERE LEJATSZASI_LISTA_ID = :playlistId AND VIDEO_ID = :videoId
    ");
    oci_bind_by_name($check, ":playlistId", $playlistId);
    oci_bind_by_name($check, ":videoId", $videoId);
    oci_execute($check);
    $row = oci_fetch_assoc($check);
    oci_free_statement($check);

    if ($row['CNT'] > 0) {
        return "<div class='alert alert-warning'>Ez a videó már szerepel ebben a lejátszási listában.</div>";
    }

    $insert = oci_parse($conn, "
        INSERT INTO LEJATSZASI_LISTA_VIDEO (LEJATSZASI_LISTA_ID, VIDEO_ID)
        VALUES (:playlistId, :videoId)
    ");
    oci_bind_by_name($insert, ":playlistId", $playlistId);
    oci_bind_by_name($insert, ":videoId", $videoId);
    oci_execute($insert);
    oci_free_statement($insert);

    return "<div class='alert alert-success'>Videó sikeresen hozzáadva a lejátszási listához!</div>";
}

function getPlaylistById($conn, $playlistId, $userEmail) {
    $sql = "SELECT LEJATSZASI_LISTA_ID AS id, NEV AS name
            FROM LEJATSZASI_LISTA
            WHERE LEJATSZASI_LISTA_ID = :id AND FELHASZNALO_EMAIL = :email";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $playlistId);
    oci_bind_by_name($stmt, ":email", $userEmail);
    oci_execute($stmt);

    $playlist = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    return $playlist;
}

function updatePlaylistName($conn, $playlistId, $newName) {
    $newName = trim($newName);

    if (empty($newName)) {
        return "<div class='alert alert-danger'>Add meg az új nevet.</div>";
    }

    if (strcasecmp($newName, 'Kedvencek') == 0) {
        return "<div class='alert alert-danger'>Nem nevezheted át a listát 'Kedvencek'-re.</div>";
    }

    $stmt = oci_parse($conn, "SELECT NEV FROM LEJATSZASI_LISTA WHERE LEJATSZASI_LISTA_ID = :id");
    oci_bind_by_name($stmt, ":id", $playlistId);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if ($row && strcasecmp($row['NEV'], 'Kedvencek') == 0) {
        return "<div class='alert alert-danger'>A 'Kedvencek' listát nem lehet átnevezni.</div>";
    }

    $stmt = oci_parse($conn, "UPDATE LEJATSZASI_LISTA SET NEV = :newName WHERE LEJATSZASI_LISTA_ID = :id");
    oci_bind_by_name($stmt, ":newName", $newName);
    oci_bind_by_name($stmt, ":id", $playlistId);

    if (oci_execute($stmt)) {
        oci_free_statement($stmt);
        return "<div class='alert alert-success'>Lejátszási lista sikeresen átnevezve.</div>";
    } else {
        oci_free_statement($stmt);
        return "<div class='alert alert-danger'>Hiba történt az átnevezés során.</div>";
    }
}

function deletePlaylist($conn, $playlistId) {
    $stmt = oci_parse($conn, "SELECT NEV FROM LEJATSZASI_LISTA WHERE LEJATSZASI_LISTA_ID = :id");
    oci_bind_by_name($stmt, ":id", $playlistId);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if ($row && strcasecmp($row['NEV'], 'Kedvencek') == 0) {
        return "kedvencek_delete";
    }

    $stmt = oci_parse($conn, "DELETE FROM LEJATSZASI_LISTA WHERE LEJATSZASI_LISTA_ID = :id");
    oci_bind_by_name($stmt, ":id", $playlistId);

    if (oci_execute($stmt)) {
        oci_free_statement($stmt);
        return "success";
    } else {
        oci_free_statement($stmt);
        return "other";
    }
}

function removeVideoFromPlaylist($conn, $playlistId, $videoId) {
    $sql = "DELETE FROM LEJATSZASI_LISTA_VIDEO 
            WHERE LEJATSZASI_LISTA_ID = :playlistId AND VIDEO_ID = :videoId";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":playlistId", $playlistId);
    oci_bind_by_name($stmt, ":videoId", $videoId);

    if (oci_execute($stmt)) {
        oci_free_statement($stmt);
        return "<div class='alert alert-success'>Videó eltávolítva a lejátszási listából.</div>";
    } else {
        oci_free_statement($stmt);
        return "<div class='alert alert-danger'>Hiba a törlés során!</div>";
    }
}

function getVideosInPlaylist($conn, $playlistId) {
    $videos = [];

    $sql = "SELECT v.VIDEO_ID AS id, v.CIM AS title
            FROM LEJATSZASI_LISTA_VIDEO lv
            JOIN VIDEO v ON lv.VIDEO_ID = v.VIDEO_ID
            WHERE lv.LEJATSZASI_LISTA_ID = :id
            ORDER BY v.DATUM DESC";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $playlistId);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $videos[] = $row;
    }

    oci_free_statement($stmt);

    return $videos;
}

function addVideoToFavorites($conn, $email, $videoId) {
    $stmt = oci_parse($conn, "
        SELECT LEJATSZASI_LISTA_ID 
        FROM LEJATSZASI_LISTA 
        WHERE FELHASZNALO_EMAIL = :email AND NEV = 'Kedvencek'
    ");
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$row) return;

    $playlistId = $row['LEJATSZASI_LISTA_ID'];

    $check = oci_parse($conn, "
        SELECT COUNT(*) AS CNT
        FROM LEJATSZASI_LISTA_VIDEO
        WHERE LEJATSZASI_LISTA_ID = :playlistId AND VIDEO_ID = :videoId
    ");
    oci_bind_by_name($check, ":playlistId", $playlistId);
    oci_bind_by_name($check, ":videoId", $videoId);
    oci_execute($check);
    $checkRow = oci_fetch_assoc($check);
    oci_free_statement($check);

    if ($checkRow['CNT'] == 0) {
        $insert = oci_parse($conn, "
            INSERT INTO LEJATSZASI_LISTA_VIDEO (LEJATSZASI_LISTA_ID, VIDEO_ID)
            VALUES (:playlistId, :videoId)
        ");
        oci_bind_by_name($insert, ":playlistId", $playlistId);
        oci_bind_by_name($insert, ":videoId", $videoId);
        oci_execute($insert);
        oci_free_statement($insert);
    }
}


function removeVideoFromFavorites($conn, $email, $videoId) {
    $stmt = oci_parse($conn, "
        SELECT LEJATSZASI_LISTA_ID 
        FROM LEJATSZASI_LISTA 
        WHERE FELHASZNALO_EMAIL = :email AND NEV = 'Kedvencek'
    ");
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$row) return;

    $playlistId = $row['LEJATSZASI_LISTA_ID'];

    $delete = oci_parse($conn, "
        DELETE FROM LEJATSZASI_LISTA_VIDEO
        WHERE LEJATSZASI_LISTA_ID = :playlistId AND VIDEO_ID = :videoId
    ");
    oci_bind_by_name($delete, ":playlistId", $playlistId);
    oci_bind_by_name($delete, ":videoId", $videoId);
    oci_execute($delete);
    oci_free_statement($delete);
}

function searchVideos($conn, $category = '', $sort = 'latest') {
    $videos = [];

    $sql = "
        SELECT v.VIDEO_ID AS id, v.CIM AS title
        FROM VIDEO v
        LEFT JOIN VIDEO_KATEGORIA vk ON v.VIDEO_ID = vk.VIDEO_ID
    ";

    $conditions = [];
    if (!empty($category)) {
        $conditions[] = "LOWER(vk.KATEGORIA_NEV) = LOWER(:category)";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY v.VIDEO_ID, v.CIM, v.DATUM, v.NEZETTSEG ";

    switch ($sort) {
        case 'oldest':
            $sql .= " ORDER BY v.DATUM ASC";
            break;
        case 'most_viewed':
            $sql .= " ORDER BY v.NEZETTSEG DESC";
            break;
        case 'least_viewed':
            $sql .= " ORDER BY v.NEZETTSEG ASC";
            break;
        default:
            $sql .= " ORDER BY v.DATUM DESC";
    }

    $stmt = oci_parse($conn, $sql);

    if (!empty($category)) {
        oci_bind_by_name($stmt, ":category", $category);
    }

    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $videos[] = $row;
    }

    oci_free_statement($stmt);

    return $videos;
}

function getAvailableCategories($conn) {
    $categories = [];
    $sql = "SELECT DISTINCT LOWER(KATEGORIA_NEV) AS tag FROM VIDEO_KATEGORIA ORDER BY tag";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) {
        $categories[] = $row['TAG'];
    }
    oci_free_statement($stmt);
    return $categories;
}

function getPopularityStats($conn, $email) {
    $videos = [];

    $sql = "
        SELECT
            v.VIDEO_ID,
            v.CIM,
            v.NEZETTSEG +
            NVL(vr.REAKCIOK_SZAMA, 0) +
            NVL(h.HOZZASZOLASOK_SZAMA, 0) AS FELKAPOTTSAG
        FROM VIDEO v
        LEFT JOIN (
            SELECT VIDEO_ID, COUNT(*) AS REAKCIOK_SZAMA
            FROM VIDEO_REAKCIO
            GROUP BY VIDEO_ID
        ) vr ON v.VIDEO_ID = vr.VIDEO_ID
        LEFT JOIN (
            SELECT VIDEO_ID, COUNT(*) AS HOZZASZOLASOK_SZAMA
            FROM HOZZASZOLAS
            GROUP BY VIDEO_ID
        ) h ON v.VIDEO_ID = h.VIDEO_ID
        WHERE v.FELHASZNALO_EMAIL = :email
        ORDER BY FELKAPOTTSAG DESC
    ";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $videos[] = $row;
    }

    oci_free_statement($stmt);
    return $videos;
}

function registerUser($conn, $username, $email, $birthdate, $password) {
    $message = '';

    $felhasznalonev = htmlspecialchars(trim($username ?? ''), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($email ?? ''), FILTER_SANITIZE_EMAIL);
    $szuletesi_datum = trim($birthdate ?? '');
    $jelszo = trim($password ?? '');

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

    return $message;
}

function loginUser($conn, $emailInput, $passwordInput) {
    $message = '';

    $email = filter_var(trim($emailInput ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($passwordInput ?? '');

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

    return $message;
}
?>
