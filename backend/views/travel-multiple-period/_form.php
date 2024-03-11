<?php

use common\models\Partner;
use common\models\TravelProgram;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TravelMultiplePeriod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="travel-multiple-period-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo $form->field($model, 'partner_id')->hiddenInput(['value' => $partner->id])->label('');
    ?>

    <?php
    $partners = TravelProgram::find()->where(['partner_id' => $partner->id])->asArray()->all();

    echo $form->field($model, 'program_id')->dropDownList(ArrayHelper::map($partners, 'id', 'name'), ['prompt' => '- select -'])->label('Program');
    ?>


    <?= $form->field($model, 'available_interval_days')->textInput() ?>

    <?= $form->field($model, 'days')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
