<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Autobrand;
use common\models\Automodel;
use common\models\Period;
use common\models\Region;
use yii\helpers\ArrayHelper;
use common\models\Page;

$page = Page::findOne(3);

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


$this->title = Yii::t('app', 'Рассчитать стоимость КАСКО');
         

$prompt = Yii::t('app', '- Выберите -');		
$this->registerJs("
  $('.submit-box').hide();
  $('.result').hide();
  $('.promo').hide();


  $('#calc-button').click(function() {
  	var auto = $('#kasko-auto').val(),
  		price_coeff = $('#price_coeff').val(),
  		autobrand_id = $('#kasko-autobrand_id').val(),
        promo = $('#kasko-promo_code').val();

    if((auto || autobrand_id == 0) && price_coeff) {
    	$.ajax({
	      type: 'GET',
	      url: '" . Yii::$app->urlManager->createUrl('product/get-kasko-amounts') . "',
	      data: {auto: auto, price_coeff: price_coeff, autobrand_id: autobrand_id, promo: promo},
	      dataType: 'json',
	      success: function(result) {
	        if(result) {
 			  $('.result').slideDown();
	          $('.companies').html(result.html);

			  $('html, body').animate({ scrollTop: $('.kasko-form').height() + 200 },  1000);
			  $('.n1').css({ 'background-color': '#fff' });
			  $('.n2').css({ 'background-color': '#dfdfdf' });
	        } else {
 			  $('.result').hide();
	          $('.companies').hide();
	        }

  			$('.risks').hide();
	        
	      },
	    });
    }
  });

  $(document).on('click', '.read-more', function() {
  	if($(this).hasClass('show-info')) {
  		$(this).parent().parent().next().slideUp();
  		$(this).removeClass('show-info');
  		$(this).text('".Yii::t('app', 'Подробнее')."');
  	} else {
  		$(this).parent().parent().next().slideDown();
  		$(this).addClass('show-info');
  		$(this).text('".Yii::t('app', 'Скрыть')."');
  	}
  });

  $(document).ajaxStart(function(){
    //$('.preloader').fadeIn();
  });
  $(document).ajaxComplete(function(){
    //$('.preloader').fadeOut();
  });

  $('.range-slider').hide();

  function cost() {
  	var auto = $('#kasko-auto').val();

    $.ajax({
      type: 'GET',
      url: '" . Yii::$app->urlManager->createUrl('product/kasko-auto-price') . "',
      data: {auto: auto},
      dataType: 'json',
      success: function(result) {
        if(result) {
          var price = result.price;

          $('.autoprice').text(formatNumber(price, 0));

          min = 30000000;
          max = price;

          $('.range-slider').slideDown();
  		  $('.submit-box').slideDown();
  		  $('.promo').slideDown();


          $('.min').text(formatNumber(min, 0));
          $('.max').text(formatNumber(max, 0));
          $('.current-cost').text(formatNumber(price, 0));
          $('.current-cost').css('left', 450 / 6 + '%');
          $('input[name=\'price_coeff\']').val((price/max).toFixed(4));

          //calcPrice(result.amounts, result.has_margin);

          $('#cost-slide').slider({
              animate: true,
              range: 'min',
              min: min,
              max: max,
              value: Math.round(price),
              step: 1000000,
              slide: function(event, ui) {
                $('.current-cost').text(formatNumber(ui.value, 0));
                $('.current-cost').css('left', $('.ui-slider-range').width());
                $('#policy_cost_uzs').text(formatNumber(nvl(ui.value * 0.015), 0));
                $('#policy_cost_usd').text(formatNumber((nvl(ui.value * 0.015) / 9500), 0));
          		$('input[name=\'price_coeff\']').val((ui.value/max).toFixed(4));
              }
          });

        }
      },
    });
  }

  function costOthers() {
  	var price = 2000000000;

	$('.autoprice').text(formatNumber(price, 0));

	min = 30000000;
	max = price;

	$('.range-slider').slideDown();
	$('.submit-box').slideDown();
	$('.promo').slideDown();

	$('.min').text(formatNumber(min, 0));
	$('.max').text(formatNumber(max, 0));
	$('.current-cost').text(formatNumber(price, 0));
	$('.current-cost').css('left', 450 / 6 + '%');
	$('input[name=\'price_coeff\']').val((price/max).toFixed(4));

	$('#cost-slide').slider({
	  animate: true,
	  range: 'min',
	  min: min,
	  max: max,
	  value: Math.round(price),
	  step: 1000000,
	  slide: function(event, ui) {
	    $('.current-cost').text(formatNumber(ui.value, 0));
	    $('.current-cost').css('left', $('.ui-slider-range').width());
	    $('#policy_cost_uzs').text(formatNumber(nvl(ui.value * 0.015), 0));
	    $('#policy_cost_usd').text(formatNumber((nvl(ui.value * 0.015) / 9500), 0));
			$('input[name=\'price_coeff\']').val((ui.value/max).toFixed(4));
	  }
	});

  }



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
	

 	<?php $form = ActiveForm::begin(['id' => 'kasko-form']); ?>

  	<!-- Start Contact Area -->
	<section class="contact-area mt-lg-5 mt-0 mb-4">
			<div class="row">
				<div class="col-lg-12">
					<div class="kasko-form shadow bg-white p-4">
						<div class="section-title">
							<h2><?= Yii::t('app', 'Укажите данные страхового полиса, чтобы узнать цену')?></h2>
						</div>

						<div class="row mt-4">
							<div class="col-lg-10 offset-lg-1">
								<div class="row">
									<div class="col-lg-4">
										<?php
							            $autobrands = Autobrand::find()->asArray()->all();
						                $items = ArrayHelper::map($autobrands, 'id', 'name');
						                $items[] = [0 => Yii::t('app', 'Others')];
						                $params = [
						                    'prompt' => $prompt,
						                    'onchange' => '
						                    if($(this).val()) {
						                    	if($(this).val() == 0) {
						                    		costOthers();
						                        	$("select#kasko-auto").html("");
						                            $(".field-kasko-auto ul.list").html("");
						                            $(".field-kasko-auto").hide();
						                    	} else {
						                    		$.post("'.Yii::$app->urlManager->createUrl('product/automodel-list').'?id=" + $(this).val(), function(data) {
						                            	$(".field-kasko-auto").show();
							                        	$("select#kasko-auto").html(data.s);
							                            $(".field-kasko-auto ul.list").html(data.n);
							                            $(".field-kasko-auto .nice-select").trigger("click");
							                        });
						                    	}
						                    	$(".field-kasko-auto .nice-select span.current").text("");
						                    	$("select#kasko-auto").val("");
						                    }'
						                    
						                    	
						                ];
						                echo $form->field($model, 'autobrand_id')->dropDownList($items, $params)->label(Yii::t('app','Марка'));
						                ?>
									</div>
									<div class="col-lg-8">
							           <?php
							            $automodels = Automodel::find()->asArray()->all();
						                $items = ArrayHelper::map($automodels, 'id', 'name');
						                $params = [
						                    'prompt' => $prompt,
						                    'onchange' => '
						                        $.post("'.Yii::$app->urlManager->createUrl('product/autocomp-list').'?autobrand_id=" + $("#kasko-autobrand_id").val() + "&id=" + $(this).val(), function(data) {
						                        	if(data.change) {
						                        		$("select#kasko-auto").html(data.s);
							                            $(".field-kasko-auto ul.list").html(data.n);
							                            $(".field-kasko-auto .nice-select").trigger("click");
						                        	} else {
						                        		cost();
						                        	}
						                            
						                          });

						                        '
						                ];
						                echo $form->field($model, 'auto')->dropDownList([], $params)->label(Yii::t('app','Модель, комплектация, год'));
						                ?>
						            </div>
						        </div>


					            <div class="range-slider">
					              <label class="mt-3" id="price_title"><?=Yii::t('app', 'На какую сумму Вы хотите застраховать свой автомобиль')?></label>
					              <div class="row">
					                <div class="col-6"><p class="min"></p></div>
					                <div class="col-6"><p class="max text-right"></p></div>
					                <div class="current-cost"></div>
					              </div>
					              <div id="cost-slide"></div>
					            </div>

								<div class="row mt-4 promo">
									<div class="col-md-4">
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
								<div class="col-md-3 col-6 p-lg-4 p-2			">
									<p class="text-center"><?=Yii::t('app','Insurance premium')?></p>
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


    echo Html::hiddenInput('price_coeff', 0, ['id' => 'price_coeff']);

    ActiveForm::end(); 

    ?>

</div>