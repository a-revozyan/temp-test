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


$autotypes = Autotype::find()->all();
$periods = Period::find()->all();
$regions = Region::find()->all();
$citizenships = Citizenship::find()->orderBy('id')->all();
$numbers = NumberDrivers::find()->all();

$this->title = Yii::t('app', 'Выберите формы оплаты');
$this->registerJs("

    // Data Picker Initialization
	$('.input-daterange').datepicker({
		format: 'dd-mm-yyyy',
		autoclose: true
	});
", \yii\web\View::POS_END);
?>
<div class="container">
	
<div class="container px-1 px-sm-5 mx-auto">
    <form autocomplete="off">
        <div class="flex-row d-flex justify-content-center">
            <div class="col-lg-6 col-11">
                <div class="input-group input-daterange"> <input type="text" class="form-control input1" placeholder="Start Date" readonly> <input type="text" class="form-control input2" placeholder="End Date" readonly> </div>
            </div>
        </div>
    </form>
</div>

  	<!-- Start Contact Area -->
	<section class="contact-area mt-0 ptb-100">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="shadow bg-white p-4">
						<div class="section-title">
							<h2><?= Yii::t('app', 'Оплатите удобной для Вас платежной системой')?></h2>
						</div>

						<div class="row">
							<div class="col-md-4 offset-md-1">
								<img src="/img/payme_01.png" class="img-fluid" />
							</div>
							<div class="col-md-4 offset-md-2">
								<img src="/img/click.png" class="img-fluid" />
							</div>
						</div>

						

					</div>
				</div>
			</div>
		</div>
	</section>
		<!-- End Contact Area -->


</div>