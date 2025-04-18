<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VideoShare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/index.css">

</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="public/js/index.js"></script>

<body class="min-vh-100 d-flex flex-column">
<div>
    <?php
    $page = $_GET['page'] ?? 'landing';
    $hideNavbarPages = ['landing', 'login', 'signup'];

    if (!in_array($page, $hideNavbarPages)) {
        include 'src/includes/nav.php';
    }

    ?>
</div>



<div>
    <?php
    $file = "src/pages/$page.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<p>Az oldal nem található.</p>";
    }
    ?>
</div>

</body>
</html>

