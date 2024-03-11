<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Partner;

/* @var $this yii\web\View */
/* @var $model common\models\TravelExtraInsurance */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="travel-extra-insurance-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    // $partners = Partner::find()->asArray()->all();

    // echo $form->field($model, 'partner_id')->dropDownList(ArrayHelper::map($partners, 'id', 'name'), ['prompt' => '- select -'])->label('Partner');
    ?>

    <?= $form->field($model, 'name_ru')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_uz')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->radioList(['1' => 'Active', '0' => 'Passive']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
