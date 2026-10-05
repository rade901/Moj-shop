<?php

use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\OrderTable $model */

$this->title = 'Narudžba #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Narudžbe', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-table-view py-4">
    <div class="container">

        <!-- Zaglavlje s akcijskim gumbima -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold text-body mb-0"><?= Html::encode($this->title) ?></h1>
            <div>
                <?= Html::a('✏️ Uredi', ['update', 'id' => $model->id], ['class' => 'btn btn-primary px-3 rounded-3']) ?>
                <?= Html::a('🗑️ Obriši', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-outline-danger px-3 rounded-3',
                    'data' => [
                        'confirm' => 'Jeste li sigurni da želite obrisati ovu narudžbu?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>

        <!-- 1. KARTICA: PODACI O KUPCU I DOSTAVI -->
        <div class="card border border-secondary border-opacity-10 rounded-4 p-4 shadow-sm bg-body-tertiary mb-5">
            <h3 class="h5 fw-bold text-body mb-3">📍 Podaci o dostavi</h3>
            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-bordered text-body bg-transparent mb-0'],
                'attributes' => [
                    'id',
                    [
                        'label' => 'Kupac',
                        'value' => $model->customer_name . ' ' . $model->customer_lastname,
                    ],
                    'email:email',
                    'phone',
                    'address:ntext',
                    'post_code',
                    [
                        'attribute' => 'payment_method',
                        'value' => 'Plaćanje pouzećem (Gotovina/Kartica kuriru)',
                    ],
                    [
                        'attribute' => 'status',
                        'label' => Yii::t('app', 'Status'),
                        'value' => function ($model) {
                            $klasa = $model->status === 'pending' ? 'bg-warning text-dark' : 'bg-success text-white';

                            // Prevodi vrijednost statusa (npr. 'pending' ili 'approved')
                            $prevedeniStatus = Yii::t('app', $model->status);

                            return '<span class="badge ' . $klasa . ' px-2.5 py-1.5 rounded-pill">' . Html::encode($prevedeniStatus) . '</span>';
                        },
                        'format' => 'raw',
                    ],
                    'created_at:datetime',
                ],
            ]) ?>
        </div>

        <!-- 2. KARTICA: KUPLJENI ARTIKLI (PROIZVODI I KOLIČINE) -->
        <div class="card border border-secondary border-opacity-10 rounded-4 p-4 shadow-sm bg-body-tertiary">
            <h3 class="h4 fw-bold text-body mb-4 d-flex align-items-center">
                <span class="me-2">📦</span> Kupljeni artikli u ovoj narudžbi
            </h3>

            <?= GridView::widget([
                'dataProvider' => new ActiveDataProvider([
                    // POZIVAMO POPRAVLJENU RELACIJU
                    'query' => $model->getOrderItems(),
                    'pagination' => false,
                ]),
                'summary' => false,
                'tableOptions' => ['class' => 'table table-hover align-middle text-body mb-0'],
                'columns' => [
                    [
                        'label' => 'Naziv Proizvoda',
                        'attribute' => 'product_id',
                        'contentOptions' => ['class' => 'fw-bold'],
                        'value' => function ($item) {
                            if (!empty($item->product)) {
                                return Html::encode($item->product->name);
                            }
                            return 'Proizvod (ID: ' . $item->product_id . ')';
                        },
                    ],
                    [
                        'label' => 'Količina',
                        'attribute' => 'quantity',
                        'contentOptions' => ['class' => 'text-center fw-semibold'],
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 120px;'],
                        'value' => function ($item) {
                            return $item->quantity . ' kom';
                        }
                    ],
                    [
                        'label' => 'Cijena po komadu',
                        'attribute' => 'price',
                        'contentOptions' => ['class' => 'text-end'],
                        'headerOptions' => ['class' => 'text-end', 'style' => 'width: 160px;'],
                        'value' => function ($item) {
                            return number_format((float)$item->price, 2, ',', '.') . ' EUR';
                        }
                    ],
                    [
                        'label' => 'Ukupno',
                        'contentOptions' => ['class' => 'text-end fw-bold text-primary'],
                        'headerOptions' => ['class' => 'text-end', 'style' => 'width: 160px;'],
                        'value' => function ($item) {
                            $total = (float)$item->price * (int)$item->quantity;
                            return number_format($total, 2, ',', '.') . ' EUR';
                        }
                    ],
                ],
            ]); ?>

            <!-- Ukupni prikaz za naplatu pouzećem -->
            <div
                class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-secondary border-opacity-25">
                <span class="fs-5 fw-bold text-body">Ukupan iznos za naplatu (Pouzećem):</span>
                <span class="fs-3 text-success fw-bold">
                    <?= number_format((float)$model->total_price, 2, ',', '.') ?> EUR
                </span>
            </div>
        </div>

    </div>
</div>

<?php
$this->registerCss("
    .fw-bold { font-weight: 700; }
    [data-bs-theme='dark'] .table-bordered th, 
    [data-bs-theme='dark'] .table-bordered td {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
");
?>