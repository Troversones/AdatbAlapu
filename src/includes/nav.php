<div>
<nav class="navbar navbar-expand-sm navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold " href="index.php?page=home">VideoShare</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Navigáció megnyitása">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav gap-2">
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo ($_GET['page'] ?? 'home') === 'home' ? 'active fw-semibold' : ''; ?>" href="index.php?page=home">Főoldal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo ($_GET['page'] ?? '') === 'profile' ? 'active fw-semibold' : ''; ?>" href="index.php?page=profile">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo ($_GET['page'] ?? '') === 'playlists' ? 'active fw-semibold' : ''; ?>" href="index.php?page=playlists">Lejátszási listák</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo ($_GET['page'] ?? '') === 'subscriptions' ? 'active fw-semibold' : ''; ?>" href="index.php?page=subscriptions">Feliratkozások</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo ($_GET['page'] ?? '') === 'leaderboard' ? 'active fw-semibold' : ''; ?>" href="index.php?page=leaderboard">Ranglista</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo ($_GET['page'] ?? '') === 'my_videos' ? 'active fw-semibold' : ''; ?>" href="index.php?page=my_videos">Videóim</a>
                </li>

                <!--  és így tovább a többi oldallal  -->
            </ul>
        </div>
    </div>
</nav>
</div>