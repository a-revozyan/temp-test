<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Partner;
use common\models\Product;

/* @var $this yii\web\View */
/* @var $model common\models\PartnerProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="partner-product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $partners = Partner::find()->asArray()->all();

    echo $form->field($model, 'partner_id')->dropDownList(ArrayHelper::map($partners, 'id', 'name'), ['prompt' => '- select -'])->label('Partner');
    ?>

    <?php
    $products = Product::find()->asArray()->all();

    echo $form->field($model, 'product_id')->dropDownList(ArrayHelper::map($products, 'id', 'name'), ['prompt' => '- select -'])->label('Product');
    ?>

    <?= $form->field($model, 'percent')->textInput() ?>

    <?php
    
    echo $form->field($model, 'star')->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5], ['prompt' => '- select -'])->label('Star');
    ?>

    <?= $form->field($model, 'delivery_info_ru')->textInput() ?>

    <?= $form->field($model, 'delivery_info_uz')->textInput() ?>

    <?= $form->field($model, 'delivery_info_en')->textInput() ?>
    
    <div class="row">

            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'public_offer_ruFile')->fileInput();
                    
                if($model->public_offer_ru):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/offer/<?=$model->public_offer_ru?>">Rules.pdf</a>
                <?php
                endif;
                ?>
            </div>
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'public_offer_uzFile')->fileInput();
                    
                if($model->public_offer_uz):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/offer/<?=$model->public_offer_uz?>">Rules.pdf</a>
                <?php
                endif;
                ?>
            </div>
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'public_offer_enFile')->fileInput();
                    
                if($model->public_offer_en):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/offer/<?=$model->public_offer_en?>">Rules.pdf</a>
                <?php
                endif;
                ?>
            </div>

            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'conditions_ruFile')->fileInput();
                    
                if($model->conditions_ru):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/conditions/<?=$model->conditions_ru?>">Rules.pdf</a>
                <?php
                endif;
                ?>
            </div>
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'conditions_uzFile')->fileInput();
                    
                if($model->conditions_uz):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/conditions/<?=$model->conditions_uz?>">Rules.pdf</a>
                <?php
                endif;
                ?>
            </div>
            <div class="col-md-6" style="margin-bottom: 20px;">
                <?php
                echo $form->field($model, 'conditions_enFile')->fileInput();
                    
                if($model->conditions_en):
                ?>
                <a target="_blank" href="http://netkost.uz/uploads/conditions/<?=$model->conditions_en?>">Rules.pdf</a>
                <?php
                endif;
                ?>
            </div>

        </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
