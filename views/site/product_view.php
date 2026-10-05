<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Product $product */

$this->title = $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Proizvodi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Pronalaženje glavne slike i ostalih slika iz relacije
$mainImage = null;
$galleryImages = [];

if (!empty($product->images)) {
    foreach ($product->images as $img) {
        if ($img->is_main == 1) {
            $mainImage = $img;
        } else {
            $galleryImages[] = $img;
        }
    }
    if ($mainImage === null && isset($product->images)) {
        $mainImage = $product->images;
        array_shift($galleryImages);
    }
}

$mainImagePath = $mainImage !== null ? Yii::getAlias('@web/') . $mainImage->path : Yii::getAlias('@web/images/no-image.jpg');
?>

<div class="product-view py-5">
    <div class="container">

        <!-- Glavni blok proizvoda -->
        <div class="row g-4 lg-g-5">

            <!-- LIJEVA KOLONA: Slike proizvoda -->
            <div class="col-md-6 col-lg-5">
                <div class="position-sticky" style="top: 2rem;">
                    <!-- Glavna slika - prilagođena za tamni način -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 bg-body-tertiary">
                        <img id="main-product-image" src="<?= Html::encode($mainImagePath) ?>" class="img-fluid w-100"
                            alt="<?= Html::encode(ucfirst($product->name)) ?>"
                            style="max-height: 450px; object-fit: cover;">
                    </div>

                    <!-- Galerija (male sličice) -->
                    <?php if (!empty($galleryImages)): ?>
                        <div class="row g-2">
                            <div class="col-3">
                                <div class="card border border-2 border-primary rounded-3 overflow-hidden cursor-pointer thumbnail-card bg-body-tertiary"
                                    onclick="changeImage('<?= Html::encode($mainImagePath) ?>', this)">
                                    <img src="<?= Html::encode($mainImagePath) ?>" class="img-fluid w-100"
                                        style="height: 70px; object-fit: cover;">
                                </div>
                            </div>
                            <?php foreach ($galleryImages as $img): ?>
                                <?php $thumbPath = Yii::getAlias('@web/') . $img->path; ?>
                                <div class="col-3">
                                    <div class="card border border-secondary border-opacity-25 rounded-3 overflow-hidden cursor-pointer thumbnail-card bg-body-tertiary"
                                        onclick="changeImage('<?= Html::encode($thumbPath) ?>', this)">
                                        <img src="<?= Html::encode($thumbPath) ?>" class="img-fluid w-100"
                                            style="height: 70px; object-fit: cover;">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- DESNA KOLONA: Detalji, cijena i kupovina -->
            <div class="col-md-6 col-lg-7 d-flex flex-column justify-content-between">
                <div>
                    <!-- Značka i Brojač pregleda -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-secondary px-3 py-2 rounded-pill small">Novo</span>
                        <small class="text-secondary d-flex align-items-center">
                            👁️ <span class="ms-1"><?= (int) $product->views_count ?> pregleda</span>
                        </small>
                    </div>

                    <!-- Naslov proizvoda - tekst prati temu -->
                    <h1 class="display-6 fw-bold text-body mb-3"><?= Html::encode(ucfirst($product->name)) ?></h1>

                    <!-- Cijena (Pretpostavljeno polje $product->price) -->
                    <div class="mb-4">
                        <?php if (isset($product->price)): ?>
                            <span
                                class="fs-2 fw-extrabold text-primary"><?= number_format((float)$product->price, 2, ',', '.') ?>
                                EUR</span>
                        <?php else: ?>
                            <span class="fs-4 text-secondary">Cijena na upit</span>
                        <?php endif; ?>
                    </div>

                    <hr class="text-secondary opacity-25 my-4">

                    <!-- Kratke informacije - pozadina se prilagođava -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-sm-4">
                            <!-- Početak IF provjere za skladište -->
                            <?php if (isset($product->stock) && $product->stock > 0): ?>
                                <div
                                    class="p-3 bg-body-tertiary rounded-3 text-center border border-success border-opacity-25">
                                    <small class="text-secondary d-block mb-1">Dostupnost</small>
                                    <span class="fw-semibold text-success">
                                        Na skladištu (<?= (int)$product->stock ?>)
                                    </span>
                                </div>
                            <?php else: ?>
                                <div
                                    class="p-3 bg-body-tertiary rounded-3 text-center border border-danger border-opacity-25">
                                    <small class="text-secondary d-block mb-1">Dostupnost</small>
                                    <span class="fw-semibold text-danger">Rasprodano</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-6 col-sm-4">
                            <div
                                class="p-3 bg-body-tertiary rounded-3 text-center border border-secondary border-opacity-10">
                                <small class="text-secondary d-block mb-1">Dostava</small>
                                <span class="fw-semibold text-body">1-3 radna dana</span>
                            </div>
                        </div>
                    </div>


                    <!-- Akcijski gumbi -->
                    <div class="d-flex flex-wrap gap-2 pt-3 mt-auto">
                        <!-- POČETAK PROVJERE SKLADIŠTA I FORME ZA KOŠARICU -->
                        <?php if (isset($product->stock) && $product->stock > 0): ?>
                            <?php yii\widgets\ActiveForm::begin([
                                // KLJUČNI ISPRAVAK: Dodana je kosa crta (/) ispred 'cart/add' radi apsolutne rute
                                'action' => ['/cart/add'],
                                'method' => 'post',
                                'options' => ['class' => 'flex-grow-1']
                            ]); ?>
                            <!-- Skriveni podaci -->
                            <input type="hidden" name="product_id" value="<?= $product->id ?>">
                            <input type="hidden" name="quantity" value="1">

                            <?= Html::submitButton('🛒 Dodaj u košaricu', [
                                'class' => 'btn btn-primary btn-lg px-5 py-3 w-100 rounded-3 shadow-sm fw-bold'
                            ]) ?>
                            <?php yii\widgets\ActiveForm::end(); ?>
                        <?php else: ?>
                            <!-- Gumb je zaključan (disabled) ako artikla nema na skladištu -->
                            <button class="btn btn-secondary btn-lg px-5 py-3 flex-grow-1 rounded-3 shadow-sm fw-bold"
                                disabled>
                                ❌ Trenutno nedostupno
                            </button>
                        <?php endif; ?>

                        <!-- Gumb za uređivanje - vidljiv samo prijavljenom korisniku -->
                        <?php if (!Yii::$app->user->isGuest): ?>
                            <?= Html::a('✏️ Uredi', ['product/update', 'id' => $product->id], ['class' => 'btn btn-outline-secondary btn-lg px-4 py-3 rounded-3']) ?>
                        <?php endif; ?>
                    </div>


                    <!-- DONJI DIO: Opis -->
                    <div class="row mt-5 pt-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs border-bottom border-secondary border-opacity-25" id="productTab"
                                role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-tab-link active fw-bold text-body border-0 pb-3 bg-transparent position-relative"
                                        id="description-tab" data-bs-toggle="tab" data-bs-target="#description-tab-pane"
                                        type="button" role="tab" aria-selected="true" style="z-index: 1;">
                                        Opis proizvoda
                                    </button>
                                </li>
                            </ul>
                            <!-- bg-body i tekst prilagođeni za tamni/svijetli način -->
                            <div class="tab-content bg-body p-4 rounded-bottom-4 shadow-sm border border-secondary border-opacity-25 border-top-0"
                                id="productTabContent">
                                <div class="tab-pane fade show active text-body-secondary leading-relaxed"
                                    id="description-tab-pane" role="tabpanel" aria-labelledby="description-tab"
                                    tabindex="0">
                                    <?= yii\helpers\HtmlPurifier::process($product->description) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php
            $css = <<<CSS
.cursor-pointer { cursor: pointer; }
.fw-extrabold { font-weight: 800; }
.thumbnail-card { transition: all 0.2s ease-in-out; }
.thumbnail-card:hover { transform: translateY(-2px); border-color: #0d6efd !important; }
.nav-tab-link {
    border-bottom: 3px solid transparent !important;
    margin-right: 1.5rem;
}
.nav-tab-link.active {
    color: #0d6efd !important;
    border-bottom-color: #0d6efd !important;
}
/* Osigurava dobru vidljivost rubova u tamnom načinu rada */
[data-bs-theme="dark"] .card, 
[data-bs-theme="dark"] .tab-content {
    border-color: rgba(255, 255, 255, 0.15) !important;
}
CSS;
            $this->registerCss($css);

            $js = <<<JS
function changeImage(src, element) {
    document.getElementById('main-product-image').src = src;
    document.querySelectorAll('.thumbnail-card').forEach(card => {
        card.classList.remove('border-primary');
        card.classList.add('border-secondary', 'border-opacity-25');
    });
    element.classList.remove('border-secondary', 'border-opacity-25');
    element.classList.add('border-primary');
}
window.changeImage = changeImage;
JS;
            $this->registerJs($js, \yii\web\View::POS_HEAD);
            ?>