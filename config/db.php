<?php
$tns = "
(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    (CONNECT_DATA =
        (SID = orania2)
    )
)";
$db_username = "C##IFQA67";
$db_password = "Kisdoomritahap45";
$conn = oci_connect("C##IFQA67", "Kisdoomritahap45", $tns);
if (!$conn) {
  $e = oci_error();
  echo "<h2 style='color:red;'>Nem sikerült csatlakozni az Oracle adatbázishoz!</h2>";
  echo "<pre>" . htmlentities($e['message']) . "</pre>";
  exit;
} else {
  echo "<h2 style='color:green;'>Sikeres kapcsolat az Oracle adatbázishoz!</h2>";
}
?>