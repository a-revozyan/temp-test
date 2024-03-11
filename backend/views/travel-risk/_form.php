<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Partner;

/* @var $this yii\web\View */
/* @var $model common\models\TravelRisk */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="travel-risk-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $partners = Partner::find()->asArray()->all();

    echo $form->field($model, 'partner_id')->dropDownList(ArrayHelper::map($partners, 'id', 'name'), ['prompt' => '- select -'])->label('Partner');
    ?>

    <?= $form->field($model, 'name_ru')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_uz')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

    <?php
    $categories = \common\models\KaskoRiskCategory::find()->all();

    echo $form->field($model, 'category_id')->dropDownList(\yii\helpers\ArrayHelper::map($categories, 'id', 'name'), ['prompt' => '- select -']);
    ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'status')->radioList(['1' => 'Active', '0' => 'Passive']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
