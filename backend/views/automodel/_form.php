<?php

use common\models\AutoRiskType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Autobrand;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Automodel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="automodel-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $autobrands = Autobrand::find()->all();
    $auto_risk_types = AutoRiskType::find()->all();

    echo $form->field($model, 'autobrand_id')->dropDownList(ArrayHelper::map($autobrands, 'id', 'name'))->label('Autobrand');
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order')->input('number', ['step' => 1]) ?>

    <?= $form->field($model, 'auto_risk_type_id')->dropDownList(
            ArrayHelper::map($auto_risk_types, 'id', 'name'),
            array('prompt'=>'Пожалуйста выберите'))
        ->label('Auto Type') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
