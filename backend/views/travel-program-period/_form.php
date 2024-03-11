<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Partner;
use common\models\TravelProgram;


/* @var $this yii\web\View */
/* @var $model common\models\TravelProgramPeriod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="travel-program-period-form">

    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="col-md-6">
            <?php
            $partners = Partner::find()->asArray()->all();

            echo $form->field($model, 'partner_id')->dropDownList(ArrayHelper::map($partners, 'id', 'name'), [
                'prompt' => '- select -',
                'onchange' => '$.post( "'.Yii::$app->urlManager->createUrl('travel-program-period/program-list?id=').'"+$(this).val(), function( data ) {
                  $( "select#travelprogramperiod-program_id" ).html(data);
                });'
            ])->label('Partner');
            ?>

        </div>

        <div class="col-md-6">
            <?php

            $programs = TravelProgram::find()->asArray()->all();

            echo $form->field($model, 'program_id')->dropDownList(ArrayHelper::map($programs, 'id', 'name'), ['prompt' => '- select -'])->label('Program');
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'from_day')->textInput() ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'to_day')->textInput() ?>
        </div>
    </div>

    <?= $form->field($model, 'amount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
