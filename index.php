<?php
require("config/db.php");

echo "<h1>Video sharing website - database connection test kekw</h1>";

$sql = "SELECT EMAIL FROM FELHASZNALO";

// parse query
$stid = oci_parse($conn, $sql);

// query futtatás
oci_execute($stid);

echo "<h3>Felhasználók listája:</h3>";
echo "<ul>";
while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
    foreach ($row as $item) {
        echo "<li>" . htmlspecialchars($item, ENT_QUOTES | ENT_SUBSTITUTE) . "</li>";
    }
}
echo "</ul>";

// erőforrás felszabadítás
oci_free_statement($stid);
oci_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    
</body>

</html>