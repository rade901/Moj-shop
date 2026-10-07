<?php

/** @var yii\web\View $this */
/** @var app\models\Post $post */

use yii\helpers\Html;

// Mapiranje varijable iz SiteController-a


$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Objave'), 'url' => ['all-posts']];
$this->params['breadcrumbs'][] = $this->title;

// Logika za dohvaćanje glavne/naslovne slike
$mainImageUrl = null;
if (!empty($model->images)) {
    foreach ($model->images as $img) {
        if ($img->is_main == 1) {
            $mainImageUrl = Yii::$app->request->baseUrl . '/' . $img->path;
            break;
        }
    }
    if (!$mainImageUrl && isset($model->images)) {
        $mainImageUrl = Yii::$app->request->baseUrl . '/' . $model->images->path;
    }
}
?>

<div class="site-post-view-container"
    style="max-width: 1200px; margin: 0 auto; padding: 40px 20px 80px 20px; box-sizing: border-box;">

    <!-- GLAVNI DVOSTUPČANI RASPODJED (Flex s automatskim prebacivanjem na mobitelima) -->
    <div class="post-split-container" style="
        display: flex; 
        flex-direction: row; 
        gap: 50px; 
        align-items: flex-start;
    ">

        <!-- LIJEVA STRANA: TEKST I SADRŽAJ ČLANKA (60% širine) -->
        <div class="post-text-side" style="flex: 1.5; min-width: 0; width: 100%;">

            <!-- Kategorija članka -->
            <?php if (!empty($model->category)): ?>
                <span class="badge mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase"
                    style="font-size: 11px; letter-spacing: 1px; background-color: #34495e; color: #ffffff; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    <?= Html::encode($model->category->name) ?>
                </span>
            <?php else: ?>
                <span class="badge mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase"
                    style="font-size: 11px; letter-spacing: 1px; background-color: #0d6efd; color: #ffffff; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    <?= Yii::t('app', 'Istaknuto') ?>
                </span>
            <?php endif; ?>

            <!-- Naslov članka -->
            <h1 class="display-5 fw-bold mb-3 hero-post-title"
                style="color: #ffffff !important; font-weight: 800; letter-spacing: -1.5px; line-height: 1.25; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                <?= Html::encode($this->title) ?>
            </h1>

            <!-- Metapodaci -->
            <div class="d-flex align-items-center gap-3 small mb-4 post-metadata"
                style="border-bottom: 1px solid #34495e; padding-bottom: 18px; color: #b2bec3 !important;">
                <span class="d-flex align-items-center gap-2">
                    <span>📅</span>
                    <span style="color: #cbd5e1;"><?= Yii::$app->formatter->asDate($model->created_at, 'long') ?></span>
                </span>
                <span style="color: #475569;">•</span>
                <span class="d-flex align-items-center gap-2">
                    <span>⏱️</span>
                    <span style="color: #cbd5e1;"><?= ceil(str_word_count(strip_tags($model->content)) / 200) ?> min
                        čitanja</span>
                </span>
            </div>

            <!-- Tekst članka -->
            <div class="entry-content" style="
                font-size: 1.15rem; 
                line-height: 1.9; 
                color: #e2e8f0 !important;
                letter-spacing: -0.1px;
                width: 100%;
                box-sizing: border-box;
            ">
                <?= $model->content ?>
            </div>
        </div>

        <!-- DESNA STRANA: VELIKA SLIKA ČLANKA (Uklonjen padding i fiksni omjeri) -->
        <div class="post-image-side" style="flex: 1; position: sticky; top: 40px; width: 100%;">
            <?php if ($mainImageUrl): ?>
                <div class="main-image-card" style="
                    position: relative;
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
                    border: 1px solid #2d3748;
                    background: #1a202c;
                    width: 100%;
                ">
                    <img src="<?= $mainImageUrl ?>" alt="<?= Html::encode($model->title) ?>"
                        style="width: 100%; height: auto; display: block; transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);"
                        onmouseover="this.style.transform='scale(1.02)';" onmouseout="this.style.transform='scale(1)';">
                </div>
            <?php else: ?>
                <div style="
                    aspect-ratio: 1 / 1; 
                    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); 
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                "></div>
            <?php endif; ?>
        </div>

    </div>

    <!-- 3. DONJI DIO: GALERIJA SVEKOLIKIH FOTOGRAFIJA -->
    <?php if (!empty($model->images)): ?>
        <div class="post-gallery-section" style="margin-top: 80px; padding-top: 40px; border-top: 1px solid #34495e;">
            <h3 class="mb-4" style="font-size: 1.5rem; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                🖼️ <?= Yii::t('app', 'Sve slike iz ove galerije') ?>
            </h3>

            <div class="gallery-grid" style="
                display: grid; 
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); 
                gap: 20px;
                width: 100%;
            ">
                <?php foreach ($model->images as $image): ?>
                    <div class="gallery-card" style="
                        position: relative;
                        background: #1a202c;
                        border-radius: 14px;
                        overflow: hidden;
                        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                        border: 1px solid #2d3748;
                    "
                        onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.5)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 0, 0, 0.3)';">
                        <a href="<?= Yii::$app->request->baseUrl . '/' . $image->path ?>" target="_blank"
                            style="display: block; width: 100%; height: auto; cursor: zoom-in;">
                            <img src="<?= Yii::$app->request->baseUrl . '/' . $image->path ?>" alt="Gallery thumbnail"
                                style="width: 100%; height: auto; display: block; transition: transform 0.4s ease;"
                                onmouseover="this.style.transform='scale(1.04)';" onmouseout="this.style.transform='scale(1)';">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php
