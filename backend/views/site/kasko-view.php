<?php

use common\helpers\GeneralHelper;
use common\models\Kasko;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Transactions;
use common\models\Promo;

/* @var $this yii\web\View */
/* @var $model common\models\Autocomp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'KASKO requests'), 'url' => ['kasko']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

//if($model->id == 300) {$model->policy_number = 'test0001'; $model->save();}


?>
<div class="autocomp-view">

    <p>
        <?php if(Yii::$app->user->can('/site/kasko-delete')) echo Html::a(Yii::t('app', 'Delete'), ['kasko-delete', 'id' => $model->id], [
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
            [
                'label' => 'Марка',
                'value' => function($model) {
                    if($model->autobrand_id == 0) return "Другие";
                    else return $model->autocomp->automodel->autobrand->name;
                }
            ],
            [
                'label' => 'Модель',
                'attribute' => 'autocomp.automodel.name'
            ],
            [
                'label' => 'Комплектация',
                'attribute' => 'autocomp.name'
            ],
            'year',
            [
                'label' => 'Тариф',
                'attribute' => 'tariff.name'
            ],
            'insurer_name',
            'insurer_phone',
            'insurer_passport_number',
            'insurer_passport_series',
            'insurer_pinfl',
            'insurer_tech_pass_series',
            'insurer_tech_pass_number',
            'autonumber',
            'address_delivery',
            'partner.name',
            'begin_date',
            'end_date',
            'price',
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
                'label' => 'Surveyer uploaded photos',
                'format' => "raw",
                'value' => function($model) {
                    $files = $model->getKaskoFile();
                    if ($files->all() == [])
                        return "";
                    $images = $files->where(['type' => \common\models\KaskoFile::TYPE['image']])->all();
                    $images_html = "";
                    foreach ($images as $image) {
                        $image_html = Html::img($image->path, ['width' => "200", 'alt' => "surveyer uploaded photo"]);
                        $a_html = Html::a($image_html, $image->path, ['download' => 'download']) . " , ";
                        $images_html .= $a_html;
                    }
                    return $images_html;
                }
            ],
            [
                'label' => 'Surveyer uploaded files',
                'format' => "raw",
                'value' => function($model) {
                    $files = $model->getKaskoFile();
                    if ($files->all() == [])
                        return "";
                    $docs = $files->where(['type' => \common\models\KaskoFile::TYPE['doc']])->all();
                    $docs_html = "";
                    foreach ($docs as $doc) {
                        $docs_html .= Html::a(basename($doc->path) . " , ", $doc->path, ['download' => 'download']);
                    }
                    return $docs_html;
                }
            ],
            'surveyer_comment',
            [
                'label' => 'Полис',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a('Скачать', GeneralHelper::env('front_website_send_request_url') . 'product/gen-kasko-pdf?id=' . $model->id);
                }
            ],
        ],
    ]) ?>
</div>
