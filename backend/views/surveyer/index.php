<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SurveyerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Surveyers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="surveyer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Surveyer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'id',
//            'username',
//            'auth_key',
//            'password_hash',
//            'password_reset_token',
            //'status',
            //'verification_token',
            //'partner_id',
            'phone_number',
            'last_name',
            'first_name',
//            'region_id',
            [
                'label'=>Yii::t('app', 'Region'),
                'value'=>'region.name_ru'
            ],
            'email:email',
            [
                'label'=>Yii::t('app', 'Status'),
                'value'=> function($model){
                    return \common\models\Surveyer::STATUSE_LABELS[$model->status];
                }
            ],
            [
                'label' => Yii::t('app', 'created_at'),
                'value' => function ($model) {
                    if ($model->created_at == null)
                        return null;
                   return date('d.m.Y H:i', $model->created_at);
                },
            ],
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
