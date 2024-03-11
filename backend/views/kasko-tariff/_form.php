<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Partner;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\KaskoTariff */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kasko-tariff-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $partners = Partner::find()->asArray()->all();

    echo $form->field($model, 'partner_id')->dropDownList(ArrayHelper::map($partners, 'id', 'name'), ['prompt' => '- select -'])->label('Partner');
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

     <?= $form->field($model, 'amount_kind')->radioList( ['P' => 'Percent', 'A' => 'Amount'] ) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'file')->fileInput() ?>

    <?php
        echo $form->field($model, 'is_conditional')->dropDownList(['conditional', 'unconditional'], ['prompt' => '- select -']);
    ?>

    <?= $form->field($model, 'franchise_ru')->textarea() ?>
    <?= $form->field($model, 'franchise_en')->textarea() ?>
    <?= $form->field($model, 'franchise_uz')->textarea() ?>

    <?= $form->field($model, 'only_first_risk_ru')->textarea() ?>
    <?= $form->field($model, 'only_first_risk_en')->textarea() ?>
    <?= $form->field($model, 'only_first_risk_uz')->textarea() ?>

    <?= Html::a('Download the File', $model->file ? [$model->file] : '#') ?> <?= !is_null($model->file) ?  basename($model->file) : "" ?>
    <?php $auto_risk_types = \common\models\AutoRiskType::find()->all() ?>
    <?= $form->field($model, 'auto_risk_type_ids')->dropDownList(ArrayHelper::map($auto_risk_types, 'id', 'name'), ['multiple' => 'multiple', 'required' => 'required'])->label('Auto Type') ?>

    <?= $form->field($model, 'min_price')->textInput(['type' => 'number', 'step' => 1]) ?>
    <?= $form->field($model, 'max_price')->textInput(['type' => 'number', 'step' => 1]) ?>

    <?= $form->field($model, 'min_year')->textInput(['type' => 'number', 'step' => 1]) ?>
    <?= $form->field($model, 'max_year')->textInput(['type' => 'number', 'step' => 1]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
