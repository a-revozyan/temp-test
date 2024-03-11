<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PartnerProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Partner Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-product-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Partner Product'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'partner',
                'value' => 'partner.name'
            ],
            [
                'attribute' => 'product',
                'value' => 'product.name'
            ],
            'percent',
            'star',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
