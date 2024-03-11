<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\OsagoAmountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Osago Amounts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="osago-amount-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'insurance_premium',
            'insurance_amount',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'template' => '{update} {view}',
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
