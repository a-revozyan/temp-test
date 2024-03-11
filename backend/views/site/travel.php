<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Message;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AgeGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Travel applications');
$this->params['breadcrumbs'][] = $this->title;

$template = '{view}';

if(Yii::$app->user->can('/site/travel-delete')) {
    $template .= ' {delete}';
}
?>
<div class="translates-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'partner.name',
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
            'insurer_name',
            'insurer_phone',
            'amount_uzs',
            'amount_usd',
            [
                'label' => 'Просмотрено',
                'format' => 'raw',
                'value' => function($model) {
                    if($model->viewed) {
                         return '<span class="label label-success">Да</span>';
                    } else {
                         return '<span class="label label-danger">Нет</span>';
                    }
                },
            ],
            [
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($model) {
                    if($model->trans) {
                        if($model->trans->status == 1) return 'Waiting';
                        elseif($model->trans->status == 2) return '<span class="text-success">Paid</span>';
                        elseif($model->trans->status == -2) return '<span class="text-danger">Cancelled</span>';
                    } else {
                        return "Not Paid";
                    }
                },
                'filter' => [
                    1 => 'Waiting',
                    2 => 'Paid',
                    -2 => 'Cancelled'
                ]
            ],
            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'template' => $template,
                'urlCreator' => function ($action, $model) {
                    if ($action === 'delete') {
                        return \yii\helpers\Url::to(['site/travel-delete', 'id' => $model->id]);
                    }
                    if ($action === 'view') {
                        return \yii\helpers\Url::to(['site/travel-view', 'id' => $model->id]);
                    }
                }
            ],
        ],
    ]); ?>


</div>
