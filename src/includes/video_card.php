<?php if (isset($video)): ?>
<a href="index.php?page=video&id=<?= $video['ID'] ?>" class="text-decoration-none text-dark">
    <div class="col">
        <div class="card h-100 shadow-sm border-0">
            <img src="public/images/video_thumbnail.jpg" class="card-img-top mt-2 w-75 h-75 mx-auto rounded" alt="Thumbnail">
            <div class="card-body text-center">
                <h6 class="card-title mb-0"><?= htmlentities($video['TITLE']) ?></h6>
            </div>
        </div>
    </div>
</a>
<?php endif; ?>