<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Automodel;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Autocomp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="autocomp-form">

    <?php $form = ActiveForm::begin(); 

    $automodels = Automodel::find()->orderBy('name')->all();

    echo $form->field($model, 'automodel_id')->dropDownList(ArrayHelper::map($automodels, 'id', 'name'))->label('Automodel');
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
