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

// Check if the user is logged in and set the navigation items accordingly
if (!Yii::$app->user->isGuest) {
    $items = [
        [
            'label' => 'Početna',
            'url' => ['/site/index'],
        ],
        [
            'label' => 'Proizvodi',
            'url' => ['/product/index'],
        ],
        [
            'label' => 'Postovi',
            'url' => ['/post/index'],
        ],
        [
            'label' => 'Kategorije',
            'url' => ['/category/index'],
        ],
        [
            'label' => 'Narudžbe',
            'url' => ['/order-table/index'],
        ],
        // DODANO: Košarica za prijavljene korisnike
        [
            'label' => $cartLabel,
            'url' => ['/cart/index'],
        ],
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
    $items = [
        [
            'label' => 'Početna',
            'url' => ['/site/index'],
        ],
        [
            'label' => 'Proizvodi',
            'url' => ['/site/all-products'],
        ],
        [
            'label' => 'Objave',
            'url' => ['/site/all-posts'],
        ],
        [
            'label' => 'O nama',
            'url' => ['/site/about'],
        ],
        [
            'label' => 'Kontakt',
            'url' => ['/site/contact'],
        ],
        // DODANO: Košarica za goste
        [
            'label' => $cartLabel,
            'url' => ['/cart/index'],
        ],
        [
            'label' => 'Prijava',
            'url' => ['/site/login'],
        ],
    ];
}

?>
<header id="header">
    <?php NavBar::begin(
        [
            'brandLabel' => Yii::$app->name,
            'brandLabel' => Html::img(Yii::$app->request->baseUrl . '/images/lazo.png', [
                'alt' => Yii::$app->name,
                'style' => 'height: 40px; display: inline-block; vertical-align: middle;'
            ]),
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false, // OVO OMOGUĆUJE RAD BADGE-a (HTML oznaka) U IZBORNIKU
            'items' => $items,
        ],
    ) ?>
    <?= Html::button(
        '🌙',
        [
            'id' => 'theme-toggle',
            'class' => 'btn btn-link nav-link fs-5',
            'aria-label' => 'Switch to dark mode',
        ],
    )
    ?>
    <?php NavBar::end() ?>
</header>