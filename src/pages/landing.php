<?php
session_start();

//if (!isset($_SESSION['username'])) {
//    header("Location: index.php?page=signup");
//    exit;
//}
//
//$username = $_SESSION['username'];
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-8">
            <h1 class="display-4 fw-bold">Fedezz fel és ossz meg videókat korlátok nélkül</h1>
            <p class="lead mt-3 mb-4">
                Tölts fel videókat, kövesd a kedvenceidet, és csatlakozz egy közösséghez, amely a tartalomról szól.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="index.php?page=signup" class="btn btn-success btn-lg">Regisztrálok</a>
                <a href="index.php?page=login" class="btn btn-outline-light btn-lg bg-primary text-white">Bejelentkezés</a>
                <a href="index.php?page=logout" class="btn btn-danger btn-lg">Kijelentkezés</a>
            </div> <br>
            <!--
            A gomb a bejelentkezés tesztelésre
            <div class="alert alert-success text-center my-4" role="alert">
                Üdv újra, <strong><?//= htmlentities($username) ?></strong>!
            </div>
            -->
        </div>
    </div>
</div>


<div class="container py-5">
    <div class="row text-center">
        <div class="col-md-4">
            <h4 class="fw-semibold">📹 Könnyű feltöltés</h4>
            <p>Egyszerűen tölthetsz fel videókat néhány kattintással.</p>
        </div>
        <div class="col-md-4">
            <h4 class="fw-semibold">🎯 Célzott keresés</h4>
            <p>Intelligens keresés, kategóriák és címkék alapján.</p>
        </div>
        <div class="col-md-4">
            <h4 class="fw-semibold">💬 Közösségi élmény</h4>
            <p>Lájkok, kommentek és követés funkció – építs közönséget!</p>
        </div>
    </div>
</div>