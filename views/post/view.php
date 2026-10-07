<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Post $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            // PROMIJENJENO: :raw omogućuje ispravan prikaz HTML sadržaja iz Summernote editora
            'content:raw',
            // NOVO POLJE: Prikaz glavne slike unutar same tablice detalja
            [
                'attribute' => 'images',
                'label' => Yii::t('app', 'Glavna slika'),
                'format' => 'raw',
                'value' => function ($model) {
                    // Tražimo sliku koja ima zastavicu is_main = 1
                    $mainImage = null;
                    foreach ($model->images as $img) {
                        if ($img->is_main == 1) {
                            $mainImage = $img;
                            break;
                        }
                    }
                    // Ako nema slike s is_main=1, uzmi prvu dostupnu sliku kao zamjensku
                    if (!$mainImage && !empty($model->images)) {
                        $mainImage = $model->images[0];
                    }

                    if ($mainImage) {
                        return Html::img(Yii::$app->request->baseUrl . '/' . $mainImage->path, [
                            'class' => 'img-thumbnail',
                            'style' => 'max-width: 200px; max-height: 200px; object-fit: cover;'
                        ]);
                    }
                    return '<span class="text-muted">' . Yii::t('app', 'Nema slike') . '</span>';
                },
            ],
            'created_at',
            'updated_at',
            // Prikaz naziva kategorije umjesto samo sirovog ID-ja (ako relacija getCategory postoji)
            [
                'attribute' => 'category_id',
                'value' => function ($model) {
                    return $model->category ? $model->category->name : null;
                },
            ],
        ],
    ]) ?>

    <!-- NOVI BLOK: Galerija svih slika članka ispod tablice detalja -->
    <?php if (!empty($model->images)): ?>
        <div class="post-gallery mt-5" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
            <h3 class="mb-4"
                style="font-size: 1.4rem; font-weight: 700; color: #2c3e50; letter-spacing: -0.5px; margin-bottom: 20px;">
                <i class="bi bi-images" style="margin-right: 8px;"></i><?= Yii::t('app', 'Galerija slika') ?>
            </h3>

            <!-- Moderni CSS Grid kontejner -->
            <div class="gallery-grid" style="
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); 
            gap: 20px;
        ">
                <?php foreach ($model->images as $image): ?>
                    <div class="gallery-card" style="
                    position: relative;
                    background: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                    transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.3s ease;
                    border: 1px solid #eef2f5;
                "
                        onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.1)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';">
                        <!-- Link i slika -->
                        <a href="<?= Yii::$app->request->baseUrl . '/' . $image->path ?>" target="_blank"
                            style="display: block; width: 100%; height: 150px;">
                            <img src="<?= Yii::$app->request->baseUrl . '/' . $image->path ?>" alt="Post image"
                                style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        </a>

                        <!-- Značka za glavnu sliku smještena elegantno u gornji lijevi kut unutar same slike -->
                        <?php if ($image->is_main == 1): ?>
                            <span class="badge bg-primary" style="
                            position: absolute; 
                            top: 10px; 
                            left: 10px; 
                            font-size: 10px; 
                            font-weight: 600;
                            padding: 5px 9px;
                            border-radius: 6px;
                            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.4);
                            letter-spacing: 0.3px;
                        ">
                                <?= Yii::t('app', 'Glavna') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>


</div>