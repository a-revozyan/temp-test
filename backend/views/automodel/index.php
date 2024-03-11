<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\AutomodelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Automodels');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="automodel-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Automodel'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label'=>'Autobrand',
                'value'=>'autobrand.name'
            ],
            'name',
            'order',
            [
                'attribute' => 'auto_risk_type_name',
                'label' => 'auto-risk-type',
                'value' => 'autoRiskType.name'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
