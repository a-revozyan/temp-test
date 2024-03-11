<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Qa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'question_uz')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'question_en')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'question_ru')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'answer_uz')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'answer_en')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'answer_ru')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([1 => 'active', 0 => 'in active']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
