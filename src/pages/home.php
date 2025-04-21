<?php
session_start();
?>
<form method="get" class="mt-1 px-3">
    <input type="hidden" name="page" value="home">

    <div class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Keresés...">
        </div>

        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Összes #tag</option>
                <option value="zene">#zene</option>
                <option value="vlog">#vlog</option>
                <option value="oktatas">#oktatás</option>
                <option value="gaming">#gaming</option>
            </select>
        </div>

        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="latest">Legújabb elöl</option>
                <option value="oldest">Legrégebbi elöl</option>
                <option value="most_viewed">Legnézettebb elöl</option>
                <option value="least_viewed">Legkevésbé nézett</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">Keresés</button>
        </div>
    </div>
</form>
<div class="container py-4">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-sm-4 g-4">
<?php
// itt majd egy funkció lesz ami lekérdezi a videókat és az alapján generálja le a kártyákat, plussz szerintem annak
// a funkciónak legyenek szűrésből származó paraméterei is
// (keresés mezőt azt csak hozzáadatm de a dokumentávióba nincs benne szóval elhagyhatjuk)
// ha rákattintunk egy kártyára akkor megnyitja az video oldalt ahol az id-t átadjuk és az alapján töltjuk be a videót

// Dummy videó adatok tömbben
$videos = [
    ['id' => 1, 'title' => 'Frontend alapok'],
    ['id' => 2, 'title' => 'PHP bevezető'],
    ['id' => 3, 'title' => 'Oracle lekérdezések'],
    ['id' => 4, 'title' => 'Bootstrap kártyák'],
    ['id' => 5, 'title' => 'Adatbázis normalizálás'],
];


    foreach ($videos as $video):
        include 'src/includes/video_card.php';
    endforeach;
?>
    </div>
</div>