// CSS ZA POTPUNI FULL SIZE NA MOBITELIMA (BEZ RUBOVA I BEZ ODREZIVANJA)
Yii::$app->view->registerCss("
    @media (max-width: 991px) {
        .post-split-container {
            flex-direction: column-reverse !important; /* Slika ide prva na vrh ekrana */
            gap: 30px !important;
        }
        .post-image-side {
            position: relative !important;
            top: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 auto;
        }
    }

    @media (max-width: 650px) {
        /* Ekstremni Full-Width za mobitel: skroz do ivica ekrana telefona */
        .site-post-view-container {
            padding: 20px 0px 60px 0px !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .post-image-side {
            padding: 0 !important;
        }
        
        /* Slika gubi zaobljenje i okvire da bi savršeno nalegla od ruba do ruba */
        .main-image-card {
            border: none !important;
            border-radius: 0px !important;
            box-shadow: none !important;
            width: 100% !important;
        }
        .main-image-card img {
            width: 100% !important;
            height: auto !important;
            border-radius: 0px !important;
        }
        
        /* Sadržaj teksta dobiva minimalni odmak od rubova ekrana da slova ne diraju staklo */
        .post-text-side {
            padding: 0px 15px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .hero-post-title {
            font-size: 1.85rem !important;
            letter-spacing: -0.5px !important;
        }

        /* 👑 RESPONZIVNO REKONSEKUTIRANJE ZA COATING (Slike i YouTube videozapisi unutar teksta) */
        .entry-content iframe,
        .entry-content video,
        .entry-content img,
        .entry-content table {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            aspect-ratio: 16 / 9 !important; /* Savršen bioskopski prikaz videa */
            display: block !important;
            margin: 20px 0 !important;
            box-sizing: border-box !important;
            border-radius: 0px !important; /* Puni ekran i za elemente u tekstu */
        }
        
        .entry-content table {
            aspect-ratio: auto !important;
            display: table !important;
            overflow-x: auto !important;
        }
        
        /* Galerija na dnu se širi i prikazuje 2 slike u redu bez kraćenja */
        .post-gallery-section {
            padding: 30px 15px 0px 15px !important;
        }
        .gallery-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 15px !important;
        }
        .gallery-card {
            border-radius: 10px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
        }
    }
");
