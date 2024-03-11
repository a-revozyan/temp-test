<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap4\Accordion;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use common\models\Relationship;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use common\models\PartnerProduct;

$partner_product = PartnerProduct::find()->where(['product_id' => 1, 'partner_id' => $model->partner_id])->one();

$this->title = Yii::t('app', 'Заполните персональные данные');

$this->registerJs("
  var show_rel = false;  

  $('.relationships').hide();

  $('.add-rel').click(function(){
    if(!show_rel) {
      $('.relationships').show();
    }
  });

  jQuery('.dynamicform_wrapper').on('afterInsert', function(e, item) {
    $(this).find('.driver-clear').hide();
    jQuery('.dynamicform_wrapper .driver-title').each(function(index) {
        jQuery(this).html('" . Yii::t('app', 'Родственник') . ": ' + (index + 1))
    });
  });

  jQuery('.dynamicform_wrapper').on('afterDelete', function(e) {
    jQuery('.dynamicform_wrapper .driver-title').each(function(index) {
        jQuery(this).html('" . Yii::t('app', 'Родственник') . ": ' + (index + 1))
    });
  });

  $('#polis-price').text(formatNumber(parseFloat($('#polis-price').text()), 2));

  $('.person-info').hide();
  $('.contact-info').hide();
  $('.drivers-info').hide();
  $('.doc-files').hide();
  $('.submit-box').hide();
  $('.clear1').hide();
  $('.clear2').hide();
  $('.driver-clear').hide();

  $('.load1').click(function() {
    getTechData();
  });

  $('.load2').click(function() {
    getPassData();
  });

  function getDriverData(driver) {
    var driver_id = $(driver).parent().parent().prev().prev().find('input[type=\'text\']').attr('id').substr(12, 1),
        pass_series = $('#osagodriver-' + driver_id + '-passport_series').val(),
        pass_number = $('#osagodriver-' + driver_id + '-passport_number').val(),
        pinfl = $('#osagodriver-' + driver_id + '-pinfl').val();

    if(pass_series && pass_number && pinfl) {
      if(pinfl) $('.field-osago-insurer_pinfl').removeClass('has-error'); 
      if(pass_number) $('.field-osagodriver-' + driver_id + '-passport_number').removeClass('has-error'); 
      if(pass_series) $('.field-osagodriver-' + driver_id + '-passport_series').removeClass('has-error'); 

      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-pass-data') . "',
        data: {pass_series: nvl(pass_series), pass_number: nvl(pass_number), pinfl: nvl(pinfl)},
        dataType: 'json',
        timeout: 3000,
        error: function() {
          $('#osagodriver-' + driver_id + '-name').removeAttr('readonly');
          $('#osagodriver-' + driver_id + '-license_series').removeAttr('readonly');
          $('#osagodriver-' + driver_id + '-license_number').removeAttr('readonly');
        },
        success: function(result) {
          if(result) {
            $('#osagodriver-' + driver_id + '-name').val(result.name);
            $('#osagodriver-' + driver_id + '-license_series').val(result.licenseSeria);
            $('#osagodriver-' + driver_id + '-license_number').val(result.licenseNumber);


            if(result.name) $('#osagodriver-' + driver_id + '-name').attr('readonly', 'readonly');
            if(result.licenseSeria) $('#osagodriver-' + driver_id + '-license_series').attr('readonly', 'readonly');
            if(result.licenseNumber) $('#osagodriver-' + driver_id + '-license_number').attr('readonly', 'readonly');

    		  	$(driver).hide();
    		  	$(driver).next().show();
          } else {         	

            $('#osagodriver-' + driver_id + '-name').removeAttr('readonly');
            $('#osagodriver-' + driver_id + '-license_series').removeAttr('readonly');
            $('#osagodriver-' + driver_id + '-license_number').removeAttr('readonly');
          }
        },
      });
    } else {  
      if(!pinfl) $('.field-osago-insurer_pinfl').addClass('has-error'); 
      if(!pass_number) $('.field-osagodriver-' + driver_id + '-passport_number').addClass('has-error'); 
      if(!pass_series) $('.field-osagodriver-' + driver_id + '-passport_series').addClass('has-error'); 

      $('#osagodriver-' + driver_id + '-name').val('');
      $('#osagodriver-' + driver_id + '-license_series').val('');
      $('#osagodriver-' + driver_id + '-license_number').val('');

      $('#osagodriver-' + driver_id + '-name').removeAttr('readonly');
      $('#osagodriver-' + driver_id + '-license_series').removeAttr('readonly');
      $('#osagodriver-' + driver_id + '-license_number').removeAttr('readonly');
    }
  }

  function clearDriverData(driver) {
    var driver_id = $(driver).parent().parent().prev().prev().find('input[type=\'text\']').attr('id').substr(12, 1);

    $('#osagodriver-' + driver_id + '-pinfl').val('');
    $('#osagodriver-' + driver_id + '-passport_series').val('');
    $('#osagodriver-' + driver_id + '-passport_number').val('');
    $('#osagodriver-' + driver_id + '-name').val('');
    $('#osagodriver-' + driver_id + '-license_series').val('');
    $('#osagodriver-' + driver_id + '-license_number').val('');

  	$(driver).hide();
  	$(driver).prev().show();

    $('#osagodriver-' + driver_id + '-pinfl').removeAttr('readonly');
    $('#osagodriver-' + driver_id + '-passport_series').removeAttr('readonly');
    $('#osagodriver-' + driver_id + '-passport_number').removeAttr('readonly');
    $('#osagodriver-' + driver_id + '-name').removeAttr('readonly');
    $('#osagodriver-' + driver_id + '-license_series').removeAttr('readonly');
    $('#osagodriver-' + driver_id + '-license_number').removeAttr('readonly');
  }

  function getTechData() {
    var tech_series = $('#osago-insurer_tech_pass_series').val(),
        tech_number = $('#osago-insurer_tech_pass_number').val(),
        autonumber = $('#osago-autonumber').val();

    if(tech_series && tech_number && autonumber) {      
      if(tech_series) $('.field-osago-insurer_tech_pass_series').removeClass('has-error'); 
      if(tech_number) $('.field-osago-insurer_tech_pass_number').removeClass('has-error'); 
      if(autonumber) $('.field-osago-autonumber').removeClass('has-error'); 

      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-tech-pass-data') . "',
        data: {tech_series: nvl(tech_series), tech_number: nvl(tech_number), autonumber: nvl(autonumber)},
        dataType: 'json',
        timeout: 3000,
        error: function() {
          $('#osago-insurer_name').val('');
          $('#osago-insurer_name').removeAttr('readonly');
          $('#osago-insurer_pinfl').val('');
          $('#osago-insurer_pinfl').removeAttr('readonly');

          $('.clear1').hide();
          $('.load1').show();
          $('.person-info').show();
        },
        success: function(result) {
          if(result) {
            $('#osago-insurer_name').val(result.name);
            if(result.name) $('#osago-insurer_name').attr('readonly', 'readonly');
            $('#osago-insurer_pinfl').val(result.pinfl);
            if(result.pinfl) $('#osago-insurer_pinfl').attr('readonly', 'readonly');

    		    $('.clear1').show();
    		    $('.load1').hide();
          } else {            
            $('#osago-insurer_name').val('');
            $('#osago-insurer_name').removeAttr('readonly');
            $('#osago-insurer_pinfl').val('');
            $('#osago-insurer_pinfl').removeAttr('readonly');

    		    $('.clear1').hide();
    		    $('.load1').show();
          }
          $('.person-info').show();
        },
      });
    } else {   
      if(!tech_series) $('.field-osago-insurer_tech_pass_series').addClass('has-error'); 
      if(!tech_number) $('.field-osago-insurer_tech_pass_number').addClass('has-error'); 
      if(!autonumber) $('.field-osago-autonumber').addClass('has-error'); 
      $('#osago-insurer_name').val('');
      $('#osago-insurer_name').removeAttr('readonly');
      $('#osago-insurer_pinfl').val('');
      $('#osago-insurer_pinfl').removeAttr('readonly');
    }
  }

  $('.clear1').click(function() {
    $('#osago-insurer_name').val('');
    $('#osago-insurer_name').removeAttr('readonly');
    $('#osago-insurer_pinfl').val('');
    $('#osago-insurer_pinfl').removeAttr('readonly');
    
    $('.load1').show();
    $('.clear1').hide();
  });

  $('#osago-insurer_passport_series').blur(function() {
    getPinfl();

    if($('#osago-number_drivers_id').val() == 2) {
  		$('#osagodriver-0-passport_series').val($(this).val());
  	}
  });

  $('#osago-insurer_passport_number').blur(function() {
    getPinfl();

    if($('#osago-number_drivers_id').val() == 2) {
  		$('#osagodriver-0-passport_number').val($(this).val());
  	}
  });

  $('#osago-insurer_pinfl').blur(function() {
  	if($('#osago-number_drivers_id').val() == 2) {
  		$('#osagodriver-0-pinfl').val($(this).val());
  	}
  });

  $('#osago-insurer_name').blur(function() {
  	if($('#osago-number_drivers_id').val() == 2) {
  		$('#osagodriver-0-name').val($(this).val());

  		if($('#osagodriver-0-pinfl').val() || $('#osagodriver-0-passport_series').val() || $('#osagodriver-0-passport_number').val() || $('#osagodriver-0-name').val() || $('#osagodriver-0-license_series').val() || $('#osagodriver-0-license_number').val()) {  
  			driver = $('.field-osagodriver-0-name').parent().parent().prev();

	  		driver.find('.driver-load').hide();
	  		driver.find('.driver-clear').show();
  		}
  	}
  });

  function getPinfl() {
    var pass_series = $('#osago-insurer_passport_series').val(),
        pass_number = $('#osago-insurer_passport_number').val();

    if(pass_series && pass_number) {
      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-pinfl') . "',
        data: {pass_series: nvl(pass_series), pass_number: nvl(pass_number)},
        dataType: 'json',
        timeout: 3000,
        error: function() {
            $('#osago-insurer_pinfl').removeAttr('readonly');
            $('#osago-insurer_address').removeAttr('readonly');
            $('#osagodriver-0-pinfl').removeAttr('readonly');
            $('#osagodriver-0-passport_series').removeAttr('readonly');
            $('#osagodriver-0-passport_number').removeAttr('readonly');
            $('#osagodriver-0-name').removeAttr('readonly');
            $('#osagodriver-0-license_series').removeAttr('readonly');
            $('#osagodriver-0-license_number').removeAttr('readonly');
        },
        success: function(result) {
          var number_drivers = $('#osago-number_drivers_id').val();

          if(result) {
            $('#osago-insurer_pinfl').val(result.pinfl);
            $('#osago-insurer_address').val(result.address);

            if(result.pinfl) $('#osago-insurer_pinfl').attr('readonly', 'readonly');
            if(result.address) $('#osago-insurer_address').attr('readonly', 'readonly');

            if(result.pinfl) {              
              $('.contact-info').show();
              $('.doc-files').show();
              $('.drivers-info').show();
              $('.doc-files').show();
              $('.submit-box').show();
              
              if($('#osago-number_drivers_id').val() == 2) {
                $('.relationships').show();
                $('.field-osagodriver-0-relationship_id').hide();
              }
            }

            if(number_drivers == 2) {
              $('#osagodriver-0-license_series').val(result.licenseSeria);
              $('#osagodriver-0-license_number').val(result.licenseNumber);
              $('#osagodriver-0-pinfl').val(result.pinfl);
              $('#osagodriver-0-passport_series').val(pass_series);
              $('#osagodriver-0-passport_number').val(pass_number);
              $('#osagodriver-0-name').val(result.name);

              driver0 = $('.field-osagodriver-0-name').parent().parent().prev();

              if(result.pinfl || pass_series || pass_number || result.licenseSeria || result.licenseNumber || result.name) {
    		  	    driver0.find('.driver-load').hide();
    		        driver0.find('.driver-clear').show();
                  } else {
    		  	    driver0.find('.driver-load').show();
    		        driver0.find('.driver-clear').hide();
              }

  	          if(result.pinfl) $('#osagodriver-0-pinfl').attr('readonly', 'readonly');
  	          if(pass_series) $('#osagodriver-0-passport_series').attr('readonly', 'readonly');
  	          if(pass_number) $('#osagodriver-0-passport_number').attr('readonly', 'readonly');
  	          if(result.name) $('#osagodriver-0-name').attr('readonly', 'readonly');
  	          if(result.licenseSeria) $('#osagodriver-0-license_series').attr('readonly', 'readonly');
  	          if(result.licenseNumber) $('#osagodriver-0-license_number').attr('readonly', 'readonly');
            }

    		    $('.clear2').show();
    		    $('.load2').hide();
          } else {            
            $('#osago-insurer_pinfl').removeAttr('readonly');
            $('#osago-insurer_address').removeAttr('readonly');
            $('#osagodriver-0-pinfl').removeAttr('readonly');
            $('#osagodriver-0-passport_series').removeAttr('readonly');
            $('#osagodriver-0-passport_number').removeAttr('readonly');
            $('#osagodriver-0-name').removeAttr('readonly');
            $('#osagodriver-0-license_series').removeAttr('readonly');
            $('#osagodriver-0-license_number').removeAttr('readonly');
          }


        },
      });
    } else {  
      $('#osago-insurer_pinfl').removeAttr('readonly');
      $('#osago-insurer_address').removeAttr('readonly');
      $('#osagodriver-0-pinfl').removeAttr('readonly');
      $('#osagodriver-0-passport_series').removeAttr('readonly');
      $('#osagodriver-0-passport_number').removeAttr('readonly');
      $('#osagodriver-0-name').removeAttr('readonly');
      $('#osagodriver-0-license_series').removeAttr('readonly');
      $('#osagodriver-0-license_number').removeAttr('readonly');
    }
  }

  function getPassData() {
    var pass_series = $('#osago-insurer_passport_series').val(),
        pass_number = $('#osago-insurer_passport_number').val(),
        pinfl = $('#osago-insurer_pinfl').val();

    if(pass_series && pass_number && pinfl) {
      if(pinfl) $('.field-osago-insurer_pinfl').removeClass('has-error'); 
      if(pass_number) $('.field-osago-insurer_passport_number').removeClass('has-error'); 
      if(pass_series) $('.field-osago-insurer_passport_series').removeClass('has-error'); 

      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-pass-data') . "',
        data: {pass_series: nvl(pass_series), pass_number: nvl(pass_number), pinfl: nvl(pinfl)},
        dataType: 'json',
        timeout: 3000,
        error: function() {          
          $('#osago-insurer_pinfl').removeAttr('readonly');
          $('#osago-insurer_address').removeAttr('readonly');
          $('#osagodriver-0-pinfl').removeAttr('readonly');
          $('#osagodriver-0-passport_series').removeAttr('readonly');
          $('#osagodriver-0-passport_number').removeAttr('readonly');
          $('#osagodriver-0-name').removeAttr('readonly');
          $('#osagodriver-0-license_series').removeAttr('readonly');
          $('#osagodriver-0-license_number').removeAttr('readonly');

          $('.clear2').hide();
          $('.load2').show();


          $('.contact-info').show();
          $('.doc-files').show();
          $('.drivers-info').show();
          $('.doc-files').show();
          $('.submit-box').show();
          
          if($('#osago-number_drivers_id').val() == 2) {
            $('.relationships').show();
            $('.field-osagodriver-0-relationship_id').hide();
          }
        },
        success: function(result) {
          var number_drivers = $('#osago-number_drivers_id').val();

          if(result) {
            $('#osago-insurer_address').val(result.address);

            if(result.address) $('#osago-insurer_address').attr('readonly', 'readonly');

            if(number_drivers == 2) {
              $('#osagodriver-0-license_series').val(result.licenseSeria);
              $('#osagodriver-0-license_number').val(result.licenseNumber);
              $('#osagodriver-0-pinfl').val(pinfl);
              $('#osagodriver-0-passport_series').val(pass_series);
              $('#osagodriver-0-passport_number').val(pass_number);
              $('#osagodriver-0-name').val(result.name);

              driver0 = $('.field-osagodriver-0-name').parent().parent().prev();

              if(result.licenseSeria || result.licenseNumber || result.name) {
    		  	    driver0.find('.driver-load').hide();
    		        driver0.find('.driver-clear').show();
              } else {
    		  	    driver0.find('.driver-load').show();
    		        driver0.find('.driver-clear').hide();
              }

              $('.field-osagodriver-0-relationship_id').hide();


  	          if(result.pinfl) $('#osagodriver-0-pinfl').attr('readonly', 'readonly');
  	          if(pass_series) $('#osagodriver-0-passport_series').attr('readonly', 'readonly');
  	          if(pass_number) $('#osagodriver-0-passport_number').attr('readonly', 'readonly');
  	          if(result.name) $('#osagodriver-0-name').attr('readonly', 'readonly');
  	          if(result.licenseSeria) $('#osagodriver-0-license_series').attr('readonly', 'readonly');
  	          if(result.licenseNumber) $('#osagodriver-0-license_number').attr('readonly', 'readonly');
            }

    		    $('.clear2').show();
    		    $('.load2').hide();
          } else {            
            $('#osago-insurer_pinfl').removeAttr('readonly');
            $('#osago-insurer_address').removeAttr('readonly');
            $('#osagodriver-0-pinfl').removeAttr('readonly');
            $('#osagodriver-0-passport_series').removeAttr('readonly');
            $('#osagodriver-0-passport_number').removeAttr('readonly');
            $('#osagodriver-0-name').removeAttr('readonly');
            $('#osagodriver-0-license_series').removeAttr('readonly');
            $('#osagodriver-0-license_number').removeAttr('readonly');

    		    $('.clear2').hide();
    		    $('.load2').show();
          }

          $('.contact-info').show();
          $('.doc-files').show();
          $('.drivers-info').show();
          $('.doc-files').show();
          $('.submit-box').show();
          
          if($('#osago-number_drivers_id').val() == 2) {
            $('.relationships').show();
            $('.field-osagodriver-0-relationship_id').hide();
          }

        },
      });
    } else {  
      if(!pinfl) $('.field-osago-insurer_pinfl').addClass('has-error'); 
      if(!pass_number) $('.field-osago-insurer_passport_number').addClass('has-error'); 
      if(!pass_series) $('.field-osago-insurer_passport_series').addClass('has-error'); 


      $('#osago-insurer_pinfl').removeAttr('readonly');
      $('#osago-insurer_address').removeAttr('readonly');
      $('#osagodriver-0-pinfl').removeAttr('readonly');
      $('#osagodriver-0-passport_series').removeAttr('readonly');
      $('#osagodriver-0-passport_number').removeAttr('readonly');
      $('#osagodriver-0-name').removeAttr('readonly');
      $('#osagodriver-0-license_series').removeAttr('readonly');
      $('#osagodriver-0-license_number').removeAttr('readonly');

      $('#osago-insurer_address').val('');
    }
  }

  $('.clear2').click(function() {
    $('#osago-insurer_address').val('');

    if($('#osago-number_drivers_id').val() == 2) {
      $('#osagodriver-0-passport_series').val('');
      $('#osagodriver-0-passport_number').val('');
      $('#osagodriver-0-license_series').val('');
      $('#osagodriver-0-license_number').val('');
      $('#osagodriver-0-name').val('');
      $('#osagodriver-0-pinfl').val('');

  		driver = $('.field-osagodriver-0-name').parent().parent().prev();

  		driver.find('.driver-load').show();
  		driver.find('.driver-clear').hide();
  	}
    
    $('.load2').show();
    $('.clear2').hide();

    $('#osago-insurer_pinfl').removeAttr('readonly');
    $('#osago-insurer_address').removeAttr('readonly');
    $('#osagodriver-0-pinfl').removeAttr('readonly');
    $('#osagodriver-0-passport_series').removeAttr('readonly');
    $('#osagodriver-0-passport_number').removeAttr('readonly');
    $('#osagodriver-0-name').removeAttr('readonly');
    $('#osagodriver-0-license_series').removeAttr('readonly');
    $('#osagodriver-0-license_number').removeAttr('readonly');
  });
  

  // $(document).ajaxStart(function(){
  //   $('.preloader').fadeIn();
  // });
  // $(document).ajaxComplete(function(){
  //   $('.preloader').fadeOut();
  // });

