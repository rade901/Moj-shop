<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\helpers\Html;

$this->render('_head');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100" data-bs-theme="light">

<head>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title) ?></title>

    <!-- KONAČNI CSS STILOVI ZA LOADER I PREMIUM GUMB -->
    <style>
        .fullscreen-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(15, 23, 42, 0.85);
            /* Tamna moderna pozadina */
            z-index: 9999;
            /* Iznad svih elemenata na stranici */
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
            /* Efekt zamućenja pozadine */

            /* Glatka CSS animacija pojavljivanja (Fade-in) */
            animation: globalLoaderFadeIn 0.3s ease-out forwards;
        }

        .loader-content {
            color: #fff;
        }

        /* Ključni kadrovi za fade-in animaciju cjelokupnog loadera */
        @keyframes globalLoaderFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* MODERNIZIRANI FIKSIRANI GUMB ZA TESTIRANJE */
        .test-loader-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 9990;
            background: rgba(30, 41, 59, 0.75);
            /* Poluprozirna tamna podloga */
            color: #f8fafc;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 22px;
            border-radius: 50px;
            /* Oblik pilule */
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
            /* Efekt zamućenog stakla */
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Efekt prelaska mišem (Hover) */
        .test-loader-btn:hover {
            background: rgba(30, 41, 59, 0.95);
            color: #fbbf24;
            /* Tekst i ikona postaju zlatno-žuti */
            border-color: rgba(251, 191, 36, 0.4);
            transform: translateY(-3px) scale(1.02);
            /* Blago podizanje i povećanje */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.4);
        }

        /* Efekt pritiska gumba (Active) */
        .test-loader-btn:active {
            transform: translateY(-1px) scale(0.98);
        }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <?= $this->render('_header') ?>

    <main id="main" class="flex-grow-1" role="main">
        <div class="container">
            <?php if (!empty($this->params['breadcrumbs'])): ?>
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
            <?php endif ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <?= $this->render('_footer') ?>

    <!-- GLOBALNI SPINNER -->
    <div id="global-html-loader" class="fullscreen-loader" style="display: none;">
        <div class="loader-content text-center">
            <!-- Bootstrap 5 žuti spinner -->
            <div class="spinner-border text-warning" role="status" style="width: 4rem; height: 4rem;"></div>
            <!-- Element za tekstualnu poruku -->
            <h4 id="global-loader-text" class="text-light mt-3 fw-light">Učitavanje...</h4>
        </div>
    </div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>

<!-- INICIJALIZACIJA GLOBALNIH FUNKCIJA I EVENT LISTENERA -->
<?php
$this->registerJs("
    // 1. Funkcije za ručno upravljanje loaderom (dostupne kroz cijeli projekt i podstranice)
    window.pokreniGlobalniLoader = function(poruka = 'Molimo pričekajte...') {
        var loader = document.getElementById('global-html-loader');
        var tekst = document.getElementById('global-loader-text');
        
        if (loader && tekst) {
            tekst.innerText = poruka;
            loader.style.display = 'flex'; // Prikazuje loader preko cijelog ekrana
        }
    };

    window.zaustaviGlobalniLoader = function() {
        var loader = document.getElementById('global-html-loader');
        if (loader) {
            loader.style.display = 'none'; // Skriva loader
        }
    };

    // 2. AUTOMATSKO ZAKLJUČAVANJE I PALJENJE PRI SLANJU BILO KOJE FORME
    document.addEventListener('submit', function(event) {
        if (event.target && event.target.tagName === 'FORM') {
            if (typeof window.pokreniGlobalniLoader === 'function') {
                window.pokreniGlobalniLoader('Obrađujem zahtjev, molimo pričekajte...');
            }
        }
    });
", \yii\web\View::POS_END);
?>