<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TravelFamilyKoef */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="travel-family-koef-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo $form->field($model, 'partner_id')->hiddenInput(['value' => $partner->id])->label('');
    ?>

    <?= $form->field($model, 'members_count')->textInput() ?>

    <?= $form->field($model, 'koef')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
