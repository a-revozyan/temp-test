<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Partner;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\AccidentPartnerProgram */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="accident-partner-program-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $partners = Partner::find()->asArray()->all();

    echo $form->field($model, 'partner_id')->dropDownList(ArrayHelper::map($partners, 'id', 'name'), ['prompt' => '- select -'])->label('Partner');
    ?>

    <div class="row">
	    <div class="col-md-4">
	    	<?= $form->field($model, 'insurance_amount_from')->textInput() ?>
	    </div>
	    <div class="col-md-4">
	    	<?= $form->field($model, 'insurance_amount_to')->textInput() ?>
	    </div>
	    <div class="col-md-4">
	    	<?= $form->field($model, 'percent')->textInput() ?>
	    </div>
	</div>    

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
