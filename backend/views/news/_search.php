<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\NewsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title_ru') ?>

    <?= $form->field($model, 'title_uz') ?>

    <?= $form->field($model, 'title_en') ?>

    <?= $form->field($model, 'image_ru') ?>

    <?php // echo $form->field($model, 'image_uz') ?>

    <?php // echo $form->field($model, 'image_en') ?>

    <?php // echo $form->field($model, 'short_info_ru') ?>

    <?php // echo $form->field($model, 'short_info_uz') ?>

    <?php // echo $form->field($model, 'short_info_en') ?>

    <?php // echo $form->field($model, 'body_ru') ?>

    <?php // echo $form->field($model, 'body_uz') ?>

    <?php // echo $form->field($model, 'body_en') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
