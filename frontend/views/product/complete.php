<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap4\Accordion;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Получить полис');

?>
<div class="container">

<?php
if(is_null($status) || $status == 2):
  ?>
<div class="mt-3 mb-4">
  <div class="bg-white shadow p-4">
<h4 class="text-center"><?=Yii::t('app', 'Спасибо, что выбрали NETKOST!')?></h4>
<div class="row mt-3">
  <div class="col-md-4 offset-md-2">
    <?=Yii::t('app', 'Номер Вашей заявки')?>:
  </div>
  <div class="col-md-4">
    <?php
    if($product == 'travel') echo 'TRAVEL ' . $model->id;
    elseif($product == 'kasko') echo Yii::t('app', 'KACKO') . ' ' . $model->id;
    elseif($product == 'osago') echo Yii::t('app', 'ОСАГО') . ' ' . $model->id;
    elseif($product == 'accident') echo Yii::t('app', 'ACCIDENT') . ' ' . $model->id;

    ?>
  </div>
</div>

<?php
if($model->partner_id == 1):
  if($product == 'travel'):
?>
<div class="row mt-3">
  <div class="col-md-4 offset-md-2">
    <?=Yii::t('app', 'Полис')?>:
  </div>
  <div class="col-md-4">
    <?php
    $countries = \common\models\Country::find()->where(['id' => array_column($model->travelCountries, 'country_id')])->all();
    $countries = implode(', ', array_column($countries, 'name_en'));

    $sum = 0;
    $risks = [];

    foreach($model->program->travelProgramRisks as $pr_risk):
        if($pr_risk->risk_id != 5) :
            $sum += $pr_risk->amount;
            $n = [
                'risk_id' => $pr_risk->risk_id,
                'name' => $pr_risk->risk->name_ru . ' / ' . $pr_risk->risk->name_en,
                'amount' => number_format($pr_risk->amount,0,","," ")
            ];
            $risks[] = $n;
        endif;
    endforeach;

    usort($risks, function ($item1, $item2) {
        return $item1['risk_id'] <=> $item2['risk_id'];
    });

    /*
    'insurer_name' => Yii::$app->request->post('insurer_name'),
                'insurer_phone' => Yii::$app->request->post('insurer_phone'),
                'program_id' => Yii::$app->request->post('program_id'),
                'program_name' => Yii::$app->request->post('program_name'),
                'purpose_name' => Yii::$app->request->post('purpose_name'),
                'countries' => Yii::$app->request->post('countries'),
                'risks' => Yii::$app->request->post('risks'),
                'sum' => Yii::$app->request->post('sum'),
                'travelers' => Yii::$app->request->post('travelers'),
                'begin_date' => Yii::$app->request->post('begin_date'),
                'end_date' => Yii::$app->request->post('end_date'),
                'days' => Yii::$app->request->post('days'),
                'amount_uzs' => Yii::$app->request->post('amount_uzs'),
                'policy_number' => Yii::$app->request->post('policy_number'),
                'trans_date' => Yii::$app->request->post('trans_date'),
    */
    echo Html::beginForm('https://gross.uz/ru/site/netkost-travel-pdf'); 
    
    echo Html::hiddenInput('access_token', '85b7ba1e18156c5880f242288046a52b');
    echo Html::hiddenInput('insurer_name', $model->insurer_name);
    echo Html::hiddenInput('insurer_phone', $model->insurer_phone);
    echo Html::hiddenInput('program_name', $model->program->name);
    echo Html::hiddenInput('program_id', $model->program_id);
    echo Html::hiddenInput('countries', $countries);
    echo Html::hiddenInput('purpose_name', $model->purpose->name_en);
    echo Html::hiddenInput('sum', $sum);
    echo Html::hiddenInput('amount_uzs', $model->amount_uzs);
    echo Html::hiddenInput('policy_number', $model->policy_number);
    echo Html::hiddenInput('begin_date', date('d.m.Y', strtotime($model->begin_date)));
    echo Html::hiddenInput('end_date', date('d.m.Y', strtotime($model->end_date)));
    echo Html::hiddenInput('days', $model->days);
    echo Html::hiddenInput('trans_date', date('d.m.Y', strtotime($model->trans->trans_date))); 

    foreach($model->travelers as $i => $t) {
      echo Html::hiddenInput("travelers[{$i}][name]", $t->name);
      echo Html::hiddenInput("travelers[{$i}][birthday]", date('d.m.Y', strtotime($t->birthday)));
      echo Html::hiddenInput("travelers[{$i}][passport_series]", $t->passport_series);
      echo Html::hiddenInput("travelers[{$i}][passport_number]", $t->passport_number);
    } 

    foreach($risks as $i => $t) {
      echo Html::hiddenInput("risks[{$i}][name]", $t['name']);
      echo Html::hiddenInput("risks[{$i}][amount]", $t['amount']);
    } 
    
    echo Html::submitButton(Yii::t('app', 'Скачать'), ['class' => 'btn btn-link pl-0']);
    
    echo Html::endForm();
    

    ?>
  </div>
