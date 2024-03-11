<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\OsagoPartnerRatingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Osago Partner Ratings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="osago-partner-rating-index">

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
                    return Html::a('Set rating', ['osago-partner-rating/update', 'partner_id' => $data->id]);
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
