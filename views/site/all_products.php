<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Product[] $products */

$this->title = 'Svi Proizvodi';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-products py-5">
    <div class="container">

        <!-- Naslov i uvodni tekst -->
        <div class="text-center mb-4">
            <h1 class="display-4 fw-bold text-body mb-3"><?= Html::encode($this->title) ?></h1>
            <p class="lead text-secondary mx-auto mb-4" style="max-width: 600px;">
                Pregledajte našu kompletnu ponudu vrhunskih proizvoda. Pronađite idealno rješenje za sebe.
            </p>
        </div>

        <!-- SEKTOR ZA PRETRAŽIVANJE I FILTRIRANJE -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-body-tertiary">
                    <?= Html::beginForm(['site/all-products'], 'get', ['class' => 'row g-3 align-items-center']) ?>

                    <!-- Tekstualni unos -->
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-secondary">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search"
                                class="form-control border-start-0 ps-0 form-control-lg rounded-end-3"
                                placeholder="Pretraži po nazivu ili opisu..."
                                value="<?= Html::encode(Yii::$app->request->get('search')) ?>">
                        </div>
                    </div>

                    <!-- Filter: Dostupnost -->
                    <div class="col-sm-6 col-md-3">
                        <select name="stock" class="form-select form-control-lg">
                            <option value=""><?= Yii::t('app', 'Sva zaliha') ?></option>
                            <option value="instock"
                                <?= Yii::$app->request->get('stock') === 'instock' ? 'selected' : '' ?>>
                                <?= Yii::t('app', 'Dostupno odmah') ?>
                            </option>
                            <option value="outstock"
                                <?= Yii::$app->request->get('stock') === 'outstock' ? 'selected' : '' ?>>
                                <?= Yii::t('app', 'Rasprodano') ?>
                            </option>
                        </select>
                    </div>

                    <!-- Filter: Sortiranje -->
                    <div class="col-sm-6 col-md-2">
                        <select name="sort" class="form-select form-control-lg">
                            <option value=""><?= Yii::t('app', 'Sortiraj') ?></option>
                            <option value="price_asc"
                                <?= Yii::$app->request->get('sort') === 'price_asc' ? 'selected' : '' ?>>
                                <?= Yii::t('app', 'Cijena: Manja prva') ?>
                            </option>
                            <option value="price_desc"
                                <?= Yii::$app->request->get('sort') === 'price_desc' ? 'selected' : '' ?>>
                                <?= Yii::t('app', 'Cijena: Veća prva') ?>
                            </option>
                        </select>
                    </div>

                    <!-- Gumbi za akciju -->
                    <div class="col-md-2 d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary btn-lg px-4 rounded-3 w-100 fw-bold">
                            <?= Yii::t('app', 'Traži') ?>
                        </button>
                        <?php if (Yii::$app->request->get('search') || Yii::$app->request->get('stock') || Yii::$app->request->get('sort')): ?>
                            <a href="<?= Url::to(['site/all-products']) ?>"
                                class="btn btn-outline-secondary btn-lg rounded-3 text-nowrap" title="Poništi filtre">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>

        <!-- Mreža s proizvodima (Grid) -->
        <div class="row g-4">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div
                            class="card h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 extension-card overflow-hidden bg-body-tertiary transition-card">

                            <!-- Dohvaćanje glavne slike iz relacije -->
                            <?php
                            $mainImage = null;
                            if (!empty($product->images)) {
                                foreach ($product->images as $img) {
                                    if ($img->is_main == 1) {
                                        $mainImage = $img;
                                        break;
                                    }
                                }
                                if ($mainImage === null && isset($product->images)) {
                                    $mainImage = $product->images;
                                }
                            }
                            $imagePath = $mainImage !== null ? Yii::getAlias('@web/') . $mainImage->path : Yii::getAlias('@web/images/no-image.jpg');
                            ?>

                            <!-- Slika proizvoda kao poveznica -->
                            <a href="<?= Url::to(['site/product-view', 'id' => $product->id]) ?>"
                                class="d-block overflow-hidden position-relative ratio ratio-4x3 card-img-hover">
                                <img src="<?= Html::encode($imagePath) ?>" class="card-img-top object-fit-cover"
                                    alt="<?= Html::encode($product->name) ?>">

                                <!-- Sivi overlay i tekst preko CIJELE slike ako je rasprodano -->
                                <?php if (isset($product->stock) && $product->stock <= 0): ?>
                                    <div class="sold-out-overlay">
                                        <span
                                            class="badge bg-dark bg-opacity-80 text-white px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wider shadow-sm">
                                            <?= Yii::t('app', 'Rasprodano') ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </a>

                            <!-- Sadržaj kartice -->
                            <div class="card-body d-flex flex-column justify-content-between p-3.5">
                                <div>
                                    <!-- Naslov -->
                                    <h3 class="h6 fw-bold mb-2 text-body line-clamp-1">
                                        <a href="<?= Url::to(['site/product-view', 'id' => $product->id]) ?>"
                                            class="text-decoration-none text-body-hover">
                                            <?= Html::encode($product->name) ?>
                                        </a>
                                    </h3>

                                    <!-- Kratki opis -->
                                    <div class="text-secondary small mb-3 line-clamp-2 leading-relaxed">
                                        <?= yii\helpers\HtmlPurifier::process($product->description) ?>
                                    </div>
                                </div>

                                <div>
                                    <!-- Cijena i zaliha -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-extrabold text-primary fs-5">
                                            <?php if (isset($product->price)): ?>
                                                <?= number_format((float)$product->price, 2, ',', '.') ?> <span
                                                    class="fs-6 fw-normal text-secondary">EUR</span>
                                            <?php else: ?>
                                                <span class="fs-6 text-secondary fw-normal">Na upit</span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (isset($product->stock) && $product->stock > 0): ?>
                                            <small class="text-success fw-semibold">✔ Dostupno</small>
                                        <?php elseif (isset($product->stock)): ?>
                                            <small class="text-danger fw-semibold">✖ Nedostupno</small>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Gumb za pregled -->
                                    <?= Html::a(
                                        'Pogledaj proizvod &raquo;',
                                        ['site/product-view', 'id' => $product->id],
                                        [
                                            'class' => 'btn btn-sm btn-outline-primary w-100 rounded-3 fw-semibold py-2 transition-button',
                                        ],
                                    ) ?>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Poruka ako nema proizvoda -->
                <div class="col-12 text-center py-5">
                    <div class="fs-3 text-secondary mb-2">📭 Nema pronađenih proizvoda</div>
                    <p class="text-muted">Pokušajte promijeniti pojam pretraživanja ili očistiti filtre.</p>
                    <a href="<?= Url::to(['site/all-products']) ?>" class="btn btn-primary rounded-3 mt-2">Prikaži sve
                        proizvode</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php
