<?php

use common\helpers\GeneralHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Promo;

/* @var $this yii\web\View */
/* @var $model common\models\Autocomp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'OSAGO requests'), 'url' => ['osago']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="autocomp-view">

    <p>
        <?php if(Yii::$app->user->can('/site/osago-delete')) echo Html::a(Yii::t('app', 'Delete'), ['osago-delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]); ?>
    </p>

    <?php //echo date('Y-m-d H:i:s', $model->trans->perform_time/1000);?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'partner.name',
            [
                'label' => 'Вид ТС',
                'attribute' => 'autotype.name_ru'
            ],
            [
                'label' => 'Регион',
                'attribute' => 'region.name_ru'
            ],
            [
                'label' => 'Периоды страхования',
                'attribute' => 'period.name_ru'
            ],
            [
                'label' => 'Регистрация ТС',
                'attribute' => 'citizenship.name_ru'
            ],
            [
                'label' => 'Количество водителей',
                'attribute' => 'numberDrivers.name_ru'
            ],
            'trans.status',
            'trans.trans_date',
            'insurer_name',
            'insurer_address',
            'insurer_phone',
            'autonumber',
            'insurer_passport_series',
            'insurer_passport_number',
            'insurer_tech_pass_series',
            'insurer_tech_pass_number',
            [
                'label' => 'Download passport',
                'format' => 'raw',
                'value' => function($model) {
                    return "<a target='_blank' href='". GeneralHelper::env('front_website_send_request_url') ."/uploads/passport_files/osago/".$model->passport_file."'>".$model->passport_file."</a>";
                }
            ],
            [
                'label' => 'Download tech passport front',
                'format' => 'raw',
                'value' => function($model) {
                    return "<a target='_blank' href='" . GeneralHelper::env('front_website_send_request_url') . "/uploads/passport_files/osago/".$model->tech_passport_file_front."'>".$model->tech_passport_file_front."</a>";
                }
            ],
            [
                'label' => 'Download tech passport back',
                'format' => 'raw',
                'value' => function($model) {
                    return "<a target='_blank' href='" . GeneralHelper::env('front_website_send_request_url') . "/uploads/passport_files/osago/".$model->tech_passport_file_back."'>".$model->tech_passport_file_back."</a>";
                }
            ],
            'amount_uzs',
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
        ],
    ]) ?>

    <div style="margin-top:20px">
        <h3>Drivers</h3>
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <th>PINFL</th>
                <th>Серия и номер прав</th>
                <th>Серия и номер паспорта</th>
                <th>Relationship</th>
                <th>License file</th>
            </tr>
            <?php
            foreach($model->drivers as $d):
            ?>
            <tr>
                <td><?=$d->name?></td>
                <td><?=$d->pinfl?></td>
                <td><?=$d->license_series . ' ' . $d->license_number?></td>
                <td><?=$d->passport_series . ' ' . $d->passport_number?></td>
                <td><?=$d->relationship ? $d->relationship->name_ru : ''?></td>
                <td><a target='_blank' href='<?= GeneralHelper::env('front_website_send_request_url') ?>/uploads/license_files/osago/<?=$d->license_file?>'><?=$d->license_file?></a></td>
            </tr>
            <?php
            endforeach;
            ?>
        </table>
    </div>

</div>
