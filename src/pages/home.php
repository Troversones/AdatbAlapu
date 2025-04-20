<form method="get" class="mt-1 px-3 ">
    <input type="hidden" name="page" value="home">

    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Keresés...">
        <button class="btn btn-primary" type="submit">Keresés</button>
    </div>
</form>
<div class="container py-4">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-sm-4 g-4">
<?php
for ($i = 0; $i < 23; $i++) {
    include 'src/includes/video_card.php';
}
?>
    </div>
</div>




