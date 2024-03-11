<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use kartik\file\FileInput;
use kartik\date\DatePicker;
use common\models\Category;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model common\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'status')->radioList( [1=>'Active', 0 => 'Passive'] ); ?>

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


    <div class="tab-content">
      <div class="tab-pane active" id="ru">
        <?= $form->field($model, 'title_ru')->textInput(['maxlength' => true]) ?>

        <?php
        echo $form->field($model, 'imageFileRu')->widget(FileInput::classname(), [
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
                    Html::img('../../uploads/cnews/'.$model->image_ru, ['class' => 'file-preview-image img-responsive', 'alt' => '', 'title' => '']),
                ],
            ],

        ]);
        ?>

        <?= $form->field($model, 'short_info_ru')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'body_ru')->widget(CKEditor::className(), [
            'options' => ['rows' => 6],
            'preset' => 'full'
        ]) ?>
      </div>
      <div class="tab-pane" id="en">
        <?= $form->field($model, 'title_en')->textInput(['maxlength' => true]) ?>

        <?php
        echo $form->field($model, 'imageFileEn')->widget(FileInput::classname(), [
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
                    Html::img('../../uploads/cnews/'.$model->image_en, ['class' => 'file-preview-image img-responsive', 'alt' => '', 'title' => '']),
                ],
            ],

        ]);
        ?>

        <?= $form->field($model, 'short_info_en')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'body_en')->widget(CKEditor::className(), [
            'options' => ['rows' => 6],
            'preset' => 'full'
        ]) ?>
      </div>
      <div class="tab-pane" id="uz">
        <?= $form->field($model, 'title_uz')->textInput(['maxlength' => true]) ?>

        <?php
        echo $form->field($model, 'imageFileUz')->widget(FileInput::classname(), [
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
                    Html::img('../../uploads/cnews/'.$model->image_uz, ['class' => 'file-preview-image img-responsive', 'alt' => '', 'title' => '']),
                ],
            ],

        ]);
        ?>

        <?= $form->field($model, 'short_info_uz')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'body_uz')->widget(CKEditor::className(), [
            'options' => ['rows' => 6],
            'preset' => 'full'
        ]) ?>
      </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
