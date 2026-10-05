<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\OrderTable $model */

$this->title = Yii::t('app', 'Create Order Table');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Order Tables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-table-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
