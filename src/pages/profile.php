
<!-- a felhasználói adatoknál szerintem csak a felhasználónév legyen változtható-->

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Profil adatok</h2>

    <form>
        <div class="mb-3">
            <label for="username" class="form-label">Felhasználónév</label>
            <input type="text" class="form-control" id="username" name="username" value="peldauser">
        </div>

        <div class="mb-3">
            <label class="form-label">Email cím</label>
            <input type="text" class="form-control" value="pelda@email.com" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Születési dátum</label>
            <input type="text" class="form-control" value="2000-01-01" readonly>
        </div>

        <div class="d-flex justify-content-between">
            <div>
                <button type="submit" class="btn btn-primary me-2">Mentés</button>
                <a href="index.php?page=logout" class="btn btn-outline-danger">Kijelentkezés</a>
            </div>
            <a href="index.php?page=delete_account" class="btn btn-danger">Fiók törlése</a>
        </div>
    </form>
</div>

<?php
