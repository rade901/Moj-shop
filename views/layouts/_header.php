<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

// 1. DOHVAĆANJE I ZBRAJANJE ARTIKALA IZ KOŠARICE (SESIJE)
$session = Yii::$app->session;
$cart = $session->get('cart', []);
$cartCount = 0;

if (!empty($cart)) {
    foreach ($cart as $quantity) {
        $cartCount += (int)$quantity;
    }
}

// Kreiranje oznake za košaricu (ako ima artikala, prikazuje se brojčani badge)
$cartLabel = '🛒 Košarica' . ($cartCount > 0 ? ' <span class="badge bg-primary rounded-pill ms-1">' . $cartCount . '</span>' : '');

// 2. PODJELA IZBORNIKA NA LIJEVU I DESNU STRANU
$leftItems = [];  // Linkovi koji idu lijevo
$rightItems = []; // Linkovi koji idu desno (Prijava, Odjava, Košarica)

if (!Yii::$app->user->isGuest) {
    // Stavke za prijavljene korisnike - LIJEVO
    $leftItems = [
        ['label' => 'Početna', 'url' => ['/site/index']],
        ['label' => 'Proizvodi', 'url' => ['/product/index']],
        ['label' => 'Postovi', 'url' => ['/post/index']],
        ['label' => 'Kategorije', 'url' => ['/category/index']],
        ['label' => 'Narudžbe', 'url' => ['/order-table/index']],
    ];

    // Stavke za prijavljene korisnike - DESNO
    $rightItems = [
        ['label' => $cartLabel, 'url' => ['/cart/index']],
        [
            'label' => 'Odjava (' . Html::encode(Yii::$app->user->identity?->username ?? '') . ')',
            'url' => ['/site/logout'],
            'linkOptions' => [
                'data-method' => 'post',
                'class' => 'nav-link logout',
            ],
        ],
    ];
} else {
    // Stavke za goste - LIJEVO
    $leftItems = [
        ['label' => 'Početna', 'url' => ['/site/index']],
        ['label' => 'Proizvodi', 'url' => ['/site/all-products']],
        ['label' => 'Objave', 'url' => ['/site/all-posts']],
        ['label' => 'O nama', 'url' => ['/site/about']],
        ['label' => 'Kontakt', 'url' => ['/site/contact']],
    ];

    // Stavke za goste - DESNO
    $rightItems = [
        ['label' => $cartLabel, 'url' => ['/cart/index']],
        ['label' => 'Prijava', 'url' => ['/site/login']],
    ];
}

?>
<header id="header">
    <?php NavBar::begin([
        'brandUrl' => Yii::$app->homeUrl,
        // ISPRAVLJENO: Maknut dupli brandLabel ključ, ostavljen samo onaj sa slikom lazo.png
        'brandLabel' => Html::img(Yii::$app->request->baseUrl . '/images/lazo.png', [
            'alt' => Yii::$app->name,
            'style' => 'height: 50px; display: inline-block; vertical-align: middle; filter: invert(1) contrast(1.2);'
        ]),
        'options' => ['class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top']
    ]) ?>

    <!-- WIDGET 1: Gura elemente na LIJEVU stranu (me-auto) -->
    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto'],
        'encodeLabels' => false,
        'items' => $leftItems,
    ]) ?>

    <!-- WIDGET 2: Gura elemente na DESNU stranu (ms-auto) zajedno s Prijava/Odjava -->
    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto align-items-center'],
        'encodeLabels' => false,
        'items' => $rightItems,
    ]) ?>

    <!-- GUMB ZA TEMU: Nalazi se na skroz desnoj strani, odmah pored zadnjeg linka -->
    <div class="d-flex align-items-center ms-2">
        <?= Html::button(
            '🌙',
            [
                'id' => 'theme-toggle',
                'class' => 'btn btn-link nav-link fs-5 p-0',
                'aria-label' => 'Switch to dark mode',
                'style' => 'line-height: 1;'
            ],
        ) ?>
    </div>

    <?php NavBar::end() ?>
</header>