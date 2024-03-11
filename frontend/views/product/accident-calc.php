<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\AccidentPartnerProgram;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\models\Page;

$page = Page::findOne(6);

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


$this->title = Yii::t('app', 'Рассчитать стоимость страхование от несчастных случаев');
         

$prompt = Yii::t('app', '- Выберите -');		
$this->registerJs("
  // $('.submit-box').hide();
  $('.result').hide();

  $('#calc-button').click(function() {
  	var insurance_amount = $('#accident-insurance_amount').val(),
        insurer_count = $('#accident-insurer_count').val();

    if(insurance_amount && insurer_count) {
    	$.ajax({
	      type: 'GET',
	      url: '" . Yii::$app->urlManager->createUrl('product/get-accident-amounts') . "',
	      data: {insurance_amount: insurance_amount, insurer_count: insurer_count},
	      dataType: 'json',
	      success: function(result) {
	        if(result) {
 			  $('.result').slideDown();
	          $('.companies').html(result.html);
	        } else {
 			  $('.result').hide();
	          $('.companies').hide();
	        }
	        
	      },
	    });
    }
  });

  var min = 1000000, max = 20000000, current = 1000000;
  $('.min').text(formatNumber(min, 0));
  $('.max').text(formatNumber(max, 0));
  $('.current-cost').text(formatNumber(current, 0));

  $('#cost-slide').slider({
	animate: true,
	range: 'min',
	min: min,
	max: max,
	value: current,
	step: 100000,
	slide: function(event, ui) {
		$('.current-cost').text(formatNumber(ui.value, 0));
		$('.current-cost').css('left', $('.ui-slider-range').width());
		$('#accident-insurance_amount').val(ui.value);
	}
  });

  function selectDate() {
    var begin_date = $('#accident-begin_date').kvDatepicker('getDate');

    endDate = new Date(Date.parse(begin_date) + 364 * 24 * 60 * 60 * 1000);
    $('#accident-end_date').val(moment(endDate).format('DD.MM.YYYY'));

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
					<p><?=Yii::t('app','Выберите')?></p>
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

<div class="container mt-lg-5 mt-0 mb-4">
	

 	<?php $form = ActiveForm::begin(['id' => 'accident-form']); ?>
 	
  	<!-- Start Contact Area -->
	<section class="contact-area">
		<div class="row">
			<div class="col-lg-6">
					<div class="kasko-form shadow bg-white p-4">
						<div class="section-title">
							<h2><?= Yii::t('app', 'Укажите данные страхового полиса, чтобы узнать цену')?></h2>
						</div>

						<div class="row mt-4">
			              <div class="col-md-6 insurer-count">
			                <?php

			                $counts = range(1, 5);
			                $params = [
			                    // 'onchange' => 'checkProgram()',
			                ];
			                echo $form->field($model, 'insurer_count')->dropDownList($counts, $params)->label(Yii::t('app', 'Выберите количество страхователей'));
			                ?>
			              </div>
			            </div>

			            <div class="row mt-4">
			              <div class="col-md-6">
			                <?php echo '<label class="control-label">'.Yii::t('app', 'Дата начала страхования').'</label>' . DatePicker::widget([
			                    'model' => $model,
			                    'attribute' => 'begin_date',
			                    'name' => 'date',
			                    'type' => DatePicker::TYPE_INPUT,
			                    'pluginOptions' => [
			                        'todayHighlight' => true,
			                        'autoclose' => true,
			                        'format' => 'dd.mm.yyyy',
			                        'enableOnReadonly' => false,
			                        'startDate' => '+1d',
			                        'endDate' => '+2y'
			                    ],

			                    'pluginEvents' => [
			                     'changeDate' => 'function() {
			                        selectDate();
			                     }',
			                    ],
			                ]);

			                ?>
			              </div>
			              <div class="col-md-6">
			                <?php echo $form->field($model, 'end_date')->textInput(['readonly' => true])->label(Yii::t('app', 'Дата окончания страхования'));
			                ?>
			              </div>
			            </div>


			            <div class="range-slider">
			              <label class="mt-3" id="price_title"><?=Yii::t('app', 'На какую сумму Вы хотите застраховать')?></label>
			              <div class="row">
			                <div class="col-6"><p class="min"></p></div>
			                <div class="col-6"><p class="max text-right"></p></div>
			                <div class="current-cost"></div>
			              </div>
			              <div id="cost-slide"></div>
			            </div>  

			            <?php

                		echo $form->field($model, 'insurance_amount')->hiddenInput(['value' => 1000000])->label(false);
			            ?>

						<div class="row mt-4 submit-box">
							<div class="col-lg-12 col-md-12 text-center">
								<button id="calc-button" type="button" class="mybtn page-btn">
									<?= Yii::t('app', 'Рассчитать стоимость')?>
								</button>
							</div>
						</div>

					</div>
				</div>

				<div class="col-lg-6 mt-lg-0 mt-3">

					<div class="result">
						<div class="bg-white shadow p-2 border-bottom">
							<div class="row">
								<div class="col-md-4 col-6 p-lg-4 p-2">
									<p class="pl-lg-0 pl-2 text-center"><?=Yii::t('app','Company')?></p>
								</div>
								<div class="col-md-4 col-6 p-lg-4 p-2			">
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



    ActiveForm::end(); 

    ?>

</div>