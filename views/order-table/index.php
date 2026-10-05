<?php

use app\models\OrderTable;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\OrderTableSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Lista narudžbi');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-table-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Kreiraj narudžbu'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table'], // Dodato da se lepo vidi tabela
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'customer_name',
            'customer_lastname',
            'email:email',
            'phone',
            //'address:ntext',
            //'status',
            //'post_code',
            //'payment_method',
            //'total_price',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, OrderTable $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
