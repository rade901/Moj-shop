<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\editors\Summernote;

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-form">

    <!-- DODANO: enctype omogućuje slanje datoteka -->
    <?php $form = ActiveForm::begin([
        'id' => 'product-form',
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(Summernote::class, [
        'useKrajeePresets' => true, // Aktivira stabilne zadane postavke alata
        'pluginOptions' => [
            'height' => 250, // Postavlja visinu editora u pikselima
        ]
    ]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stock')->textInput() ?>

    <!-- Promijenjeno u checkbox jer je is_active boolean -->
    <?= $form->field($model, 'is_active')->checkbox() ?>

    <!-- PRIKAZ POSTOJEĆIH SLIKA (Prikazuje se samo pri ažuriranju proizvoda) -->
    <?php if (!$model->isNewRecord && !empty($model->images)): ?>
        <div class="form-group mb-4">
            <label class="control-label" style="font-weight: bold; margin-bottom: 10px;">Trenutne slike proizvoda (Kliknite na X za brisanje):</label>
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 15px; margin-left: 0; margin-right: 0;">
                <?php foreach ($model->images as $image): ?>
                    <div class="image-thumb-container" id="img-container-<?= $image->id ?>" style="position: relative; width: 120px; text-align: center;">
                        
                        <!-- Prikaz sličice -->
                        <img src="<?= Yii::$app->request->baseUrl . '/' . $image->path ?>" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                        
                        <!-- Ako je slika glavna, prikazujemo značku -->
                        <span class="badge-main-status" id="badge-main-<?= $image->id ?>">
                            <?php if ($image->is_main == 1): ?>
                                <span class="badge bg-primary" style="position: absolute; top: 5px; left: 5px; font-size: 10px;">Glavna</span>
                            <?php endif; ?>
                        </span>

                        <!-- Gumb za brisanje (X) -->
                        <button type="button" 
                                class="btn btn-danger delete-image-btn" 
                                data-id="<?= $image->id ?>" 
                                style="position: absolute; top: 5px; right: 5px; padding: 2px 6px; font-size: 12px; line-height: 1; border-radius: 3px; font-weight: bold;">
                            &times;
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- POLJE: Polje za upload više slika odjednom -->
    <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>

    <!-- NOVO: Kontejner za pretpregled tek odabranih slika prije nego se spreme na server -->
    <div class="form-group mb-4">
        <div id="new-images-preview" class="row" style="display: flex; flex-wrap: wrap; gap: 15px; margin-left: 0; margin-right: 0; margin-top: 10px;"></div>
    </div>

    <div class="form-group mt-3" style="margin-top: 15px;">
        <!-- Dodan ID na gumb i dodatni html unutar gumba za spinner -->
        <?= Html::submitButton(
            '<span id="btn-text">' . Yii::t('app', 'Save') . '</span>' .
            '<span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" style="margin-left: 8px;"></span>', 
            ['class' => 'btn btn-success', 'id' => 'product-form-submit']
        ) ?>
        
        <!-- Dodatna tekstualna obavijest koja se pojavljuje samo tijekom uploada -->
        <small id="upload-msg" class="text-muted d-none" style="display: block; margin-top: 5px; font-style: italic;">
            Učitavanje datoteka i spremanje na server je u tijeku, molimo pričekajte...
        </small>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// Generiranje ispravne putanje do akcije u kontroleru
$deleteUrl = \yii\helpers\Url::to(['product/delete-image']);

$js = <<<JS
// 1. Logika za brisanje postojećih slika preko AJAX-a
$('.delete-image-btn').on('click', function() {
    var imgId = $(this).data('id');
    
    if (confirm('Jeste li sigurni da želite obrisati ovu sliku? Datoteka će biti trajno uklonjena sa servera.')) {
        $.ajax({
            url: '{$deleteUrl}',
            type: 'POST',
            data: { id: imgId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Sakrij i ukloni kontejner sa slikom s ekrana
                    $('#img-container-' + imgId).fadeOut(300, function() { 
                        $(this).remove(); 
                        
                        // Ako je obrisana slika bila glavna, a kontroler je postavio novu glavnu sliku
                        if (response.wasMain && response.newMainId) {
                            var containerBadge = $('#badge-main-' + response.newMainId);
                            if (containerBadge.find('.badge').length === 0) {
                                containerBadge.html('<span class="badge bg-primary" style="position: absolute; top: 5px; left: 5px; font-size: 10px;">Glavna</span>');
                            }
                        }
                    });
                } else {
                    alert('Pogreška pri brisanju slike sa servera: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Komunikacija sa serverom nije uspjela. Provjerite jeste li prijavljeni.');
                console.log(xhr.responseText);
            }
        });
    }
});

// 2. NOVO: Logika za pretpregled tek odabranih slika (Multi-upload preview)
$('#product-imagefiles').on('change', function(e) {
    var files = e.target.files;
    var previewContainer = $('#new-images-preview');
    
    // Očisti prethodni pretpregled ako je korisnik ponovno odabirao datoteke
    previewContainer.empty();
    
    if (files && files.length > 0) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            
            // Provjera je li datoteka slika
            if (file.type.match('image.*')) {
                var imgUrl = URL.createObjectURL(file);
                
                // Generiranje HTML-a sa zelenim obrubom za nove slike
                var imgHtml = 
                    '<div class="image-thumb-container" style="position: relative; width: 120px; text-align: center;">' +
                        '<img src="' + imgUrl + '" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #5cb85c;">' +
                        '<span class="badge bg-success" style="position: absolute; top: 5px; left: 5px; font-size: 10px;">Nova</span>' +
                    '</div>';
                
                previewContainer.append(imgHtml);
            }
        }
    }
});

// 3. Logika prije slanja forme (Prikaz spinnera i onemogućavanje gumba)
$('#product-form').on('beforeSubmit', function (e) {
    var \$btn = $('#product-form-submit');
    var \$spinner = $('#btn-spinner');
    var \$text = $('#btn-text');
    var \$msg = $('#upload-msg');

    \$btn.prop('disabled', true);
    \$spinner.removeClass('d-none');
    \$msg.removeClass('d-none');
    \$text.text('Spremanje...');
    
    return true; 
});
JS;

// Registracija JavaScripta na stranici
$this->registerJs($js);

// Prisila bijele pozadine i tamnog teksta za Summernote u Night Mode-u
Yii::$app->view->registerCss("
    .note-editor.note-frame {
        background-color: #ffffff !important;
        color: #333333 !important;
    }
    .note-editor .note-editing-area .note-editable {
        background-color: #ffffff !important;
        color: #333333 !important;
        font-family: sans-serif !important;
    }
    .note-editor .note-toolbar {
        background-color: #f5f5f5 !important;
        color: #333333 !important;
        border-bottom: 1px solid #ddd !important;
    }
    .note-editor .note-btn {
        background-color: #ffffff !important;
        color: #333333 !important;
        border: 1px solid #ccc !important;
    }
    .note-editor .note-btn:hover {
        background-color: #e6e6e6 !important;
        color: #000000 !important;
    }
    .note-editor .note-dropdown-menu {
        background-color: #ffffff !important;
        color: #333333 !important;
    }
    .note-editor .note-status-output, .note-editor .note-resizebar {
        background-color: #f5f5f5 !important;
    }
");
?>
