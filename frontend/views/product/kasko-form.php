<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap4\Accordion;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\file\FileInput;
use common\models\PartnerProduct;

$partner_product = PartnerProduct::find()->where(['product_id' => 2, 'partner_id' => $model->partner_id])->one();

$this->title = Yii::t('app', 'Заполните персональные данные');
$this->registerJs("
  $('.clear1').hide();
  $('.clear2').hide();
  $('.person-info').hide();
  $('.contact-info').hide();
  $('.upload-passport').hide();
  $('.submit-box').hide();

  $('.load1').click(function() {
    getTechData();
  });

  $('.clear1').click(function() {
    $('#kasko-insurer_name').val('');
    $('#kasko-insurer_name').removeAttr('readonly');
    $('#kasko-insurer_pinfl').val('');
    $('#kasko-insurer_pinfl').removeAttr('readonly');
    
    $('.load1').show();
    $('.clear1').hide();
  });

  $('.load2').click(function() {
    getPassData();
  });

  $('.clear2').click(function() {
    $('#kasko-insurer_address').val('');
    
    $('.load2').show();
    $('.clear2').hide();

    $('#kasko-insurer_pinfl').removeAttr('readonly');
    $('#kasko-insurer_address').removeAttr('readonly');
  });

  function getTechData() {
    var tech_series = $('#kasko-insurer_tech_pass_series').val(),
        tech_number = $('#kasko-insurer_tech_pass_number').val(),
        autonumber = $('#kasko-autonumber').val();

    if(tech_series && tech_number && autonumber) {
      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-tech-pass-data') . "',
        data: {tech_series: nvl(tech_series), tech_number: nvl(tech_number), autonumber: nvl(autonumber)},
        dataType: 'json',
        timeout: 3000,
        error: function() {
          $('#kasko-insurer_name').val('');
          $('#kasko-insurer_name').removeAttr('readonly');
          $('#kasko-insurer_pinfl').val('');
          $('#kasko-insurer_pinfl').removeAttr('readonly');

          $('.clear1').hide();
          $('.load1').show();
          
          $('.person-info').show();
        },
        success: function(result) {
          if(result) {
            $('#kasko-insurer_name').val(result.name);
            if(result.name) $('#kasko-insurer_name').attr('readonly', 'readonly');
            $('#kasko-insurer_pinfl').val(result.pinfl);
            if(result.pinfl) $('#kasko-insurer_pinfl').attr('readonly', 'readonly');

            $('.clear1').show();
            $('.load1').hide();
          } else {            
            $('#kasko-insurer_name').val('');
            $('#kasko-insurer_name').removeAttr('readonly');
            $('#kasko-insurer_pinfl').val('');
            $('#kasko-insurer_pinfl').removeAttr('readonly');

            $('.clear1').hide();
            $('.load1').show();
          }
          $('.person-info').show();
        },
      });
    } else {     
      if(!tech_series) $('.field-kasko-insurer_tech_pass_series').addClass('has-error'); 
      if(!tech_number) $('.field-kasko-insurer_tech_pass_number').addClass('has-error'); 
      if(!autonumber) $('.field-kasko-autonumber').addClass('has-error'); 

      $('#kasko-insurer_name').val('');
      $('#kasko-insurer_name').removeAttr('readonly');
      $('#kasko-insurer_pinfl').val('');
      $('#kasko-insurer_pinfl').removeAttr('readonly');
    }
  }

  function getPassData() {
    var pass_series = $('#kasko-insurer_passport_series').val(),
        pass_number = $('#kasko-insurer_passport_number').val(),
        pinfl = $('#kasko-insurer_pinfl').val();

    if(pass_series && pass_number && pinfl) {
      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-pass-data') . "',
        data: {pass_series: nvl(pass_series), pass_number: nvl(pass_number), pinfl: nvl(pinfl)},
        dataType: 'json',
        timeout: 3000,
        error: function() {
          $('#kasko-insurer_address').val('');

          $('#kasko-insurer_address').removeAttr('readonly');

          $('.clear2').hide();
          $('.load2').show();

          $('.contact-info').show();
          $('.upload-passport').show();
          $('.submit-box').show();
        },
        success: function(result) {
          if(result) {
            $('#kasko-insurer_address').val(result.address);

            if(result.address) $('#kasko-insurer_address').attr('readonly', 'readonly');

            $('.clear2').show();
            $('.load2').hide();
          } else {            
            $('#kasko-insurer_address').val('');

            $('#kasko-insurer_address').removeAttr('readonly');

            $('.clear2').hide();
            $('.load2').show();
          }

          $('.contact-info').show();
          $('.upload-passport').show();
          $('.submit-box').show();
        },
      });
    } else {    
      if(!pinfl) $('.field-kasko-insurer_pinfl').addClass('has-error'); 
      if(!pass_number) $('.field-kasko-insurer_passport_number').addClass('has-error'); 
      if(!pass_series) $('.field-kasko-insurer_passport_series').addClass('has-error'); 

      $('#kasko-insurer_address').val('');
    }
  }

  $('#kasko-insurer_passport_series').blur(function() {
    getPinfl();
  });

  $('#kasko-insurer_passport_number').blur(function() {
    getPinfl();
  });

  function getPinfl() {
    var pass_series = $('#kasko-insurer_passport_series').val(),
        pass_number = $('#kasko-insurer_passport_number').val();

    if(pass_series && pass_number) {
      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-pinfl') . "',
        data: {pass_series: nvl(pass_series), pass_number: nvl(pass_number)},
        dataType: 'json',
        timeout: 3000,
        success: function(result) {
          if(result) {
            $('#kasko-insurer_pinfl').val(result.pinfl);
            $('#kasko-insurer_address').val(result.address);

            if(result.pinfl) {          
              $('.contact-info').show();
              $('.upload-passport').show();
              $('.submit-box').show();
            }
          } else {            
            // $('#kasko-pinfl').val('');
            // $('#kasko-address').val('');
          }


        },
      });
    } else {  
      // $('#kasko-pinfl').val('');
      // $('#kasko-address').val('');
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
          <p><?=Yii::t('app','Выберите Ваше авто')?></p>
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

<div class="container mt-5 mb-4">
  <div class="alert alert-info p-4 mb-3">
    <div class="row">
      <div class="col-md-2">
        <img src="/uploads/partners/<?=$model->partner->image?>" class="img-fluid" />
      </div>
      <div class="col-md-3 offset-md-1">
        <h4 class=" text-dark"><?=Yii::t('app', 'Стоимость:') . "<br>". number_format($model->amount_uzs,0,","," ") . ' ' . Yii::t('app', 'сум') ?></h4>
      </div>
      <div class="col-md-6">
        <?php 
        if($model->autobrand_id == 0):
        ?>
        <h6><span class="normal-bold"><?= Yii::t('app', 'Марка: ')?></span><?=Yii::t('app', 'Others')?></h6>
        <?php 
        else:
        ?>
        <h6><span class="normal-bold"><?= Yii::t('app', 'Марка: ')?></span><?=$model->autocomp->automodel->autobrand->name?></h6>
        <h6><span class="normal-bold"><?= Yii::t('app', 'Модель: ')?></span><?=$model->autocomp->automodel->name?></h6>
        <h6><span class="normal-bold"><?= Yii::t('app', 'Комплектация: ')?></span><?=$model->autocomp->name?></h6>
        <h6><span class="normal-bold"><?= Yii::t('app', 'Год: ')?></span><?=$model->year?></h6>
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

  <div class="shadow bg-white p-4">
    <h4 class="text-center mb-4"><?=Yii::t('app', 'Заполните персональные данные')?></h4>

   <?php $form = ActiveForm::begin(); ?>
      <div class="form-row">
        <div class="col-md-12">
          <h5><?=Yii::t('app','Transport vositasining texnik pasport (qayd etish guvohnomasi) ma`lumoti:')?></h5>
        </div>
        <div class="col-md-5">
          <?=$form->field($model, 'autonumber')->textInput(['placeholder' => '00A000AA', 'class' => 'form-control text-uppercase'])->label(Yii::t('app', 'Номер автомобиля'))?>
        </div>
        <div class="col-md-5">
          <div class="form-row">
            <div class="col-md-12"><label class="control-label"><?=Yii::t('app', 'Технический паспорт ТС')?></label></div>
            <div class="col-4"><?=$form->field($model, 'insurer_tech_pass_series')->widget(\yii\widgets\MaskedInput::className(), [
              'mask' => 'AAA', 
      ])->label(false)?></div>
            <div class="col-8"><?=$form->field($model, 'insurer_tech_pass_number')->widget(\yii\widgets\MaskedInput::className(), [
          'mask' => '9999999', 
          // 'options' => [
          //   'onchange'=>'getData()'
          // ]
      ])->label(false)?></div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label class="control-label">&nbsp;</label>
            <button type="button" class="mybtn w-100 load1">
              <?=Yii::t('app', 'load data')?>
            </button>
            <button type="button" class="mybtn danger-btn w-100 clear1">
              <?=Yii::t('app', 'clear data')?>
            </button>
          </div>
        </div>
      </div>

      <div class="form-row person-info">
        <div class="col-md-12">
          <h5><?=Yii::t('app','Transport vositasining mulkdori haqida ma`lumot:')?></h5>
        </div>
        <div class="col-md-12">
          <?=$form->field($model, 'insurer_name')->textInput()->label(Yii::t('app', "Фамилия, имя, отчество (латиницей)"))?>
        </div>
        <div class="col-md-5">
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
        <div class="col-md-5">
          <?php
            echo $form->field($model, 'insurer_pinfl', [
              'template' => '<label class="control-label">'.Yii::t('app', 'ПИНФЛ').'</label><div class="input-group">{input}<div class="input-group-append">
                <button data-toggle="modal" data-target="#pinfl-modal" class="btn btn-outline-secondary" type="button" title="'.Yii::t('app', 'How to know PINFL').'"><i class="fa fa-question-circle"></i></button>
              </div></div>','inputOptions' => ['class' => 'form-control pinfl'],
          ])->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999999999999', 
              ]);
          ?>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label class="control-label">&nbsp;</label>
            <button type="button" class="mybtn w-100 load2">
              <?=Yii::t('app', 'load data')?>
            </button>
            <button type="button" class="mybtn danger-btn w-100 clear2">
              <?=Yii::t('app', 'clear data')?>
            </button>
          </div>
        </div>
      </div>

      <div class="form-row contact-info">
        <div class="col-md-6">
          <?=$form->field($model, 'insurer_address')->textInput()->label(Yii::t('app', 'Адрес (латиницей)'))?>
        </div>
        <div class="col-md-3">
          <?=$form->field($model, 'insurer_phone')->widget(\yii\widgets\MaskedInput::className(), [
          'mask' => '+\9\98(99)-999-99-99',
      ])->label(Yii::t('app', 'Номер телефона'))?>
        </div>        
        <div class="col-md-3">
          <?php 
          echo '<label class="control-label">'.Yii::t('app', 'Дата начала страхования').'</label>' . DatePicker::widget([
                    'model' => $model,
                    'attribute' => 'begin_date',
                    'name' => 'date',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy',
                        'startDate' => '+1d',
                    ],
                ]);
          ?> 
        </div>
      </div>

      <div class="form-row contact-info">
        <div class="col-md-12">
          <?=$form->field($model, 'address_delivery')->textInput()->label(Yii::t('app', 'Адрес доставки'))?>
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
        <?php if (Yii::$app->language == 'ru-RU') : ?>
        <img class="img-fluid" src="/images/pinfl_ru.jpg" />
        <?php elseif (Yii::$app->language == 'uz-UZ') : ?>
        <img class="img-fluid" src="/images/pinfl_uz.jpg" />
        <?php else : ?>
        <img class="img-fluid" src="/images/pinfl_en.jpg" />
        <?php endif; ?>
      </div>
      
    </div>
  </div>
</div>

