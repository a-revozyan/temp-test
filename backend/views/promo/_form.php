<?php

use common\models\Promo;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Promo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="promo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'amount_type')->dropDownList([
                Promo::AMOUNT_TYPE['percent'] => 'percent',
                Promo::AMOUNT_TYPE['fixed'] => 'fixed',
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'begin_date')->widget(\kartik\date\DatePicker::className(), ['pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'autoApply' => true,
                'format' => 'yyyy-mm-dd',
//               'startDate' => '+1d',
//               'endDate' => '+2y'
            ],]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'end_date')->widget(\kartik\date\DatePicker::className(), ['pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'autoApply' => true,
                'format' => 'yyyy-mm-dd',
//               'startDate' => '+1d',
//               'endDate' => '+2y'
            ],]) ?>
        </div>
    </div>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([
        Promo::STATUS['inactive'] => 'inactive',
        Promo::STATUS['active'] => 'active',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
