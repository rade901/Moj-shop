<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url; // Dodano kako bismo lakše generirali URL-ove ako zatreba

$this->title = 'My Yii Application';
$this->params['meta_description'] = 'A high-performance PHP framework best for developing web applications. Fast, secure, and professional.';
$this->params['meta_keywords'] = 'yii, yii2, php, framework, web application, high-performance';
?>
<div class="site-index">

    <!-- Hero banner with Yii gradient -->
    <div class="hero-banner text-white rounded-4 p-5 mb-4 position-relative overflow-hidden">
        <?= Html::img(Yii::getAlias('@web/images/yii3_full_white_for_dark.svg'), [
            'alt' => '',
            'class' => 'd-none d-lg-block position-absolute hero-logo',
        ]) ?>
        <div class="position-relative">
            <h1 class="display-5 fw-bold mb-3"><?= Html::encode($model->title) ?></h1>
            <p class="lead opacity-75 mb-4 hero-lead">
                <?= Html::encode($model->content) ?>
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <?= Html::a(
                    'Get Started',
                    'https://www.yiiframework.com/doc/guide/2.0/en/start-installation',
                    [
                        'class' => 'btn btn-light btn-lg fw-semibold px-4',
                        'rel' => 'noopener',
                        'target' => '_blank',
                    ],
                ) ?>
                <?= Html::a(
                    'API Reference',
                    'https://www.yiiframework.com/doc/api/2.0',
                    [
                        'class' => 'btn btn-outline-light btn-lg px-4',
                        'rel' => 'noopener',
                        'target' => '_blank',
                    ],
                ) ?>
            </div>
        </div>
    </div>

<!-- Extensions grid -->
<div class="row g-3">
    <?php foreach ($products as $product): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card overflow-hidden">
                
                <!-- DOHVAĆANJE SLIKE IZ POVEZANE TABLICE -->
                <?php 
                $mainImage = null;
                if (!empty($product->images)) {
                    foreach ($product->images as $img) {
                        if ($img->is_main == 1) {
                            $mainImage = $img;
                            break;
                        }
                    }
                    if ($mainImage === null && isset($product->images[0])) {
                        $mainImage = $product->images[0];
                    }
                }
                ?>

                <!-- Klik na sliku također vodi na pregled proizvoda -->
                <a href="<?= Url::to(['site/product-view', 'id' => $product->id]) ?>">
                    <?php if ($mainImage !== null): ?>
                        <img src="<?= Yii::getAlias('@web/') . Html::encode($mainImage->path) ?>" 
                             class="card-img-top" 
                             alt="<?= Html::encode($product->name) ?>"
                             style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <!-- Zamjenska slika ako proizvod nema niti jednu sliku -->
                        <img src="<?= Yii::getAlias('@web/images/no-image.jpg') ?>" 
                             class="card-img-top" 
                             alt="No image available"
                             style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                </a>

                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon" aria-hidden="true">🔎</span>
                        <h3 class="h6 fw-bold mb-0 ms-2"><?= Html::encode($product->name) ?></h3>
                    </div>
                    <div class="text-body-secondary small mb-0">
                        <?= yii\helpers\HtmlPurifier::process($product->description) ?>
                    </div>
                </div>
                
                <div class="card-footer bg-transparent border-0 pt-0">
                    <!-- PROMIJENJENO: Dinamička poveznica koja vodi na ProductController actionView -->
                    <?= Html::a(
                        'Pogledaj proizvod &raquo;',
                        ['site/product-view', 'id' => $product->id],
                        [
                            'class' => 'btn btn-sm btn-outline-primary w-100 mb-2', // Malo uočljiviji gumb preko cijele širine
                        ],
                    ) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
