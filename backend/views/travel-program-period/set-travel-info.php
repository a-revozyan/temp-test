<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;


$this->title = $partner->name . ' ' . Yii::t('app', 'Travel Info');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Partners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
/* @var $model common\models\TravelProgramPeriod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="travel-partner-info-form">

    <?php $form = ActiveForm::begin(); ?>
    <ul class="nav nav-tabs">
      <li class="nav-item active">
        <a class="nav-link" data-toggle="tab" href="#ru">Russian</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#uz">Uzbek</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#en">English</a>
      </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane active" id="ru">
        <?= $form->field($model, 'assistance')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'franchise')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'limitation')->textarea(['rows' => 6]) ?>

        <div class="row">
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'rulesFile')->fileInput();
                    
                if($model->rules):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/travel_info/<?=$model->rules?>">Rules.pdf</a>
                <?php
                endif;
                ?>
            </div>
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'policyFile')->fileInput();
                    
                if($model->policy_example):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/travel_info/<?=$model->policy_example?>">Policy.pdf</a>
                <?php
                endif;
                ?>
            </div>
        </div>
      </div>

      <div class="tab-pane" id="uz">
        <?= $form->field($model, 'assistance_uz')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'franchise_uz')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'limitation_uz')->textarea(['rows' => 6]) ?>

        <div class="row">
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'rulesFileUz')->fileInput();
                    
                if($model->rules_uz):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/travel_info/<?=$model->rules_uz?>">Rules.pdf</a>
                <?php
                endif;
                ?>
            </div>
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'policyFileUz')->fileInput();
                    
                if($model->policy_example_uz):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/travel_info/<?=$model->policy_example_uz?>">Policy.pdf</a>
                <?php
                endif;
                ?>
            </div>
        </div>
      </div>

      <div class="tab-pane" id="en">
        <?= $form->field($model, 'assistance_en')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'franchise_en')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'limitation_en')->textarea(['rows' => 6]) ?>

        <div class="row">
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'rulesFileEn')->fileInput();
                    
                if($model->rules_en):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/travel_info/<?=$model->rules_en?>">Rules.pdf</a>
                <?php
                endif;
                ?>
            </div>
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'policyFileEn')->fileInput();
                    
                if($model->policy_example_en):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/travel_info/<?=$model->policy_example_en?>">Policy.pdf</a>
                <?php
                endif;
                ?>
            </div>
        </div>
      </div>
  </div>

    

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>