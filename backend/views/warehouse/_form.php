<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Warehouse */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="warehouse-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $partner = \common\models\Partner::find()->all();

    echo $form->field($model, 'partner_id')->dropDownList(\yii\helpers\ArrayHelper::map($partner, 'id', 'name'), ['prompt' => '- select -']);
    ?>

    <?php
    $product = \common\models\Product::find()->all();

    echo $form->field($model, 'product_id')->dropDownList(\yii\helpers\ArrayHelper::map($product, 'id', 'name'), ['prompt' => '- select -']);
    ?>

    <?= $form->field($model, 'series')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?php

    echo $form->field($model, 'status')->dropDownList(['new', 'reserve', 'paid', 'cancel'], ['prompt' => '- select -']);
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