</div>
<?php
  elseif($product == 'kasko'):
?>
<div class="row mt-3">
  <div class="col-md-4 offset-md-2">
    <?=Yii::t('app', 'Полис')?>:
  </div>
  <div class="col-md-4">
    <?php 
    echo Html::beginForm('https://gross.uz/ru/site/netkost-kasko-pdf'); 

    echo Html::hiddenInput('access_token', '85b7ba1e18156c5880f242288046a52b');
    echo Html::hiddenInput('insurer_name', $model->insurer_name);
    echo Html::hiddenInput('insurer_phone', $model->insurer_phone);
    echo Html::hiddenInput('tariff_name', $model->tariff->name);
    echo Html::hiddenInput('tariff_id', $model->tariff_id);
    echo Html::hiddenInput('autobrand_name', $model->autocomp->automodel->autobrand->name);
    echo Html::hiddenInput('automodel_name', $model->autocomp->automodel->name);
    echo Html::hiddenInput('year', $model->year);
    echo Html::hiddenInput('autonumber', $model->autonumber);
    echo Html::hiddenInput('autoprice', $model->autocomp->price);
    echo Html::hiddenInput('price', $model->price);
    echo Html::hiddenInput('amount_uzs', $model->amount_uzs);
    echo Html::hiddenInput('policy_number', $model->policy_number);
    echo Html::hiddenInput('begin_date', $model->begin_date);
    echo Html::hiddenInput('trans_date', date('d.m.Y', strtotime($model->trans->trans_date)));    
    
    echo Html::submitButton(Yii::t('app', 'Скачать'), ['class' => 'btn btn-link pl-0']);
    
    echo Html::endForm();
    ?>
  </div>
</div>
<?php
  elseif($product == 'accident'):
?>
<div class="row mt-3">
  <div class="col-md-4 offset-md-2">
    <?=Yii::t('app', 'Полис')?>:
  </div>
  <div class="col-md-4">
    <?php 
    echo Html::beginForm('https://gross.uz/ru/site/netkost-accident-pdf'); 
    
    echo Html::hiddenInput('access_token', '85b7ba1e18156c5880f242288046a52b');
    echo Html::hiddenInput('insurer_name', $model->insurer_name);
    echo Html::hiddenInput('insurer_phone', $model->insurer_phone);
    echo Html::hiddenInput('insurer_birthday', $model->insurer_birthday);
    echo Html::hiddenInput('insurer_passport_series', $model->insurer_passport_series);
    echo Html::hiddenInput('insurer_passport_number', $model->insurer_passport_number);
    echo Html::hiddenInput('insurance_amount', $model->insurance_amount);
    echo Html::hiddenInput('amount_uzs', $model->amount_uzs);
    echo Html::hiddenInput('policy_number', $model->policy_number);
    echo Html::hiddenInput('begin_date', $model->begin_date);
    echo Html::hiddenInput('end_date', $model->end_date);
    echo Html::hiddenInput('trans_date', date('d.m.Y', strtotime($model->trans->trans_date))); 

    foreach($model->accidentInsurers as $i => $t) {
      echo Html::hiddenInput("insurers[{$i}][name]", $t->name);
      echo Html::hiddenInput("insurers[{$i}][birthday]", date('d.m.Y', strtotime($t->birthday)));
      echo Html::hiddenInput("insurers[{$i}][passport_series]", $t->passport_series);
      echo Html::hiddenInput("insurers[{$i}][passport_number]", $t->passport_number);
    } 
    
    echo Html::submitButton(Yii::t('app', 'Скачать'), ['class' => 'btn btn-link pl-0']);
    
    echo Html::endForm();
    ?>
  </div>
</div>
<?php
  else:
?>
<div class="row mt-3">
  <div class="col-md-8   offset-md-2">
    <?=Yii::t('app', 'Ваш полис будет доставлен Вам по указанному адресу'). ' : ' . $model->address_delivery?>
  </div>
</div>
<?php
  endif;
else:
?>

<div class="row mt-3">
  <div class="col-md-8   offset-md-2">
    <?=Yii::t('app', 'Ваш полис будет доставлен Вам по указанному адресу'). ' : ' . $model->address_delivery?>
  </div>
</div>
<?php
endif;
?>

 </div>
</div>
<?php
endif;
?>
</div>