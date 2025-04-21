<a href="index.php?page=playlist_videos&id=<?= $playlist['id'] ?>" class="text-decoration-none text-dark">
    <div class="card shadow-sm border-0 text-center bg-light playlist-card h-100">
        <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
            <i class="bi bi-folder-fill display-4 text-primary mb-3"></i>
            <h6 class="card-title mb-1"><?= $playlist['name'] ?></h6>
            <p class="text-muted small mb-0"><?= $playlist['count'] ?> videó</p>
        </div>
    </div>
</a>