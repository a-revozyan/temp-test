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

$partner_product = PartnerProduct::find()->where(['product_id' => 3, 'partner_id' => $model->partner_id])->one();

$this->title = Yii::t('app', 'Заполните персональные данные');

$this->registerJs("  
  $('.clear1').hide();

  $('.load1').click(function() {
    getPassData();
  });

  function getPassData() {
    var pass_series = $('#travel-insurer_passport_series').val(),
        pass_number = $('#travel-insurer_passport_number').val(),
        pinfl = $('#travel-insurer_pinfl').val();

    if(pass_series && pass_number && pinfl) {
      if(pinfl) $('.field-travel-insurer_pinfl').removeClass('has-error'); 
      if(pass_number) $('.field-travel-insurer_passport_number').removeClass('has-error'); 
      if(pass_series) $('.field-travel-insurer_passport_series').removeClass('has-error'); 

      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-pass-data') . "',
        data: {pass_series: nvl(pass_series), pass_number: nvl(pass_number), pinfl: nvl(pinfl)},
        dataType: 'json',
        success: function(result) {
          if(result) {
            $('#travel-insurer_name').val(result.name);
            $('#travel-insurer_birthday').val(result.birthday);
            $('#travel-insurer_address').val(result.address);

            /*if(result.address) $('#travel-insurer_address').attr('readonly', 'readonly');
            if(result.name) $('#travel-insurer_name').attr('readonly', 'readonly');
            if(result.birthday) $('#travel-insurer_birthday').attr('readonly', 'readonly');*/

          } else {
            $('#travel-insurer_address').removeAttr('readonly');
            $('#travel-insurer_name').removeAttr('readonly');
            $('#travel-insurer_birthday').removeAttr('readonly');
          }
        },
      });
    } else {  
      if(!pinfl) $('.field-travel-insurer_pinfl').addClass('has-error'); 
      if(!pass_number) $('.field-travel-insurer_passport_number').addClass('has-error'); 
      if(!pass_series) $('.field-travel-insurer_passport_series').addClass('has-error'); 

      $('#travel-insurer_address').removeAttr('readonly');
      $('#travel-insurer_name').removeAttr('readonly');
      $('#travel-insurer_birthday').removeAttr('readonly');

      $('#travel-insurer_name').val('');
      $('#travel-insurer_birthday').val('');
      $('#travel-insurer_address').val('');
    }
  }

  $('#travel-insurer_passport_series').blur(function() {
    getPinfl();
  });

  $('#travel-insurer_passport_number').blur(function() {
    getPinfl();
  });

  function getPinfl() {
    var pass_series = $('#travel-insurer_passport_series').val(),
        pass_number = $('#travel-insurer_passport_number').val();

    if(pass_series && pass_number) {
      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-pinfl') . "',
        data: {pass_series: nvl(pass_series), pass_number: nvl(pass_number)},
        dataType: 'json',
        success: function(result) {
          if(result) {
            $('#travel-insurer_pinfl').val(result.pinfl);
            $('#travel-insurer_name').val(result.name);
            $('#travel-insurer_birthday').val(result.birthday);
            $('#travel-insurer_address').val(result.address);

            /*if(result.pinfl) $('#travel-insurer_pinfl').attr('readonly', 'readonly');
            if(result.address) $('#travel-insurer_address').attr('readonly', 'readonly');
            if(result.name) $('#travel-insurer_name').attr('readonly', 'readonly');
            if(result.birthday) $('#travel-insurer_birthday').attr('readonly', 'readonly');*/
          } else {
            $('#travel-insurer_pinfl').removeAttr('readonly');
            $('#travel-insurer_address').removeAttr('readonly');
            $('#travel-insurer_name').removeAttr('readonly');
            $('#travel-insurer_birthday').removeAttr('readonly');
          }
        },
      });
    } else {  
      $('#travel-insurer_pinfl').removeAttr('readonly');
      $('#travel-insurer_address').removeAttr('readonly');
      $('#travel-insurer_name').removeAttr('readonly');
      $('#travel-insurer_birthday').removeAttr('readonly');
    }
  }

  $('.isinsurer').change(function() {
    if($(this).is(':checked')) {
      var order = $(this).attr('id').slice(-1);

      $('#travel-insurer_name').val($('#traveler-'+order+'-name').val());
      $('#travel-insurer_address').val($('#traveler-'+order+'-address').val());
      $('#travel-insurer_phone').val($('#traveler-'+order+'-phone').val());
      $('#travel-insurer_birthday').val($('#traveler-'+order+'-birthday').val());
      $('#travel-insurer_passport_series').val($('#traveler-'+order+'-passport_series').val());
      $('#travel-insurer_passport_number').val($('#traveler-'+order+'-passport_number').val());

      $('.isinsurer').not('#'+$(this).attr('id')).each(function(){
        $(this).prop('checked', false);
      });
    }
  });
  

  $(document).ajaxStart(function(){
    $('.preloader').fadeIn();
  });
  $(document).ajaxComplete(function(){
    $('.preloader').fadeOut();
  });

  jQuery('.parents .dynamicform_wrapper').on('afterInsert', function(e, item) {
    jQuery('.parents .dynamicform_wrapper .driver-title').each(function(index) {
        jQuery(this).html('" . Yii::t('app', 'Родитель') . ": ' + (index + 1))
    });
  });

  jQuery('.parents .dynamicform_wrapper').on('afterDelete', function(e) {
    jQuery('.parents .dynamicform_wrapper .driver-title').each(function(index) {
        jQuery(this).html('" . Yii::t('app', 'Родитель') . ": ' + (index + 1))
    });
  });

  jQuery('.children .dynamicform_wrapper').on('afterInsert', function(e, item) {
    jQuery('.children .dynamicform_wrapper .driver-title').each(function(index) {
        jQuery(this).html('" . Yii::t('app', 'Ребёнок') . ": ' + (index + 1))
    });
  });

  jQuery('.children .dynamicform_wrapper').on('afterDelete', function(e) {
    jQuery('.children .dynamicform_wrapper .driver-title').each(function(index) {
        jQuery(this).html('" . Yii::t('app', 'Ребёнок') . ": ' + (index + 1))
    });
  });
