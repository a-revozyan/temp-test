<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\Partner */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="partner-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'imageFile')->widget(FileInput::classname(), [
            'options' => ['accept' => 'image/*'],
            'language' => 'ru',
            'pluginOptions' => [
                'showCaption' => false,
                'showRemove' => true,
                'showUpload' => true,
                'browseClass' => 'btn btn-primary',
                // 'browseIcon' => ' ',
                'browseLabel' => 'Изменить',
                'layoutTemplates' => [
                    'main1' => '<div class="kv-upload-progress hide"></div>{upload}{browse}{preview}',
                ],
                'initialPreview' => [
                    Html::img('../../uploads/partners/'.$model->image, ['class' => 'file-preview-image img-responsive', 'alt' => '', 'title' => '']),
                ],
            ],

        ]) ?>

    <?= $form->field($model, 'status')->radioList( [1=>'Active', 0 => 'Passive'] ); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
