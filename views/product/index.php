<?php

use app\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Proizvodi');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Kreiraj proizvod'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table'], 
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 1. NOVI STUPAC: Prikaz naslovne sličice proizvoda
            [
                'label' => 'Slika',
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 80px; text-align: center; vertical-align: middle;'],
                'value' => function ($model) {
                    // Tražimo glavnu sliku (is_main = 1)
                    $mainImage = null;
                    foreach ($model->images as $img) {
                        if ($img->is_main == 1) {
                            $mainImage = $img;
                            break;
                        }
                    }
                    // Ako nema glavne, uzmi prvu dostupnu u galeriji
                    if (!$mainImage && !empty($model->images)) {
                        $mainImage = $model->images[0];
                    }

                    if ($mainImage) {
                        return Html::img(Yii::$app->request->baseUrl . '/' . $mainImage->path, [
                            'class' => 'img-thumbnail',
                            'style' => 'width: 50px; height: 50px; object-fit: cover; margin: 0;'
                        ]);
                    }
                    return '<span class="text-muted" style="font-size: 11px;">Nema slike</span>';
                },
            ],

            'id',
            [
                'attribute' => 'name',
                'contentOptions' => ['style' => 'vertical-align: middle;'],
            ],
            [
                'attribute' => 'price',
                'value' => function ($model) {
                    return number_format($model->price, 2) . ' EUR';
                },
                'contentOptions' => ['style' => 'vertical-align: middle;'],
            ],
            [
                'attribute' => 'stock',
                'contentOptions' => ['style' => 'vertical-align: middle; text-align: center;'],
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'filter' => [1 => 'Aktivan', 0 => 'Neaktivan'], // Padajući izbornik za filter pretrage
                'value' => function ($model) {
                    return $model->is_active 
                        ? '<span class="badge bg-success">Aktivan</span>' 
                        : '<span class="badge bg-danger">Neaktivan</span>';
                },
                'contentOptions' => ['style' => 'vertical-align: middle; text-align: center;'],
            ],

            // 2. NOVI STUPAC: Broj pregleda
            [
                'attribute' => 'views_count',
                'label' => 'Pregledi 👁️',
                'contentOptions' => ['style' => 'vertical-align: middle; text-align: center; font-weight: bold; font-size: 15px; text-decoration: none;'],
                'headerOptions' => ['style' => 'text-align: center;'],
            ],

            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['style' => 'vertical-align: middle; text-align: center; width: 100px;'],
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

</div>
