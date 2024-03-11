<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap4\Accordion;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use common\models\Relationship;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use common\models\PartnerProduct;
use kartik\file\FileInput;

$partner_product = PartnerProduct::find()->where(['product_id' => 4, 'partner_id' => $model->partner_id])->one();

$this->title = Yii::t('app', 'Заполните персональные данные');

$this->registerJs("
  $('.isinsurer').change(function() {
    if($(this).is(':checked')) {
      var order = $(this).attr('id').slice(-1);

      $('#accident-insurer_name').val($('#accidentinsurer-'+order+'-name').val());
      $('#accident-insurer_birthday').val($('#accidentinsurer-'+order+'-birthday').val());
      $('#accident-insurer_passport_series').val($('#accidentinsurer-'+order+'-passport_series').val());
      $('#accident-insurer_passport_number').val($('#accidentinsurer-'+order+'-passport_number').val());

      $('.isinsurer').not('#'+$(this).attr('id')).each(function(){
        $(this).prop('checked', false);
      });
    }
  });



  function checkIdentity(value, id, type) {
    let order = id.substr(16, 1);
    if(type == 1) {
      let passport_series = $('#accidentinsurer-' + order + '-passport_series').val(),
          passport_number = $('#accidentinsurer-' + order + '-passport_number').val();

      if(passport_series || passport_number) {
        $('#accidentinsurer-' + order + '-identity_number').attr('readonly', 'readonly');
      } else  {
        $('#accidentinsurer-' + order + '-identity_number').removeAttr('readonly');
      }
    } else {
      if(value) {
        $('#accidentinsurer-' + order + '-passport_series').attr('readonly', 'readonly');
        $('#accidentinsurer-' + order + '-passport_number').attr('readonly', 'readonly');
      } else {
        $('#accidentinsurer-' + order + '-passport_series').removeAttr('readonly');
        $('#accidentinsurer-' + order + '-passport_number').removeAttr('readonly');
      }
    }
    
  }
  
", \yii\web\View::POS_END);


?>

<div class="container">
  <div class="row number-cont">
    <div class="col-3">
      <div class="number-box" style="padding-left: 15px;">
        <div class="row n-rows">
          <div class="numbers col-3">
            <h2>1</h2>
          </div>
          <div class="number-text col-9">
            <p><?=Yii::t('app','Выберите')?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="number-box">
        <div class="row n-rows">
          <div class="numbers col-3">
            <h2>2</h2>
          </div>
          <div class="number-text col-9">
            <p><?=Yii::t('app','Сравните предложения')?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="number-box" style="background-color: #dfdfdf;">
        <div class="row n-rows">
          <div class="numbers col-3">
            <h2>3</h2>
          </div>
          <div class="number-text col-9">
            <p><?=Yii::t('app','Введите ваши данные')?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="number-box">
        <div class="row n-rows">
          <div class="numbers col-3">
            <h2>4</h2>
          </div>
          <div class="number-text col-9">
            <p><?=Yii::t('app','Оплатите и получите полис')?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container mt-5 mb-5">

  <div class="row">
    <div class="col">
      <div class="alert alert-info p-4 mb-3">
        <div class="row">
          <div class="col-md-2">
            <img src="/uploads/partners/<?=$model->partner->image?>" class="img-fluid" />
          </div>
          <div class="col-md-3 offset-md-1">
            <h4 class=" text-dark"><?=Yii::t('app', 'Стоимость:') . "<br>". number_format($model->amount_uzs,0,","," ") . ' ' . Yii::t('app', 'сум') ?></h4>
          </div>
          <div class="col-md-6">
            <h6><span class="normal-bold"><?= Yii::t('app', 'Period: ')?></span><?=$model->begin_date . ' - ' . $model->end_date?></h6>
          </div>
        </div>
        <?php
          if(Yii::$app->language == 'ru'){
            $delivery_info = $partner_product->delivery_info_ru;
          } elseif(Yii::$app->language == 'uz') {
            $delivery_info = $partner_product->delivery_info_uz;
           
          } elseif(Yii::$app->language == 'en') {
            $delivery_info = $partner_product->delivery_info_en;
          }
        ?>
        <p class="text-danger">* <?=$delivery_info?></p>
      </div>
    	<div class="shadow bg-white p-4 blog-details-area">
    		<h4 class="text-center mb-4"><?=Yii::t('app','Заполните следующие данные')?></h4>
        <?php $form = ActiveForm::begin(['id' => 'accident-form']);
        ?>        
        <div class="container-items widget-area">
          <?php foreach ($insurers as $i => $insurer): ?>
          <div class="item widget widget_categories mt-4"><!-- widgetBody -->
              <div class="widget-title">
                <h3 class="traveler-title"><?=Yii::t('app', 'Застрахованное лицо') . ': ' . ($i+1) ?></h3>
              </div>
              <div class="post-wrap">
                <div class="form-row">
                  <div class="col-md-4">
                    <?=$form->field($insurer, "[{$i}]name")->textInput()->label(Yii::t('app', 'Ф.И.О. (латиницей)'))?>
                  </div>
                  <div class="col-md-3">
                    <?php 
                      echo $form->field($insurer, "[{$i}]birthday")->widget(MaskedInput::className(), [
                        'clientOptions' => [
                            'alias' => 'dd.mm.yyyy',
                            "placeholder" => Yii::t('app', "dd.mm.yyyy"),
                        ]
                      ])->label(Yii::t('app', 'Дата рождения'));
                      ?> 
                  </div>
                  <div class="col-md-3">
                    <?php
                    echo $form->field($insurer, "[{$i}]passFile")->widget(FileInput::classname(), [
                      'language' => Yii::$app->language,
                      'pluginOptions' => [
                          'showPreview' => false,
                          'showCaption' => true,
                          'showRemove' => true,
                          'showUpload' => false,
                          'browseClass' => 'btn mybtn w-100',
                          'browseIcon' => ' ',
                          'removeLabel' => '',
                          'removeIcon' => '<i class="fa fa-trash"></i>',
                          'browseLabel' => Yii::t('app', 'Загрузить'),
                          'dropZoneTitle' => Yii::t('app', 'Перетащите файлы сюда...'),
                          // 'layoutTemplates' => [
                          //     'main1' => '<div class="kv-upload-progress hide"></div>{upload}{browse}{preview}',
                          // ],
                      ],

                    ])->label(Yii::t('app', 'Загрузить пасспорт'));
                    ?>
                  </div>
                  <div class="col-md-2">
                    <?php echo $form->field($insurer, "[{$i}]isInsurer", ['template' =>
                        "<label class='control-label'>&nbsp;</label><div class='custom-control custom-checkbox mr-sm-2'>
                                <input type='checkbox' class='custom-control-input isinsurer' id='insurer-isinsurer-".$i."' name='AccidentInsurer[".$i."][isInsurer]' value='1'><label class='custom-control-label' for='insurer-isinsurer-".$i."'>".Yii::t('app', 'Страхователь') ."</label>
                              </div>"])->checkbox();?>
                  </div>                  
                  <div class="col-md-4">
                    <div class="form-row">
                      <div class="col-md-12"><label class="control-label"><?=Yii::t('app', 'Паспортные данные')?></label></div>
                      <div class="col-4"><?=$form->field($insurer, "[{$i}]passport_series")->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => 'AA',
                        'options' => [
                          'onchange' => 'checkIdentity(this.value, this.id, 1)'
                        ]  
                      ])->label(false)?></div>
                      <div class="col-8"><?=$form->field($insurer, "[{$i}]passport_number")->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '9999999', 
                        'options' => [
                          'onchange' => 'checkIdentity(this.value, this.id, 1)'
                        ]  
                      ])->label(false)?></div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <?php
                    echo $form->field($insurer, "[{$i}]identity_number")->textInput(['onchange' => 'checkIdentity(this.value, this.id, 2)'])->label(Yii::t('app', 'Номер свидетельства о рождении'));
                    ?>
                  </div>
                </div>
              </div>
          </div>
          <?php endforeach; ?>

          <div class="item widget widget_categories mt-4"><!-- widgetBody -->
              <div class="widget-title">
                <h3 class="traveler-title"><?=Yii::t('app', 'Страхователь') ?></h3>
              </div>
              <div class="post-wrap">
                  <div class="form-row">
                    <div class="col-md-4">                      
                      <?=$form->field($model, 'insurer_name')->textInput()->label(Yii::t('app', "Фамилия, имя, отчество (латиницей)"))?>
                    </div>
                    <div class="col-md-4">
                      <?php 
                      echo $form->field($model, "insurer_birthday")->widget(MaskedInput::className(), [
                        'clientOptions' => [
                            'alias' => 'dd.mm.yyyy',
                            "placeholder" => Yii::t('app', "dd.mm.yyyy"),
                        ]
                      ])->label(Yii::t('app', 'Дата рождения'));
                      ?> 
                    </div>
                    <div class="col-md-4">
                      <div class="form-row">
                        <div class="col-md-12"><label class="control-label"><?=Yii::t('app', 'Паспортные данные')?></label></div>
                        <div class="col-4"><?=$form->field($model, 'insurer_passport_series')->widget(\yii\widgets\MaskedInput::className(), [
                          'mask' => 'AA', 
                        ])->label(false)?></div>
                        <div class="col-8"><?=$form->field($model, 'insurer_passport_number')->widget(\yii\widgets\MaskedInput::className(), [
                          'mask' => '9999999', 
                        ])->label(false)?></div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <?php
                    echo $form->field($model, "passFile")->widget(FileInput::classname(), [
                      'language' => Yii::$app->language,
                      'pluginOptions' => [
                          'showPreview' => false,
                          'showCaption' => true,
                          'showRemove' => true,
                          'showUpload' => false,
                          'browseClass' => 'btn mybtn w-100',
                          'browseIcon' => ' ',
                          'removeLabel' => '',
                          'removeIcon' => '<i class="fa fa-trash"></i>',
                          'browseLabel' => Yii::t('app', 'Загрузить'),
                          'dropZoneTitle' => Yii::t('app', 'Перетащите файлы сюда...'),
                          // 'layoutTemplates' => [
                          //     'main1' => '<div class="kv-upload-progress hide"></div>{upload}{browse}{preview}',
                          // ],
                      ],

                    ])->label(Yii::t('app', 'Загрузить пасспорт'));
                    ?>
                    </div>
                  </div>

                  <!-- <div class="form-row info">
                    <div class="col-md-12">
                      <?=$form->field($model, 'address_delivery')->textInput()->label(Yii::t('app', 'Адрес доставки'))?>
                    </div>
                  </div> -->

                  <div class="form-row info">
                    <div class="col-md-5">
                      <?= $form->field($model, "insurer_phone")->widget(\yii\widgets\MaskedInput::className(), ['mask' => '+\9\98(99)-999-99-99',])->label(Yii::t('app', 'Номер телефона')) ?>
                    </div>
                    <?php
                    if($model->partner_id == 1) :
                    ?>
                    <div class="col-md-5 offset-md-2">
                      <?= $form->field($model, "insurer_email")->textInput()->label(Yii::t('app', 'Email')) ?>
                    </div>
                    <?php
                    endif;
                    ?>
                  </div>
              </div>
          </div>
        </div>

        
      <div class="row submit-box">
        <div class="col-lg-9">
          <?php
          if(Yii::$app->language == 'ru'){
            $public_offer =$partner_product->public_offer_ru;
            $conditions= $partner_product->conditions_ru;
          }
          elseif (Yii::$app->language == 'uz') {
            $public_offer =$partner_product->public_offer_uz;
            $conditions= $partner_product->conditions_uz;
           
          }
          elseif (Yii::$app->language == 'en') {
            $public_offer =$partner_product->public_offer_en;
            $conditions= $partner_product->conditions_en;
           
          }
          ?>
          <p><?=Yii::t('app', 'Нажимая кнопку "Перейти к оплате", я подтверждаю свою дееспособность, принимаю {link1} и подтверждаю свое согласие {link2}', ['link1' => "<a href='/uploads/offer/".$public_offer ."'  target='_blank'>".Yii::t('app', 'условия страхования')."</a>", 'link2' => "<a href='/uploads/conditions/".$conditions ."' target='_blank'>".Yii::t('app', 'на обработку персональных данных')."</a>"])?></p>
        </div>
        <div class="col-lg-3">
          <?php
            echo Html::submitButton(Yii::t('app', 'Перейти к оплате') . '&nbsp;&nbsp;<i class="bx bx-chevrons-right"></i>', ['class' => 'mybtn submit-button']);
          ?>
        </div>
      </div>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>

</div>



<div class="modal fade" id="pinfl-modal">
  <div class="modal-dialog">
    <div class="modal-content">
    
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title"><?=Yii::t('app', 'ПИНФЛ (ЖШШИР)')?></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      
      <!-- Modal body -->
      <div class="modal-body">
        <?php if (Yii::$app->language == 'ru') : ?>
        <img class="img-fluid" src="/img/pinfl_ru.jpg" />
        <?php elseif (Yii::$app->language == 'uz') : ?>
        <img class="img-fluid" src="/img/pinfl_uz.jpg" />
        <?php else : ?>
        <img class="img-fluid" src="/img/pinfl_ru.jpg" />
        <?php endif; ?>
      </div>
      
    </div>
  </div>
</div>
