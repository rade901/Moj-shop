<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\PostSearch|null $searchModel */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = Yii::t('app', 'Blog & Objave');
$this->params['breadcrumbs'][] = $this->title;

// Sigurno dohvaćanje modela iz DataProvider-a
$posts = $dataProvider->getModels();
?>

<div class="site-all-posts" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px 80px 20px;">

    <!-- NASLOV SEKCIJE -->
    <div class="section-header mb-5" style="border-bottom: 1px solid #34495e; padding-bottom: 20px;">
        <h1 class="display-6 fw-bold text-white mb-2" style="font-weight: 800; letter-spacing: -1px;">
            📰 <?= Html::encode($this->title) ?>
        </h1>
        <p class="text-muted mb-0" style="color: #b2bec3 !important; font-size: 1.05rem;">
            <?= Yii::t('app', 'Pratite naše najnovije priče, savjete i obavijesti iz svijeta ribolova.') ?>
        </p>
    </div>

    <?php if (!empty($posts)): ?>
    <!-- 3 ČLANKA U JEDNOM REDU (Grid s točno 3 stupca na velikim ekranima) -->
    <div class="posts-clean-grid" style="
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        ">
        <?php foreach ($posts as $post): ?>
        <?php
                // Logika za dohvaćanje naslovne slike pojedinog članka
                $mainImageUrl = null;
                if (!empty($post->images)) {
                    foreach ($post->images as $img) {
                        if ($img->is_main == 1) {
                            $mainImageUrl = Yii::$app->request->baseUrl . '/' . $img->path;
                            break;
                        }
                    }
                    if (!$mainImageUrl && isset($post->images[0])) {
                        $mainImageUrl = Yii::$app->request->baseUrl . '/' . $post->images[0]->path;
                    }
                }
                ?>

        <!-- KARTICA ČLANKA (3 u redu) -->
        <div class="post-grid-card" style="
                    background: #1a202c;
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
                    border: 1px solid #2d3748;
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                "
            onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 12px 25px rgba(0,0,0,0.4)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 0, 0, 0.25)';">

            <!-- GORNJI DIO: Slika u kartici -->
            <a href="<?= Url::to(['site/post-view', 'id' => $post->id]) ?>"
                style="display: block; width: 100%; height: 210px; overflow: hidden; position: relative;">
                <?php if ($mainImageUrl): ?>
                <img src="<?= $mainImageUrl ?>" alt="<?= Html::encode($post->title) ?>"
                    style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;"
                    onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                <?php else: ?>
                <!-- Fallback gradijent ako članak nema sliku -->
                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                </div>
                <?php endif; ?>

                <!-- Značka kategorije diskretno u kutu slike -->
                <?php if (!empty($post->category)): ?>
                <span class="badge" style="
                                position: absolute;
                                top: 15px;
                                left: 15px;
                                background-color: rgba(26, 32, 44, 0.85);
                                color: #ffffff;
                                padding: 6px 12px;
                                border-radius: 8px;
                                font-size: 10px;
                                font-weight: 700;
                                text-transform: uppercase;
                                letter-spacing: 0.5px;
                                backdrop-filter: blur(4px);
                                border: 1px solid rgba(255,255,255,0.1);
                            ">
                    <?= Html::encode($post->category->name) ?>
                </span>
                <?php endif; ?>
            </a>

            <!-- TIJELO KARTICE -->
            <div class="card-grid-body" style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">

                <!-- Metapodaci (Svijetli i uočljivi na tamnoj temi) -->
                <div class="d-flex align-items-center gap-2 text-muted small mb-2"
                    style="color: #a0aec0 !important; font-size: 0.85rem;">
                    <span>📅 <?= Yii::$app->formatter->asDate($post->created_at, 'medium') ?></span>
                    <span>•</span>
                    <span>⏱️ <?= ceil(str_word_count(strip_tags($post->content)) / 200) ?> min</span>
                </div>

                <!-- Naslov (Prisilno bijeli, maksimalno 2 reda, fiksne visine za savršeno ravne kartice) -->
                <h2 class="h5 fw-bold mb-2"
                    style="line-height: 1.4; height: 50px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical;">
                    <a href="<?= Url::to(['site/post-view', 'id' => $post->id]) ?>"
                        style="color: #ffffff !important; text-decoration: none; font-weight: 700;">
                        <?= Html::encode($post->title) ?>
                    </a>
                </h2>

                <!-- Kratki opis (Očišćen od Summernote koda, maksimalno 3 reda) -->
                <p class="text-secondary small mb-4"
                    style="color: #cbd5e0 !important; line-height: 1.6; height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; -webkit-box-orient: vertical;">
                    <?= yii\helpers\StringHelper::truncate(strip_tags($post->content), 120, '...') ?>
                </p>

                <!-- Gumb postavljen ravno na dno unutar kartice -->
                <div style="margin-top: auto; padding-top: 15px; border-top: 1px solid #2d3748;">
                    <a href="<?= Url::to(['site/post-view', 'id' => $post->id]) ?>" class="btn btn-sm w-100 fw-bold"
                        style="
                                background-color: #34495e;
                                color: #ffffff;
                                border-radius: 10px;
                                padding: 8px;
                                border: 1px solid #475569;
                                transition: background 0.2s;
                            " onmouseover="this.style.backgroundColor='#475569';"
                        onmouseout="this.style.backgroundColor='#34495e';">
                        <?= Yii::t('app', 'Saznaj više &raquo;') ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- STRANIČENJE -->
    <div class="pagination-container mt-5 d-flex justify-content-center">
        <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'options' => ['class' => 'pagination gap-1'],
                'linkOptions' => ['class' => 'page-link', 'style' => 'background-color: #1a202c; border-color: #2d3748; color: #cbd5e0; border-radius: 8px;'],
                'activePageCssClass' => 'active',
                'disabledPageCssClass' => 'disabled',
            ]) ?>
    </div>

    <?php else: ?>
    <!-- Prazno stanje -->
    <div class="text-center py-5 rounded-4"
        style="background: #1a202c; border: 1px dashed #34495e; padding: 40px; margin-top: 20px;">
        <span style="font-size: 3.5rem; display: block; margin-bottom: 15px;">📭</span>
        <h3 class="text-white mt-3 fw-bold"><?= Yii::t('app', 'Trenutno nema objavljenih članaka') ?></h3>
        <p class="text-muted" style="color: #a0aec0 !important;">
            <?= Yii::t('app', 'Posjetite nas malo kasnije, svježe priče s Dunava su u pripremi.') ?></p>
    </div>
    <?php endif; ?>

</div>

<?php
// CSS za responzivnost (Na tabletima skače na 2 u redu, na mobitelima na 1)
Yii::$app->view->registerCss("
    .pagination .active .page-link {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
    }
    .pagination .page-link:hover {
        background-color: #2d3748 !important;
        color: #ffffff !important;
    }
    @media (max-width: 991px) {
        .posts-clean-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 600px) {
        .posts-clean-grid {
            grid-template-columns: 1fr !important;
        }
    }
");
?>