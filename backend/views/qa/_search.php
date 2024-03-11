<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\QaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'question_uz') ?>

    <?= $form->field($model, 'question_en') ?>

    <?= $form->field($model, 'question_ru') ?>

    <?= $form->field($model, 'answer_uz') ?>

    <?php // echo $form->field($model, 'answer_en') ?>

    <?php // echo $form->field($model, 'answer_ru') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
