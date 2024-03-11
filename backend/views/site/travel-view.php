<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\httpclient\Client;
use common\models\Promo;

/* @var $this yii\web\View */
/* @var $model common\models\Autocomp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Insurances'), 'url' => ['travel']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="autocomp-view">

    <p>
        <?php if(Yii::$app->user->can('/site/travel-delete')) Html::a(Yii::t('app', 'Delete'), ['travel-delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Countries',
                'value' => function($model) {
                    $countries = [];
                    foreach($model->travelCountries as $c) {
                        $countries[] = $c->country->name_ru;
                    }
                    return implode(', ', $countries);
                }
            ],
            [
                'label' => 'Программа',
                'attribute' => 'program.name'
            ],
            'partner.name',
            'begin_date',
            'end_date',
            'days',
            'insurer_name',
            'insurer_address',
            'insurer_phone',
            'insurer_passport_series',
            'insurer_passport_number',
            'address_delivery',
            'amount_uzs',
            'amount_usd',
            [
                'label' => 'Create time',
                'format' => 'raw',
                'value' => function($model) {
                    if($model->created_at) {
                      $tz = 'Asia/Tashkent';
                      $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
                      $dt->setTimestamp($model->created_at); //adjust the object to correct timestamp
                      return $dt->format('d.m.Y H:i:s');
                    } else 
                    return '';
                  }
            ],
            'trans.payment_type',
            [
                'label' => 'Payment time',
                'format' => 'raw',
                'value' => function($model) {
                    $tz = 'Asia/Tashkent';
                    if($model->trans) {
                      if($model->trans->payment_type == 'payme') $time = $model->trans->perform_time/1000;
                      else $time = $model->trans->perform_time;

                      $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
                      $dt->setTimestamp($time); //adjust the object to correct timestamp
                      return $dt->format('d.m.Y H:i:s');
                    } else {
                      return '';
                    }
                    
                }
            ],
            [
                'attribute' => 'promo_code',
                'value' => function($model) {
                    $promo = Promo::findOne($model->promo_id);

                    if($promo) return $promo->code;
                    else return null;
                    
                }
            ],
            'promo_percent',
            'promo_amount',
            'trans.status',

            [
                'label' => 'Полис',
                'format' => 'raw',
                'value' => function($model) {
                    if($model->partner_id == 1 && $model->trans) {
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
                        
                        $result = '';

                        $result .= Html::beginForm('https://gross.uz/ru/site/netkost-travel-pdf'); 
                        
                        $result .= Html::hiddenInput('access_token', '85b7ba1e18156c5880f242288046a52b');
                        $result .= Html::hiddenInput('insurer_name', $model->insurer_name);
                        $result .= Html::hiddenInput('insurer_phone', $model->insurer_phone);
                        $result .= Html::hiddenInput('program_name', $model->program->name);
                        $result .= Html::hiddenInput('program_id', $model->program_id);
                        $result .= Html::hiddenInput('countries', $countries);
                        $result .= Html::hiddenInput('purpose_name', $model->purpose->name_en);
                        $result .= Html::hiddenInput('sum', $sum);
                        $result .= Html::hiddenInput('amount_uzs', $model->amount_uzs);
                        $result .= Html::hiddenInput('policy_number', $model->policy_number);
                        $result .= Html::hiddenInput('begin_date', date('d.m.Y', strtotime($model->begin_date)));
                        $result .= Html::hiddenInput('end_date', date('d.m.Y', strtotime($model->end_date)));
                        $result .= Html::hiddenInput('days', $model->days);
                        $result .= Html::hiddenInput('trans_date', date('d.m.Y', strtotime($model->trans->trans_date))); 

                        foreach($model->travelers as $i => $t) {
                          $result .= Html::hiddenInput("travelers[{$i}][name]", $t->name);
                          $result .= Html::hiddenInput("travelers[{$i}][birthday]", date('d.m.Y', strtotime($t->birthday)));
                          $result .= Html::hiddenInput("travelers[{$i}][passport_series]", $t->passport_series);
                          $result .= Html::hiddenInput("travelers[{$i}][passport_number]", $t->passport_number);
                        } 

                        foreach($risks as $i => $t) {
                          $result .= Html::hiddenInput("risks[{$i}][name]", $t['name']);
                          $result .= Html::hiddenInput("risks[{$i}][amount]", $t['amount']);
                        } 
                        
                        $result .= Html::submitButton(Yii::t('app', 'Скачать'), ['class' => 'btn btn-link pl-0']);
                        
                        $result .= Html::endForm();

                        return $result;
    
                    } else {
                        return "";
                    }
                    
                    
                }

            ],
        ],
    ]) ?>

    <div style="margin-top:20px">
        <h3>Travelers</h3>
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <th>Birthday</th>
                <th>Passport data</th>
                <th>Phone</th>
                <th>Address</th>
            </tr>
            <?php
            foreach($model->travelers as $d):
            ?>
            <tr>
                <td><?=$d->name?></td>
                <td><?=$d->birthday?></td>
                <td><?=$d->passport_series . ' ' . $d->passport_number?></td>
                <td><?=$d->phone?></td>
                <td><?=$d->address?></td>
            </tr>
            <?php
            endforeach;
            ?>
        </table>
    </div>

</div>
