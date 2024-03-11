<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Autotype;
use common\models\Period;
use common\models\Region;
use common\models\Citizenship;
use common\models\NumberDrivers;
use yii\helpers\ArrayHelper;
use common\models\Page;

$page = Page::findOne(2);

$this->registerMetaTag([
    'name' => 'description',
    'lang' => 'ru',
    'content' => $page->description_ru
]);

$this->registerMetaTag([
    'name' => 'description',
    'lang' => 'uz',
    'content' => $page->description_uz
]);

$this->registerMetaTag([
    'name' => 'description',
    'lang' => 'en',
    'content' => $page->description_en
]);

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $page->keywords
]);

if(Yii::$app->language == 'ru') {
	$autotypes = Autotype::find()->select('id, name_ru as name')->asArray()->all();
	$periods = Period::find()->select('id, name_ru as name')->asArray()->all();
	$regions = Region::find()->select('id, name_ru as name')->asArray()->all();
	$citizenships = Citizenship::find()->select('id, name_ru as name')->orderBy('id')->asArray()->all();
	$numbers = NumberDrivers::find()->select('id, name_ru as name')->asArray()->all();
} elseif(Yii::$app->language == 'uz') {
	$autotypes = Autotype::find()->select('id, name_uz as name')->asArray()->all();
	$periods = Period::find()->select('id, name_uz as name')->asArray()->all();
	$regions = Region::find()->select('id, name_uz as name')->asArray()->all();
	$citizenships = Citizenship::find()->select('id, name_uz as name')->orderBy('id')->asArray()->all();
	$numbers = NumberDrivers::find()->select('id, name_uz as name')->asArray()->all();
} else {
	$autotypes = Autotype::find()->select('id, name_en as name')->asArray()->all();
	$periods = Period::find()->select('id, name_en as name')->asArray()->all();
	$regions = Region::find()->select('id, name_en as name')->asArray()->all();
	$citizenships = Citizenship::find()->select('id, name_en as name')->orderBy('id')->asArray()->all();
	$numbers = NumberDrivers::find()->select('id, name_en as name')->asArray()->all();
}


