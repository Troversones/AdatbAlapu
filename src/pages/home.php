<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php?page=login");
    exit;
}
require_once __DIR__ . '/../config/db.php';
include 'src/includes/functions.php';
$fromPage = 'home';
$videos = getRecentVideos($conn, 20);

$selectedCategory = $_GET['category'] ?? '';
$selectedSort = $_GET['sort'] ?? 'latest';
$videos = searchVideos($conn, $selectedCategory, $selectedSort);

$categories = getAvailableCategories($conn);
?>
<form method="get" class="mt-1 px-3">
    <input type="hidden" name="page" value="home">

    <div class="row g-2 justify-content-center">


        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Összes #tag</option>
                <?php
                $categories = getAvailableCategories($conn);
                foreach ($categories as $cat) {
                    $selected = $selectedCategory === $cat ? 'selected' : '';
                    echo "<option value=\"$cat\" $selected>#$cat</option>";
                }
                ?>
            </select>
        </div>


            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="latest" <?= $selectedSort === 'latest' ? 'selected' : '' ?>>Legújabb</option>
                    <option value="oldest" <?= $selectedSort === 'oldest' ? 'selected' : '' ?>>Legrégebbi</option>
                    <option value="most_viewed" <?= $selectedSort === 'most_viewed' ? 'selected' : '' ?>>Legnézettebb</option>
                    <option value="least_viewed" <?= $selectedSort === 'least_viewed' ? 'selected' : '' ?>>Legkevésbé nézettebb</option>
                </select>
            </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">Keresés</button>
        </div>
    </div>
</form>
<div class="container py-4">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($videos as $video):
            include 'src/includes/video_card.php';
        endforeach; ?>
    </div>
</div>