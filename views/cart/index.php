<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $products */
/** @var float $totalPrice */
/** @var app\models\Order $orderModel */

$this->title = 'Vaša Košarica';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="cart-index py-5">
    <div class="container">
        <h1 class="mb-4 text-body fw-bold"><?= Html::encode($this->title) ?></h1>

        <?php if (empty($products)): ?>
            <div class="text-center py-5 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                <p class="fs-4 text-secondary mb-3">🛒 Vaša košarica je prazna.</p>
                <?= Html::a('Natrag na trgovinu', ['product/index'], ['class' => 'btn btn-primary px-4']) ?>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Tablica proizvoda u košarici -->
                <div class="col-lg-8">
                    <div class="table-responsive bg-body-tertiary p-4 rounded-4 border border-secondary border-opacity-10 shadow-sm">
                        <table class="table align-middle text-body mb-0">
                            <thead>
                                <tr class="text-secondary border-bottom border-secondary border-opacity-25">
                                    <th>Proizvod</th>
                                    <th class="text-center">Količina</th>
                                    <th class="text-end">Cijena</th>
                                    <th class="text-center">Ukloni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $item): ?>
                                    <tr class="border-bottom border-secondary border-opacity-10">
                                        <td>
                                            <div class="fw-bold"><?= Html::encode($item['model']->name) ?></div>
                                        </td>
                                        <td class="text-center fw-semibold"><?= $item['quantity'] ?></td>
                                        <td class="text-end fw-bold text-primary">
                                            <?= number_format($item['line_total'], 2, ',', '.') ?> EUR
                                        </td>
                                        <td class="text-center">
                                            <?= Html::a('❌', ['cart/remove', 'id' => $item['model']->id], [
                                                'class' => 'btn btn-link text-decoration-none p-0',
                                                'data-method' => 'post'
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-secondary border-opacity-25">
                            <span class="fs-4 fw-bold text-body">Ukupno za uplatu:</span>
                            <span class="fs-3 fw-extrabold text-primary"><?= number_format($totalPrice, 2, ',', '.') ?> EUR</span>
                        </div>
                    </div>
                </div>

                <!-- FORMA ZA PODATKE I PLAĆANJE POUZEĆEM -->
                <div class="col-lg-4">
                    <div class="bg-body-tertiary p-4 rounded-4 border border-secondary border-opacity-10 shadow-sm position-sticky" style="top: 2rem;">
                        <h3 class="h5 fw-bold text-body mb-3">Podaci za dostavu</h3>
                        
                        <!-- Informativni box za pouzeće -->
                        <div class="alert alert-info border-0 rounded-3 mb-4 text-start small bg-info bg-opacity-10 text-info">
                            <strong>ℹ Način plaćanja: Plaćanje pouzećem</strong><br>
                            Narudžbu ćete platiti gotovinom ili karticom kuriru prilikom same dostave na Vašu adresu. nema skrivenih troškova.
                        </div>

                        <?php $form = ActiveForm::begin(); ?>

                        <?= $form->field($orderModel, 'customer_name')->textInput([
                            'class' => 'form-control bg-body border-secondary border-opacity-25 text-body mb-3',
                            'placeholder' => 'Ime'
                        ])->label('Vaše ime') ?>

                        <?= $form->field($orderModel, 'customer_lastname')->textInput([
                            'class' => 'form-control bg-body border-secondary border-opacity-25 text-body mb-3',
                            'placeholder' => 'Prezime'
                        ])->label('Vaše prezime') ?>

                        <?= $form->field($orderModel, 'email')->input('email', [
                            'class' => 'form-control bg-body border-secondary border-opacity-25 text-body mb-3',
                            'placeholder' => 'primjer@email.com'
                        ])->label('E-mail adresa') ?>

                        <?= $form->field($orderModel, 'phone')->textInput([
                            'class' => 'form-control bg-body border-secondary border-opacity-25 text-body mb-3',
                            'placeholder' => '+385...'
                        ])->label('Broj mobitela/telefona') ?>

                        <?= $form->field($orderModel, 'post_code')->textInput([
                            'class' => 'form-control bg-body border-secondary border-opacity-25 text-body mb-3',
                            'placeholder' => '10000'
                        ])->label('Poštanski broj') ?>

                        <?= $form->field($orderModel, 'address')->textarea([
                            'class' => 'form-control bg-body border-secondary border-opacity-25 text-body mb-4',
                            'rows' => 3,
                            'placeholder' => 'Ulica, kućni broj, Grad'
                        ])->label('Adresa za dostavu') ?>

                        <?= Html::submitButton('📦 Potvrdi narudžbu (Plaćanje pouzećem)', [
                            'class' => 'btn btn-success btn-lg w-100 rounded-3 fw-bold py-3 shadow-sm'
                        ]) ?>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$this->registerCss(".fw-extrabold { font-weight: 800; }");
?>
