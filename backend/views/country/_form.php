<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Country;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\web\UploadedFile;
use kartik\file\FileInput;
/* @var $this yii\web\View */
/* @var $model common\models\Country */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="country-form">

   <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'name_ru')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_uz')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'schengen')->checkbox() ?>

    <?php
    if(!is_null($model->id)) {
        $countries = Country::find()->where(['!=', 'id', $model->id])->asArray()->all();
    } else {
        $countries = Country::find()->asArray()->all();
    }

    echo $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($countries, 'id', 'name_ru'), ['prompt' => '- select -'])->label('Parent');
    ?>


    <?php 
        echo $form->field($model, 'imageFile')->widget(FileInput::classname(), [
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
                    Html::img('../../uploads/countries/'.$model->image, ['class' => 'file-preview-image img-responsive', 'alt' => '', 'title' => '']),
                ],
            ],
        ]);
    ?>

    <div class="row">
        <div class="col-md-6">
        <?=$form->field($model, "code")->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => 'AA'
            ])?>
        </div>
        <div class="col-md-6">
            <?=$form->field($model, "order")->textInput(['type' => 'number', 'step' => 0.1])?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
