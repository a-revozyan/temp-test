<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\KaskoTariff;
use common\models\KaskoRisk;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\KaskoTariffRisk */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kasko-tariff-risk-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $tariffs = KaskoTariff::find()->all();

    echo $form->field($model, 'tariff_id')->dropDownList(ArrayHelper::map($tariffs, 'id', 'name'), ['prompt' => '- select -']);
    ?>

    <?php
    $risks = KaskoRisk::find()->all();

    echo $form->field($model, 'risk_id')->dropDownList(ArrayHelper::map($risks, 'id', 'name_ru'), ['prompt' => '- select -']);
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
