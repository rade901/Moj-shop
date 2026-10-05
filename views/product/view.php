<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Product $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Proizvodi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Ažuriraj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Izbriši'), ['delete', 'id' => $model->id], [
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
            'name',
            'description:html',
            [
                'attribute' => 'price',
                'value' => function ($model) {
                    return number_format($model->price, 2) . ' EUR'; // Lijep prikaz cijene
                },
            ],
            'stock',
            'views_count', // Prikaz broja pregleda
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->is_active
                        ? '<span class="badge bg-success">Aktivan</span>'
                        : '<span class="badge bg-danger">Neaktivan</span>';
                },
            ],
            // NOVO: Prikaz glavne slike unutar DetailView-a
            [
                'label' => 'Glavna slika',
                'format' => 'raw',
                'value' => function ($model) {
                    // Tražimo sliku koja ima is_main = 1
                    $mainImage = null;
                    foreach ($model->images as $img) {
                        if ($img->is_main == 1) {
                            $mainImage = $img;
                            break;
                        }
                    }
                    // Ako nema eksplicitno glavne, uzmi prvu dostupnu
                    if (!$mainImage && !empty($model->images)) {
                        $mainImage = $model->images[0];
                    }

                    if ($mainImage) {
                        return Html::img(Yii::$app->request->baseUrl . '/' . $mainImage->path, [
                            'class' => 'img-thumbnail',
                            'style' => 'width: 150px; height: 150px; object-fit: cover;'
                        ]);
                    }
                    return '<span class="text-muted">Nema slike</span>';
                },
            ],
            'created_at:datetime', // formatira datum ljudski čitljivo
            'updated_at:datetime',
        ],
    ]) ?>

    <!-- NOVO: Galerija svih slika proizvoda ispod tablice -->
    <?php if (!empty($model->images)): ?>
        <div class="product-gallery" style="margin-top: 30px;">
            <h3>Galerija slika</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <?php foreach ($model->images as $image): ?>
                    <div style="position: relative; text-align: center;">
                        <?= Html::img(Yii::$app->request->baseUrl . '/' . $image->path, [
                            'class' => 'img-thumbnail',
                            'style' => 'width: 120px; height: 120px; object-fit: cover;'
                        ]) ?>
                        <?php if ($image->is_main == 1): ?>
                            <div style="font-size: 11px; margin-top: 5px;"><span class="badge bg-primary">Glavna</span></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>