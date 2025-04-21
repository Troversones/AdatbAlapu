
<!-- ezek az elem kattinhatók lesznek és a saját id-juk alapján fogja majd betölteni a vido.php-t
ott lehet majd megnézni a vdiók és ott lesz a többi adata is -->
<a href="index.php?page=video&id=<?= $video['id'] ?>" class="text-decoration-none text-dark">
<div class="col">
    <div class="card  h-100 shadow-sm border-0" >
        <img src="public/images/video_thumbnail.jpg" class="card-img-top mt-2 w-75 h-75 mx-auto rounded" alt="Thumbnail">
        <div class="card-body text-center">
            <h6 class="card-title mb-0"><?=$video['title'] ?></h6>
        </div>
    </div>
</div>
</a>