// Dodatni CSS za efekte, blagi sivi sloj i podršku za teme
$css = <<<CSS
.fw-extrabold { font-weight: 800; }
.transition-card {
    transition: transform 0.25s ease-in-out, box-shadow 0.25s ease-in-out, border-color 0.25s ease-in-out;
}
.transition-card:hover {
Pripazite na kôd.
transform: translateY(-5px);
box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
border-color: rgba(13, 110, 253, 0.25) !important;
}
.card-img-hover img {
transition: transform 0.3s ease-in-out;
}
.card-img-hover:hover img {
transform: scale(1.06);
}
/* NOVO: Stil za blagi sivi overlay preko cijele slike proizvoda /
.sold-out-overlay {
position: absolute;
top: 0;
left: 0;
width: 100%;
height: 100%;
background-color: rgba(108, 117, 125, 0.45); / Blaga siva boja sa 45% vidljivosti /
display: flex;
align-items: center;
justify-content: center;
backdrop-filter: blur(1px); / Dodaje vrlo blago estetsko zamućenje */
z-index: 2;
transition: background-color 0.3s ease;
}
/* Zadržava efekt i kada se mišem prijeđe preko rasprodanog artikla */
.card-img-hover:hover .sold-out-overlay {
background-color: rgba(108, 117, 125, 0.55);
}
/* Kraćenje teksta na određeni broj redova (Clamp) /
.line-clamp-1 {
display: -webkit-box;
-webkit-line-clamp: 1;
-webkit-box-orient: vertical;
overflow: hidden;
}
.line-clamp-2 {
display: -webkit-box;
-webkit-line-clamp: 2;
-webkit-box-orient: vertical;
overflow: hidden;
}
.text-body-hover {
transition: color 0.2s ease;
}
.text-body-hover:hover {
color: #0d6efd !important;
}
/ Prilagodba rubova za noćni način rada */
[data-bs-theme="dark"] .transition-card {
border-color: rgba(255, 255, 255, 0.1) !important;
}
[data-bs-theme="dark"] .transition-card:hover {
border-color: rgba(13, 110, 253, 0.4) !important;
}
CSS;
$this->registerCss($css);
?>