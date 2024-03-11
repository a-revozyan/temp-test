<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\KaskoTariffRiskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Kasko Tariff Risks');
$this->params['breadcrumbs'][] = $this->title;

$a = \common\models\TravelAgeGroup::find()->where(['partner_id' => 2])->all();
foreach($a as $b) {
    $b->delete();
}
?>
<div class="kasko-tariff-risk-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Kasko Tariff Risk'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Tariff',
                'value' => 'tariff.name'
            ],
            [
                'label' => 'Risk',
                'value' => 'risk.name_ru'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
