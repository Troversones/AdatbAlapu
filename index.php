<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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