$this->title = Yii::t('app', 'Рассчитать стоимость ОСАГО');
$this->registerJs("
  $('.citizenship-box').hide();
  $('.period-box').hide();
  $('.region-box').hide();
  $('.number-drivers-box').hide();
  $('.submit-box').hide();
  $('.result').hide();
  $('.promo').hide();

 //  $.ajax({
	// type: 'GET',
	// url: '" . Yii::$app->urlManager->createUrl('product/check-osago-session') . "',
	// data: {},
	// dataType: 'json',
	// success: function(result) {
	//   if(result) {
	//   } else {
	//   }
	    
	// },
 //  });

  function selectAutotype() {
	var autotype = $('input[name=\'Osago[autotype_id]\']:checked').val();

  	if(autotype) {
  		$('.citizenship-box').slideDown();
		$('html, body').animate({ scrollTop: 200 },  1000);
  	} else {
  		$('.citizenship-box').slideUp();
 		$('.result').slideUp();
  	}
  }

  function selectCitizenship() {
	var citizenship_id = $('input[name=\'Osago[citizenship_id]\']:checked').val();

  	if(citizenship_id) {
  		if(citizenship_id == 3) {
  			$('.period-box').slideDown();
			$('.region-box').slideUp();
		} else {
			$('.region-box').slideDown();
		}
  		
		$('html, body').animate({ scrollTop: 300 },  1000);
  	} else {
  		$('.region-box').slideUp();
 		$('.result').slideUp();
  	}
  }

  function selectRegion() {
	var region_id = $('input[name=\'Osago[region_id]\']:checked').val();

  	if(region_id) {
  		$('.period-box').slideDown();
		$('html, body').animate({ scrollTop: 400 },  1000);
  	} else {
  		$('.period-box').slideUp();
 		$('.result').slideUp();
  	}
  }

  function selectPeriod() {
	var period_id = $('input[name=\'Osago[period_id]\']:checked').val();

  	if(period_id) {
  		$('.number-drivers-box').slideDown();
		$('html, body').animate({ scrollTop: 500 },  1000);
  	} else {
  		$('.number-drivers-box').slideUp();
 		$('.result').slideUp();
  	}
  }

  function selectNumberDrivers() {
	var number_drivers_id = $('input[name=\'Osago[number_drivers_id]\']:checked').val();

  	if(number_drivers_id) {
  		$('.submit-box').slideDown();
  		$('.promo').slideDown();
		$('html, body').animate({ scrollTop: 600 },  1000);
  	} else {
  		$('.submit-box').slideUp();
  		$('.promo').slideUp();
 		$('.result').slideUp();
  	}
  }

  $('.my-radiolist .radio-label').click(function(){
  	$(this).find('input').attr('checked', 'checked');
  	$(this).siblings().each(function(){
	    $(this).find('input').removeAttr('checked');
	});
  });

  $('#calc-button').click(function() {
    var autotype = parseInt($('input[name=\'Osago[autotype_id]\']:checked').val()),
        region = parseInt($('input[name=\'Osago[region_id]\']:checked').val()),
        period = parseInt($('input[name=\'Osago[period_id]\']:checked').val()),
        citizenship = parseInt($('input[name=\'Osago[citizenship_id]\']:checked').val()),
        number = parseInt($('input[name=\'Osago[number_drivers_id]\']:checked').val()),
        promo = $('#osago-promo_code').val();

    if(citizenship == 3) region = 2;

    if(autotype && region && period && number) {
    	$.ajax({
	      type: 'GET',
	      url: '" . Yii::$app->urlManager->createUrl('product/get-osago-amounts') . "',
	      data: {autotype: autotype, region: region, period: period, citizenship: citizenship, number: number, promo: promo},
	      dataType: 'json',
	      success: function(result) {
	        if(result) {
 			  $('.result').slideDown();
	          $('.companies').html(result.html);

			  $('html, body').animate({ scrollTop: $('.osago-form').height() + 200 },  1000);
			  $('.n1').css({ 'background-color': '#fff' });
			  $('.n2').css({ 'background-color': '#dfdfdf' });
	        } else {
 			  $('.result').hide();
	          $('.companies').hide();
	        }
	        
	      },
	    });
    }
  });

  $(document).ajaxStart(function(){
    $('.preloader').fadeIn();
  });
  $(document).ajaxComplete(function(){
    $('.preloader').fadeOut();
  });


", \yii\web\View::POS_END);
?>

	 <div class="container">
<div class="row number-cont">
	<div class="col-3">
		<div class="number-box n1" style="padding-left: 15px; background-color: #dfdfdf;">
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
		<div class="number-box n2">
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
		<div class="number-box">
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

<div class="container">
	

 	<?php $form = ActiveForm::begin(['id' => 'osago-form']); ?>

  	<!-- Start Contact Area -->
	<section class="contact-area mt-lg-5 mt-0 mb-4">
			<div class="row">
				<div class="col-lg-12">
					<div class="osago-form shadow bg-white p-4">
						<div class="section-title mb-2">
							<h2><?= Yii::t('app', 'Укажите данные страхового полиса, чтобы узнать цену')?></h2>
						</div>

						<div class="row">
							<div class="col-lg-10 offset-lg-1">
					            <div class="form-group field-osago-autotype_id required autotype mt-4">
					          		<label class="control-label"><?=Yii::t('app', 'Вид транспортного средства')?></label><br>
						          	
						          	<div class="row btn-group-toggle my-radiolist" data-toggle="buttons">
					          		<?php

					          		foreach($autotypes as $p):
					          		?>
									  <label class="osago-btn col-lg mt-2 btn radio-label mr-2 ml-2" onclick="selectAutotype()">
									    <input type="radio" name="Osago[autotype_id]" value="<?=$p['id']?>" autocomplete="off"> <?=$p['name']?>
									  </label>
									<?php
					          		endforeach;
					          		?>
									</div>
									<div class="help-block"></div>
								</div>

					            <div class="form-group field-osago-citizenship_id required citizenship-box mt-4">
					          		<label class="osago-cal control-label"><?=Yii::t('app', 'Регистрация автомобиля')?></label><br>
						          	
						          	<div class="row btn-group-toggle my-radiolist" data-toggle="buttons">
					          		<?php

					          		foreach($citizenships as $p):
					          		?>
									  <label class="osago-btn btn radio-label col-lg mt-2 mr-2 ml-2" onclick="selectCitizenship()">
									    <input type="radio" name="Osago[citizenship_id]" value="<?=$p['id']?>" autocomplete="off"> <?=$p['name']?>
									  </label>
									<?php
					          		endforeach;
					          		?>
									</div>
									<div class="help-block"></div>
								</div>

					            <div class="form-group field-osago-region_id required region-box mt-4">
					          		<label class="control-label"><?=Yii::t('app', 'Регион регистрации автомобиля')?></label><br>
						          	
						          	<div class="row btn-group-toggle my-radiolist" data-toggle="buttons">
					          		<?php

					          		foreach($regions as $p):
					          		?>
									  <label class="osago-btn btn radio-label col-lg mt-2 mr-2 ml-2" onclick="selectRegion()">
									    <input type="radio" name="Osago[region_id]" value="<?=$p['id']?>" autocomplete="off"> <?=$p['name']?>
									  </label>
									<?php
					          		endforeach;
					          		?>
									</div>
									<div class="help-block"></div>
								</div>

					            <div class="form-group field-osago-period_id required period-box mt-4">
					          		<label class="control-label"><?=Yii::t('app', 'Период страхования')?></label><br>
						          	
						          	<div class="row btn-group-toggle my-radiolist" data-toggle="buttons">
					          		<?php

					          		foreach($periods as $p):
					          		?>
									  <label class="osago-btn btn radio-label col-lg mt-2 mr-2 ml-2" onclick="selectPeriod()">
									    <input type="radio" name="Osago[period_id]" value="<?=$p['id']?>" autocomplete="off"> <?=$p['name']?>
									  </label>
									<?php
					          		endforeach;
					          		?>
									</div>
									<div class="help-block"></div>
								</div>

					            <div class="form-group field-osago-number_drivers_id required number-drivers-box mt-4">
					          		<label class="control-label"><?=Yii::t('app', 'Количество водителей')?></label><br>
						          	
						          	<div class="row btn-group-toggle my-radiolist" data-toggle="buttons">
					          		<?php

					          		foreach($numbers as $p):
					          		?>
									  <label class="osago-btn btn radio-label col-lg mt-2 mr-2 ml-2" onclick="selectNumberDrivers()">
									    <input type="radio" name="Osago[number_drivers_id]" value="<?=$p['id']?>" autocomplete="off"> <?=$p['name']?>
									  </label>
									<?php
					          		endforeach;
					          		?>
									</div>
									<div class="help-block"></div>
								</div>

								<div class="row mt-4 promo">
									<div class="col-md-4" style="padding-left: 9px;">
										<?php 

										echo $form->field($model, 'promo_code')->textInput()->label(Yii::t('app','Promo code'));

										?>
									</div>
								</div>

							</div>
						</div>

						<div class="row mt-4 submit-box">
							<div class="col-lg-12 col-md-12 text-center">
								<button id="calc-button" type="button" class="mybtn page-btn">
									<?= Yii::t('app', 'Рассчитать стоимость')?>
								</button>
							</div>
						</div>

					</div>

					<div class="result mt-4">
						<div class="bg-white shadow p-2 border-bottom">
							<div class="row">
								<div class="col-md-3 col-6 p-lg-4 p-2">
									<p class="pl-lg-0 pl-2 text-center"><?=Yii::t('app','Company')?></p>
								</div>
								<div class="col-md-2 col-6 p-lg-4 p-2">
									<p class="text-center"><?=Yii::t('app','Insurance premium')?></p>
								</div>
								<div class="col-md-2 col-6 p-lg-4 p-2">
									<p class="pl-lg-0 pl-2 text-center"><?=Yii::t('app','Rating')?></p>
								</div>
								<div class="col-md-3 col-6 p-lg-4 p-2">
									<p class="text-center"><?=Yii::t('app','Insurance amount')?></p>
								</div>
							</div>
						</div>
						<div class="companies shadow">
							
						</div>
					</div>
				</div>
			</div>
	</section>
		<!-- End Contact Area -->

    <?php 

    ActiveForm::end(); 

    ?>

</div>