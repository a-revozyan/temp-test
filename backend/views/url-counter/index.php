<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UrlCounterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Url Counters');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-counter-index">

<!-- 
    <p>
        <?= Html::a(Yii::t('app', 'Create Url Counter'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'url:url',
            'code',
            'count',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
