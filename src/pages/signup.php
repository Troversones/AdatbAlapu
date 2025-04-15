<div class=" min-vh-100 d-flex justify-content-center align-items-center bg-light px-3">
    <div class="card p-4 shadow w-100" style="max-width: 480px;">
        <h4 class="text-center mb-4">Regisztráció</h4>
        <form action="#" method="post" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Felhasználónév</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="pl. nagyjani" >
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email cím</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="valami@email.com" >
            </div>
            <div class="mb-3">
                <label for="birthdate" class="form-label">Születési dátum</label>
                <input type="date" class="form-control" id="birthdate" name="birthdate" >
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Jelszó</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="********" >
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Regisztráció</button>
        </form>
        <div class="text-center mt-3">
            <small>Van már fiókod? <a href="index.php?page=login">Jelentkezz be</a></small>
        </div>
    </div>
</div>
