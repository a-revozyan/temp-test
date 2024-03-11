<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\TravelProgramPeriodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Travel Partner data');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-program-period-index">

    <p>
        <?php // echo Html::a(Yii::t('app', 'Create Travel Program Period'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
               'label'=>'',
               'format' => 'raw',
               'value' => function ($data) {
                    return Html::a('Set program periods', ['travel-program-period/set-amounts', 'partner_id' => $data->id]);
                },
            ],
            [
                'label'=>'',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a('Set program multiple periods', ['travel-multiple-period/index', 'partner_id' => $data->id]);
                },
            ],
            [
                'label'=>'',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a('Set family koef', ['travel-family-koef/index', 'partner_id' => $data->id]);
                },
            ],
            [
               'label'=>'',
               'format' => 'raw',
               'value' => function ($data) {
                    return Html::a('Set risk amounts', ['travel-risk/set-amounts', 'partner_id' => $data->id]);
                },
            ],
            [
               'label'=>'',
               'format' => 'raw',
               'value' => function ($data) {
                    return Html::a('Set policy info', ['travel-program-period/set-travel-info', 'partner_id' => $data->id]);
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
