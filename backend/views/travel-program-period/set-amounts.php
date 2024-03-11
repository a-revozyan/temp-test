<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Partner;
use common\models\Currency;
use common\models\TravelProgram;
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;


$this->title = $partner->name . ' ' . Yii::t('app', 'Program Periods');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Partners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
/* @var $model common\models\TravelProgramPeriod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="travel-program-period-form">

    <?php $form = ActiveForm::begin(['id' => 'program-period-form']); 

    $currencies = Currency::find()->asArray()->all();
    ?>
    <div class="row">
        <div class="col-md-4">
            <?php
            echo $form->field($partner, "travel_currency_id")->dropDownList(ArrayHelper::map($currencies, 'id', 'name'), [
                'prompt' => '- select -'
            ])->label('Currency');
            ?>
        </div>
    </div>
    <div class="relationships">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 100, // the maximum times, an element can be cloned (default 999)
            'min' => 1, // 0 or 1 (default 1)
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $program_periods[0],
            'id' => 'dynamic-form',
            'formId' => 'program-period-form',
            'formFields' => [
                'program_id',
                'from_day',
                'to_day',
                'amount',
            ],
        ]); ?>

        <div class="row">
        	<div class="col-md-4">Program</div>
        	<div class="col-md-2">From day</div>
        	<div class="col-md-2">To day</div>
        	<div class="col-md-2">Amount</div>
        </div>

        <div class="container-items" style="margin-top: 10px;"><!-- widgetContainer -->
        <?php foreach ($program_periods as $i => $p): ?>
            <div class="item row"><!-- widgetBody -->
                <?php
                    // necessary for update action.
                    if (!$p->isNewRecord) {
                        echo Html::activeHiddenInput($p, "[{$i}]id");
                    }
                ?>
                <div class="col-md-4">
                <?php
		            $programs = TravelProgram::find()->where(['partner_id' => $partner->id])->asArray()->all();

		            echo $form->field($p, "[{$i}]program_id")->dropDownList(ArrayHelper::map($programs, 'id', 'name'), [
		                'prompt' => '- select -'
		            ])->label(false);
		        ?>
                </div>

                <div class="col-md-2">
			        <?= $form->field($p, "[{$i}]from_day")->textInput()->label(false) ?>
			    </div>

		        <div class="col-md-2">
		            <?= $form->field($p, "[{$i}]to_day")->textInput()->label(false) ?>
		        </div>

		        <div class="col-md-2">
				    <?= $form->field($p, "[{$i}]amount")->textInput()->label(false) ?>
		        </div>

		        <div class="col-md-2">
                    <button type="button" class="remove-item btn btn-danger"><i class="fa fa-minus"></i></button>
                    <button type="button" class="add-item btn btn-success"><i class="fa fa-plus"></i></button>
		        </div>
		    </div>
          <?php endforeach; ?>

        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
