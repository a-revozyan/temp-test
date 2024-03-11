<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Message;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AgeGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'KASKO requests');
$this->params['breadcrumbs'][] = $this->title;
$template = '{view}';

if(Yii::$app->user->can('/site/kasko-delete')) {
    $template .= ' {delete} {remove_from_surveyer}';
}

$remove_button = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-minus" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M5.5 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1-.5-.5z"/>
  <path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911l-1.318.016z"/>
</svg>';
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
            'amount_uzs',
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
                        return \yii\helpers\Url::to(['site/kasko-delete', 'id' => $model->id]);
                    }
                    if ($action === 'view') {
                        return \yii\helpers\Url::to(['site/kasko-view', 'id' => $model->id]);
                    }
                },
                'buttons' => [
                        'remove_from_surveyer' => function($url, $model, $key) use($remove_button) {     // render your custom button
                            return Html::a($remove_button, \yii\helpers\Url::to(['site/kasko-remove-from-surveyer', 'id' => $model->id]));
                        }
                ]
            ],
        ],
    ]); ?>


</div>