", \yii\web\View::POS_END);

if(Yii::$app->language == 'ru') {
  $autotype = $model->autotype->name_ru;
  $citizenship = $model->citizenship->name_ru;
  $region = $model->region->name_ru;
  $period = $model->period->name_ru;
  $numberDrivers = $model->numberDrivers->name_ru;
} elseif(Yii::$app->language == 'uz') {
  $autotype = $model->autotype->name_uz;
  $citizenship = $model->citizenship->name_uz;
  $region = $model->region->name_uz;
  $period = $model->period->name_uz;
  $numberDrivers = $model->numberDrivers->name_uz;
} elseif(Yii::$app->language == 'en') {
  $autotype = $model->autotype->name_en;
  $citizenship = $model->citizenship->name_en;
  $region = $model->region->name_en;
  $period = $model->period->name_en;
  $numberDrivers = $model->numberDrivers->name_en;
}

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

<div class="container mt-lg-5 mt-0 mb-5">

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
            <h6><span class="normal-bold"><?= Yii::t('app', 'Вид транспортного средства: ')?></span><?=$autotype?></h6>
            <h6><span class="normal-bold"><?= Yii::t('app', 'Регистрация автомобиля: ')?></span><?=$citizenship?></h6>

            <?php 
            if($model->citizenship_id != 3):
            ?>
            <h6><span class="normal-bold"><?= Yii::t('app', 'Регион регистрации: ')?></span><?=$region?></h6>
            <?php 
            endif;
            ?>

            <h6><span class="normal-bold"><?= Yii::t('app', 'Период страхования: ')?></span><?=$period?></h6>
            <h6><span class="normal-bold"><?= Yii::t('app', 'Количество водителей: ')?></span><?=$numberDrivers?></h6>
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
    		<h4 class="text-center mb-4"><?=Yii::t('app','Заполните следующие данные')?></h4>
        <?php $form = ActiveForm::begin(['id' => 'OSAGO-form']);?>
        <div class="form-row">
          <div class="col-md-12">
            <h5><?=Yii::t('app','Transport vositasining texnik pasport (qayd etish guvohnomasi) ma`lumoti:')?></h5>
          </div>
          <div class="col-lg-4">
            <?=$form->field($model, 'autonumber')->textInput(['placeholder' => '00A000AA', 'class' => 'form-control text-uppercase'])->label(Yii::t('app', 'Номер автомобиля'))?>
          </div>
          <div class="col-lg-5">
            <div class="form-row">
              <div class="col-md-12 required"><label class="control-label"><?=Yii::t('app', 'Технический паспорт ТС')?></label></div>
              <div class="col-4"><?=$form->field($model, 'insurer_tech_pass_series')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => 'AAA', 
                // 'options' => [
                //   'onchange'=>'getData()'
                // ]
        ])->label(false)?></div>
              <div class="col-8"><?=$form->field($model, 'insurer_tech_pass_number')->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '9999999', 
            // 'options' => [
            //   'onchange'=>'getData()'
            // ]
        ])->label(false)?></div>
            </div>
          </div>
          <div class="col-lg-3">
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
            <?=$form->field($model, 'insurer_name')->textInput()->label(Yii::t('app', 'Ф.И.О. (латиницей)'))?>
          </div>
          <div class="col-lg-5">
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
          <div class="col-lg-4">
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
          <div class="col-lg-3">
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
          <div class="col-md-8">
            <?=$form->field($model, 'insurer_address')->textInput()->label(Yii::t('app', 'Адрес (латиницей)'))?>
          </div>
          <div class="col-md-4">
            <?=$form->field($model, 'insurer_phone')->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '+\9\98(99)-999-99-99',
        ])->label(Yii::t('app', 'Номер телефона'))?>
          </div>
        </div>

        <div class="form-row contact-info mt-lg-0 mt-3">
          <div class="col-md-12">
            <?=$form->field($model, 'address_delivery')->textInput()->label(Yii::t('app', 'Адрес доставки'))?>
          </div>
        </div>

        <div class="form-row doc-files">
          <div class="col-md-12"><h5><?=Yii::t('app', 'Данные поля являются необязательными для заполнения*')?></h5></div>
          <div class="col-md-4">
            <?php
            echo $form->field($model, 'passFile')->widget(FileInput::classname(), [
              // 'options' => ['accept' => 'image/*'],
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
          <div class="col-md-4">
            <?php
            echo $form->field($model, 'techPassFileFront')->widget(FileInput::classname(), [
              // 'options' => ['accept' => 'image/*'],
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

            ])->label(Yii::t('app', 'Передная сторона тех. пасспорта'));
            ?>
          </div>
          <div class="col-md-4">
            <?php
            echo $form->field($model, 'techPassFileBack')->widget(FileInput::classname(), [
              // 'options' => ['accept' => 'image/*'],
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

            ])->label(Yii::t('app', 'Обратная сторона тех. пасспорта'));
            ?>
          </div>

        </div>


      <?php
        if($model->number_drivers_id == 1) :?>
          <p class="drivers-info"><button type="button" class="mybtn add-rel"><?=Yii::t('app', 'Добавить родственников')?></button></p>
        <?php endif;?>
        <div class="relationships blog-details-area">
            <div class="">
          <?php DynamicFormWidget::begin([
              'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
              'widgetBody' => '.container-items', // required: css class selector
              'widgetItem' => '.item', // required: css class
              'limit' => 5, // the maximum times, an element can be cloned (default 999)
              'min' => 1, // 0 or 1 (default 1)
              'insertButton' => '.add-item', // css class
              'deleteButton' => '.remove-item', // css class
              'model' => $drivers[0],
              'id' => 'dynamic-form',
              'formId' => 'OSAGO-form',
              'formFields' => [
                  'pinfl',
                  'passport_number',
                  'passport_series',
                  'relationship_id',
              ],
          ]); ?>

          <div class="container-items widget-area"><!-- widgetContainer -->
                <?php foreach ($drivers as $i => $driver): ?>
                    <div class="item widget widget_categories mt-4"><!-- widgetBody -->
                        <div class="widget-title">
                          <div class="row">
                            <h3 class="col driver-title"><?=Yii::t('app', 'Родственник') . ': ' . ($i+1) ?></h3>
                            <div class="col text-right">
                                <button type="button" class="add-item mybtn pl-3 pr-3"><i class="bx bx-plus"></i></button>
                                <button type="button" class="remove-item mybtn danger-btn pl-3 pr-3"><i class="bx bx-minus"></i></button>
                            </div>
                          </div>
                        </div>
                        <div class="post-wrap">
                            <?php
                                // necessary for update action.
                                if (!$driver->isNewRecord) {
                                    echo Html::activeHiddenInput($driver, "[{$i}]id");
                                }

                                if($model->number_drivers_id == 2) :
                            ?>
                            <div class="row">
                              <div class="col-lg-4">
                                <div class="form-row">
                                  <div class="col-md-12"><label class="control-label"><?=Yii::t('app', 'Паспортные данные')?></label></div>
                                  <div class="col-4"><?=$form->field($driver, "[{$i}]passport_series")->widget(\yii\widgets\MaskedInput::className(), [
                                    'mask' => 'AA', 
                                  ])->label(false)?></div>
                                  <div class="col-8"><?=$form->field($driver, "[{$i}]passport_number")->widget(\yii\widgets\MaskedInput::className(), [
                                    'mask' => '9999999', 
                                  ])->label(false)?></div>
                                </div>
                              </div>
                              <div class="col-lg-5 col-pinfl">
                                <?php
                                  echo $form->field($driver, "[{$i}]pinfl", [
                                    'template' => '<label class="control-label">'.Yii::t('app', 'ПИНФЛ').'</label><div class="input-group">{input}<div class="input-group-append">
                                      <button data-toggle="modal" data-target="#pinfl-modal" class="btn btn-outline-secondary" type="button" title="'.Yii::t('app', 'How to know PINFL').'"><i class="fa fa-question-circle"></i></button>
                                    </div></div>','inputOptions' => ['class' => 'form-control pinfl'],
                                ])->widget(\yii\widgets\MaskedInput::className(), [
                                    'mask' => '99999999999999', 
                                  ]);
                                ?>
                              </div>
                              <div class="col-lg-3">
                                <div class="form-group">
                                  <label class="control-label">&nbsp;</label>
                                  <button type="button" class="mybtn w-100 driver-load" onclick="getDriverData(this)">
                                    <?=Yii::t('app', 'load data')?>
                                  </button>
                                  <button type="button" class="mybtn danger-btn w-100 driver-clear" onclick="clearDriverData(this)">
                                    <?=Yii::t('app', 'clear data')?>
                                  </button>
                                </div>
                              </div>
                            </div>
                          <?php endif; ?>
                            <div class="row">
                              <div class="col-lg-6">
                                <?= $form->field($driver, "[{$i}]name")->textInput(['maxlength' => true])->label(Yii::t('app', 'Ф.И.О. (латиницей)')) ?>
                              </div>
                              <div class="col-md-6">
                                <div class="form-row">
                                  <div class="col-md-12"><label class="control-label"><?=Yii::t('app', 'Серия и номер прав')?></label></div>
                                  <div class="col-md-4"><?= $form->field($driver, "[{$i}]license_series")->textInput([
                                      'maxlength' => 3, 'class' => 'form-control text-uppercase'
                                  ])->label(false) ?></div>
                                  <div class="col-md-8"><?= $form->field($driver, "[{$i}]license_number")->widget(\yii\widgets\MaskedInput::className(), [
                                      'mask' => '9999999',
                                  ])->label(false) ?></div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-6">
                                <?php
                                  $relationships = Relationship::find()->all();

                                  if(Yii::$app->language == 'ru') {
                                    $items = ArrayHelper::map($relationships, 'id', 'name_ru');
                                  } elseif(Yii::$app->language == 'uz') {
                                    $items = ArrayHelper::map($relationships, 'id', 'name_uz');
                                  } else {
                                    $items = ArrayHelper::map($relationships, 'id', 'name_en');
                                  }
                                  
                                  $params = [
                                      'prompt' => Yii::t('app', '- Выберите -'),
                                  ];
                                  echo $form->field($driver, "[{$i}]relationship_id")->dropDownList($items, $params)->label(Yii::t('app', 'Степень родства'));
                                  ?>

                              </div>
                              <div class="col-lg-6">
                                <?php

                                if($model->number_drivers_id == 2) :
                                echo $form->field($driver, "[{$i}]licenseFile")->widget(FileInput::classname(), [
                                  // 'options' => ['accept' => 'image/*'],
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

                                ])->label(Yii::t('app', 'Загрузить права'));
                              endif;
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


        <div class="row submit-box">
          <div class="col-lg-9">
            <p><?=Yii::t('app', 'Нажимая кнопку "Перейти к оплате", я подтверждаю свою дееспособность, принимаю {link1} и подтверждаю свое согласие {link2}', ['link1' => "<a href='/public_offers/osago_oferta.pdf' target='_blank'>".Yii::t('app', 'условия страхования')."</a>", 'link2' => "<a href='/public_offers/osago_oferta.pdf' target='_blank'>".Yii::t('app', 'на обработку персональных данных')."</a>"])?></p>
          </div>
          <div class="col-lg-3">
            <?php
              echo $form->field($model, 'number_drivers_id')->hiddenInput(['value'=> $model->number_drivers_id])->label(false);
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
