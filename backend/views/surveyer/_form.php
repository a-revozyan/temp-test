<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Surveyer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="surveyer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([
        [
            0 => 'inactive',
        ],
        [
            10 => 'active',
        ]

    ]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?php
    $regions = \common\models\Region::find()->all();

    echo $form->field($model, 'region_id')->dropDownList(ArrayHelper::map($regions, 'id', 'name_ru'), ['prompt' => '- select -']);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
