<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Partner;
use kartik\select2\Select2;
use common\models\Country;
use common\models\TravelProgramCountry;

/* @var $this yii\web\View */
/* @var $model common\models\TravelProgram */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="travel-program-form">

    <?php $form = ActiveForm::begin(); ?>

	  <?php
    $partners = Partner::find()->asArray()->all();

    echo $form->field($model, 'partner_id')->dropDownList(ArrayHelper::map($partners, 'id', 'name'), ['prompt' => '- select -'])->label('Partner');
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?php
    $countries = Country::find()->where(['parent_id' => null])->all();
    $data = array_combine(array_column($countries,'id'), array_column($countries,'name_ru'));

    $countries1 = TravelProgramCountry::find()->select('country_id')->where(['program_id' => $model->id])->asArray()->all();
        //$model->countries = array_column($countries,'name','id');
        $data1 = Country::find()->select('id')->where(['id' => array_column($countries1, 'country_id')])->asArray()->all();
        $model->countries = array_column($data1, 'id');

    echo $form->field($model, 'countries')->widget(Select2::classname(), [
      'data' => $data,
      'theme' => Select2::THEME_BOOTSTRAP,
      'options' => ['placeholder' => 'Select a state ...', 'multiple' => true, 'autocomplete' => 'off'],
      'pluginOptions' => [
          'allowClear' => true
      ],
    ])->label('Territories');

    ?>

    <?= $form->field($model, 'has_covid')->checkbox() ?>

    <?= $form->field($model, 'status')->radioList(['1' => 'Active', '0' => 'Passive']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
