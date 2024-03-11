<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Partner;

/* @var $this yii\web\View */
/* @var $model common\models\TravelAgeGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="travel-age-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $partners = Partner::find()->asArray()->all();

    echo $form->field($model, 'partner_id')->dropDownList(ArrayHelper::map($partners, 'id', 'name'), ['prompt' => '- select -'])->label('Partner');
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'from_age')->textInput() ?>

    <?= $form->field($model, 'to_age')->textInput() ?>

    <?= $form->field($model, 'coeff')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