", \yii\web\View::POS_END);

$countries = \common\models\Country::find()->where(['id' => $model->countries])->all();
if($model->extraInsurances) $extraInsurances = \common\models\TravelExtraInsurance::find()->where(['id' => $model->extraInsurances])->all();

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
            <p><?=Yii::t('app','Укажите детали поездки')?></p>
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
            <h6><span class="normal-bold"><?= Yii::t('app', 'Countries: ')?></span><?php
            foreach($countries as $i => $c) {
              if($i != 0) echo ", ";
              if(Yii::$app->language == 'ru') {
                echo $c->name_ru;
              } elseif(Yii::$app->language == 'uz') {
                echo $c->name_uz;
              } elseif(Yii::$app->language == 'en') {
                echo $c->name_en;
              }
            }
            ?></h6>
            <h6><span class="normal-bold"><?= Yii::t('app', 'Period: ')?></span><?=$model->begin_date . ' - ' . $model->end_date?></h6>
            <h6><span class="normal-bold"><?= Yii::t('app', 'Purpose: ')?></span><?php 
            if(Yii::$app->language == 'ru') {
              echo $model->purpose->name_ru;
            } elseif(Yii::$app->language == 'uz') {
              echo $model->purpose->name_uz;
            } elseif(Yii::$app->language == 'en') {
              echo $model->purpose->name_en;
            }
            ?></h6>
            <?php
            if($model->extraInsurances):
            ?>
            <h6><span class="normal-bold"><?= Yii::t('app', 'Extra: ')?></span><?php
            foreach($extraInsurances as $i => $c) {
              if($i != 0) echo ", ";
              echo $c->name_ru;
            }
            ?></h6>
            <?php
            endif;
            ?>
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
        <?php $form = ActiveForm::begin(['id' => 'travel-form']);
        if($model->isFamily == 0) :
        ?>        
        <div class="container-items widget-area"><!-- widgetContainer -->
          <h4><?=Yii::t('app', 'Путешественники')?></h4>
          <?php foreach ($travelers as $i => $traveler): ?>
          <div class="item widget widget_categories mt-4"><!-- widgetBody -->
              <div class="widget-title">
                <h3 class="traveler-title"><?=Yii::t('app', 'Путешественник') . ': ' . ($i+1) ?></h3>
              </div>
              <div class="post-wrap">
                <div class="form-row">
                  <div class="col-md-4">
                    <?=$form->field($traveler, "[{$i}]name")->textInput()->label(Yii::t('app', 'Ф.И.О. (латиницей)'))?>
                  </div>
                  <div class="col-md-3">
                    <?=$form->field($traveler, "[{$i}]birthday")->textInput(['readonly' => true])->label(Yii::t('app', 'Дата рождения'))?>
                  </div>
                  <div class="col-md-3">
                    <div class="form-row">
                      <div class="col-md-12"><label class="control-label"><?=Yii::t('app', 'Паспортные данные')?></label></div>
                      <div class="col-4"><?=$form->field($traveler, "[{$i}]passport_series")->textInput([
                                          'maxlength' => 2, 'class' => 'form-control text-uppercase'
                                      ])->label(false)?></div>
                      <div class="col-8"><?=$form->field($traveler, "[{$i}]passport_number")->widget(\yii\widgets\MaskedInput::className(), [
                          'mask' => '9999999', 
                      ])->label(false)?></div>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <?php echo $form->field($traveler, "[{$i}]isInsurer", ['template' =>
                        "<label class='control-label'>&nbsp;</label><div class='custom-control custom-checkbox mr-sm-2'>
                                <input type='checkbox' class='custom-control-input isinsurer' id='traveler-isinsurer-".$i."' name='Traveler[".$i."][isInsurer]' value='1'><label class='custom-control-label' for='traveler-isinsurer-".$i."'>".Yii::t('app', 'Страхователь') ."</label>
                              </div>"])->checkbox();?>
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
                        <div class="col-4"><?=$form->field($model, 'insurer_passport_series')->textInput([
                                            'maxlength' => 2, 'class' => 'form-control text-uppercase'
                                        ])->label(false)?></div>
                        <div class="col-8"><?=$form->field($model, 'insurer_passport_number')->widget(\yii\widgets\MaskedInput::className(), [
                          'mask' => '9999999', 
                        ])->label(false)?></div>
                      </div>
                    </div>
                  </div>

                  <div class="form-row info">
                    <div class="col-md-12">
                      <?=$form->field($model, 'address_delivery')->textInput()->label(Yii::t('app', 'Адрес доставки'))?>
                    </div>
                  </div>

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

        <?php
        else :
        ?>
        <div class="widget-area"><!-- widgetContainer -->
          <div class="parents blog-details-area">
            <div class="">
          <?php DynamicFormWidget::begin([
              'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
              'widgetBody' => '.container-items', // required: css class selector
              'widgetItem' => '.item1', // required: css class
              'limit' => 2, // the maximum times, an element can be cloned (default 999)
              'min' => 1, // 0 or 1 (default 1)
              'insertButton' => '.add-item', // css class
              'deleteButton' => '.remove-item', // css class
              'model' => $parents[0],
              'id' => 'dynamic-form',
              'formId' => 'travel-form',
              'formFields' => [
                  'name',
                  'passport_number',
                  'passport_series',
                  'birthday',
              ],
          ]); ?>

          <div class="container-items widget-area"><!-- widgetContainer -->
                <?php foreach ($parents as $i => $p): ?>
                    <div class="item1 item widget widget_categories mt-4"><!-- widgetBody -->
                        <div class="widget-title">
                          <div class="row">
                            <h3 class="col driver-title"><?=Yii::t('app', 'Родитель') . ': ' . ($i+1) ?></h3>
                            <div class="col text-right">
                                <button type="button" class="add-item mybtn success-btn pl-3 pr-3"><i class="bx bx-plus"></i></button>
                                <button type="button" class="remove-item mybtn danger-btn pl-3 pr-3"><i class="bx bx-minus"></i></button>
                            </div>
                          </div>
                        </div>
                        <div class="post-wrap">
                            <div class="row">
                              <div class="col-lg-4">
                                <?= $form->field($p, "[{$i}]name")->textInput(['maxlength' => true])->label(Yii::t('app', 'Ф.И.О. (латиницей)')) ?>
                              </div>
                              <div class="col-md-4">
                                <div class="form-row">
                                  <div class="col-md-12"><label class="control-label"><?=Yii::t('app', 'Паспортные данные')?></label></div>
                                  <div class="col-md-4"><?= $form->field($p, "[{$i}]passport_series")->textInput([
                                      'maxlength' => 2, 'class' => 'form-control text-uppercase'
                                  ])->label(false) ?></div>
                                  <div class="col-md-8"><?= $form->field($p, "[{$i}]passport_number")->widget(\yii\widgets\MaskedInput::className(), [
                                      'mask' => '9999999',
                                  ])->label(false) ?></div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <?php 
                                echo $form->field($p, "[{$i}]birthday")->widget(MaskedInput::className(), [
                                  'clientOptions' => [
                                      'alias' => 'dd.mm.yyyy',
                                      "placeholder" => Yii::t('app', "dd.mm.yyyy"),
                                  ]
                                ])->label(Yii::t('app', 'Дата рождения'));
                                ?> 
                              </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); 
                ?>
            </div>
      </div>

        <div class="children blog-details-area">
            <div class="">
          <?php DynamicFormWidget::begin([
              'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
              'widgetBody' => '.container-items2', // required: css class selector
              'widgetItem' => '.item2', // required: css class
              'limit' => 4, // the maximum times, an element can be cloned (default 999)
              'min' => 1, // 0 or 1 (default 1)
              'insertButton' => '.add-item2', // css class
              'deleteButton' => '.remove-item2', // css class
              'model' => $children[0],
              'id' => 'dynamic-form2',
              'formId' => 'travel-form',
              'formFields' => [
                  'name',
                  'passport_number',
                  'passport_series',
                  'birthday',
              ],
          ]); ?>

          <div class="container-items2 widget-area"><!-- widgetContainer -->
                <?php foreach ($children as $i => $p): ?>
                    <div class="item2 item widget widget_categories mt-4"><!-- widgetBody -->
                        <div class="widget-title">
                          <div class="row">
                            <h3 class="col driver-title"><?=Yii::t('app', 'Ребёнок') . ': ' . ($i+1) ?></h3>
                            <div class="col text-right">
                                <button type="button" class="add-item2 mybtn success-btn pl-3 pr-3"><i class="bx bx-plus"></i></button>
                                <button type="button" class="remove-item2 mybtn danger-btn pl-3 pr-3"><i class="bx bx-minus"></i></button>
                            </div>
                          </div>
                        </div>
                        <div class="post-wrap">
                            <div class="row">
                              <div class="col-lg-4">
                                <?= $form->field($p, "[{$i}]name")->textInput(['maxlength' => true])->label(Yii::t('app', 'Ф.И.О. (латиницей)')) ?>
                              </div>
                              <div class="col-md-4">
                                <div class="form-row">
                                  <div class="col-md-12"><label class="control-label"><?=Yii::t('app', 'Паспортные данные')?></label></div>
                                  <div class="col-md-4"><?= $form->field($p, "[{$i}]passport_series")->textInput([
                                      'maxlength' => 2, 'class' => 'form-control text-uppercase'
                                  ])->label(false) ?></div>
                                  <div class="col-md-8"><?= $form->field($p, "[{$i}]passport_number")->widget(\yii\widgets\MaskedInput::className(), [
                                      'mask' => '9999999',
                                  ])->label(false) ?></div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <?php 
                                echo $form->field($p, "[{$i}]birthday")->widget(MaskedInput::className(), [
                                  'clientOptions' => [
                                      'alias' => 'dd.mm.yyyy',
                                      "placeholder" => Yii::t('app', "dd.mm.yyyy"),
                                  ]
                                ])->label(Yii::t('app', 'Дата рождения'));
                                ?> 
                              </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); 
                ?>
            </div>
      </div>

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
                        <div class="col-4"><?=$form->field($model, 'insurer_passport_series')->textInput([
                                            'maxlength' => 2, 'class' => 'form-control text-uppercase'
                                        ])->label(false)?></div>
                        <div class="col-8"><?=$form->field($model, 'insurer_passport_number')->widget(\yii\widgets\MaskedInput::className(), [
                          'mask' => '9999999', 
                        ])->label(false)?></div>
                      </div>
                    </div>
                  </div>

                  <div class="form-row info mt-lg-0 mt-3">
                    <div class="col-md-12">
                      <?=$form->field($model, 'address_delivery')->textInput()->label(Yii::t('app', 'Адрес доставки'))?>
                    </div>
                  </div>

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

        <?php
        endif;
        ?>




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
