<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Meta Tags');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'url',
            'description_ru',
            // 'description_uz',
            // 'description_en',
            'keywords',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
