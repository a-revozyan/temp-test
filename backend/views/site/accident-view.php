<?php

use common\helpers\GeneralHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Transactions;
use common\models\Promo;

/* @var $this yii\web\View */
/* @var $model common\models\Autocomp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Accident requests'), 'url' => ['accident']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

//if($model->id == 300) {$model->policy_number = 'test0001'; $model->save();}


?>
<div class="autocomp-view">

    <p>
        <?php if(Yii::$app->user->can('/site/accident-delete')) echo Html::a(Yii::t('app', 'Delete'), ['accident-delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]); ?>
    </p>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'insurer_name',
            'insurer_phone',
            'insurer_birthday',
            [
                'label' => 'Download passport',
                'format' => 'raw',
                'value' => function($model) {
                    return "<a target='_blank' href='". GeneralHelper::env('front_website_send_request_url') ."/uploads/passport_files/accident/".
                    $model->insurer_passport_file."'>".$model->insurer_passport_file."</a>";
                }
            ],
            'address_delivery',
            'partner.name',
            'begin_date',
            'end_date',
            'insurance_amount',
            'amount_uzs',
            'trans.status',
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
            [
                'label' => 'Полис',
                'format' => 'raw',
                'value' => function($model) {
                    if($model->partner_id == 1 && $model->trans) {
                        $result = '';
                        $result .= Html::beginForm('https://gross.uz/ru/site/netkost-accident-pdf'); 
                        
                        $result .= Html::hiddenInput('access_token', '85b7ba1e18156c5880f242288046a52b');
                        $result .= Html::hiddenInput('insurer_name', $model->insurer_name);
                        $result .= Html::hiddenInput('insurer_phone', $model->insurer_phone);
                        $result .= Html::hiddenInput('insurer_birthday', $model->insurer_birthday);
                        $result .= Html::hiddenInput('insurer_passport_series', $model->insurer_passport_series);
                        $result .= Html::hiddenInput('insurer_passport_number', $model->insurer_passport_number);
                        $result .= Html::hiddenInput('insurance_amount', $model->insurance_amount);
                        $result .= Html::hiddenInput('amount_uzs', $model->amount_uzs);
                        $result .= Html::hiddenInput('policy_number', $model->policy_number);
                        $result .= Html::hiddenInput('begin_date', date('d.m.Y', strtotime($model->begin_date)));
                        $result .= Html::hiddenInput('end_date', date('d.m.Y', strtotime($model->end_date)));
                        $result .= Html::hiddenInput('trans_date', date('d.m.Y', strtotime($model->trans->trans_date))); 

                        foreach($model->accidentInsurers as $i => $t) {
                          $result .= Html::hiddenInput("insurers[{$i}][name]", $t->name);
                          $result .= Html::hiddenInput("insurers[{$i}][birthday]", date('d.m.Y', strtotime($t->birthday)));
                          $result .= Html::hiddenInput("insurers[{$i}][passport_series]", $t->passport_series);
                          $result .= Html::hiddenInput("insurers[{$i}][passport_number]", $t->passport_number);
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
        <h3>Insurers</h3>
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <th>Birthday</th>
                <th>Passport</th>
            </tr>
            <?php
            foreach($model->accidentInsurers as $d):
            ?>
            <tr>
                <td><?=$d->name?></td>
                <td><?=$d->birthday?></td>
                <td><?="<a target='_blank' href='". GeneralHelper::env('front_website_send_request_url') ."/uploads/passport_files/accident/".
                    $d->passport_file."'>".$d->passport_file."</a>";?></td>
            </tr>
            <?php
            endforeach;
            ?>
        </table>
    </div>



</div>
