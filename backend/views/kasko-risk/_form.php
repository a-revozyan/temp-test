<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\KaskoRisk */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kasko-risk-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name_ru')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_uz')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

    <?php
    $tariffs = \common\models\KaskoRiskCategory::find()->all();

    echo $form->field($model, 'category_id')->dropDownList(\yii\helpers\ArrayHelper::map($tariffs, 'id', 'name'), ['prompt' => '- select -']);
    ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description_ru')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description_uz')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description_en')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'show_desc')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
