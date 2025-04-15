<div>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-uppercase" href="index.php?page=home">MySite</a>
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
                <!--  és így tovább a többi oldallal  -->
            </ul>
        </div>
    </div>
</nav>
</div>