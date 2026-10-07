<?php

/** @var yii\web\View $this */
/** @var app\models\Post|null $model */
/** @var app\models\Product[] $products */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Yii Application';
$this->params['meta_description'] = 'A high-performance PHP framework best for developing web applications. Fast, secure, and professional.';
$this->params['meta_keywords'] = 'yii, yii2, php, framework, web application, high-performance';

// 1. Logika za sigurno dohvaćanje glavne slike iz Post modela ($model)
$backgroundImageUrl = null;
if (!empty($model) && !empty($model->images)) {
    foreach ($model->images as $img) {
        if ($img->is_main == 1) {
            $backgroundImageUrl = Yii::getAlias('@web/') . $img->path;
            break;
        }
    }
    // Fallback: Ako nijedna slika nije označena kao glavna, uzmi prvu dostupnu
    if (!$backgroundImageUrl && isset($model->images[0])) {
        $backgroundImageUrl = Yii::getAlias('@web/') . $model->images[0]->path;
    }
}

// 2. Kreiranje dinamičkog stila za Hero Banner (Tamni gradijent overlay preko slike ili fiksni gradijent)
$bannerStyle = $backgroundImageUrl
    ? "background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.7)), url('{$backgroundImageUrl}') center center / cover no-repeat;"
    : "background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);";
?>
<div class="site-index">

    <!-- Hero banner s dinamičkom slikom iz baze ili zamjenskim gradijentom -->
    <div class="hero-banner text-white rounded-4 p-5 mb-5 position-relative overflow-hidden"
        style="<?= $bannerStyle ?> min-height: 350px; display: flex; align-items: center;">

        <div class="position-relative w-100">
            <!-- Prikaz kategorije posta ako je definirana -->
            <?php if (!empty($model->category)): ?>
                <span class="badge bg-light text-dark mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase"
                    style="font-size: 10px; letter-spacing: 0.5px;">
                    <?= Html::encode($model->category->name) ?>
                </span>
            <?php endif; ?>

            <h1 class="display-4 fw-bold mb-3" style="text-shadow: 0 2px 12px rgba(0,0,0,0.4); max-width: 850px;">
                <?= Html::encode($model->title ?? 'Dobrodošli u Yii aplikaciju') ?>
            </h1>

            <p class="lead opacity-90 mb-4 hero-lead" style="text-shadow: 0 1px 6px rgba(0,0,0,0.4); max-width: 750px;">
                <!-- Čišćenje Summernote HTML tagova i skraćivanje teksta na sigurnu duljinu -->
                <?= !empty($model->content) ? yii\helpers\StringHelper::truncate(strip_tags($model->content), 240, '...') : 'Istražite naše najnovije proizvode i funkcionalnosti.' ?>
            </p>

            <div class="d-flex gap-2 flex-wrap">
                <?php if (!empty($model)): ?>
                    <?= Html::a(
                        'Pročitaj članak &raquo;',
                        ['post/view', 'id' => $model->id],
                        ['class' => 'btn btn-light btn-lg fw-semibold px-4 shadow-sm']
                    ) ?>
                <?php endif; ?>
                <?= Html::a(
                    'Sve objave',
                    ['post/index'],
                    ['class' => 'btn btn-outline-light btn-lg px-4']
                ) ?>
            </div>
        </div>
    </div>

    <!-- Grid proizvoda -->
    <div class="row g-4">
        <?php $products = $products ?? []; ?>
        <?php foreach ($products as $product): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 extension-card overflow-hidden"
                    style="transition: transform 0.2s, box-shadow 0.2s;"
                    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.08)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';">

                    <!-- Dohvaćanje glavne slike proizvoda -->
                    <?php
                    $productImage = null;
                    if (!empty($product->images)) {
                        foreach ($product->images as $img) {
                            if ($img->is_main == 1) {
                                $productImage = $img;
                                break;
                            }
                        }
                        if ($productImage === null && isset($product->images[0])) {
                            $productImage = $product->images[0];
                        }
                    }
                    ?>

                    <!-- Slika proizvoda kao poveznicu -->
                    <a href="<?= Url::to(['site/product-view', 'id' => $product->id]) ?>"
                        style="display: block; overflow: hidden;">
                        <?php if ($productImage !== null): ?>
                            <img src="<?= Yii::getAlias('@web/') . Html::encode($productImage->path) ?>" class="card-img-top"
                                alt="<?= Html::encode($product->name) ?>" style="height: 220px; object-fit: cover;">
                        <?php else: ?>
                            <!-- Zamjenska slika ako proizvod nema priloženih slika -->
                            <img src="<?= Yii::getAlias('@web/images/no-image.jpg') ?>" class="card-img-top"
                                alt="No image available" style="height: 220px; object-fit: cover;">
                        <?php endif; ?>
                    </a>

                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <span class="extension-icon" style="font-size: 1.25rem;">📦</span>
                            <h3 class="h5 fw-bold mb-0 ms-2" style="color: #2c3e50;"><?= Html::encode($product->name) ?>
                            </h3>
                        </div>
                        <div class="text-body-secondary small mb-3 flex-grow-1">
                            <!-- Prikaz pročišćenog HTML sadržaja opisa proizvoda -->
                            <?= yii\helpers\StringHelper::truncate(strip_tags($product->description), 130, '...') ?>
                        </div>
                        <div class="mt-auto">
                            <?= Html::a(
                                'Pogledaj proizvod &raquo;',
                                ['site/product-view', 'id' => $product->id],
                                [
                                    'class' => 'btn btn-sm btn-outline-primary w-100 fw-semibold py-2',
                                ],
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>