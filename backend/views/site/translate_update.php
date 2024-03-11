<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin();

    ?>

    <h3><b>Message: </b><?=$model->message?></h3>

    
    <?= $form->field($translateModels[0], '[0]translation')->textarea(['rows' => 3])->label('Russian') ?>

    <?= $form->field($translateModels[1], '[1]translation')->textarea(['rows' => 3])->label('Uzbek') ?>

    <?= $form->field($translateModels[2], '[2]translation')->textarea(['rows' => 3])->label('English') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>



</div>
